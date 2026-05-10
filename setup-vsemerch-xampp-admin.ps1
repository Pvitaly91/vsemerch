$ErrorActionPreference = 'Stop'

$principal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw 'Run this script from an elevated PowerShell session.'
}

$hostsPath = 'C:\Windows\System32\drivers\etc\hosts'
$vhostsPath = 'C:\Program Files\xampp\apache\conf\extra\httpd-vhosts.conf'
$httpdPath = 'C:\Program Files\xampp\apache\bin\httpd.exe'
$localVhostPath = Join-Path $PSScriptRoot 'vsemerch-vhost.conf'

$hostLine = '127.0.0.1       vsemerch.loc'
if (-not (Select-String -Path $hostsPath -Pattern '(^|\s)vsemerch\.loc(\s|$)' -Quiet)) {
    Add-Content -Path $hostsPath -Value "`r`n$hostLine"
}

if (-not (Select-String -Path $vhostsPath -Pattern 'ServerName\s+vsemerch\.loc' -Quiet)) {
    $vhostBlock = Get-Content -Path $localVhostPath -Raw
    Add-Content -Path $vhostsPath -Value $vhostBlock
}

& $httpdPath -t -f 'C:\Program Files\xampp\apache\conf\httpd.conf'
if ($LASTEXITCODE -ne 0) {
    throw 'Apache config test failed.'
}

Get-Process httpd -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Seconds 2
Start-Process -FilePath $httpdPath -ArgumentList @('-d', 'C:/Program Files/xampp/apache') -WindowStyle Hidden
