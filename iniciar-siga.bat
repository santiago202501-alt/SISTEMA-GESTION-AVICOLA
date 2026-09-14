@echo off
cd /d "%~dp0"
echo Iniciando SIGA Avicola en http://localhost:8080
mvn spring-boot:run
pause
