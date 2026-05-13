param(
    [string]$Path = "",
    [int]$Threads = 4,
    [int]$Limit = 0,
    [string]$Log = ""
)

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ToolRoot = Split-Path -Parent $ScriptDir
$RepoRoot = Split-Path -Parent (Split-Path -Parent $ToolRoot)
$Exe = Join-Path $ToolRoot "build\Release\vsemerch-image-optimizer.exe"

if (-not (Test-Path $Exe)) {
    throw "Binary was not found: $Exe. Run scripts\build-windows.ps1 first."
}

if (-not $Path) {
    $Path = Join-Path $RepoRoot "frontend\web\upload\shop"
}

$Args = @(
    "--path", $Path,
    "--min-size-kb=400",
    "--max-width=1500",
    "--max-height=1500",
    "--quality=82",
    "--max-growth-percent-for-dimensions=30",
    "--skip-dimension-resize-below-kb=90",
    "--show-skipped=1",
    "--threads=$Threads",
    "--limit=$Limit"
)

if ($Log) {
    $Args += "--log=$Log"
}

& $Exe @Args
