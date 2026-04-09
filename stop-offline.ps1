$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$mysqlRoot = 'C:\crm_orcamentos_mysql'
$mysqlPidFile = Join-Path $projectRoot '.mysqld-local.pid'
$mysqlRuntimePidFile = Join-Path $mysqlRoot 'mysql.pid'
$phpPidFile = Join-Path $projectRoot '.php-server.pid'

function Stop-TrackedProcess {
    param([string]$PidPath)

    if (-not (Test-Path -LiteralPath $PidPath)) {
        return
    }

    $pidValue = (Get-Content -LiteralPath $PidPath -ErrorAction SilentlyContinue | Select-Object -First 1).Trim()

    if ($pidValue -match '^\d+$') {
        Stop-Process -Id ([int]$pidValue) -Force -ErrorAction SilentlyContinue
    }

    Remove-Item -LiteralPath $PidPath -Force -ErrorAction SilentlyContinue
}

Stop-TrackedProcess -PidPath $phpPidFile
Stop-TrackedProcess -PidPath $mysqlPidFile
Stop-TrackedProcess -PidPath $mysqlRuntimePidFile

Get-CimInstance Win32_Process -Filter "name='mysqld.exe'" |
    Where-Object { $_.CommandLine -like '*--datadir=C:/crm_orcamentos_mysql/data*' } |
    ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }

Write-Output 'Servidores offline finalizados.'
