$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path

foreach ($pidFile in '.php-server.pid', '.mysqld-local.pid') {
    $fullPath = Join-Path $projectRoot $pidFile
    if (Test-Path $fullPath) {
        $pidValue = Get-Content $fullPath -ErrorAction SilentlyContinue
        if ($pidValue) {
            Stop-Process -Id $pidValue -Force -ErrorAction SilentlyContinue
        }
        Remove-Item $fullPath -Force -ErrorAction SilentlyContinue
    }
}

Write-Host 'Servidores offline finalizados.'
