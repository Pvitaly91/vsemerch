#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOOL_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
BUILD_DIR="${1:-${TOOL_ROOT}/build}"

command -v cmake >/dev/null || { echo "cmake was not found" >&2; exit 1; }
command -v pkg-config >/dev/null || { echo "pkg-config was not found" >&2; exit 1; }
pkg-config --exists vips || {
  echo "libvips development files were not found. Install: sudo apt install -y build-essential cmake pkg-config libvips-dev libcurl4-openssl-dev" >&2
  exit 1
}
pkg-config --exists libcurl || {
  echo "libcurl development files were not found. Install: sudo apt install -y libcurl4-openssl-dev" >&2
  exit 1
}

cmake -S "${TOOL_ROOT}" -B "${BUILD_DIR}" -DCMAKE_BUILD_TYPE=Release
cmake --build "${BUILD_DIR}" --parallel

echo "Built: ${BUILD_DIR}/vsemerch-image-optimizer"
