#pragma once

#include <atomic>
#include <condition_variable>
#include <cstddef>
#include <deque>
#include <mutex>

template <typename T>
class BoundedQueue {
public:
    explicit BoundedQueue(std::size_t capacity)
        : capacity_(capacity == 0 ? 1 : capacity)
    {
    }

    bool push(T item, const std::atomic<bool>& stop)
    {
        std::unique_lock<std::mutex> lock(mutex_);
        notFull_.wait(lock, [&] {
            return closed_ || stop.load(std::memory_order_relaxed) || queue_.size() < capacity_;
        });

        if (closed_ || stop.load(std::memory_order_relaxed)) {
            return false;
        }

        queue_.push_back(std::move(item));
        notEmpty_.notify_one();
        return true;
    }

    bool pop(T& item)
    {
        std::unique_lock<std::mutex> lock(mutex_);
        notEmpty_.wait(lock, [&] {
            return closed_ || !queue_.empty();
        });

        if (queue_.empty()) {
            return false;
        }

        item = std::move(queue_.front());
        queue_.pop_front();
        notFull_.notify_one();
        return true;
    }

    void close()
    {
        {
            std::lock_guard<std::mutex> lock(mutex_);
            closed_ = true;
        }
        notFull_.notify_all();
        notEmpty_.notify_all();
    }

private:
    std::size_t capacity_;
    std::deque<T> queue_;
    std::mutex mutex_;
    std::condition_variable notFull_;
    std::condition_variable notEmpty_;
    bool closed_ = false;
};
