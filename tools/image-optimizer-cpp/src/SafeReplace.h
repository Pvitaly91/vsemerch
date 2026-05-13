#pragma once

#include <filesystem>
#include <string>

namespace vsemerch {

std::string pathToUtf8(const std::filesystem::path& path);
std::filesystem::path buildTemporaryPath(const std::filesystem::path& original);
bool safeReplaceOriginal(const std::filesystem::path& original, const std::filesystem::path& temp, std::string& error);
void removeQuietly(const std::filesystem::path& path);
unsigned long currentProcessId();

class LockFile {
public:
    LockFile() = default;
    LockFile(const LockFile&) = delete;
    LockFile& operator=(const LockFile&) = delete;
    ~LockFile();

    bool acquire(const std::filesystem::path& root, bool force, std::string& error);
    void release();
    const std::filesystem::path& path() const { return path_; }

private:
    std::filesystem::path path_;
#ifdef _WIN32
    void* handle_ = nullptr;
#else
    int fd_ = -1;
#endif
};

} // namespace vsemerch
