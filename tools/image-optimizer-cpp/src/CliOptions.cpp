#include "CliOptions.h"

#include <algorithm>
#include <charconv>
#include <cstdlib>
#include <cctype>
#include <sstream>
#include <string_view>
#include <thread>
#include <vector>

namespace {

std::string toLower(std::string value)
{
    std::transform(value.begin(), value.end(), value.begin(), [](unsigned char c) {
        return static_cast<char>(std::tolower(c));
    });
    return value;
}

bool parseUnsigned(std::string_view value, std::uint64_t& out)
{
    if (value.empty() || value.front() == '-') {
        return false;
    }

    std::uint64_t parsed = 0;
    const auto* first = value.data();
    const auto* last = value.data() + value.size();
    auto result = std::from_chars(first, last, parsed);
    if (result.ec != std::errc() || result.ptr != last) {
        return false;
    }

    out = parsed;
    return true;
}

bool parseInt(std::string_view value, int& out)
{
    if (value.empty()) {
        return false;
    }

    int parsed = 0;
    const auto* first = value.data();
    const auto* last = value.data() + value.size();
    auto result = std::from_chars(first, last, parsed);
    if (result.ec != std::errc() || result.ptr != last) {
        return false;
    }

    out = parsed;
    return true;
}

bool parseDouble(std::string_view value, double& out)
{
    if (value.empty()) {
        return false;
    }

    char* end = nullptr;
    std::string copy(value);
    const double parsed = std::strtod(copy.c_str(), &end);
    if (end == copy.c_str() || *end != '\0') {
        return false;
    }

    out = parsed;
    return true;
}

bool parseBool(std::string_view value, bool& out)
{
    const std::string normalized = toLower(std::string(value));
    if (normalized == "1" || normalized == "true" || normalized == "yes" || normalized == "on") {
        out = true;
        return true;
    }
    if (normalized == "0" || normalized == "false" || normalized == "no" || normalized == "off") {
        out = false;
        return true;
    }
    return false;
}

bool isValueToken(const char* token)
{
    return token != nullptr && std::string_view(token).rfind("--", 0) != 0;
}

bool readOptionValue(int argc, char** argv, int& index, std::string& key, std::string& value, std::string& error)
{
    std::string token(argv[index]);
    if (token.rfind("--", 0) != 0) {
        error = "Unexpected argument: " + token;
        return false;
    }

    token.erase(0, 2);
    const auto equals = token.find('=');
    if (equals != std::string::npos) {
        key = token.substr(0, equals);
        value = token.substr(equals + 1);
        return true;
    }

    key = token;
    if (index + 1 < argc && isValueToken(argv[index + 1])) {
        ++index;
        value = argv[index];
    } else {
        value.clear();
    }

    return true;
}

template <typename Setter>
bool setWithError(const std::string& key, const std::string& value, Setter setter, std::string& error)
{
    if (!setter()) {
        error = "Invalid value for --" + key + ": " + value;
        return false;
    }
    return true;
}

} // namespace

bool parseCliOptions(int argc, char** argv, CliOptions& options, std::string& error, bool& showHelp)
{
    showHelp = false;

    for (int i = 1; i < argc; ++i) {
        const std::string raw(argv[i]);
        if (raw == "--help" || raw == "-h" || raw == "/?") {
            showHelp = true;
            return true;
        }

        std::string key;
        std::string value;
        if (!readOptionValue(argc, argv, i, key, value, error)) {
            return false;
        }

        std::uint64_t unsignedValue = 0;
        int intValue = 0;
        double doubleValue = 0.0;
        bool boolValue = false;

        if (key == "path") {
            if (value.empty()) {
                error = "--path is required";
                return false;
            }
            options.path = value;
        } else if (key == "min-size-kb") {
            if (!setWithError(key, value, [&] { return parseUnsigned(value, unsignedValue); }, error)) {
                return false;
            }
            options.minSizeKb = std::max<std::uint64_t>(1, unsignedValue);
        } else if (key == "max-width") {
            if (!setWithError(key, value, [&] { return parseInt(value, intValue); }, error)) {
                return false;
            }
            options.maxWidth = std::max(1, intValue);
        } else if (key == "max-height") {
            if (!setWithError(key, value, [&] { return parseInt(value, intValue); }, error)) {
                return false;
            }
            options.maxHeight = std::max(1, intValue);
        } else if (key == "quality") {
            if (!setWithError(key, value, [&] { return parseInt(value, intValue); }, error)) {
                return false;
            }
            options.quality = std::max(0, std::min(100, intValue));
        } else if (key == "max-growth-percent-for-dimensions") {
            if (!setWithError(key, value, [&] { return parseDouble(value, doubleValue); }, error)) {
                return false;
            }
            options.maxGrowthPercentForDimensions = std::max(0.0, doubleValue);
        } else if (key == "skip-dimension-resize-below-kb") {
            if (!setWithError(key, value, [&] { return parseUnsigned(value, unsignedValue); }, error)) {
                return false;
            }
            options.skipDimensionResizeBelowKb = unsignedValue;
        } else if (key == "show-skipped") {
            if (!setWithError(key, value, [&] { return parseBool(value, boolValue); }, error)) {
                return false;
            }
            options.showSkipped = boolValue;
        } else if (key == "dry-run") {
            if (!setWithError(key, value, [&] { return parseBool(value, boolValue); }, error)) {
                return false;
            }
            options.dryRun = boolValue;
        } else if (key == "limit") {
            if (!setWithError(key, value, [&] { return parseUnsigned(value, unsignedValue); }, error)) {
                return false;
            }
            options.limit = unsignedValue;
        } else if (key == "threads") {
            if (!setWithError(key, value, [&] { return parseInt(value, intValue); }, error)) {
                return false;
            }
            options.threads = intValue;
        } else if (key == "log") {
            options.logPath = value;
        } else if (key == "verbose") {
            if (!setWithError(key, value, [&] { return parseBool(value, boolValue); }, error)) {
                return false;
            }
            options.verbose = boolValue;
        } else if (key == "process-png") {
            if (!setWithError(key, value, [&] { return parseBool(value, boolValue); }, error)) {
                return false;
            }
            options.processPng = boolValue;
        } else if (key == "force-lock") {
            if (!setWithError(key, value, [&] { return parseBool(value, boolValue); }, error)) {
                return false;
            }
            options.forceLock = boolValue;
        } else if (key == "allow-small-dimensions") {
            if (!setWithError(key, value, [&] { return parseBool(value, boolValue); }, error)) {
                return false;
            }
            options.allowSmallDimensions = boolValue;
        } else {
            error = "Unknown option: --" + key;
            return false;
        }
    }

    if (options.path.empty()) {
        error = "--path is required";
        return false;
    }

    if (!options.allowSmallDimensions && (options.maxWidth < 500 || options.maxHeight < 500)) {
        error = "Refusing suspicious dimensions below 500px. Check --max-width/--max-height, or pass --allow-small-dimensions=1 intentionally.";
        return false;
    }

    if (options.threads == 0) {
        const unsigned int hardware = std::thread::hardware_concurrency();
        options.threads = static_cast<int>(std::min<unsigned int>(hardware == 0 ? 4 : hardware, 8));
    } else {
        options.threads = std::max(1, std::min(options.threads, 64));
    }

    return true;
}

std::string cliUsage(const char* argv0)
{
    std::ostringstream out;
    out << "Usage:\n"
        << "  " << argv0 << " --path <directory> [options]\n\n"
        << "  " << argv0 << " cache-one --remote-url <url> --target <file> --thumb-dir <directory> --profiles <name:WxH,...> --lock <file> [options]\n\n"
        << "Options:\n"
        << "  --path <directory>\n"
        << "  --min-size-kb=400\n"
        << "  --max-width=1500\n"
        << "  --max-height=1500\n"
        << "  --quality=82\n"
        << "  --max-growth-percent-for-dimensions=30\n"
        << "  --skip-dimension-resize-below-kb=90\n"
        << "  --show-skipped=1\n"
        << "  --dry-run=0\n"
        << "  --limit=0\n"
        << "  --threads=4 (0 = auto, capped to 8)\n"
        << "  --log=\n"
        << "  --verbose=0\n"
        << "  --process-png=0\n"
        << "  --force-lock=0\n"
        << "  --allow-small-dimensions=0\n";
    return out.str();
}
