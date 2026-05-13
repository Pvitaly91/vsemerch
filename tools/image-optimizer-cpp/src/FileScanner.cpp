#include "FileScanner.h"

#include <algorithm>
#include <array>
#include <cctype>
#include <system_error>
#include <utility>

FileScanner::FileScanner(std::filesystem::path root, Stats& stats, ErrorHandler onError)
    : root_(std::move(root))
    , stats_(stats)
    , onError_(std::move(onError))
{
}

void FileScanner::scan(BoundedQueue<ImageTask>& queue, const std::atomic<bool>& stop)
{
    namespace fs = std::filesystem;

    std::error_code ec;
    fs::recursive_directory_iterator it(root_, fs::directory_options::skip_permission_denied, ec);
    const fs::recursive_directory_iterator end;
    if (ec) {
        onError_("scan error: " + ec.message());
        return;
    }

    while (it != end && !stop.load(std::memory_order_relaxed)) {
        const fs::directory_entry entry = *it;
        std::error_code entryEc;
        const bool regular = entry.is_regular_file(entryEc);
        if (entryEc) {
            onError_("scan error: " + entry.path().string() + ": " + entryEc.message());
        } else if (regular && entry.path().filename() != ".vsemerch-image-optimizer.lock") {
            stats_.scannedFiles.fetch_add(1, std::memory_order_relaxed);
            const std::string extension = normalizedExtension(entry.path());
            if (isKnownImageExtension(extension)) {
                stats_.checkedImageFiles.fetch_add(1, std::memory_order_relaxed);
                if (!queue.push(ImageTask{entry.path(), extension}, stop)) {
                    break;
                }
            }
        }

        it.increment(ec);
        if (ec) {
            onError_("scan error: " + ec.message());
            ec.clear();
        }
    }
}

bool FileScanner::isKnownImageExtension(const std::string& extension)
{
    static const std::array<const char*, 13> known = {
        "jpg", "jpeg", "png", "webp", "gif", "bmp", "tif",
        "tiff", "ico", "svg", "psd", "avif", "heic",
    };

    return std::find(known.begin(), known.end(), extension) != known.end();
}

std::string FileScanner::normalizedExtension(const std::filesystem::path& path)
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
