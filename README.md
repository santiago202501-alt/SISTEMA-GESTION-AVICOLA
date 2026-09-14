<<<<<<< HEAD
# SIGA Avícola - Spring Boot

Versión Java del sistema SIGA, con login, roles, permisos, aislamiento por cliente y CRUD operativo.

## Requisitos
- Java 21
- Maven 3.9+
- MySQL/MariaDB
- Base de datos `siga_avicola`

## Primer arranque
1. Haz una copia de `siga_avicola`.
2. Ejecuta `database/01_migracion_siga.sql` en phpMyAdmin.
3. Revisa `database/02_verificacion.sql`.
4. Abre este proyecto en VS Code.
5. Ejecuta: `mvn spring-boot:run`
6. Navega a `http://localhost:8080`

## Credenciales heredadas
Las contraseñas BCrypt existentes en el SQL original son compatibles con Spring Security. El usuario administrador del SQL original utiliza `Admin123!`.

## Roles
- Administrador: todo.
- Ojeador: lectura y generación de reportes.
- Cliente: opera únicamente los datos de su cliente; dashboard inicia en 0 para un cliente nuevo.

## Módulos
Dashboard, Galpones, Agua, Alimento, Amoniaco, Mortalidad, Inventario, Movimientos, Alertas, Reportes, Usuarios, Clientes y Roles.

La carpeta `legacy-php` de los paquetes anteriores se conserva como referencia, pero esta versión ya no depende de PHP/XAMPP para ejecutarse.
