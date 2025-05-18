# 🐾 AniValientes

**AniValientes** es una plataforma digital desarrollada para luchar contra el abandono animal. Permite a la ciudadanía denunciar casos desde una app móvil y a las administraciones locales gestionarlos desde un panel web.

> Proyecto académico — Grado en Técnicas de Interacción Digital y Multimedia  
> **Proyecto Media 3 - PR**  
> Autor: **Jordi Hernández Vinyals**

---

## 🧩 Arquitectura general

- 🔧 **AnivalientesWeb**: Panel web institucional (Laravel + Vue 3 + Inertia)
- 📱 **AnivalientesApp**: Aplicación móvil ciudadana (React Native + Expo)
- 🗄️ **MySQL** como base de datos
- 🔔 **Firebase Cloud Messaging** para notificaciones push (desde backend)
- 🐳 **Docker** para entorno de desarrollo y despliegue local

---

## 🚀 Tecnologías principales

| Componente        | Tecnología                             |
|------------------|-----------------------------------------|
| Backend API       | Laravel 10 + Sanctum                   |
| Frontend Web      | Vue 3 + Inertia.js                     |
| Base de datos     | MySQL                                  |
| App móvil         | React Native (Expo)                    |
| Documentación API | Scribe                                 |
| Contenedores      | Docker + Docker Compose                |

---

## 📲 Aplicación móvil (AniValientesApp)

- Registro de denuncias con foto, ubicación y descripción
- Consulta del estado de denuncias previas
- Recepción de notificaciones push cuando la administración responde

📦 Repositorio correspondiente: [AnivalientesApp_PR](https://github.com/jourdian/AnivalientesApp_PR)

---

## 🖥️ Panel web institucional (AniValientesWeb)

- Listado en tiempo real de denuncias recibidas
- Detalle completo con mapa, imagen y datos del denunciante
- Respuesta al ciudadano desde la propia web
- Estadísticas (por estado, zonas, efectividad)
- Gestión de usuarios institucionales y personalización visual

🖥️ El panel está desplegado públicamente en:  
🔗 [http://138.68.174.17:8080](http://138.68.174.17:8080)

---

## 🔐 Autenticación y roles

- **Aplicación móvil**: autenticación con token (`Sanctum`)
- **Panel web**: sesión clásica con login y contraseña

| Rol           | Acceso           |
|---------------|------------------|
| `citizen`     | App móvil        |
| `institutional` | Panel web      |

---

## 📚 Documentación de la API REST

La documentación de todos los endpoints, generada con [Scribe](https://scribe.knuckles.wtf/), está disponible online en:

🔗 [http://138.68.174.17:8080/docs](http://138.68.174.17:8080/docs)

Incluye:

- Autenticación con token `Bearer`
- Endpoints para login, creación de denuncia, consulta, respuesta
- Ejemplos en cURL, JavaScript y más

---

## 🔑 Accesos de prueba

| Rol           | Email                         | Contraseña |
|---------------|-------------------------------|------------|
| Ciudadano     | alumnouocbcn@email.com        | password   |
| Institucional | uocbcn@uoc.com                | password   |

Solo se puede acceder al panel institucional con rol institucional. El usuario ciudadano solo puede acceder a la app móvil.

---

## 📝 Licencia y condiciones de uso

Este proyecto ha sido desarrollado como entrega académica en el marco del Grado en Técnicas de Interacción Digital y Multimedia de la Universitat Oberta de Catalunya (UOC).

Se autoriza exclusivamente su uso con fines académicos y educativos, citando al autor.  
Queda prohibido su uso comercial, reproducción total o parcial en otros contextos, o redistribución sin consentimiento expreso.

© Jordi Hernández Vinyals, 2025

---
