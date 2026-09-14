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

## Diagrama de clases
El diseño orientado a objetos define las entidades principales, sus atributos, métodos de negocio y enumeraciones del sistema:

### 🏠 1. Entidades Principales

* **`Galpon`**: Entidad central que gestiona el espacio físico.
  * **Atributos**: `id`, `nombre`, `ubicacion`, `capacidad`, `descripcion`, `estado` (`EstadoGalpon`), `createdAt`, `updatedAt`.
  * **Métodos**: `listarSensores()`, `listarLotes()`, `estaActivo()`.
  * **Relaciones**: Alberga `Lote` (`1` a `0..*`), contiene `Sensor` (`1` a `0..*`) y genera `Alerta` (`1` a `0..*`).

* **`Lote`**: Modela el grupo de aves alojado en la instalación.
  * **Atributos**: `id`, `idGalpon`, `codigo`, `fechaIngreso`, `cantidadInicial`, `cantidadActual`, `estado` (`EstadoLote`), `observaciones`, `createdAt`, `updatedAt`.
  * **Métodos**: `calcularMortalidad()`, `calcularDiasEnGalpon()`, `cerrar()`.
  * **Relaciones**: Registra `Consumo` (`1` a `0..*`).

* **`Sensor`**: Dispositivo encargo de capturar métricas.
  * **Atributos**: `id`, `idGalpon`, `idTipo`, `modelo`, `estado` (`EstadoSensor`), `createdAt`.
  * **Métodos**: `obtenerUltimaLectura()`, `estaOperativo()`.
  * **Relaciones**: Tipificado por `TipoSensor` (`1` a `0..*`), registra `LecturaSensor` (`1` a `0..*`) y dispara `Alerta` (`1` a `0..*`).

* **`TipoSensor`**: Catálogo de configuración de parámetros ambientales.
  * **Atributos**: `id`, `nombre`, `unidad`, `umbraWarn`, `umbralCrit`, `icons`.
  * **Métodos**: `clasificarNivel(valor: Decimal): NivelAlerta`.

* **`LecturaSensor`**: Histórico de telemetría.
  * **Atributos**: `id`, `idSensor`, `valor`, `fecha`.
  * **Métodos**: `esReciente()`.

* **`Consumo`**: Control de insumos utilizados por el lote.
  * **Atributos**: `id`, `idLote`, `tipo` (`TipoConsumo`), `cantidad`, `fecha`.
  * **Métodos**: `esMismoMes(fecha: data)`.

* **`Alerta`**: Notificaciones de estado e incidencias.
  * **Atributos**: `id`, `idGalpon`, `idSensor`, `tipo` (`NivelAlerta`), `titulo`, `descripcion`, `falsa`, `createdAt`.
  * **Métodos**: `marcarLeida()`, `esActiva()`.

---

### 🏷️ 2. Enumeraciones (`<<enumeration>>`)

* **`EstadoGalpon`**: `ACTIVO`, `INACTIVO`.
* **`EstadoLote`**: `ACTIVO`, `CERRADO`, `VENDIDO`.
* **`EstadoSensor`**: `ACTIVO`, `INACTIVO`, `FALLA`.
* **`TipoConsumo`**: `ALIMENTO`, `AGUA`.
* **`NivelAlerta`**: `CRIT`, `WARN`, `OK`.
  ### link
https://drive.google.com/drive/folders/15ZtwqyFIx3AvaAI0KJdHYuzebWLXXBhm?usp=drive_link

## Diagrama de componentes 
El sistema **SIGA Avícola** se organiza mediante un esquema por capas que desacopla la interacción de los actores, la presentación visual, la lógica de negocio y la persistencia de datos:

### 👥 1. Actores Externos y Dispositivos
* **`Sensor físico`** (DHT22 / MQ-135 / MQ-811): Dispositivos encargados de publicar lecturas directamente hacia la capa de aplicación.
* **`Operario de campo`**: Interactúa con el sistema registrando consumos y gestionando lotes.
* **`Administrador`**: Supervisa el sistema visualizando el dashboard en tiempo real y gestionando las alertas.

### 💻 2. Subsistema: Capa de Presentación
* **`Registro de consumos`**: Módulo para la entrada de datos de alimento y agua.
* **`Gestión de lotes`**: Interfaz para el alta y consulta de lotes de aves.
* **`Dashboard`**: Componente de monitoreo en tiempo real.
* **`Gestión de alertas`**: Vista para revisión y marcado de notificaciones.

### ⚙️ 3. Subsistema: Capa de Aplicación (Lógica de Negocio)
* **`Sensor Service`**: Recibe y valida la telemetría enviada por los sensores físicos.
* **`Consumo Service`**: Gestiona el registro y las estadísticas de consumo (valida lote activo con `Lote Repository`).
* **`Lote Service`**: Administra el ciclo de vida del lote y evalúa umbrales en conjunto con `Alerta Service`.
* **`Alerta Service`**: Encargado del análisis y evaluación de umbrales para la generación de notificaciones.

### 🗄️ 4. Subsistema: Capa de Datos y Persistencia
* **Repositorios**: `Consumo Repository`, `Lote Repository`, `Sensor Repository`, `Alerta Repository` y `Galpón Repository`. Encargados de abstraer la comunicación con la base de datos.
* **`Base de datos`**: Base de datos relacional **MySQL - InnoDB** (`siga_avicola`), la cual recibe y procesa todas las consultas SQL emitidas por los repositorios.
  ### link
* https://drive.google.com/drive/folders/1ApXewjp5ks--klJuIjsrUwLB9HP_Dfw2?usp=drive_link

## Diagrama de paquetes 
El sistema organiza sus entidades de base de datos en **cuatro paquetes principales**, garantizando una arquitectura modular y escalable.

### 🏢 1. Paquete `infraestructura`
Representa el núcleo de la infraestructura física de la granja avícola.
* **`galpon`**: Entidad principal que gestiona las instalaciones (`id`, `nombre`, `ubicacion`, `capacidad`, `estado`, `created_at`).
* **Relaciones**:
  * Un galpón aloja un único **`lote`** activo (`1` a `0..1`).
  * Un galpón contiene múltiples **`sensor`** de monitoreo (`1` a `0..*`).
  * Importa los módulos de `monitoreo_ambiental`, `produccion` y `gestion_operacional`.

### 🌡️ 2. Paquete `monitoreo_ambiental`
Encargado de la parametrización y registro de las condiciones físicas del entorno.
* **`tipo_sensor`**: Define categorías y umbrales de advertencia/críticos (`umbral_warn`, `umbral_crit`, `unidad`).
* **`sensor`**: Mapea los dispositivos instalados en cada galpón (`id_galpon`, `id_tipo`, `modelo`, `estado`).
* **`lectura_sensor`**: Histórico de mediciones capturadas (`id_sensor`, `valor`, `fecha`).

### 🐔 3. Paquete `produccion`
Administra la operación biológica y el control de insumos de los lotes.
* **`lote`**: Seguimiento de las aves ingresadas a un galpón (`codigo`, `fecha_ingreso`, `cantidad_inicial`, `cantidad_actual`, `estado`).
* **`consumo`**: Histórico de insumos y alimento suministrados por lote (`id_lote`, `tipo`, `cantidad`, `fecha`).

### 🔔 4. Paquete `gestion_operacional`
Módulo transversal para el control de incidencias del sistema.
* **`alerta`**: Registro unificado de notificaciones y eventos anómalos disparados por sensores o galpones (`id_galpon`, `id_sensor`, `tipo`, `titulo`, `descripcion`, `leida`).
  ### link
* https://drive.google.com/drive/folders/1SFLdc2suXPcetvhh-T60ukXjJPw522QI?usp=drive_link

## Diagrama de despliegue 
La infraestructura del sistema está distribuida en arquitectura cliente-servidor e IoT, conectada mediante protocolos estándar a través de red local (LAN/Wi-Fi):

### 🍓 1. Nodo de Campo - Galpón
Representa la capa física donde se realiza la captura de variables ambientales.
* **Dispositivos físicos**: Sensores DHT22 (Temperatura/Humedad), MQ-135 (Amoníaco/CO) y BH1750 (Luminosidad) interconectados vía GPIO / I2C.
* **Microcontrolador**: ESP32 / Raspberry Pi encargado de ejecutar los drivers (`driver_sensores.py`) y el firmware del sistema.
* **Comunicación**: Envía las métricas hacia el servidor mediante peticiones `HTTP POST /api/lecturas` y protocolo de mensajería `MQTT publish :1883`.

### 🖥️ 2. Servidor de Aplicación (Linux Ubuntu 24 LTS)
Centraliza el procesamiento de la lógica de negocio y la gestión del tráfico web.
* **Proceso de Aplicación**:
  * **MQTT Broker (Mosquitto 2.x)**: Recibe y suscribe las lecturas transmitidas por los microcontroladores.
  * **API REST (`siga_api`)**: Desarrollada en Python / FastAPI, procesa la lógica interna mediante servicios dedicados (`sensor_service`, `alerta_service`, `lote_service`, `consumo_service`) y genera alertas automáticas.
* **Servidor Web**:
  * **Frontend (`siga_web`)**: Servido sobre Nginx (HTML/JS), expone los módulos de dashboard, gestión de lotes, registro de consumos y alertas.
  * **Reverse Proxy**: Redirecciona las peticiones desde el servidor web hacia el backend mediante `reverse proxy /api/*`.

### 🗄️ 3. Servidor de Base de Datos (Linux Ubuntu 24 LTS)
Almacena y gestiona la persistencia de los datos del sistema.
* **Engine**: MySQL 8.x - InnoDB.
* **Conexión**: Se comunica con el backend mediante `TCP :3306` utilizando ORM (SQLAlchemy).
* **Módulos persistidos**: Tablas relacionales para galpón-lote, sensor-tipo_sensor, lecturas, consumos y alertas.

### 📱 4. Terminal de Usuario
Representa los clientes finales desde los cuales se accede a la plataforma visual.
* Soporta la interacción desde navegadores web (Chrome, Firefox) y dispositivos móviles (Android / iOS) a través de peticiones seguras `HTTP/HTTPS :443`.
  ### link
* https://drive.google.com/drive/folders/11viHQWiDaP-0Ev3hwxxmACV-8fJo6ssy?usp=drive_link
