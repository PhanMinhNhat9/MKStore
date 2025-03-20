@echo off
:: Định dạng ngày tháng
set DATESTAMP=%DATE:~6,4%-%DATE:~3,2%-%DATE:~0,2%
set TIMESTAMP=%TIME:~0,2%-%TIME:~3,2%-%TIME:~6,2%
set DATETIME=%DATESTAMP%_%TIMESTAMP%

:: Đường dẫn MySQL trong XAMPP
set MYSQL_PATH=C:\xampp\mysql\bin

:: Thư mục lưu backup
set BACKUP_DIR=C:\xampp\mysql\backup
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

:: Tên database
set DB_NAME=quanlybanpk

:: Lệnh backup
"%MYSQL_PATH%\mysqldump.exe" -u root -p --databases %DB_NAME% > "%BACKUP_DIR%\%DB_NAME%_%DATETIME%.sql"

:: Hoàn tất
echo Backup hoàn thành: %DB_NAME%_%DATETIME%.sql
exit
