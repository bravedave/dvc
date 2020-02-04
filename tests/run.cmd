@Echo Off
REM ~ for /F "tokens=3 delims=: " %%H in ('sc query "MySQL" ^| findstr "        STATE"') do (
	REM ~ if /I "%%H" NEQ "RUNNING" (
		REM ~ @REM Put your code you want to execute here
		REM ~ @REM For example, the following line
		REM ~ CScript StartMySQL.vbs

	REM ~ )

REM ~ )

SETLOCAL
SET WD=%CD%

CD www
C:\PHP\php -S localhost:80 -c c:\php\php.ini _dvc.php
CD %WD%
