#include "ImageOptimizer.h"

#include "SafeReplace.h"

#include <algorithm>
#include <cctype>
#include <cmath>
#include <filesystem>
#include <iomanip>
#include <memory>
#include <sstream>
#include <stdexcept>
#include <vips/vips.h>

namespace fs = std::filesystem;

namespace {

constexpr double minSavingRatio = 0.05;

struct VipsImageDeleter {
    void operator()(VipsImage* image) const
    {
        if (image != nullptr) {
            g_object_unref(image);
        }
    }
};

using VipsImagePtr = std::unique_ptr<VipsImage, VipsImageDeleter>;

std::uint64_t fileSize(const fs::path& path)
{
    std::error_code ec;
    const auto size = fs::file_size(path, ec);
    if (ec) {
        throw std::runtime_error("cannot read file size: " + ec.message());
    }
    return static_cast<std::uint64_t>(size);
}

std::runtime_error vipsError(const std::string& fallback)
{
    const char* buffer = vips_error_buffer();
    std::string message = buffer != nullptr && *buffer != '\0' ? buffer : fallback;
    vips_error_clear();
    return std::runtime_error(message);
}

} // namespace

ImageOptimizer::ImageOptimizer(CliOptions options)
    : options_(std::move(options))
{
}

ProcessResult ImageOptimizer::process(
    const fs::path& path,
    const ReserveCallback& reserveOptimization,
    const ReleaseCallback& releaseOptimization) const
{
    ProcessResult result;
    result.path = path;

    const std::string extension = normalizedExtension(path);
    if (extension == "png" && !options_.processPng) {
        return skip(ProcessStatus::SkippedPngDisabled, path, "png disabled: " + path.string());
    }

    if (!isSupportedExtension(extension)) {
        return skip(ProcessStatus::SkippedUnsupportedFormat, path, "unsupported: " + path.string());
    }

    fs::path tempPath;
    bool reserved = false;

    try {
        result.originalSize = fileSize(path);

        VipsImagePtr image(vips_image_new_from_file(
            vsemerch::pathToUtf8(path).c_str(),
            "access",
            VIPS_ACCESS_SEQUENTIAL,
            nullptr));
        if (!image) {
            throw vipsError("cannot read image");
        }

        if (extension == "jpg" || extension == "jpeg") {
            VipsImage* rotated = nullptr;
            if (vips_autorot(image.get(), &rotated, nullptr) != 0) {
                throw vipsError("cannot autorotate image");
            }
            image.reset(rotated);
        }

        result.originalDimensions = Dimensions{vips_image_get_width(image.get()), vips_image_get_height(image.get())};
        if (result.originalDimensions.width <= 0 || result.originalDimensions.height <= 0) {
            throw std::runtime_error("invalid image dimensions");
        }

        const std::uint64_t minBytes = options_.minSizeKb * 1024ULL;
        const std::uint64_t skipDimensionResizeBelowBytes = options_.skipDimensionResizeBelowKb * 1024ULL;
        const bool tooLargeByDimensions = isTooLargeByDimensions(result.originalDimensions, options_);

        if (tooLargeByDimensions && result.originalSize < skipDimensionResizeBelowBytes) {
            result.status = ProcessStatus::SkippedDimensionsBelowLimit;
            result.message = "skip dimensions below " + std::to_string(options_.skipDimensionResizeBelowKb)
                + " KB: " + path.string() + " " + formatBytes(result.originalSize)
                + ", " + formatDimensions(result.originalDimensions);
            return result;
        }

        if (!(result.originalSize > minBytes || tooLargeByDimensions)) {
            result.status = ProcessStatus::SkippedSmallAndWithinDimensions;
            result.message = "small and within dimensions: " + path.string() + " "
                + formatBytes(result.originalSize) + ", " + formatDimensions(result.originalDimensions);
            return result;
        }

        result.targetDimensions = calculateTargetDimensions(result.originalDimensions, options_);
        const bool resized = result.targetDimensions.width != result.originalDimensions.width
            || result.targetDimensions.height != result.originalDimensions.height;

        if (resized) {
            const double scaleX = static_cast<double>(result.targetDimensions.width)
                / static_cast<double>(result.originalDimensions.width);
            const double scaleY = static_cast<double>(result.targetDimensions.height)
                / static_cast<double>(result.originalDimensions.height);
            VipsImage* resizedImage = nullptr;
            if (vips_resize(image.get(), &resizedImage, scaleX, "vscale", scaleY, nullptr) != 0) {
                throw vipsError("cannot resize image");
            }
            image.reset(resizedImage);
        }

        tempPath = vsemerch::buildTemporaryPath(path);
        saveImage(extension, tempPath, image.get());
        result.newSize = fileSize(tempPath);
        if (result.newSize == 0) {
            throw std::runtime_error("optimized file is empty");
        }

        const double savingRatio = (static_cast<double>(result.originalSize) - static_cast<double>(result.newSize))
            / static_cast<double>(result.originalSize);
        const bool wasTooLargeBySize = result.originalSize > minBytes;
        const bool canReplaceBySaving = wasTooLargeBySize
            && result.newSize < result.originalSize
            && savingRatio >= minSavingRatio;

        const bool canReplaceByDimensionsShape = tooLargeByDimensions
            && resized
            && (result.targetDimensions.width < result.originalDimensions.width
                || result.targetDimensions.height < result.originalDimensions.height)
            && result.targetDimensions.width <= options_.maxWidth
            && result.targetDimensions.height <= options_.maxHeight;

        const double growthLimit = static_cast<double>(result.originalSize)
            * (1.0 + (options_.maxGrowthPercentForDimensions / 100.0));
        const bool canReplaceByDimensions = canReplaceByDimensionsShape
            && static_cast<double>(result.newSize) <= growthLimit;

        result.growthPercent = calculateGrowthPercent(result.originalSize, result.newSize);

        if (!canReplaceBySaving && canReplaceByDimensionsShape && !canReplaceByDimensions) {
            vsemerch::removeQuietly(tempPath);
            result.status = ProcessStatus::SkippedDimensionsGrowthLimit;
            result.message = "skip dimensions growth: " + path.string() + " "
                + formatBytes(result.originalSize) + " -> " + formatBytes(result.newSize)
                + ", " + formatDimensions(result.originalDimensions) + " -> " + formatDimensions(result.targetDimensions)
                + ", growth " + [&] {
                    std::ostringstream out;
                    out << std::fixed << std::setprecision(1) << result.growthPercent << "%";
                    return out.str();
                }();
            return result;
        }

        if (!canReplaceBySaving && !canReplaceByDimensions) {
            vsemerch::removeQuietly(tempPath);
            result.status = ProcessStatus::SkippedSavingBelowFivePercent;
            result.message = "not worth replacing: " + path.string() + " "
                + formatBytes(result.originalSize) + " -> " + formatBytes(result.newSize)
                + ", " + formatDimensions(result.originalDimensions) + " -> " + formatDimensions(result.targetDimensions);
            return result;
        }

        if (!reserveOptimization()) {
            vsemerch::removeQuietly(tempPath);
            result.status = ProcessStatus::LimitReached;
            result.message = "stopped by limit";
            return result;
        }
        reserved = true;

        result.byDimensions = canReplaceByDimensions;
        result.status = options_.dryRun ? ProcessStatus::WouldOptimize : ProcessStatus::Optimized;
        result.message = std::string(options_.dryRun ? "would optimize: " : "optimized: ") + path.string()
            + " " + formatBytes(result.originalSize) + " -> " + formatBytes(result.newSize)
            + ", " + formatDimensions(result.originalDimensions) + " -> " + formatDimensions(result.targetDimensions);

        if (options_.dryRun) {
            vsemerch::removeQuietly(tempPath);
            return result;
        }

        std::string replaceError;
        if (!vsemerch::safeReplaceOriginal(path, tempPath, replaceError)) {
            if (reserved) {
                releaseOptimization();
                reserved = false;
            }
            vsemerch::removeQuietly(tempPath);
            throw std::runtime_error(replaceError);
        }

        return result;
    } catch (const std::exception& error) {
        if (reserved) {
            releaseOptimization();
        }
        if (!tempPath.empty()) {
            vsemerch::removeQuietly(tempPath);
        }
        result.status = ProcessStatus::Error;
        result.message = "error: " + path.string() + ": " + error.what();
        return result;
    }
}

bool ImageOptimizer::isSupportedExtension(const std::string& extension) const
{
    return extension == "jpg"
        || extension == "jpeg"
        || extension == "webp"
        || (extension == "png" && options_.processPng);
}

std::string ImageOptimizer::normalizedExtension(const fs::path& path)
{
    std::string extension = path.extension().string();
    if (!extension.empty() && extension.front() == '.') {
        extension.erase(extension.begin());
    }
    std::transform(extension.begin(), extension.end(), extension.begin(), [](unsigned char c) {
        return static_cast<char>(std::tolower(c));
    });
    return extension;
}

bool ImageOptimizer::isTooLargeByDimensions(const Dimensions& dimensions, const CliOptions& options)
{
    return dimensions.width > options.maxWidth || dimensions.height > options.maxHeight;
}

Dimensions ImageOptimizer::calculateTargetDimensions(const Dimensions& dimensions, const CliOptions& options)
{
    const double scale = std::min({
        static_cast<double>(options.maxWidth) / static_cast<double>(dimensions.width),
        static_cast<double>(options.maxHeight) / static_cast<double>(dimensions.height),
        1.0,
    });

    return Dimensions{
        std::max(1, static_cast<int>(std::llround(static_cast<double>(dimensions.width) * scale))),
        std::max(1, static_cast<int>(std::llround(static_cast<double>(dimensions.height) * scale))),
    };
}

ProcessResult ImageOptimizer::skip(ProcessStatus status, const fs::path& path, const std::string& message) const
{
    ProcessResult result;
    result.status = status;
    result.path = path;
    result.message = message;
    return result;
}

void ImageOptimizer::saveImage(const std::string& extension, const fs::path& tempPath, VipsImage* image) const
{
    const std::string temp = vsemerch::pathToUtf8(tempPath);

    if (extension == "jpg" || extension == "jpeg") {
        if (vips_jpegsave(
                image,
                temp.c_str(),
                "Q",
                options_.quality,
                "interlace",
                TRUE,
                "strip",
                TRUE,
                nullptr) != 0) {
            throw vipsError("cannot save jpeg");
        }
        return;
    }

    if (extension == "webp") {
        if (vips_webpsave(
                image,
                temp.c_str(),
                "Q",
                options_.quality,
                "strip",
                TRUE,
                nullptr) != 0) {
            throw vipsError("cannot save webp");
        }
        return;
    }

    if (extension == "png") {
        if (vips_pngsave(
                image,
                temp.c_str(),
                "compression",
                8,
                "strip",
                TRUE,
                nullptr) != 0) {
            throw vipsError("cannot save png");
        }
        return;
    }

    throw std::runtime_error("unsupported extension: " + extension);
}

std::string ImageOptimizer::formatBytes(std::uint64_t bytes)
{
    double value = static_cast<double>(bytes);
    const char* units[] = {"B", "KB", "MB", "GB", "TB"};
    std::size_t unit = 0;
    while (value >= 1024.0 && unit < 4) {
        value /= 1024.0;
        ++unit;
    }

    std::ostringstream out;
    out << std::fixed << std::setprecision(value >= 10.0 ? 1 : 2) << value << " " << units[unit];
    return out.str();
}

std::string ImageOptimizer::formatDimensions(const Dimensions& dimensions)
{
    return std::to_string(dimensions.width) + "x" + std::to_string(dimensions.height);
}

double ImageOptimizer::calculateGrowthPercent(std::uint64_t originalSize, std::uint64_t newSize)
{
    if (originalSize == 0) {
        return 0.0;
    }

    return ((static_cast<double>(newSize) - static_cast<double>(originalSize))
        / static_cast<double>(originalSize)) * 100.0;
}
