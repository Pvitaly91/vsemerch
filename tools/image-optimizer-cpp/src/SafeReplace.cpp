#include "SafeReplace.h"

#include <chrono>
#include <cstdint>
#include <fstream>
#include <iomanip>
#include <random>
#include <sstream>
#include <system_error>

#ifdef _WIN32
#define NOMINMAX
#include <windows.h>
#else
#include <cerrno>
#include <csignal>
#include <fcntl.h>
#include <unistd.h>
#endif

namespace fs = std::filesystem;

namespace {

std::string randomToken()
{
    const auto now = std::chrono::high_resolution_clock::now().time_since_epoch().count();
    std::random_device device;
    std::mt19937_64 generator((static_cast<std::uint64_t>(now) << 1U) ^ device());
    std::uniform_int_distribution<std::uint64_t> distribution;

    std::ostringstream out;
    out << std::hex << distribution(generator);
    return out.str();
}

std::string lockContent()
{
    const auto now = std::chrono::system_clock::now();
    const auto time = std::chrono::system_clock::to_time_t(now);

    std::tm localTime{};
#ifdef _WIN32
    localtime_s(&localTime, &time);
#else
    localtime_r(&time, &localTime);
#endif

    std::ostringstream out;
    out << vsemerch::currentProcessId() << "\n"
        << std::put_time(&localTime, "%Y-%m-%d %H:%M:%S") << "\n";
    return out.str();
}

unsigned long readLockPid(const fs::path& path)
{
    std::ifstream input(path);
    unsigned long pid = 0;
    input >> pid;
    return pid;
}

bool isProcessActive(unsigned long pid)
{
    if (pid == 0) {
        return false;
    }

#ifdef _WIN32
    HANDLE process = OpenProcess(SYNCHRONIZE, FALSE, static_cast<DWORD>(pid));
    if (process == nullptr) {
        return false;
    }
    const DWORD waitResult = WaitForSingleObject(process, 0);
    CloseHandle(process);
    return waitResult == WAIT_TIMEOUT;
#else
    if (kill(static_cast<pid_t>(pid), 0) == 0) {
        return true;
    }
    return errno == EPERM;
#endif
}

void copyMetadata(const fs::path& source, const fs::path& target)
{
    std::error_code ec;
    const auto permissions = fs::status(source, ec).permissions();
    if (!ec) {
        fs::permissions(target, permissions, fs::perm_options::replace, ec);
    }

    ec.clear();
    const auto writeTime = fs::last_write_time(source, ec);
    if (!ec) {
        fs::last_write_time(target, writeTime, ec);
    }
}

} // namespace

namespace vsemerch {

std::string pathToUtf8(const fs::path& path)
{
    const auto value = path.u8string();
    return std::string(value.begin(), value.end());
}

unsigned long currentProcessId()
{
#ifdef _WIN32
    return static_cast<unsigned long>(GetCurrentProcessId());
#else
    return static_cast<unsigned long>(getpid());
#endif
}

fs::path buildTemporaryPath(const fs::path& original)
{
    fs::path temp = original;
    temp += ".optimize-";
    temp += std::to_string(currentProcessId());
    temp += "-";
    temp += randomToken();
    temp += ".tmp";
    return temp;
}

void removeQuietly(const fs::path& path)
{
    std::error_code ec;
    fs::remove(path, ec);
}

bool safeReplaceOriginal(const fs::path& original, const fs::path& temp, std::string& error)
{
    copyMetadata(original, temp);

    std::error_code ec;
#ifdef _WIN32
    fs::path backup = original;
    backup += ".optimize-backup-";
    backup += std::to_string(currentProcessId());
    backup += "-";
    backup += randomToken();

    fs::rename(original, backup, ec);
    if (ec) {
        error = "cannot rename original to backup: " + ec.message();
        return false;
    }

    fs::rename(temp, original, ec);
    if (ec) {
        const std::string renameError = ec.message();
        std::error_code rollbackEc;
        fs::rename(backup, original, rollbackEc);
        error = "cannot rename temp to original: " + renameError;
        if (rollbackEc) {
            error += "; rollback failed: " + rollbackEc.message();
        }
        return false;
    }

    fs::remove(backup, ec);
    return true;
#else
    fs::rename(temp, original, ec);
    if (ec) {
        error = "cannot rename temp to original: " + ec.message();
        return false;
    }
    return true;
#endif
}

LockFile::~LockFile()
{
    release();
}

bool LockFile::acquire(const fs::path& root, bool force, std::string& error)
{
    path_ = root / ".vsemerch-image-optimizer.lock";

    if (fs::exists(path_)) {
        const unsigned long pid = readLockPid(path_);
        if (!force && isProcessActive(pid)) {
            error = "Another vsemerch-image-optimizer process is already running. Lock: " + path_.string();
            return false;
        }

        std::error_code removeEc;
        fs::remove(path_, removeEc);
        if (removeEc && !force) {
            error = "Cannot remove stale lock: " + removeEc.message();
            return false;
        }
    }

    const std::string content = lockContent();

#ifdef _WIN32
    HANDLE handle = CreateFileW(
        path_.wstring().c_str(),
        GENERIC_WRITE,
        FILE_SHARE_READ,
        nullptr,
        CREATE_NEW,
        FILE_ATTRIBUTE_NORMAL,
        nullptr);

    if (handle == INVALID_HANDLE_VALUE) {
        error = "Cannot create lock file: " + path_.string();
        return false;
    }

    DWORD written = 0;
    if (!WriteFile(handle, content.data(), static_cast<DWORD>(content.size()), &written, nullptr)) {
        CloseHandle(handle);
        removeQuietly(path_);
        error = "Cannot write lock file: " + path_.string();
        return false;
    }

    handle_ = handle;
#else
    fd_ = open(path_.c_str(), O_CREAT | O_EXCL | O_WRONLY, 0644);
    if (fd_ < 0) {
        error = "Cannot create lock file: " + path_.string();
        return false;
    }

    const ssize_t written = write(fd_, content.data(), content.size());
    if (written < 0 || static_cast<std::size_t>(written) != content.size()) {
        close(fd_);
        fd_ = -1;
        removeQuietly(path_);
        error = "Cannot write lock file: " + path_.string();
        return false;
    }
#endif

    return true;
}

void LockFile::release()
{
#ifdef _WIN32
    if (handle_ != nullptr) {
        CloseHandle(static_cast<HANDLE>(handle_));
        handle_ = nullptr;
        removeQuietly(path_);
    }
#else
    if (fd_ >= 0) {
        close(fd_);
        fd_ = -1;
        removeQuietly(path_);
    }
#endif
}

} // namespace vsemerch
