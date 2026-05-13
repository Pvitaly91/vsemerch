#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TOOL_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
REPO_ROOT="$(cd "${TOOL_ROOT}/../.." && pwd)"
BIN="${TOOL_ROOT}/build/vsemerch-image-optimizer"
TARGET_PATH="${1:-${REPO_ROOT}/frontend/web/upload/shop}"
THREADS="${THREADS:-4}"
LIMIT="${LIMIT:-0}"
LOG="${LOG:-}"

if [[ ! -x "${BIN}" ]]; then
  echo "Binary was not found: ${BIN}. Run scripts/build-linux.sh first." >&2
  exit 1
fi

args=(
  --path "${TARGET_PATH}"
  --min-size-kb=400
  --max-width=1500
  --max-height=1500
  --quality=82
  --max-growth-percent-for-dimensions=30
  --skip-dimension-resize-below-kb=90
  --show-skipped=1
  --threads="${THREADS}"
  --limit="${LIMIT}"
)

if [[ -n "${LOG}" ]]; then
  args+=(--log="${LOG}")
fi

"${BIN}" "${args[@]}"
