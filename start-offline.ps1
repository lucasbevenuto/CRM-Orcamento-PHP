$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$phpExe = 'C:\php\php-8.5\php.exe'
$mysqlExe = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqld.exe'
$mysqlClient = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe'
$mysqlAdmin = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqladmin.exe'
$mysqlRoot = 'C:\crm_orcamentos_mysql'
$mysqlData = Join-Path $mysqlRoot 'data'
$mysqlTmp = Join-Path $mysqlRoot 'tmp'
$mysqlLogs = Join-Path $mysqlRoot 'logs'
$mysqlErr = Join-Path $mysqlLogs 'mysql.err'
$mysqlStdout = Join-Path $projectRoot 'mysqld-stdout.log'
$mysqlStderr = Join-Path $projectRoot 'mysqld-stderr.log'
$mysqlPidFile = Join-Path $projectRoot '.mysqld-local.pid'
$mysqlRuntimePidFile = Join-Path $mysqlRoot 'mysql.pid'
$phpLog = Join-Path $projectRoot 'php-server.log'
$phpErr = Join-Path $projectRoot 'php-server-error.log'
$phpPidFile = Join-Path $projectRoot '.php-server.pid'
$publicHtml = Join-Path $projectRoot 'public_html'
$databaseSql = Join-Path $projectRoot 'database.sql'
$mysqlDatadirArg = '--datadir=C:/crm_orcamentos_mysql/data'

function Assert-FileExists {
    param([string]$Path, [string]$Description)

    if (-not (Test-Path -LiteralPath $Path)) {
        throw "$Description nao encontrado em: $Path"
    }
}

function Stop-TrackedProcess {
    param([string]$PidPath)

    if (-not (Test-Path -LiteralPath $PidPath)) {
        return
    }

    $pidValue = (Get-Content -LiteralPath $PidPath -ErrorAction SilentlyContinue | Select-Object -First 1).Trim()

    if ($pidValue -match '^\d+$') {
        Stop-Process -Id ([int]$pidValue) -Force -ErrorAction SilentlyContinue
        Start-Sleep -Milliseconds 500
    }

    Remove-Item -LiteralPath $PidPath -Force -ErrorAction SilentlyContinue
}

function Get-LocalMySqlProcesses {
    Get-CimInstance Win32_Process -Filter "name='mysqld.exe'" |
        Where-Object { $_.CommandLine -like '*--datadir=C:/crm_orcamentos_mysql/data*' }
}

function Stop-LocalMySqlProcesses {
    foreach ($process in Get-LocalMySqlProcesses) {
        Stop-Process -Id $process.ProcessId -Force -ErrorAction SilentlyContinue
    }

    Start-Sleep -Seconds 1
    Remove-Item -LiteralPath $mysqlPidFile -Force -ErrorAction SilentlyContinue
    Remove-Item -LiteralPath $mysqlRuntimePidFile -Force -ErrorAction SilentlyContinue
}

function Test-MySqlReady {
    & $mysqlAdmin --protocol=tcp -h 127.0.0.1 -P 3307 -u root ping --silent *> $null
    return $LASTEXITCODE -eq 0
}

function Wait-MySqlReady {
    param([int]$Attempts = 30, [int]$DelaySeconds = 2)

    for ($i = 0; $i -lt $Attempts; $i++) {
        if (Test-MySqlReady) {
            return $true
        }

        Start-Sleep -Seconds $DelaySeconds
    }

    return $false
}

function Save-LocalMySqlPid {
    if (Test-Path -LiteralPath $mysqlRuntimePidFile) {
        $runtimePid = (Get-Content -LiteralPath $mysqlRuntimePidFile -ErrorAction SilentlyContinue | Select-Object -First 1).Trim()
        if ($runtimePid -match '^\d+$') {
            Set-Content -LiteralPath $mysqlPidFile -Value $runtimePid
            return
        }
    }

    $localProcess = Get-LocalMySqlProcesses | Select-Object -First 1
    if ($null -ne $localProcess) {
        Set-Content -LiteralPath $mysqlPidFile -Value $localProcess.ProcessId
    }
}

Assert-FileExists -Path $phpExe -Description 'Executavel do PHP'
Assert-FileExists -Path $mysqlExe -Description 'Executavel do MySQL'
Assert-FileExists -Path $mysqlClient -Description 'Cliente mysql.exe'
Assert-FileExists -Path $mysqlAdmin -Description 'Cliente mysqladmin.exe'
Assert-FileExists -Path $publicHtml -Description 'Pasta public_html'
Assert-FileExists -Path $databaseSql -Description 'Arquivo database.sql'

New-Item -ItemType Directory -Force -Path $mysqlData, $mysqlTmp, $mysqlLogs | Out-Null

if (-not (Test-Path -LiteralPath (Join-Path $mysqlData 'mysql'))) {
    & $mysqlExe --initialize-insecure "--basedir=""C:/Program Files/MySQL/MySQL Server 8.0/""" "--datadir=""$($mysqlData -replace '\\','/')""" *> $null
    if ($LASTEXITCODE -ne 0) {
        throw 'Nao foi possivel inicializar o diretório de dados do MySQL local.'
    }
}

Stop-TrackedProcess -PidPath $phpPidFile

if ((Test-Path -LiteralPath $mysqlPidFile) -or (Test-Path -LiteralPath $mysqlRuntimePidFile)) {
    Stop-LocalMySqlProcesses
}

$portOwner = Get-NetTCPConnection -LocalPort 3307 -State Listen -ErrorAction SilentlyContinue | Select-Object -First 1
if ($null -ne $portOwner) {
    $ownerProcess = Get-CimInstance Win32_Process -Filter "ProcessId=$($portOwner.OwningProcess)" -ErrorAction SilentlyContinue
    $isLocalMySql = $null -ne $ownerProcess -and $ownerProcess.Name -eq 'mysqld.exe' -and $ownerProcess.CommandLine -like '*--datadir=C:/crm_orcamentos_mysql/data*'

    if (-not $isLocalMySql) {
        throw "A porta 3307 ja esta em uso por outro processo (PID $($portOwner.OwningProcess))."
    }
}

if (-not (Test-MySqlReady)) {
    Remove-Item -LiteralPath $mysqlStdout, $mysqlStderr, $mysqlErr -Force -ErrorAction SilentlyContinue

    $mysqlArgLine = @(
        '--standalone',
        '--console',
        '--port=3307',
        '--basedir="C:/Program Files/MySQL/MySQL Server 8.0/"',
        '--datadir=C:/crm_orcamentos_mysql/data',
        '--tmpdir=C:/crm_orcamentos_mysql/tmp',
        "--log-error=$($mysqlErr -replace '\\','/')",
        "--pid-file=$($mysqlRuntimePidFile -replace '\\','/')",
        '--character-set-server=utf8mb4',
        '--collation-server=utf8mb4_unicode_ci'
    ) -join ' '

    $launchProc = Start-Process -FilePath $mysqlExe -ArgumentList $mysqlArgLine -RedirectStandardOutput $mysqlStdout -RedirectStandardError $mysqlStderr -PassThru -WindowStyle Hidden

    if (-not (Wait-MySqlReady)) {
        if (-not $launchProc.HasExited) {
            Stop-Process -Id $launchProc.Id -Force -ErrorAction SilentlyContinue
        }
        Stop-LocalMySqlProcesses
        throw "O MySQL local nao iniciou corretamente na porta 3307. Verifique os logs em:`n$mysqlStderr`n$mysqlErr"
    }
}

Save-LocalMySqlPid

Get-Content -LiteralPath $databaseSql | & $mysqlClient --protocol=tcp -h 127.0.0.1 -P 3307 -u root
if ($LASTEXITCODE -ne 0) {
    throw 'Nao foi possivel importar ou validar o banco crm_orcamentos.'
}

$phpArgs = "-S 127.0.0.1:8000 -t `"$publicHtml`""
$phpProc = Start-Process -FilePath $phpExe -ArgumentList $phpArgs -RedirectStandardOutput $phpLog -RedirectStandardError $phpErr -PassThru -WindowStyle Hidden
Set-Content -LiteralPath $phpPidFile -Value $phpProc.Id

Write-Output 'CRM offline iniciado com sucesso.'
Write-Output 'URL: http://127.0.0.1:8000'
Write-Output 'Login: admin / admin123'
