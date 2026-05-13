#pragma once

#include <cstdint>
#include <filesystem>
#include <string>

struct CliOptions {
    std::filesystem::path path;
    std::uint64_t minSizeKb = 400;
    int maxWidth = 1500;
    int maxHeight = 1500;
    int quality = 82;
    double maxGrowthPercentForDimensions = 30.0;
    std::uint64_t skipDimensionResizeBelowKb = 90;
    bool showSkipped = true;
    bool dryRun = false;
    std::uint64_t limit = 0;
    int threads = 4;
    std::filesystem::path logPath;
    bool verbose = false;
    bool processPng = false;
    bool forceLock = false;
};

bool parseCliOptions(int argc, char** argv, CliOptions& options, std::string& error, bool& showHelp);
std::string cliUsage(const char* argv0);
