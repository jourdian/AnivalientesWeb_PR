# Estructura de la base de datos

Este documento describe la estructura de la base de datos relacional utilizada por AniValientes. El sistema se apoya en MySQL 8 para almacenar de forma estructurada toda la información relacionada con usuarios, denuncias, administraciones y notificaciones.

---

## 🔍 Visón general

La base de datos se estructura en torno a los siguientes bloques funcionales:

* Usuarios (ciudadanos e institucionales)
* Denuncias
* Administraciones
* Notificaciones asociadas a denuncias
* Configuraciones visuales

---

## 📄 Tablas principales

### `users`

* `id`: identificador primario
* `first_name`, `last_name`: nombre y apellidos
* `email`, `password`: credenciales
* `role`: `citizen` o `institutional`
* `phone`: teléfono de contacto
* `street`, `city`, `province`: dirección completa
* `photo_path`: ruta a la foto de perfil
* `administration_id`: FK opcional para usuarios institucionales
* `position`: cargo o rol dentro de la administración (solo institutional)
* Timestamps (`created_at`, `updated_at`)

### `administrations`

* `id`: identificador primario
* `name`: nombre oficial
* `email`, `phone`: datos de contacto
* `address`, `city`, `province`: localización
* `latitude`, `longitude`: coordenadas geográficas (para asignación por proximidad)
* `logo_path`: ruta al logo visual
* Timestamps

### `reports`

* `id`: identificador de la denuncia
* `user_id`: FK al denunciante (usuario citizen)
* `administration_id`: FK a la administración receptora
* `title`, `description`: contenido textual
* `latitude`, `longitude`: localización del incidente
* `address`: dirección textual introducida o calculada
* `image_path`: foto del caso
* `status`: `pending`, `reviewing`, `resolved`, `dismissed`
* `severity`: `low`, `medium`, `high`, `critical`
* `response`: texto de la actuación institucional
* Timestamps

### `report_notifications`

* `id`: identificador
* `report_id`: FK a la denuncia
* `user_id`: FK al institucional que responde
* `message`: texto del mensaje enviado al ciudadano
* Timestamps

---

## 🔗 Relaciones clave

* `users.administration_id` → `administrations.id` (opcional)
* `users.id` → `reports.user_id` (uno a muchos)
* `administrations.id` → `reports.administration_id` (uno a muchos)
* `reports.id` → `report_notifications.report_id`
* `users.id` → `report_notifications.user_id`

---

## 📈 Consideraciones

* Los usuarios `citizen` no tienen asociación a una administración, pero los `institutional` sí
* Las denuncias se asignan por geolocalización a la administración más cercana (dentro de un radio determinado)
* Las notificaciones actúan como historial y medio de comunicación
* La ruta de la imagen y del logo son relativas a `/storage/`

---

## 🔢 Scripts de referencia

* Las migraciones se encuentran en `database/migrations`
* El seeder principal (`DatabaseSeeder.php`) crea:

  * Usuarios institucionales y ciudadanos de prueba
  * Denuncias con datos simulados
  * Administraciones de ejemplo (`Ayuntamiento de UOC`, etc.)

---

## 📜 Documentación relacionada

* [backend.md](./backend.md)
* [flujos-app-movil.md](./flujos-app-movil.md)
* [flujos-panel-web.md](./flujos-panel-web.md)
