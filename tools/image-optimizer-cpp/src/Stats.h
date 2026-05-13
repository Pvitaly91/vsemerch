#pragma once

#include <atomic>
#include <cstdint>

struct Stats {
    std::atomic<std::uint64_t> scannedFiles{0};
    std::atomic<std::uint64_t> checkedImageFiles{0};
    std::atomic<std::uint64_t> optimized{0};
    std::atomic<std::uint64_t> optimizedByDimensions{0};
    std::atomic<std::uint64_t> wouldOptimize{0};
    std::atomic<std::uint64_t> wouldOptimizeByDimensions{0};
    std::atomic<std::uint64_t> skippedSmallAndWithinDimensions{0};
    std::atomic<std::uint64_t> skippedDimensionsBelowSizeLimit{0};
    std::atomic<std::uint64_t> skippedDimensionsGrowthLimit{0};
    std::atomic<std::uint64_t> skippedUnsupportedFormat{0};
    std::atomic<std::uint64_t> skippedPngDisabled{0};
    std::atomic<std::uint64_t> skippedSavingBelowFivePercent{0};
    std::atomic<std::uint64_t> errors{0};
    std::atomic<std::uint64_t> bytesBefore{0};
    std::atomic<std::uint64_t> bytesAfter{0};
    std::atomic<std::uint64_t> limitReserved{0};
    std::atomic<bool> stoppedByLimit{false};
};
