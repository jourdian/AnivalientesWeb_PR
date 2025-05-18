# Convenciones y buenas prácticas del proyecto AniValientes

Este documento recoge las normas de estilo, nomenclatura, estructura de código y decisiones de diseño utilizadas en el desarrollo de AniValientes. Su propósito es facilitar la mantenibilidad y coherencia del proyecto, especialmente en contextos colaborativos o ampliaciones futuras.

---

## ✏️ Idioma y estilo del código

* **Idioma del código**: todo el código (nombres de variables, funciones, clases, rutas...) está en **inglés**.
* **Comentarios**: redactados en **español**, pensados para el evaluador y futuros desarrolladores de habla hispana.
* **Estilo**:

  * camelCase para variables y funciones: `userEmail`, `loadReport`
  * PascalCase para clases: `ReportController`
  * snake\_case para campos de base de datos: `created_at`, `photo_path`

---

## ⚖️ Estructura de carpetas recomendada

* `Controllers/Admin`: para el panel web institucional
* `Controllers/Api`: para la API REST de la app
* `Requests/`: validaciones con FormRequest
* `Models/`: modelos Eloquent
* `resources/js/Pages`: cada vista principal (Dashboard, Reports, etc.)
* `resources/js/Components`: elementos reutilizables (Map, Chart...)

---

## 🔄 Patrón general de desarrollo

### Laravel

* Modelo-Vista-Controlador
* Middleware para roles (`EnsureInstitutionalUser`)
* Validaciones en clases Request (no en el controlador)
* Eloquent ORM para toda interacción con la base de datos

### Vue 3

* Composition API (`<script setup>`, `ref`, `computed`...)
* Un único layout base (`AdminLayout.vue`)
* Componentes específicos divididos por contexto

---

## 📝 Documentación

* API REST documentada con [Scribe](https://scribe.knuckles.wtf)
* Todos los endpoints REST llevan anotaciones `@group`, `@authenticated`, `@bodyParam`, `@response`, etc.
* La documentación técnica interna está en la carpeta `docs/` y cubre arquitectura, flujos, base de datos, backend, frontend, notificaciones y despliegue.

---

## ⚖️ Estilo en rutas y endpoints

* Prefijo `/api` para rutas móviles (REST): `/api/login`, `/api/reports`
* Prefijo `/dashboard`, `/reports`, `/settings` para rutas web
* Verbos correctos:

  * `GET /reports` → listar
  * `POST /reports` → crear
  * `PUT /reports/{id}` → actualizar

---

## 🌐 Vistas

* Todas las vistas están contenidas en componentes Vue dentro de `Pages/`
* Las props que provienen de Inertia se obtienen con `usePage()` y se manejan con `ref` o `computed`
* Se usa TailwindCSS para todo el estilo visual

---

## 🚫 Antipatrones evitados

* ❌ Lógica de negocio directamente en el controlador
* ❌ Validaciones manuales dentro del método
* ❌ Código duplicado en múltiples vistas
* ❌ Comentarios innecesarios o sin actualizar

---

## 🚀 Posibles mejoras futuras

* Centralización de estilos comunes (botones, tarjetas, modales)
* Uso de store global (ej. Pinia) si el estado crece
* Generación automática de documentación desde anotaciones de código
* Creación de test unitarios/funcionales

---

## ✉️ Notas finales

Estas convenciones deben mantenerse en futuras ampliaciones del proyecto. Si se incorpora un nuevo miembro al desarrollo, este documento sirve como guía de estilo base.
