#pragma once

#include "Stats.h"
#include "ThreadPool.h"

#include <atomic>
#include <filesystem>
#include <functional>
#include <string>

struct ImageTask {
    std::filesystem::path path;
    std::string extension;
};

class FileScanner {
public:
    using ErrorHandler = std::function<void(const std::string&)>;

    FileScanner(std::filesystem::path root, Stats& stats, ErrorHandler onError);
    void scan(BoundedQueue<ImageTask>& queue, const std::atomic<bool>& stop);

private:
    static bool isKnownImageExtension(const std::string& extension);
    static std::string normalizedExtension(const std::filesystem::path& path);

    std::filesystem::path root_;
    Stats& stats_;
    ErrorHandler onError_;
};
