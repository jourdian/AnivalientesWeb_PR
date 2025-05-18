# Flujos funcionales de la aplicación móvil

Este documento describe los flujos principales dentro de la app móvil AniValientes, utilizada por los ciudadanos para reportar casos de abandono animal.

---

## 🛡️ Requisitos generales

* El usuario debe estar autenticado para usar la app.
* Se requiere conexión a internet activa.
* Se solicita permiso para acceder a la cámara y a la ubicación geográfica del dispositivo.

---

## 🔐 Inicio de sesión

1. El usuario abre la app.
2. Se muestra una pantalla de login con email y contraseña.
3. Al autenticarse correctamente, se guarda el token de acceso (Laravel Sanctum).
4. El usuario es redirigido a la pantalla principal.

---

## 🏛️ Pantalla principal (Home)

Una vez logueado, el usuario ve:

* Una lista de denuncias previas realizadas por él mismo
* El estado de cada denuncia (pendiente, en revisión, resuelta, desestimada)
* Un botón flotante para crear una nueva denuncia

Cada tarjeta de denuncia muestra:

* Foto en miniatura
* Título del caso
* Estado actual (con color)

---

## 📷 Crear nueva denuncia

1. El usuario pulsa el botón "+" para crear una nueva denuncia.

2. Se abre la cámara directamente para capturar una imagen.

3. Tras la foto, se muestra un formulario con:

   * Campo de título
   * Campo de descripción
   * Ubicación actual mostrada (obtenida por GPS)
   * Desplegable con administración sugerida según localización

4. El usuario confirma y envía la denuncia.

5. Se recibe una confirmación y la denuncia aparece en la lista como "Pendiente".

---

## 📈 Estados posibles de una denuncia

* **Pendiente**: recién enviada, a la espera de gestión
* **En proceso**: la administración la está revisando
* **Resuelta**: ya se ha actuado o respondido
* **Desestimada**: no se considera válida o no requiere actuación

---

## 💬 Notificaciones

* Cuando una administración responde a una denuncia, el usuario recibe una notificación push (si tiene activado ese permiso).
* La respuesta también se muestra al entrar en la denuncia.

---

## 🔐 Perfil del usuario

* Desde el menú de configuración, el usuario puede ver sus datos (no modificables en esta versión).
* Se incluye: nombre, apellidos, email, teléfono y dirección completa.

---

## 🌐 Configuración

Opciones disponibles:

* Activar o desactivar notificaciones push
* Cambiar idioma (opcional)
* Cerrar sesión

---

## 🔗 Conexión con la API

Todos los datos se obtienen y envían mediante peticiones HTTP autenticadas con token:

* GET `/api/reports` → lista de denuncias
* POST `/api/reports` → enviar nueva denuncia
* GET `/api/administrations` → obtener administraciones disponibles
* GET `/api/user` → datos del usuario actual
