# Vsemerch C++ shop image optimizer

This tool optimizes product photos in place without changing file names, paths, extensions, database rows, or frontend/backend application logic. It is a standalone C++ CLI implementation of the current Yii command:

```bash
php yii optimize-shop-images --minSizeKb=400 --maxWidth=1500 --maxHeight=1500 --quality=82 --maxGrowthPercentForDimensions=30 --skipDimensionResizeBelowKb=90 --showSkipped=1
```

The implementation uses the libvips C API for fast batch image processing and a scanner thread plus worker threads with a bounded queue, so large upload trees are not loaded into memory at once.

## Supported formats

- `jpg`, `jpeg`
- `webp`
- `png` only with `--process-png=1`

PNG is skipped by default (`--process-png=0`) to match the current PHP optimizer behavior and to avoid growing uploads when recompressing PNG files.

## Windows build, Visual Studio 2022

Install the dependency with vcpkg:

```powershell
vcpkg install libvips:x64-windows
```

Build:

```powershell
cd D:\DEV\htdocs\vsemerch.loc\tools\image-optimizer-cpp
.\scripts\build-windows.ps1 -VcpkgRoot C:\vcpkg
```

If your current vcpkg registry does not contain a libvips port, the script also accepts an official `vips-dev-*` directory:

```powershell
.\scripts\build-windows.ps1 -VipsRoot "$env:LOCALAPPDATA\Microsoft\WinGet\Packages\libvips.libvips_Microsoft.Winget.Source_8wekyb3d8bbwe\vips-dev-8.18"
```

The binary is written to:

```text
tools\image-optimizer-cpp\build\Release\vsemerch-image-optimizer.exe
```

## Linux build

Install dependencies:

```bash
sudo apt install -y build-essential cmake pkg-config libvips-dev
```

Build:

```bash
cd /path/to/vsemerch/tools/image-optimizer-cpp
./scripts/build-linux.sh
```

The binary is written to:

```text
tools/image-optimizer-cpp/build/vsemerch-image-optimizer
```

## Test run

Create a small copy of candidate files first. Do not start with the full upload folder.

Windows example:

```powershell
.\build\Release\vsemerch-image-optimizer.exe --path "D:\DEV\htdocs\vsemerch.loc\console\runtime\image-optimizer-cpp-test\shop" --min-size-kb=400 --max-width=1500 --max-height=1500 --quality=82 --max-growth-percent-for-dimensions=30 --skip-dimension-resize-below-kb=90 --show-skipped=1 --threads=4 --limit=100 --log "D:\DEV\htdocs\vsemerch.loc\console\runtime\image-optimizer-cpp-test\cpp-result.log"
```

Dry run example:

```powershell
.\build\Release\vsemerch-image-optimizer.exe --path "D:\DEV\htdocs\vsemerch.loc\console\runtime\image-optimizer-cpp-test\shop" --dry-run=1 --limit=100 --threads=4 --show-skipped=1
```

## Full folder run

Windows:

```powershell
.\build\Release\vsemerch-image-optimizer.exe --path "D:\DEV\htdocs\vsemerch.loc\frontend\web\upload\shop" --min-size-kb=400 --max-width=1500 --max-height=1500 --quality=82 --max-growth-percent-for-dimensions=30 --skip-dimension-resize-below-kb=90 --show-skipped=1 --threads=4
```

Linux:

```bash
./build/vsemerch-image-optimizer --path "/var/www/vsemerch/frontend/web/upload/shop" --min-size-kb=400 --max-width=1500 --max-height=1500 --quality=82 --max-growth-percent-for-dimensions=30 --skip-dimension-resize-below-kb=90 --show-skipped=1 --threads=4
```

## Behavior

A file is processed when its size is greater than `--min-size-kb` or its dimensions exceed `--max-width` or `--max-height`.

Important skip and replace rules:

- PNG files are skipped unless `--process-png=1`.
- Files below `--skip-dimension-resize-below-kb` are not resized just because dimensions exceed the limit.
- Files below `--min-size-kb` and within dimensions are skipped.
- Size-only optimization replaces a file only when saving is at least 5%.
- Dimension resize keeps aspect ratio, does not crop, and does not upscale.
- Dimension resize replaces a file only when the new file does not exceed `--max-growth-percent-for-dimensions`.

## Safe replace

Optimized output is written to a temp file next to the original:

```text
original.jpg.optimize-<pid>-<random>.tmp
```

When replacing on Windows, the original is renamed to a backup first, the temp file is renamed to the original path, and the backup is deleted after success. If the temp rename fails, the backup is moved back. On Linux, rename is used for an atomic replacement. File mtime and permissions are preserved where the platform allows it.

## Lock

The tool creates:

```text
<path>/.vsemerch-image-optimizer.lock
```

If another active optimizer process owns the lock, the tool exits. Use `--force-lock=1` only when you are sure the previous process is not active.

## Limit and dry run

`--limit=0` means no limit. A positive limit counts only files that are actually optimized or would be optimized in dry-run mode, not scanned files.

`--dry-run=1` still writes temporary encoded output to calculate the expected size, but deletes the temp file and does not replace originals.

## Safety guard

The tool refuses `--max-width` or `--max-height` below `500` by default. Product photos should not be resized to tiny thumbnail dimensions accidentally. If you intentionally need a smaller limit, pass:

```powershell
--allow-small-dimensions=1
```
