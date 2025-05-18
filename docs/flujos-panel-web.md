# Flujos funcionales del panel web

Este documento describe los flujos de trabajo que siguen los usuarios institucionales desde el panel web AniValientes para gestionar las denuncias recibidas.

---

## 🔐 Inicio de sesión

1. El usuario accede a la URL del panel web.
2. Se muestra un formulario de inicio de sesión (email y contraseña).
3. Tras la autenticación, se almacena la sesión en el servidor y se redirige al dashboard.

---

## 📈 Dashboard principal

Al iniciar sesión, se muestra un resumen general:

* Métricas principales:

  * Total de denuncias
  * Incremento semanal
  * Ratio de confianza (porcentaje resuelto)
  * Tasa de atención
* Gráficas:

  * Distribución por estado (circular)
  * Denuncias diarias (barras)

Los datos se filtran por rango de fechas (semana / mes).

---

## 📅 Actividad reciente

* Calendario con heatmap de denuncias recibidas por mes
* Mapa con marcadores de denuncias
* Listado resumen de denuncias recientes

---

## 📄 Listado de denuncias

* Tabla con:

  * Fecha
  * Título
  * Nombre del denunciante
  * Gravedad
  * Estado
* Se puede filtrar por texto (buscador) y estado (desplegable)
* Al hacer clic en una fila se abre el panel lateral con detalles

---

## 📂 Detalle de la denuncia

Incluye:

* Imagen completa
* Texto del denunciante
* Nombre, teléfono y email del ciudadano
* Dirección completa
* Ubicación en el mapa
* Descripción larga
* Historial de notificaciones

Acciones disponibles:

* Cambiar estado (pendiente / en proceso / resuelto / desestimado)
* Cambiar gravedad (baja / media / alta / crítica)
* Redactar una respuesta oficial
* Enviar notificación al ciudadano

---

## 🛠️ Configuración

Sección accesible desde el menú lateral.

Incluye:

* Datos de la administración (nombre, teléfono, email, dirección)
* Logo visual institucional (subida de imagen)
* Listado de usuarios institucionales
* Filtro por rol y buscador
* Preferencias (no funcionales en esta versión):

  * Confirmar recepción
  * Notificar cambio de estado
  * Incluir logo en emails

---

## 👤 Perfil de usuario

Desde el menú "Mi perfil" el usuario puede:

* Ver sus datos personales (nombre, apellidos, teléfono, email, cargo)
* Cambiar su foto de perfil
* No puede modificar otros datos en esta versión

---

## 🔗 Conexiones con el backend

* GET `/dashboard`
* GET `/reports`
* PUT `/reports/{id}`
* POST `/reports/{id}/notifications`
* GET `/settings`
* POST `/settings/logo`
* GET `/profile`
* PUT `/profile`
* GET `/admin/api/dashboard`
* GET `/admin/api/reports/heatmap`
* GET `/admin/api/reports/positions`
