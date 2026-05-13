#pragma once

#include "CliOptions.h"

#include <cstdint>
#include <filesystem>
#include <functional>
#include <string>

typedef struct _VipsImage VipsImage;

struct Dimensions {
    int width = 0;
    int height = 0;
};

enum class ProcessStatus {
    Optimized,
    WouldOptimize,
    SkippedSmallAndWithinDimensions,
    SkippedDimensionsBelowLimit,
    SkippedDimensionsGrowthLimit,
    SkippedUnsupportedFormat,
    SkippedPngDisabled,
    SkippedSavingBelowFivePercent,
    LimitReached,
    Error,
};

struct ProcessResult {
    ProcessStatus status = ProcessStatus::Error;
    std::filesystem::path path;
    std::string message;
    std::uint64_t originalSize = 0;
    std::uint64_t newSize = 0;
    Dimensions originalDimensions;
    Dimensions targetDimensions;
    bool byDimensions = false;
    double growthPercent = 0.0;
};

class ImageOptimizer {
public:
    using ReserveCallback = std::function<bool()>;
    using ReleaseCallback = std::function<void()>;

    explicit ImageOptimizer(CliOptions options);

    ProcessResult process(
        const std::filesystem::path& path,
        const ReserveCallback& reserveOptimization,
        const ReleaseCallback& releaseOptimization) const;

private:
    bool isSupportedExtension(const std::string& extension) const;
    static std::string normalizedExtension(const std::filesystem::path& path);
    static bool isTooLargeByDimensions(const Dimensions& dimensions, const CliOptions& options);
    static Dimensions calculateTargetDimensions(const Dimensions& dimensions, const CliOptions& options);
    static std::string formatBytes(std::uint64_t bytes);
    static std::string formatDimensions(const Dimensions& dimensions);
    static double calculateGrowthPercent(std::uint64_t originalSize, std::uint64_t newSize);

    ProcessResult skip(ProcessStatus status, const std::filesystem::path& path, const std::string& message) const;
    void saveImage(const std::string& extension, const std::filesystem::path& tempPath, VipsImage* image) const;

    CliOptions options_;
};
