@echo off
echo ========================================
echo FIXING INCIDENTS TABLE MISSING COLUMNS
echo ========================================
echo.

php fix-incidents-columns.php

echo.
echo ========================================
echo If the fix was successful, you can now:
echo 1. Access the emergency incident page
echo 2. Submit incident reports
echo ========================================
echo.
pause