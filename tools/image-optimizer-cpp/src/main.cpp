#include "CacheOne.h"
#include "CliOptions.h"
#include "FileScanner.h"
#include "ImageOptimizer.h"
#include "SafeReplace.h"
#include "Stats.h"
#include "ThreadPool.h"

#include <chrono>
#include <filesystem>
#include <fstream>
#include <iostream>
#include <mutex>
#include <sstream>
#include <string>
#include <thread>
#include <vector>
#include <vips/vips.h>

namespace fs = std::filesystem;

namespace {

class Logger {
public:
    bool open(const fs::path& path, std::string& error)
    {
        if (path.empty()) {
            return true;
        }

        std::error_code ec;
        if (!path.parent_path().empty()) {
            fs::create_directories(path.parent_path(), ec);
            if (ec) {
                error = "Cannot create log directory: " + ec.message();
                return false;
            }
        }

        file_.open(path, std::ios::out | std::ios::trunc);
        if (!file_) {
            error = "Cannot open log file: " + path.string();
            return false;
        }
        return true;
    }

    void info(const std::string& message, bool toStdout = true)
    {
        write(message, toStdout, false);
    }

    void error(const std::string& message)
    {
        write(message, true, true);
    }

private:
    void write(const std::string& message, bool toStdout, bool toStderr)
    {
        std::lock_guard<std::mutex> lock(mutex_);
        if (file_) {
            file_ << message << '\n';
            file_.flush();
        }
        if (toStdout) {
            (toStderr ? std::cerr : std::cout) << message << '\n';
        }
    }

    std::mutex mutex_;
    std::ofstream file_;
};

std::string formatBytes(std::uint64_t bytes)
{
    double value = static_cast<double>(bytes);
    const char* units[] = {"B", "KB", "MB", "GB", "TB"};
    std::size_t unit = 0;
    while (value >= 1024.0 && unit < 4) {
        value /= 1024.0;
        ++unit;
    }

    std::ostringstream out;
    out.setf(std::ios::fixed);
    out.precision(value >= 10.0 ? 1 : 2);
    out << value << " " << units[unit];
    return out.str();
}

bool reserveLimit(const CliOptions& options, Stats& stats)
{
    if (options.limit == 0) {
        return true;
    }

    std::uint64_t current = stats.limitReserved.load(std::memory_order_relaxed);
    while (current < options.limit) {
        if (stats.limitReserved.compare_exchange_weak(
                current,
                current + 1,
                std::memory_order_relaxed,
                std::memory_order_relaxed)) {
            return true;
        }
    }

    return false;
}

void releaseLimit(const CliOptions& options, Stats& stats)
{
    if (options.limit > 0) {
        stats.limitReserved.fetch_sub(1, std::memory_order_relaxed);
    }
}

void addProcessedBytes(Stats& stats, const ProcessResult& result)
{
    stats.bytesBefore.fetch_add(result.originalSize, std::memory_order_relaxed);
    stats.bytesAfter.fetch_add(result.newSize == 0 ? result.originalSize : result.newSize, std::memory_order_relaxed);
}

void addCheckedOriginalBytes(Stats& stats, const ProcessResult& result)
{
    stats.bytesBefore.fetch_add(result.originalSize, std::memory_order_relaxed);
    stats.bytesAfter.fetch_add(result.originalSize, std::memory_order_relaxed);
}

void updateStatsAndLog(
    const CliOptions& options,
    Stats& stats,
    Logger& logger,
    BoundedQueue<ImageTask>& queue,
    std::atomic<bool>& stop,
    const ProcessResult& result)
{
    const bool showSkipped = options.showSkipped || options.verbose;

    switch (result.status) {
    case ProcessStatus::Optimized:
        stats.optimized.fetch_add(1, std::memory_order_relaxed);
        if (result.byDimensions) {
            stats.optimizedByDimensions.fetch_add(1, std::memory_order_relaxed);
        }
        addProcessedBytes(stats, result);
        logger.info(result.message, true);
        break;
    case ProcessStatus::WouldOptimize:
        stats.wouldOptimize.fetch_add(1, std::memory_order_relaxed);
        if (result.byDimensions) {
            stats.wouldOptimizeByDimensions.fetch_add(1, std::memory_order_relaxed);
        }
        addProcessedBytes(stats, result);
        logger.info(result.message, true);
        break;
    case ProcessStatus::SkippedSmallAndWithinDimensions:
        stats.skippedSmallAndWithinDimensions.fetch_add(1, std::memory_order_relaxed);
        logger.info(result.message, showSkipped);
        break;
    case ProcessStatus::SkippedDimensionsBelowLimit:
        stats.skippedDimensionsBelowSizeLimit.fetch_add(1, std::memory_order_relaxed);
        logger.info(result.message, showSkipped);
        break;
    case ProcessStatus::SkippedDimensionsGrowthLimit:
        stats.skippedDimensionsGrowthLimit.fetch_add(1, std::memory_order_relaxed);
        addCheckedOriginalBytes(stats, result);
        logger.info(result.message, showSkipped);
        break;
    case ProcessStatus::SkippedUnsupportedFormat:
        stats.skippedUnsupportedFormat.fetch_add(1, std::memory_order_relaxed);
        logger.info(result.message, showSkipped);
        break;
    case ProcessStatus::SkippedPngDisabled:
        stats.skippedPngDisabled.fetch_add(1, std::memory_order_relaxed);
        logger.info(result.message, showSkipped);
        break;
    case ProcessStatus::SkippedSavingBelowFivePercent:
        stats.skippedSavingBelowFivePercent.fetch_add(1, std::memory_order_relaxed);
        addCheckedOriginalBytes(stats, result);
        logger.info(result.message, showSkipped);
        break;
    case ProcessStatus::LimitReached:
        stats.stoppedByLimit.store(true, std::memory_order_relaxed);
        stop.store(true, std::memory_order_relaxed);
        queue.close();
        logger.info(result.message, options.verbose);
        break;
    case ProcessStatus::Error:
        stats.errors.fetch_add(1, std::memory_order_relaxed);
        logger.error(result.message);
        break;
    }
}

void printSummary(const CliOptions& options, const Stats& stats, Logger& logger, std::chrono::steady_clock::time_point started)
{
    const auto elapsed = std::chrono::duration_cast<std::chrono::milliseconds>(
        std::chrono::steady_clock::now() - started);
    const std::uint64_t before = stats.bytesBefore.load(std::memory_order_relaxed);
    const std::uint64_t after = stats.bytesAfter.load(std::memory_order_relaxed);
    const std::uint64_t saved = before > after ? before - after : 0;

    logger.info("", true);
    logger.info("Summary", true);
    logger.info("Scanned files: " + std::to_string(stats.scannedFiles.load()), true);
    logger.info("Checked image files: " + std::to_string(stats.checkedImageFiles.load()), true);
    logger.info("Optimized: " + std::to_string(stats.optimized.load()), true);
    logger.info("Optimized because dimensions exceeded limit: " + std::to_string(stats.optimizedByDimensions.load()), true);
    logger.info("Would optimize: " + std::to_string(stats.wouldOptimize.load()), true);
    logger.info("Would optimize because dimensions exceeded limit: " + std::to_string(stats.wouldOptimizeByDimensions.load()), true);
    logger.info("Skipped below size and within dimensions: " + std::to_string(stats.skippedSmallAndWithinDimensions.load()), true);
    logger.info("Skipped dimensions below " + std::to_string(options.skipDimensionResizeBelowKb)
        + " KB: " + std::to_string(stats.skippedDimensionsBelowSizeLimit.load()), true);
    logger.info("Skipped by dimensions growth limit: " + std::to_string(stats.skippedDimensionsGrowthLimit.load()), true);
    logger.info("Skipped unsupported format: " + std::to_string(stats.skippedUnsupportedFormat.load()), true);
    logger.info("Skipped PNG disabled: " + std::to_string(stats.skippedPngDisabled.load()), true);
    logger.info("Skipped saving below 5%: " + std::to_string(stats.skippedSavingBelowFivePercent.load()), true);
    logger.info("Errors: " + std::to_string(stats.errors.load()), true);
    logger.info("Before: " + formatBytes(before), true);
    logger.info("After: " + formatBytes(after), true);
    logger.info("Saved: " + formatBytes(saved), true);
    logger.info("Elapsed time: " + std::to_string(elapsed.count() / 1000.0) + " sec", true);
    logger.info("Threads: " + std::to_string(options.threads), true);
    if (stats.stoppedByLimit.load(std::memory_order_relaxed)) {
        logger.info("Stopped by limit: " + std::to_string(options.limit), true);
    }
}

} // namespace

int main(int argc, char** argv)
{
    if (argc > 1 && std::string(argv[1]) == "cache-one") {
        return runCacheOne(argc - 1, argv + 1);
    }

    CliOptions options;
    std::string error;
    bool showHelp = false;
    if (!parseCliOptions(argc, argv, options, error, showHelp)) {
        std::cerr << error << "\n\n" << cliUsage(argv[0]);
        return 2;
    }
    if (showHelp) {
        std::cout << cliUsage(argv[0]);
        return 0;
    }

    std::error_code ec;
    options.path = fs::weakly_canonical(options.path, ec);
    if (ec) {
        options.path = fs::absolute(options.path);
    }

    if (!fs::is_directory(options.path)) {
        std::cerr << "Directory does not exist: " << options.path.string() << '\n';
        return 2;
    }

    Logger logger;
    if (!logger.open(options.logPath, error)) {
        std::cerr << error << '\n';
        return 2;
    }

    vsemerch::LockFile lock;
    if (!lock.acquire(options.path, options.forceLock, error)) {
        logger.error(error);
        return 1;
    }

    if (VIPS_INIT(argv[0])) {
        logger.error("libvips initialization failed");
        return 1;
    }
    vips_concurrency_set(1);
    vips_cache_set_max(0);

    const auto started = std::chrono::steady_clock::now();
    Stats stats;
    std::atomic<bool> stop{false};
    BoundedQueue<ImageTask> queue(static_cast<std::size_t>(std::max(128, options.threads * 64)));

    logger.info("Scanning: " + options.path.string(), true);
    logger.info("Minimum size: " + std::to_string(options.minSizeKb) + " KB, max dimensions: "
        + std::to_string(options.maxWidth) + "x" + std::to_string(options.maxHeight)
        + ", quality: " + std::to_string(options.quality), true);
    logger.info("Skip dimension resize below: " + std::to_string(options.skipDimensionResizeBelowKb) + " KB", true);
    logger.info(std::string("PNG processing: ") + (options.processPng ? "enabled" : "disabled"), true);
    logger.info("Limit: " + std::to_string(options.limit) + " optimized/would optimize files", true);
    logger.info("Log: " + (options.logPath.empty() ? std::string("(disabled)") : options.logPath.string()), true);
    if (options.dryRun) {
        logger.info("Dry run: files will not be replaced.", true);
    }

    FileScanner scanner(options.path, stats, [&](const std::string& message) {
        stats.errors.fetch_add(1, std::memory_order_relaxed);
        logger.error(message);
    });

    std::thread scannerThread([&] {
        scanner.scan(queue, stop);
        queue.close();
    });

    std::vector<std::thread> workers;
    workers.reserve(static_cast<std::size_t>(options.threads));
    for (int i = 0; i < options.threads; ++i) {
        workers.emplace_back([&] {
            ImageOptimizer optimizer(options);
            ImageTask task;
            while (!stop.load(std::memory_order_relaxed) && queue.pop(task)) {
                ProcessResult result = optimizer.process(
                    task.path,
                    [&] { return reserveLimit(options, stats); },
                    [&] { releaseLimit(options, stats); });
                updateStatsAndLog(options, stats, logger, queue, stop, result);
            }
        });
    }

    scannerThread.join();
    for (auto& worker : workers) {
        worker.join();
    }

    printSummary(options, stats, logger, started);

    lock.release();
    vips_shutdown();

    return stats.errors.load(std::memory_order_relaxed) > 0 ? 1 : 0;
}
