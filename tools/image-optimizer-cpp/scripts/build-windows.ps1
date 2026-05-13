param(
    [string]$VcpkgRoot = $env:VCPKG_ROOT,
    [string]$VipsRoot = "",
    [string]$BuildDir = ""
)

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ToolRoot = Split-Path -Parent $ScriptDir

if (-not $BuildDir) {
    $BuildDir = Join-Path $ToolRoot "build"
}

$CMake = (Get-Command cmake -ErrorAction SilentlyContinue).Source
if (-not $CMake) {
    $VsCMake = "C:\Program Files\Microsoft Visual Studio\2022\Community\Common7\IDE\CommonExtensions\Microsoft\CMake\CMake\bin\cmake.exe"
    if (Test-Path $VsCMake) {
        $CMake = $VsCMake
    }
}

if (-not $CMake) {
    throw "cmake was not found. Install CMake or Visual Studio 2022 CMake tools."
}

if (-not $VipsRoot) {
    $WingetPackages = Join-Path $env:LOCALAPPDATA "Microsoft\WinGet\Packages"
    if (Test-Path $WingetPackages) {
        $DetectedVips = Get-ChildItem $WingetPackages -Directory -Recurse -Filter "vips-dev-*" -ErrorAction SilentlyContinue |
            Select-Object -First 1 -ExpandProperty FullName
        if ($DetectedVips) {
            $VipsRoot = $DetectedVips
        }
    }
}

$ConfigureArgs = @("-S", $ToolRoot, "-B", $BuildDir, "-G", "Visual Studio 17 2022", "-A", "x64")

if ($VcpkgRoot) {
    $Toolchain = Join-Path $VcpkgRoot "scripts\buildsystems\vcpkg.cmake"
    if (-not (Test-Path $Toolchain)) {
        throw "vcpkg toolchain file was not found: $Toolchain"
    }
    $ConfigureArgs += "-DCMAKE_TOOLCHAIN_FILE=$Toolchain"
} elseif ($VipsRoot) {
    $ConfigureArgs += "-DVIPS_ROOT=$VipsRoot"
} else {
    throw "Set VCPKG_ROOT or pass -VcpkgRoot C:\vcpkg. As a fallback, pass -VipsRoot <vips-dev directory>."
}

if ($VipsRoot -and -not $VcpkgRoot) {
    Write-Host "Using VIPS_ROOT fallback: $VipsRoot"
}

& $CMake @ConfigureArgs

& $CMake --build $BuildDir --config Release --parallel

Write-Host "Built: $(Join-Path $BuildDir 'Release\vsemerch-image-optimizer.exe')"
