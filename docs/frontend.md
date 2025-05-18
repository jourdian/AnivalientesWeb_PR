# Estructura del frontend web (Vue 3 + Inertia)

Este documento explica la estructura técnica del frontend web de AniValientes, desarrollado con Vue 3, Inertia.js y TailwindCSS. Forma parte del panel institucional y está estrechamente integrado con Laravel a través de Inertia.

---

## 🚀 Stack tecnológico

* **Framework**: Vue 3 (Composition API)
* **Integración con backend**: Inertia.js
* **Estilos**: TailwindCSS
* **Gráficas**: vue-chartjs + Chart.js
* **Enrutamiento**: gestionado desde Laravel

---

## 📂 Estructura de carpetas relevantes

```
resources/
├── js/
│   ├── Pages/              # Componentes de cada vista (Dashboard, Reports...)
│   ├── Components/         # Componentes reutilizables (gráficas, mapas...)
│   ├── Layouts/            # Layouts base (AdminLayout.vue)
│   └── app.js              # Punto de entrada principal
```

---

## 🔄 Flujo de una página típica

1. Laravel devuelve una vista Inertia con datos (`Inertia::render(...)`)
2. Vue monta el componente correspondiente (`Pages/...`)
3. Se muestra dentro del `AdminLayout.vue` con los datos recibidos como props
4. El componente puede tener estado local y lógica reactiva
5. Los cambios se sincronizan mediante `@inertiajs/vue3`

---

## 📗 Páginas principales

### `Dashboard.vue`

* Métricas (totales, porcentajes)
* Gráficas con vue-chartjs

### `Reports.vue`

* Lista de denuncias con filtros
* Detalle de denuncia en panel lateral
* Mapa y heatmap interactivo
* Envío de notificaciones desde la vista

### `Settings.vue`

* Información institucional
* Subida de logotipo
* Listado de usuarios institucionales con filtros

### `Profile.vue`

* Datos del usuario logueado (no editables)
* Foto de perfil

---

## 🔹 Componentes destacados

* `Map.vue`: mapa Leaflet con marcadores dinámicos
* `Heatmap.vue`: mapa de calor mensual
* `PieCaseDistribution.vue`: gráfica de distribución por estado
* `BarDailyReports.vue`: gráfica de barras por día

---

## 🚧 Integración con datos

Se usan los props de Inertia para inyectar datos en las páginas:

```js
const page = usePage()
const reports = computed(() => page.props.reports)
```

Los cambios (PUT, POST, etc.) se hacen con `router.put`, `router.post`, etc. usando Inertia.

Ejemplo:

```js
router.put(`/reports/${report.id}`, {
  status: 'resolved',
  response: 'Actuación realizada'
})
```

---

## 🛠️ Recomendaciones para ampliar

* Mantener la lógica de estado con `ref` y `computed`
* Separar componentes si una vista crece demasiado
* Reutilizar layouts base para mantener coherencia visual
* Comentar props y estructuras reactivas complejas
* Usar `watch` y `nextTick` cuando sea necesario sincronizar render
* Centralizar estilos con Tailwind y clases reutilizables

---

## 🏠 Layout base: `AdminLayout.vue`

* Contenedor principal de todas las páginas
* Contiene el encabezado, menú lateral y área de contenido
* Permite títulos dinámicos y slots para contenido interno
