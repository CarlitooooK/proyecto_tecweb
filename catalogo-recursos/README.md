# Sistema de Gestión de Catálogo de Recursos

Sistema completo de gestión de recursos con panel administrativo, desarrollado con PHP, HTML5, CSS y JavaScript.

## Características

- ✅ Sistema de autenticación (Login/Signup)
- ✅ Dashboard administrativo con estadísticas
- ✅ Catálogo público de recursos
- ✅ Carga de archivos con formulario completo
- ✅ Sistema de descargas con bitácora
- ✅ API REST con Composer
- ✅ 4 gráficas de estadísticas (Chart.js)
- ✅ Comunicación asíncrona con AJAX
- ✅ Base de datos completa con 4 entidades
- ✅ CSS puro sin frameworks
- ✅ Compatible con XAMPP y phpMyAdmin

## Instalación

### 1. Requisitos
- XAMPP (Apache + PHP 7.4+ + MySQL)
- Composer (opcional, para dependencias)

### 2. Instalación Paso a Paso

1. **Clonar o descargar el proyecto** en la carpeta `htdocs` de XAMPP:
   \`\`\`
   C:\xampp\htdocs\catalogo-recursos\
   \`\`\`

2. **Iniciar XAMPP**:
   - Abrir XAMPP Control Panel
   - Iniciar Apache
   - Iniciar MySQL

3. **Crear la base de datos**:
   - Abrir phpMyAdmin: http://localhost/phpmyadmin
   - Crear nueva base de datos: `catalogo_recursos`
   - Importar el archivo: `database/schema.sql`
   - O ejecutar el script SQL completo desde la pestaña SQL

4. **Configurar la conexión** (si es necesario):
   - Editar `config/database.php`
   - Ajustar credenciales de MySQL si son diferentes

5. **Instalar dependencias de Composer** (opcional):
   \`\`\`bash
   composer install
   \`\`\`

6. **Crear carpeta de uploads**:
   - La carpeta `uploads/` se crea automáticamente
   - Asegurarse de que tenga permisos de escritura

### 3. Acceder al Sistema

- **Catálogo público**: http://localhost/catalogo-recursos/
- **Login**: http://localhost/catalogo-recursos/login.php
- **Registro**: http://localhost/catalogo-recursos/signup.php
- **Dashboard Admin**: http://localhost/catalogo-recursos/dashboard.php

### 4. Credenciales por Defecto

**Administrador:**
- Email: admin@ejemplo.com
- Password: admin123

## Solución de Problemas Comunes

### Error: "Cannot add or update a child row: a foreign key constraint fails"

Este error ocurre cuando intentas crear un recurso pero el usuario_id de la sesión no existe en la base de datos.

**Solución:**
1. Asegúrate de haber importado el archivo `database/schema.sql` completamente
2. Verifica que el usuario administrador existe:
   \`\`\`sql
   SELECT * FROM usuarios WHERE email = 'admin@ejemplo.com';
   \`\`\`
3. Si no existe, créalo manualmente:
   \`\`\`sql
   INSERT INTO usuarios (nombre, email, password, rol) VALUES 
   ('Administrador', 'admin@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
   \`\`\`
4. Cierra sesión y vuelve a iniciar sesión con las credenciales correctas
5. Si el problema persiste, elimina las cookies del navegador y las sesiones de PHP

### Error: "Failed to open stream: No such file or directory"

**Solución:**
- Asegúrate de que la carpeta `uploads/` existe y tiene permisos de escritura
- Verifica que todas las rutas en `config/database.php` sean correctas

### Las gráficas no se muestran en el Dashboard

**Solución:**
1. Verifica que tienes conexión a internet (Chart.js se carga desde CDN)
2. Abre la consola del navegador (F12) y verifica si hay errores
3. Asegúrate de tener datos en la tabla `bitacora_descargas`

## Estructura del Proyecto

\`\`\`
catalogo-recursos/
├── api/
│   ├── index.php              # API REST
│   └── eliminar-recurso.php   # Endpoint para eliminar
├── assets/
│   ├── css/
│   │   └── styles.css         # Estilos CSS puros
│   └── js/
│       ├── main.js            # JavaScript principal
│       └── dashboard.js       # Gráficas y dashboard
├── config/
│   ├── database.php           # Configuración DB
│   └── session.php            # Manejo de sesiones
├── database/
│   └── schema.sql             # Script de base de datos
├── src/
│   ├── Usuario.php            # Clase Usuario
│   └── Recurso.php            # Clase Recurso
├── uploads/                   # Archivos subidos
├── index.php                  # Catálogo público
├── login.php                  # Inicio de sesión
├── signup.php                 # Registro
├── dashboard.php              # Panel admin
├── cargar-recurso.php         # Formulario de carga
├── descargar.php              # Descarga de archivos
├── logout.php                 # Cerrar sesión
├── composer.json              # Dependencias
└── README.md                  # Este archivo
\`\`\`

## Base de Datos

### Entidades

1. **USUARIOS**
   - id, nombre, email, password, rol, fecha_registro, activo

2. **RECURSOS** (Principal)
   - id, nombre_recurso, autor, departamento, empresa_institucion
   - fecha_creacion, descripcion, archivo_ruta, archivo_original
   - tipo_recurso, lenguaje_programacion, eliminado, usuario_id

3. **BITÁCORA DE ACCESO**
   - id, usuario_id, accion, ip_address, user_agent, fecha_hora

4. **BITÁCORA DE DESCARGAS**
   - id, recurso_id, usuario_id, ip_address, fecha_descarga
   - dia_semana, hora_dia

## Funcionalidades

### Público
- Ver catálogo de recursos
- Buscar y filtrar recursos
- Descargar recursos (con registro en bitácora)
- Registro de usuario
- Inicio de sesión

### Usuario Autenticado
- Todas las funcionalidades públicas
- Subir nuevos recursos con archivo
- Ver historial de recursos subidos

### Administrador
- Todas las funcionalidades de usuario
- Dashboard con estadísticas
- 4 gráficas interactivas:
  - Descargas por tipo de recurso
  - Descargas por día de la semana
  - Descargas por hora del día
  - Descargas por lenguaje de programación
- Gestión completa de recursos
- Ver y eliminar recursos

## API REST

### Endpoints Disponibles

\`\`\`
GET /api/index.php/recursos
Obtiene todos los recursos

GET /api/index.php/estadisticas?tipo=tipo_recurso
Obtiene estadísticas por tipo

GET /api/index.php/estadisticas?tipo=lenguaje
Obtiene estadísticas por lenguaje

GET /api/index.php/estadisticas?tipo=dia_semana
Obtiene estadísticas por día

GET /api/index.php/estadisticas?tipo=hora_dia
Obtiene estadísticas por hora
\`\`\`

## Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3 Puro, JavaScript (AJAX)
- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL
- **Gráficas**: Chart.js
- **Gestión de Dependencias**: Composer
- **Servidor**: Apache (XAMPP)

## Seguridad

- Contraseñas hasheadas con bcrypt
- Protección contra SQL Injection (PDO Prepared Statements)
- Validación de sesiones
- Control de acceso por roles
- Prevención de ejecución de scripts en carpeta uploads
- Sanitización de datos de usuario

## Licencia

Proyecto educativo de código abierto.
