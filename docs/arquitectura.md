# Arquitectura general de AniValientes

AniValientes es una plataforma digital desarrollada como ejercicio universitario para la lucha contra el abandono animal. Se compone de varios módulos interconectados que permiten a los ciudadanos registrar casos de abandono desde una app móvil y a las administraciones locales gestionarlos desde un panel web.

Este documento resume la arquitectura de alto nivel de la plataforma y su organización técnica.

---

## 🏙️ Componentes principales

| Componente              | Descripción                                                                     |
| ----------------------- | ------------------------------------------------------------------------------- |
| **AnivalientesWeb**     | Panel web institucional construido con Laravel 10 y Vue 3                       |
| **AnivalientesApp**     | Aplicación móvil (actualmente en desarrollo) basada en React Native             |
| **API REST**            | Interfaz de comunicación entre frontend móvil y backend, documentada con Scribe |
| **Base de datos**       | MySQL, con tablas normalizadas para usuarios, denuncias y notificaciones        |
| **Docker**              | Entorno de desarrollo local con servicios orquestados mediante Docker Compose   |
| **Firebase (opcional)** | Para la entrega de notificaciones push (no implementado en esta versión)        |

---

## 👁️ Visón general

```text
[APP Móvil] <--> [API REST Laravel] <--> [Base de datos MySQL]
                                     \
                                      --> [Frontend Web institucional (Vue + Inertia)]
```

---

## ⚙️ Tecnologías utilizadas

* **Backend**: Laravel 10, PHP 8.2
* **Frontend Web**: Vue 3 + Inertia.js
* **Frontend Móvil**: React Native (Expo) \[en desarrollo]
* **Autenticación**:

  * Token Bearer via Sanctum (para app móvil)
  * Sesiones clásicas (para panel web)
* **Base de datos**: MySQL 8 (persistencia estructurada y relaciones claras)
* **Contenedores**: Docker y Docker Compose (servicios: app, mysql, nginx, phpmyadmin)
* **Documentación API**: Scribe (OpenAPI + Postman + docs interactivos)

---

## 🚀 Flujos principales

### 📲 En la app móvil:

1. El ciudadano se autentica mediante email y contraseña
2. Consulta sus denuncias anteriores
3. Puede crear una nueva denuncia:

   * Captura una imagen del animal
   * Introduce descripción y ubicación geolocalizada
   * Se asigna automáticamente a una administración cercana
4. Recibe notificaciones de estado y respuestas

### 🖥️ En el panel web:

1. El personal institucional inicia sesión
2. Consulta la lista de denuncias recibidas
3. Accede al detalle de cada denuncia (foto, texto, ubicación)
4. Puede responder y cambiar el estado del caso
5. El sistema almacena la respuesta y genera una notificación
6. Se muestran estadísticas agregadas

---

## 🔗 Conectividad

Todos los módulos se comunican mediante peticiones HTTP o internamente por funciones Laravel:

* App → API: llamadas con token
* Web → Backend: sesiones Laravel + Inertia
* API → DB: Eloquent ORM

---

## 📜 Documentación relacionada

* [flujos-app-movil.md](./flujos-app-movil.md)
* [flujos-panel-web.md](./flujos-panel-web.md)
* [base-de-datos.md](./base-de-datos.md)
