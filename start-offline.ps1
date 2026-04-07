$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$phpExe = 'C:\php\php-8.5\php.exe'
$mysqlExe = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe'
$mysqlClient = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe'
$mysqlRoot = 'C:\crm_orcamentos_mysql'
$mysqlData = Join-Path $mysqlRoot 'data'
$mysqlTmp = Join-Path $mysqlRoot 'tmp'
$mysqlLogs = Join-Path $mysqlRoot 'logs'
$mysqlErr = Join-Path $mysqlLogs 'mysql.err'
$mysqlStdout = Join-Path $projectRoot 'mysqld-stdout.log'
$mysqlStderr = Join-Path $projectRoot 'mysqld-stderr.log'
$mysqlPidFile = Join-Path $projectRoot '.mysqld-local.pid'
$phpLog = Join-Path $projectRoot 'php-server.log'
$phpErr = Join-Path $projectRoot 'php-server-error.log'
$phpPidFile = Join-Path $projectRoot '.php-server.pid'
$publicHtml = Join-Path $projectRoot 'public_html'
$databaseSql = Join-Path $projectRoot 'database.sql'

New-Item -ItemType Directory -Force -Path $mysqlData, $mysqlTmp, $mysqlLogs | Out-Null

if (-not (Test-Path (Join-Path $mysqlData 'mysql'))) {
    & $mysqlExe --initialize-insecure "--basedir=""C:/Program Files/MySQL/MySQL Server 8.0/""" "--datadir=""$($mysqlData -replace '\\','/')""" | Out-Null
}

if (Test-Path $mysqlPidFile) {
    $oldMysqlPid = Get-Content $mysqlPidFile -ErrorAction SilentlyContinue
    if ($oldMysqlPid) {
        Stop-Process -Id $oldMysqlPid -Force -ErrorAction SilentlyContinue
        Start-Sleep -Seconds 2
    }
}

if (Test-Path $phpPidFile) {
    $oldPhpPid = Get-Content $phpPidFile -ErrorAction SilentlyContinue
    if ($oldPhpPid) {
        Stop-Process -Id $oldPhpPid -Force -ErrorAction SilentlyContinue
        Start-Sleep -Seconds 1
    }
}

$mysqlReady = $false

try {
    & $mysqlClient --protocol=tcp -h 127.0.0.1 -P 3307 -u root -e "SELECT 1" | Out-Null
    $mysqlReady = $true
} catch {
    $mysqlReady = $false
}

if (-not $mysqlReady) {
    $mysqlArgs = @(
        '--standalone',
        '--console',
        '--port=3307',
        '--basedir="C:/Program Files/MySQL/MySQL Server 8.0/"',
        "--datadir=""$($mysqlData -replace '\\','/')""",
        "--tmpdir=""$($mysqlTmp -replace '\\','/')""",
        "--log-error=""$($mysqlErr -replace '\\','/')""",
        "--pid-file=""$((Join-Path $mysqlRoot 'mysql.pid') -replace '\\','/')""",
        '--character-set-server=utf8mb4',
        '--collation-server=utf8mb4_unicode_ci'
    )

    $mysqlProc = Start-Process -FilePath $mysqlExe -ArgumentList $mysqlArgs -RedirectStandardOutput $mysqlStdout -RedirectStandardError $mysqlStderr -PassThru -WindowStyle Hidden
    Set-Content -Path $mysqlPidFile -Value $mysqlProc.Id

    Start-Sleep -Seconds 12
}

Get-Content $databaseSql | & $mysqlClient --protocol=tcp -h 127.0.0.1 -P 3307 -u root

$phpArgs = "-S 127.0.0.1:8000 -t `"$publicHtml`""
$phpProc = Start-Process -FilePath $phpExe -ArgumentList $phpArgs -RedirectStandardOutput $phpLog -RedirectStandardError $phpErr -PassThru -WindowStyle Hidden
Set-Content -Path $phpPidFile -Value $phpProc.Id

Write-Host 'CRM offline iniciado com sucesso.'
Write-Host 'URL: http://127.0.0.1:8000'
Write-Host 'Login: admin / admin123'
