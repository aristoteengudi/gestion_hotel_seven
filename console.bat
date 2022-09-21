@echo off

@setlocal

set CONSOLE_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%CONSOLE_PATH%console.php" %*

@endlocal
