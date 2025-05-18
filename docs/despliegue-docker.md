# Despliegue y entorno local con Docker

Este documento describe cómo está configurado el entorno de desarrollo local de AniValientes usando Docker y Docker Compose. Está pensado para facilitar la instalación rápida del proyecto sin necesidad de configurar manualmente Apache, MySQL, PHP u otros servicios.

---

## 🚀 Servicios incluidos

La configuración de Docker incluye los siguientes contenedores:

| Servicio   | Imagen base           | Puerto local   | Función principal                   |
| ---------- | --------------------- | -------------- | ----------------------------------- |
| app        | anivalientesweb-app   | 9000 (interno) | Contenedor PHP con Laravel          |
| web        | nginx\:stable         | 8080           | Servidor web frontal (Nginx)        |
| mysql      | mysql:8               | 3307           | Base de datos relacional            |
| phpmyadmin | phpmyadmin/phpmyadmin | 8081           | Interfaz de administración de MySQL |

---

## 📁 Archivos clave

### `docker-compose.yml`

Define todos los servicios, puertos y volúmenes. Contiene:

* Red interna para comunicación entre servicios
* Volúmenes persistentes para MySQL y Laravel

### `Dockerfile`

Define la imagen `anivalientesweb-app`, basada en PHP 8.2 + extensiones necesarias:

* pdo\_mysql
* gd
* fileinfo
* bcmath
* zip

---

## 🚧 Pasos para iniciar el entorno local

```bash
git clone https://github.com/usuario/anivalientes.git
cd AnivalientesWeb
cp .env.example .env
```

Configura credenciales MySQL en `.env`:

```
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=anivalientes_db
DB_USERNAME=root
DB_PASSWORD=root
```

Luego ejecuta:

```bash
docker compose up -d
```

Y dentro del contenedor:

```bash
docker compose exec app composer install
docker compose exec app php artisan migrate --seed
```

---

## 🔑 Accesos y puertos

| Servicio    | URL local                                                   |
| ----------- | ----------------------------------------------------------- |
| Panel web   | [http://localhost:8080](http://localhost:8080)              |
| phpMyAdmin  | [http://localhost:8081](http://localhost:8081)              |
| API REST    | [http://localhost:8080/api/](http://localhost:8080/api/)... |
| Docs Scribe | [http://localhost:8080/docs](http://localhost:8080/docs)    |

---

## 🔧 Comandos útiles

### Detener los servicios

```bash
docker compose down
```

### Reiniciar el contenedor `app`

```bash
docker compose restart app
```

### Acceder al shell del contenedor `app`

```bash
docker compose exec app bash
```

### Ejecutar comandos artisan

```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

## 🔍 Solución de problemas comunes

* **Permisos en `storage` y `bootstrap/cache`**:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

* **Puerto MySQL ya en uso**: cambia `3306` por `3307` en el `docker-compose.yml`
* **Laravel.log no escribible**: asegúrate de que la carpeta `storage/logs` existe y tiene permisos

---

## ⚒️ Recomendaciones

* No hacer cambios directamente dentro del contenedor si no se replican en el volumen compartido
* Usar `.env` para configurar rutas y claves API según el entorno
* Comprobar siempre que los servicios están corriendo con:

```bash
docker compose ps
```

---

## 🚤 Producción

Este entorno está pensado para desarrollo local. El despliegue en producción debe hacerse con un sistema de CI/CD o VPS adaptado, asegurando:

* Certificados SSL
* Seguridad en las variables de entorno
* Sistema de copias de seguridad
