#include "CacheOne.h"

#include "SafeReplace.h"

#ifdef _WIN32
#define NOMINMAX
#endif

#include <algorithm>
#include <cctype>
#include <chrono>
#include <curl/curl.h>
#include <cstdlib>
#include <filesystem>
#include <fstream>
#include <iostream>
#include <memory>
#include <sstream>
#include <stdexcept>
#include <string>
#include <string_view>
#include <vector>
#include <vips/vips.h>

namespace fs = std::filesystem;

namespace {

struct Profile {
    std::string name;
    int width = 0;
    int height = 0;
};

struct CacheOneOptions {
    std::string remoteUrl;
    fs::path target;
    fs::path thumbDir;
    std::vector<Profile> profiles;
    int maxWidth = 1200;
    int maxHeight = 1200;
    int quality = 75;
    fs::path lockPath;
    fs::path logPath;
    fs::path failedPath;
    std::uint64_t maxBytes = 30ULL * 1024ULL * 1024ULL;
    long connectTimeout = 3;
    long timeout = 15;
};

class CacheLogger {
public:
    void open(const fs::path& path)
    {
        if (path.empty()) {
            return;
        }

        std::error_code ec;
        if (!path.parent_path().empty()) {
            fs::create_directories(path.parent_path(), ec);
        }

        file_.open(path, std::ios::out | std::ios::trunc);
    }

    void info(const std::string& message)
    {
        write("INFO " + message);
    }

    void error(const std::string& message)
    {
        write("ERROR " + message);
    }

private:
    void write(const std::string& message)
    {
        if (file_) {
            file_ << message << '\n';
            file_.flush();
        }
        std::cerr << message << '\n';
    }

    std::ofstream file_;
};

class LockCleanup {
public:
    explicit LockCleanup(fs::path path)
        : path_(std::move(path))
    {
    }

    ~LockCleanup()
    {
        if (!path_.empty()) {
            vsemerch::removeQuietly(path_);
        }
    }

private:
    fs::path path_;
};

class CurlGlobal {
public:
    CurlGlobal()
    {
        if (curl_global_init(CURL_GLOBAL_DEFAULT) != CURLE_OK) {
            throw std::runtime_error("libcurl initialization failed");
        }
    }

    ~CurlGlobal()
    {
        curl_global_cleanup();
    }
};

class VipsGlobal {
public:
    explicit VipsGlobal(const char* argv0)
    {
        if (VIPS_INIT(argv0)) {
            throw std::runtime_error("libvips initialization failed");
        }
        vips_concurrency_set(1);
        vips_cache_set_max(0);
    }

    ~VipsGlobal()
    {
        vips_shutdown();
    }
};

struct VipsImageDeleter {
    void operator()(VipsImage* image) const
    {
        if (image != nullptr) {
            g_object_unref(image);
        }
    }
};

using VipsImagePtr = std::unique_ptr<VipsImage, VipsImageDeleter>;

struct DownloadState {
    std::ofstream output;
    std::uint64_t bytes = 0;
    std::uint64_t maxBytes = 0;
    bool tooLarge = false;
};

std::string toLower(std::string value)
{
    std::transform(value.begin(), value.end(), value.begin(), [](unsigned char c) {
        return static_cast<char>(std::tolower(c));
    });
    return value;
}

std::string normalizedExtension(const fs::path& path)
{
    std::string extension = path.extension().string();
    if (!extension.empty() && extension.front() == '.') {
        extension.erase(extension.begin());
    }
    return toLower(extension);
}

std::runtime_error vipsError(const std::string& fallback)
{
    const char* buffer = vips_error_buffer();
    std::string message = buffer != nullptr && *buffer != '\0' ? buffer : fallback;
    vips_error_clear();
    return std::runtime_error(message);
}

std::string pathToVips(const fs::path& path)
{
    const auto value = path.generic_u8string();
    return std::string(value.begin(), value.end());
}

bool parseInt(std::string_view value, int& out)
{
    if (value.empty()) {
        return false;
    }

    char* end = nullptr;
    const std::string copy(value);
    const long parsed = std::strtol(copy.c_str(), &end, 10);
    if (end == copy.c_str() || *end != '\0') {
        return false;
    }

    out = static_cast<int>(parsed);
    return true;
}

bool parseUnsigned(std::string_view value, std::uint64_t& out)
{
    if (value.empty() || value.front() == '-') {
        return false;
    }

    char* end = nullptr;
    const std::string copy(value);
    const unsigned long long parsed = std::strtoull(copy.c_str(), &end, 10);
    if (end == copy.c_str() || *end != '\0') {
        return false;
    }

    out = static_cast<std::uint64_t>(parsed);
    return true;
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
    if (index + 1 >= argc || std::string_view(argv[index + 1]).rfind("--", 0) == 0) {
        error = "Missing value for --" + key;
        return false;
    }

    ++index;
    value = argv[index];
    return true;
}

std::vector<std::string> split(const std::string& value, char separator)
{
    std::vector<std::string> result;
    std::string current;
    std::istringstream input(value);
    while (std::getline(input, current, separator)) {
        result.push_back(current);
    }
    return result;
}

bool parseProfiles(const std::string& value, std::vector<Profile>& profiles, std::string& error)
{
    profiles.clear();
    for (const std::string& item : split(value, ',')) {
        const auto colon = item.find(':');
        const auto x = item.find('x', colon == std::string::npos ? 0 : colon + 1);
        if (colon == std::string::npos || x == std::string::npos || colon == 0) {
            error = "Invalid --profiles item: " + item;
            return false;
        }

        Profile profile;
        profile.name = item.substr(0, colon);
        if (!parseInt(std::string_view(item).substr(colon + 1, x - colon - 1), profile.width)
            || !parseInt(std::string_view(item).substr(x + 1), profile.height)
            || profile.width <= 0
            || profile.height <= 0) {
            error = "Invalid --profiles dimensions: " + item;
            return false;
        }

        if (!std::all_of(profile.name.begin(), profile.name.end(), [](unsigned char c) {
                return std::isalnum(c) || c == '-' || c == '_';
            })) {
            error = "Invalid --profiles name: " + profile.name;
            return false;
        }

        profiles.push_back(profile);
    }

    if (profiles.empty()) {
        error = "--profiles is required";
        return false;
    }

    return true;
}

bool parseCacheOneOptions(int argc, char** argv, CacheOneOptions& options, std::string& error)
{
    for (int i = 1; i < argc; ++i) {
        std::string key;
        std::string value;
        if (!readOptionValue(argc, argv, i, key, value, error)) {
            return false;
        }

        int intValue = 0;
        std::uint64_t unsignedValue = 0;

        if (key == "remote-url") {
            options.remoteUrl = value;
        } else if (key == "target") {
            options.target = value;
        } else if (key == "thumb-dir") {
            options.thumbDir = value;
        } else if (key == "profiles") {
            if (!parseProfiles(value, options.profiles, error)) {
                return false;
            }
        } else if (key == "max-width") {
            if (!parseInt(value, intValue) || intValue <= 0) {
                error = "Invalid --max-width: " + value;
                return false;
            }
            options.maxWidth = intValue;
        } else if (key == "max-height") {
            if (!parseInt(value, intValue) || intValue <= 0) {
                error = "Invalid --max-height: " + value;
                return false;
            }
            options.maxHeight = intValue;
        } else if (key == "quality") {
            if (!parseInt(value, intValue)) {
                error = "Invalid --quality: " + value;
                return false;
            }
            options.quality = std::max(0, std::min(100, intValue));
        } else if (key == "lock") {
            options.lockPath = value;
        } else if (key == "log") {
            options.logPath = value;
        } else if (key == "failed") {
            options.failedPath = value;
        } else if (key == "max-bytes") {
            if (!parseUnsigned(value, unsignedValue) || unsignedValue == 0) {
                error = "Invalid --max-bytes: " + value;
                return false;
            }
            options.maxBytes = unsignedValue;
        } else if (key == "connect-timeout") {
            if (!parseInt(value, intValue) || intValue <= 0) {
                error = "Invalid --connect-timeout: " + value;
                return false;
            }
            options.connectTimeout = intValue;
        } else if (key == "timeout") {
            if (!parseInt(value, intValue) || intValue <= 0) {
                error = "Invalid --timeout: " + value;
                return false;
            }
            options.timeout = intValue;
        } else {
            error = "Unknown cache-one option: --" + key;
            return false;
        }
    }

    if (options.remoteUrl.empty()) {
        error = "--remote-url is required";
        return false;
    }
    if (options.target.empty()) {
        error = "--target is required";
        return false;
    }
    if (options.thumbDir.empty()) {
        error = "--thumb-dir is required";
        return false;
    }
    if (options.profiles.empty()) {
        error = "--profiles is required";
        return false;
    }
    if (options.lockPath.empty()) {
        error = "--lock is required";
        return false;
    }

    const std::string extension = normalizedExtension(options.target);
    if (extension != "jpg" && extension != "jpeg" && extension != "webp" && extension != "png") {
        error = "Unsupported target extension: " + extension;
        return false;
    }

    return true;
}

std::string jsonEscape(const std::string& value)
{
    std::string escaped;
    escaped.reserve(value.size());
    for (const char c : value) {
        if (c == '\\' || c == '"') {
            escaped.push_back('\\');
        }
        if (c == '\n') {
            escaped += "\\n";
        } else if (c == '\r') {
            escaped += "\\r";
        } else {
            escaped.push_back(c);
        }
    }
    return escaped;
}

void writeFailedMarker(const fs::path& path, const std::string& message)
{
    if (path.empty()) {
        return;
    }

    std::error_code ec;
    if (!path.parent_path().empty()) {
        fs::create_directories(path.parent_path(), ec);
    }

    std::ofstream output(path, std::ios::out | std::ios::trunc);
    if (!output) {
        return;
    }

    const auto now = std::chrono::system_clock::to_time_t(std::chrono::system_clock::now());
    output << "{\"created_at\":" << static_cast<long long>(now)
           << ",\"message\":\"" << jsonEscape(message) << "\"}\n";
}

fs::path buildDownloadTemporaryPath(const fs::path& target)
{
    fs::path temp = target;
    temp += ".download-";
    temp += std::to_string(vsemerch::currentProcessId());
    temp += "-";
    temp += std::to_string(std::chrono::high_resolution_clock::now().time_since_epoch().count());
    temp += target.extension().string();
    return temp;
}

size_t writeDownload(char* ptr, size_t size, size_t nmemb, void* userdata)
{
    const size_t total = size * nmemb;
    auto* state = static_cast<DownloadState*>(userdata);

    if (state->bytes + static_cast<std::uint64_t>(total) > state->maxBytes) {
        state->tooLarge = true;
        return 0;
    }

    state->output.write(ptr, static_cast<std::streamsize>(total));
    if (!state->output) {
        return 0;
    }

    state->bytes += static_cast<std::uint64_t>(total);
    return total;
}

std::string contentTypeBase(const char* contentType)
{
    if (contentType == nullptr) {
        return {};
    }

    std::string value(contentType);
    const auto semicolon = value.find(';');
    if (semicolon != std::string::npos) {
        value = value.substr(0, semicolon);
    }
    value.erase(value.begin(), std::find_if(value.begin(), value.end(), [](unsigned char c) {
        return !std::isspace(c);
    }));
    value.erase(std::find_if(value.rbegin(), value.rend(), [](unsigned char c) {
        return !std::isspace(c);
    }).base(), value.end());

    return toLower(value);
}

void downloadRemoteImage(const CacheOneOptions& options, const fs::path& tempPath, CacheLogger& logger)
{
    std::error_code ec;
    fs::create_directories(tempPath.parent_path(), ec);
    if (ec) {
        throw std::runtime_error("cannot create target directory: " + ec.message());
    }

    DownloadState state;
    state.maxBytes = options.maxBytes;
    state.output.open(tempPath, std::ios::out | std::ios::binary | std::ios::trunc);
    if (!state.output) {
        throw std::runtime_error("cannot open download temp file: " + tempPath.string());
    }

    CURL* curl = curl_easy_init();
    if (curl == nullptr) {
        throw std::runtime_error("cannot initialize curl handle");
    }

    curl_easy_setopt(curl, CURLOPT_URL, options.remoteUrl.c_str());
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, writeDownload);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &state);
    curl_easy_setopt(curl, CURLOPT_FOLLOWLOCATION, 1L);
    curl_easy_setopt(curl, CURLOPT_MAXREDIRS, 3L);
    curl_easy_setopt(curl, CURLOPT_CONNECTTIMEOUT, options.connectTimeout);
    curl_easy_setopt(curl, CURLOPT_TIMEOUT, options.timeout);
    curl_easy_setopt(curl, CURLOPT_USERAGENT, "vsemerch-image-cache/1.0");
#ifdef CURLOPT_PROTOCOLS
    curl_easy_setopt(curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FTP);
#endif
#ifdef CURLOPT_REDIR_PROTOCOLS
    curl_easy_setopt(curl, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FTP);
#endif

    const CURLcode code = curl_easy_perform(curl);
    state.output.close();

    long responseCode = 0;
    char* contentType = nullptr;
    curl_easy_getinfo(curl, CURLINFO_RESPONSE_CODE, &responseCode);
    curl_easy_getinfo(curl, CURLINFO_CONTENT_TYPE, &contentType);
    const std::string contentTypeValue = contentTypeBase(contentType);
    curl_easy_cleanup(curl);

    if (code != CURLE_OK) {
        throw std::runtime_error(state.tooLarge
            ? "download exceeded maximum size"
            : std::string("download failed: ") + curl_easy_strerror(code));
    }

    if (responseCode >= 400) {
        throw std::runtime_error("remote returned response code " + std::to_string(responseCode));
    }

    if (!contentTypeValue.empty() && contentTypeValue.rfind("image/", 0) != 0) {
        throw std::runtime_error("remote content type is not image/*: " + contentTypeValue);
    }

    if (state.bytes == 0) {
        throw std::runtime_error("downloaded file is empty");
    }

    logger.info("Downloaded " + std::to_string(state.bytes) + " bytes to " + tempPath.string());
}

VipsImagePtr loadImage(const fs::path& path)
{
    VipsImage* loaded = vips_image_new_from_file(
        pathToVips(path).c_str(),
        "access",
        VIPS_ACCESS_RANDOM,
        nullptr);
    if (loaded == nullptr) {
        throw vipsError("cannot read image");
    }

    VipsImage* rotated = nullptr;
    if (vips_autorot(loaded, &rotated, nullptr) != 0) {
        g_object_unref(loaded);
        throw vipsError("cannot autorotate image");
    }
    g_object_unref(loaded);

    return VipsImagePtr(rotated);
}

VipsImagePtr resizeToFit(VipsImage* image, int maxWidth, int maxHeight)
{
    const int width = vips_image_get_width(image);
    const int height = vips_image_get_height(image);
    if (width <= 0 || height <= 0) {
        throw std::runtime_error("invalid image dimensions");
    }

    const double scale = std::min({
        static_cast<double>(maxWidth) / static_cast<double>(width),
        static_cast<double>(maxHeight) / static_cast<double>(height),
        1.0,
    });

    if (scale >= 1.0) {
        g_object_ref(image);
        return VipsImagePtr(image);
    }

    VipsImage* resized = nullptr;
    if (vips_resize(image, &resized, scale, nullptr) != 0) {
        throw vipsError("cannot resize image");
    }

    return VipsImagePtr(resized);
}

void saveImage(const std::string& extension, const fs::path& path, VipsImage* image, int quality)
{
    const std::string target = pathToVips(path);
    if (extension == "jpg" || extension == "jpeg") {
        if (vips_jpegsave(
                image,
                target.c_str(),
                "Q",
                quality,
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
                target.c_str(),
                "Q",
                quality,
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
                target.c_str(),
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

void safeSaveImage(const fs::path& target, VipsImage* image, int quality)
{
    std::error_code ec;
    fs::create_directories(target.parent_path(), ec);
    if (ec) {
        throw std::runtime_error("cannot create output directory: " + ec.message());
    }

    const std::string extension = normalizedExtension(target);
    fs::path tempPath = vsemerch::buildTemporaryPath(target);
    tempPath.replace_extension(target.extension());

    try {
        saveImage(extension, tempPath, image, quality);

        std::string replaceError;
        if (fs::exists(target)) {
            if (!vsemerch::safeReplaceOriginal(target, tempPath, replaceError)) {
                throw std::runtime_error(replaceError);
            }
        } else {
            fs::rename(tempPath, target, ec);
            if (ec) {
                throw std::runtime_error("cannot rename temp to target: " + ec.message());
            }
        }
    } catch (...) {
        vsemerch::removeQuietly(tempPath);
        throw;
    }
}

fs::path thumbPathForProfile(const fs::path& thumbDir, const Profile& profile, const fs::path& target)
{
    return thumbDir / (profile.name + "_" + target.stem().string() + target.extension().string());
}

void createThumbnails(const CacheOneOptions& options, VipsImage* source, CacheLogger& logger)
{
    for (const Profile& profile : options.profiles) {
        VipsImagePtr thumb = resizeToFit(source, profile.width, profile.height);
        const fs::path path = thumbPathForProfile(options.thumbDir, profile, options.target);
        safeSaveImage(path, thumb.get(), options.quality);
        logger.info("Created thumbnail: " + path.string());
    }
}

int fail(const CacheOneOptions& options, CacheLogger& logger, const std::string& message)
{
    logger.error(message);
    writeFailedMarker(options.failedPath, message);
    return 1;
}

} // namespace

int runCacheOne(int argc, char** argv)
{
    CacheOneOptions options;
    std::string error;
    if (!parseCacheOneOptions(argc, argv, options, error)) {
        std::cerr << error << '\n';
        return 2;
    }

    CacheLogger logger;
    logger.open(options.logPath);
    LockCleanup lockCleanup(options.lockPath);
    fs::path downloadTemp;

    try {
        CurlGlobal curl;
        VipsGlobal vips(argv[0]);

        downloadTemp = buildDownloadTemporaryPath(options.target);
        downloadRemoteImage(options, downloadTemp, logger);

        VipsImagePtr image = loadImage(downloadTemp);
        VipsImagePtr optimized = resizeToFit(image.get(), options.maxWidth, options.maxHeight);
        safeSaveImage(options.target, optimized.get(), options.quality);
        logger.info("Saved target: " + options.target.string());

        createThumbnails(options, optimized.get(), logger);
        vsemerch::removeQuietly(downloadTemp);
        logger.info("cache-one complete");
        return 0;
    } catch (const std::exception& e) {
        if (!downloadTemp.empty()) {
            vsemerch::removeQuietly(downloadTemp);
        }
        return fail(options, logger, e.what());
    }
}
