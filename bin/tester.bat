@echo off
REM if [%1]==[] php bin\console migrations:reset
php bin\console migrations:reset
php vendor\nette\tester\src\tester -C tests\%~1