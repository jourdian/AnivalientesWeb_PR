# Sistema de notificaciones en AniValientes

Este documento explica cómo funciona el sistema de notificaciones en la plataforma AniValientes. Está pensado para facilitar la comunicación entre las administraciones locales y los ciudadanos que reportan casos de abandono animal.

---

## 💬 Objetivo del sistema

Permitir que las administraciones respondan a los ciudadanos tras revisar una denuncia, enviando un mensaje asociado a esa denuncia. Estas notificaciones se almacenan y pueden consultarse desde la app móvil o el panel web.

---

## 👤 Actores implicados

* **Usuario institucional** (panel web): genera y envía la notificación
* **Usuario ciudadano** (app): recibe y consulta el mensaje

---

## 📂 Estructura técnica

### Tabla: `report_notifications`

| Campo       | Tipo      | Descripción                                  |
| ----------- | --------- | -------------------------------------------- |
| id          | bigint    | Clave primaria                               |
| report\_id  | bigint    | FK a la denuncia correspondiente (`reports`) |
| user\_id    | bigint    | FK al usuario institucional que responde     |
| message     | text      | Texto de la notificación                     |
| created\_at | timestamp | Fecha y hora del envío                       |

---

## ⚙️ Flujo de envío desde la web

1. El personal institucional abre el panel lateral de una denuncia
2. Escribe un mensaje en el campo "Enviar notificación"
3. Pulsa el botón "Enviar"
4. Se lanza una petición `POST` a:

   * `/reports/{report}/notifications`
5. Laravel valida y guarda el mensaje en `report_notifications`
6. Se recarga el panel lateral para mostrar la notificación

---

## 📢 Recepción desde la app

1. Al cargar una denuncia, la app consulta si hay notificaciones asociadas
2. Las muestra en orden cronológico dentro de la vista de detalle
3. (Opcional) En futuras versiones se puede añadir notificación push

---

## 🔧 Endpoint REST implicado

```http
POST /api/reports/{id}/notifications
```

### Cuerpo esperado:

```json
{
  "message": "La patrulla ya ha pasado por la zona y ha recogido al animal."
}
```

### Respuesta esperada:

```json
{
  "success": true,
  "notification": {
    "id": 3,
    "message": "La patrulla ya ha pasado por la zona y ha recogido al animal.",
    "created_at": "2025-05-10T12:34:00Z"
  }
}
```

---

## 🛠️ Consideraciones técnicas

* Las notificaciones están ligadas a una única denuncia
* El usuario que las envía se registra automáticamente (usuario logueado)
* Se puede reutilizar la misma vista para mostrar notificaciones pasadas
* Para evitar duplicidades, solo se permite enviar una notificación por vez

---

## ✉️ Posibles mejoras futuras

* Integración con Firebase Cloud Messaging para notificaciones push
* Posibilidad de que el ciudadano pueda responder o reaccionar
* Clasificación por tipo (informativa, urgente, solicitud de datos)
* Historial completo con fecha, autor y contenido
* Panel de gestión de mensajes independiente
