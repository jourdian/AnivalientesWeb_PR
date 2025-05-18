# Modelo de base de datos

Este documento describe la estructura de la base de datos MySQL utilizada por la plataforma AniValientes. Todas las relaciones están normalizadas y se utilizan claves foráneas para mantener la integridad referencial.

---

## 🔒 Principales entidades

### `users`

Contiene tanto usuarios ciudadanos como institucionales.

| Campo              | Tipo      | Descripción                 |                                           |
| ------------------ | --------- | --------------------------- | ----------------------------------------- |
| id                 | bigint    | Clave primaria              |                                           |
| first\_name        | string    | Nombre del usuario          |                                           |
| last\_name         | string    | Apellidos                   |                                           |
| email              | string    | Email único                 |                                           |
| password           | string    | Hash de contraseña          |                                           |
| phone              | string    | Teléfono de contacto        |                                           |
| street             | string    | Calle                       |                                           |
| city               | string    | Ciudad                      |                                           |
| province           | string    | Provincia                   |                                           |
| photo\_path        | string    | null                        | Ruta de la foto de perfil (opcional)      |
| role               | enum      | 'citizen' o 'institutional' |                                           |
| administration\_id | bigint    | null                        | FK a `administrations` (si institucional) |
| position           | string    | null                        | Cargo institucional (si aplica)           |
| created\_at        | timestamp | Fecha de alta               |                                           |

### `administrations`

Representa a las instituciones receptoras de denuncias.

| Campo       | Tipo      | Descripción               |                             |
| ----------- | --------- | ------------------------- | --------------------------- |
| id          | bigint    | Clave primaria            |                             |
| name        | string    | Nombre oficial            |                             |
| email       | string    | Correo de contacto        |                             |
| phone       | string    | Teléfono                  |                             |
| address     | string    | Dirección física completa |                             |
| city        | string    | Ciudad                    |                             |
| province    | string    | Provincia                 |                             |
| latitude    | decimal   | Coordenadas geográficas   |                             |
| longitude   | decimal   | Coordenadas geográficas   |                             |
| logo\_path  | string    | null                      | Ruta del logo institucional |
| created\_at | timestamp | Fecha de creación         |                             |

### `reports`

Denuncias creadas desde la app móvil.

| Campo              | Tipo      | Descripción                                     |                   |
| ------------------ | --------- | ----------------------------------------------- | ----------------- |
| id                 | bigint    | Clave primaria                                  |                   |
| title              | string    | Título breve del reporte                        |                   |
| description        | text      | Descripción completa                            |                   |
| latitude           | decimal   | Coordenada de latitud                           |                   |
| longitude          | decimal   | Coordenada de longitud                          |                   |
| address            | string    | Dirección textual                               |                   |
| image\_path        | string    | Ruta de la imagen subida                        |                   |
| status             | enum      | 'pending', 'reviewing', 'resolved', 'dismissed' |                   |
| severity           | enum      | 'low', 'medium', 'high', 'critical'             |                   |
| user\_id           | bigint    | FK al denunciante (`users`)                     |                   |
| administration\_id | bigint    | FK a la administración receptora                |                   |
| response           | text      | null                                            | Respuesta oficial |
| created\_at        | timestamp | Fecha de creación                               |                   |

### `report_notifications`

Mensajes enviados desde la administración al ciudadano.

| Campo       | Tipo      | Descripción                              |
| ----------- | --------- | ---------------------------------------- |
| id          | bigint    | Clave primaria                           |
| report\_id  | bigint    | FK a la denuncia (`reports`)             |
| user\_id    | bigint    | FK al usuario institucional que responde |
| message     | text      | Texto de la notificación                 |
| created\_at | timestamp | Fecha y hora del envío                   |

---

## 🤸️ Relaciones entre entidades

* Un **usuario** puede tener muchas **denuncias** → (1\:N)
* Una **denuncia** pertenece a una **administración** → (N:1)
* Una **administración** tiene muchos **usuarios institucionales** → (1\:N)
* Una **denuncia** puede tener múltiples **notificaciones** → (1\:N)

---

## ⚖️ Buenas prácticas aplicadas

* Uso de claves foráneas y restricciones `onDelete('cascade')`
* Fechas automáticas con `timestamps()`
* Enum como tipo de campo para evitar errores tipográficos
* Uso de `nullable()` solo en campos opcionales reales (foto, respuesta)

---

## 📊 Consideraciones futuras

* Añadir tabla de logs de actividad
* Posibilidad de comentarios entre administraciones
* Implementar auditoría de cambios sobre denuncias
