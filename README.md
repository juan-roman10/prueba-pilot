# 🚀 Prueba Pilot - API REST con Laravel & Docker

API REST desarrollada con **Laravel 12 / 13 (PHP 8.4)** implementando el patrón de diseño **Cadena de Responsabilidades (Chain of Responsibility)**, autenticación stateless con **JWT (JSON Web Token)** y un entorno completamente contenedorizado con **Docker Compose** (PHP-FPM 8.4, Nginx, MySQL 5.7, phpMyAdmin y Redis).

---

## 📑 Tabla de Contenidos
1. [Servicios y Puertos](#-servicios-y-puertos)
2. [Puesta en Marcha Paso a Paso (Docker)](#-puesta-en-marcha-paso-a-paso-docker)
3. [Ejecución de Tests Automatizados](#-ejecución-de-tests-automatizados)
4. [Guía Completa de Endpoints con cURL](#-guía-completa-de-endpoints-con-curl)
   - [Autenticación JWT](#1-autenticación-api-auth)
   - [Categorías](#2-categorías-api-categories)
   - [Artículos](#3-artículos-api-articles)
   - [Usuarios](#4-usuarios-api-users)
5. [Comandos Útiles de Mantenimiento y Depuración](#-comandos-útiles-de-mantenimiento-y-depuración)

---

## 📦 Servicios y Puertos

| Servicio | Contenedor | Host / Puerto Local | Descripción |
|---|---|---|---|
| **Nginx (Web)** | `laravel_app_nginx` | `http://localhost:8000` | Servidor web que expone la API |
| **PHP 8.4-FPM** | `laravel_app_php` | Interno (`app:9000`) | Intérprete PHP con extensiones y Composer |
| **MySQL 5.7** | `laravel_app_mysql` | `localhost:3306` | Base de datos relacional principal |
| **phpMyAdmin** | `laravel_app_pma` | `http://localhost:8080` | Panel web para administración de MySQL |
| **Redis** | `laravel_app_redis` | `localhost:6379` | Cache, sesiones y colas |

---

## ⚙️ Puesta en Marcha Paso a Paso (Docker)

Ejecuta los siguientes comandos desde la raíz del proyecto:

### 1. Configurar archivos de entorno
```bash
# Copiar variables de entorno para Docker
cp .env.example .env

# Copiar variables de entorno para Laravel
cp src/.env.example src/.env
```

### 2. Construir e iniciar los contenedores
```bash
docker compose up -d --build
```

### 3. Ajustar permisos de directorios de almacenamiento
```bash
docker compose exec app chmod -R 777 storage bootstrap/cache
```

### 4. Instalar dependencias de PHP vía Composer
```bash
docker compose exec app composer install
```

### 5. Generar la clave de la aplicación Laravel
```bash
docker compose exec app php artisan key:generate
```

### 6. Generar la clave secreta para JWT
```bash
docker compose exec app php artisan jwt:secret
```

### 7. Ejecutar las migraciones de Base de Datos
```bash
docker compose exec app php artisan migrate
```

> **Verificación:**
> * API / Web: `http://localhost:8000`
> * phpMyAdmin: `http://localhost:8080` (Servidor: `mysql`, Usuario: `laravel_user`, Contraseña: `laravel_password`, BD: `laravel`).

---

## 🧪 Ejecución de Tests Automatizados

La suite de pruebas utiliza **PHPUnit 12** con base de datos SQLite en memoria (`:memory:`).

### Ejecutar toda la suite de tests:
```bash
docker compose exec app php artisan test
```

### Ejecutar con detalle de pruebas (`--testdox`):
```bash
docker compose exec app php artisan test --testdox
```

### Ejecutar solo los tests de Autenticación:
```bash
docker compose exec app php artisan test --filter=AuthTest
```

### Ejecutar solo los tests de Artículos:
```bash
docker compose exec app php artisan test --filter=ArticleTest
```

### Detener la ejecución al primer fallo:
```bash
docker compose exec app php artisan test --stop-on-failure
```

---

## 📡 Guía Completa de Endpoints con cURL

A continuación tienes todos los comandos `curl` listos para copiar, pegar y probar en la terminal.

---

### 1. Autenticación (`/api/auth`)

#### A. Registro de Usuario (`POST /api/auth/register`)
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Mariano Admin",
    "email": "admin@example.com",
    "password": "password123",
    "rol": "admin",
    "estado": "activo"
  }'
```

#### B. Inicio de Sesión (`POST /api/auth/login`)
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```
*Respuesta esperada:*
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

#### C. Obtener Datos del Usuario Autenticado (`GET /api/auth/me`)
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

#### D. Refrescar Token JWT (`POST /api/auth/refresh`)
```bash
curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

#### E. Cerrar Sesión (`POST /api/auth/logout`)
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

---

### 2. Categorías (`/api/categories`)
*(Requiere Header `Authorization: Bearer <TU_TOKEN_JWT>`)*

#### A. Crear Categoría (`POST /api/categories`)
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>" \
  -d '{
    "nombre": "Tecnología",
    "descripcion": "Artículos relacionados a desarrollo y gadgets",
    "estado": "activa"
  }'
```

#### B. Listar Categorías con Filtros y Paginación (`GET /api/categories`)
```bash
# Listar todas
curl -X GET http://localhost:8000/api/categories \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"

# Filtrar por estado y término de búsqueda
curl -X GET "http://localhost:8000/api/categories?estado=activa&search=Tecno&per_page=5" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

#### C. Actualizar Categoría (`PUT /api/categories/{id}`)
```bash
curl -X PUT http://localhost:8000/api/categories/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>" \
  -d '{
    "nombre": "Tecnología e Innovación",
    "estado": "activa"
  }'
```

#### D. Eliminar Categoría (`DELETE /api/categories/{id}`)
*(Protegido: si la categoría está asociada a un artículo, la eliminación es rechazada con código HTTP 400)*
```bash
curl -X DELETE http://localhost:8000/api/categories/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

---

### 3. Artículos (`/api/articles`)
*(Requiere Header `Authorization: Bearer <TU_TOKEN_JWT>` y usuario con `estado: activo`)*

#### A. Crear Artículo (`POST /api/articles`)
*Genera automáticamente el `slug` único a partir del título y asigna el autor autenticado.*
```bash
curl -X POST http://localhost:8000/api/articles \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>" \
  -d '{
    "titulo": "Arquitectura de Software y Patrones de Diseño",
    "contenido": "Guía práctica sobre la implementación del patrón Chain of Responsibility en Laravel...",
    "estado": "publicado",
    "fecha_publicacion": "2026-08-26 14:00:00",
    "categories": [1]
  }'
```

#### B. Listar Artículos con Filtros (`GET /api/articles`)
```bash
# Listar todos con relaciones cargadas (autor y categorías)
curl -X GET http://localhost:8000/api/articles \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"

# Filtrar por estado, categoría y texto
curl -X GET "http://localhost:8000/api/articles?estado=publicado&category_id=1&search=Arquitectura&per_page=10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

#### C. Actualizar Artículo (`PUT /api/articles/{id}`)
*Solo el autor original o un usuario con rol `admin` pueden editar el artículo. Si el título cambia, regenera el slug único.*
```bash
curl -X PUT http://localhost:8000/api/articles/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>" \
  -d '{
    "titulo": "Arquitectura de Software Moderna",
    "estado": "publicado",
    "categories": [1]
  }'
```

#### D. Eliminar Artículo (`DELETE /api/articles/{id}`)
*Solo el autor original o un administrador pueden eliminar.*
```bash
curl -X DELETE http://localhost:8000/api/articles/1 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

---

### 4. Usuarios (`/api/users`)
*(Requiere Header `Authorization: Bearer <TU_TOKEN_JWT>` y usuario con rol `admin`)*

#### A. Listar Usuarios (`GET /api/users`)
```bash
curl -X GET "http://localhost:8000/api/users?rol=editor&estado=activo&search=Mariano" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

#### B. Crear Usuario desde Panel Admin (`POST /api/users`)
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>" \
  -d '{
    "name": "Editor Junior",
    "email": "editor@example.com",
    "password": "password123",
    "rol": "editor",
    "estado": "activo"
  }'
```

#### C. Editar Usuario (`PUT /api/users/{id}`)
```bash
curl -X PUT http://localhost:8000/api/users/2 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>" \
  -d '{
    "name": "Editor Modificado",
    "estado": "inactivo"
  }'
```

#### D. Eliminar Usuario (`DELETE /api/users/{id}`)
*(Protegido contra auto-eliminación de la propia cuenta logueada).*
```bash
curl -X DELETE http://localhost:8000/api/users/2 \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TU_TOKEN_JWT>"
```

---

## 🛠️ Comandos Útiles de Mantenimiento y Depuración

### Entrar a la terminal interactiva del contenedor PHP:
```bash
docker compose exec app bash
```

### Entrar a la consola interactiva de Laravel (Tinker):
```bash
docker compose exec app php artisan tinker
```

### Ver logs en tiempo real:
```bash
# Logs del contenedor de la aplicación
docker compose logs -f app

# Logs del servidor Nginx
docker compose logs -f nginx

# Logs de la base de datos MySQL
docker compose logs -f mysql
```

### Limpiar y recargar cachés de configuración y rutas:
```bash
docker compose exec app php artisan optimize:clear
```

### Resetear la base de datos (Ejecutar migraciones desde cero):
```bash
docker compose exec app php artisan migrate:fresh
```

### Detener los contenedores:
```bash
docker compose down
```

### Detener y eliminar volúmenes (reset completo de datos):
```bash
docker compose down -v
```
