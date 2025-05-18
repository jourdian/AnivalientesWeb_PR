<script setup>
import { onMounted, ref, watch, nextTick } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

// Props del componente
const props = defineProps({
  reports: Array,              // Array de denuncias con coordenadas
  baseLat: Number,             // Latitud por defecto del centro del mapa
  baseLng: Number,             // Longitud por defecto del centro del mapa
  zoom: { type: Number, default: 9 }, // Nivel de zoom inicial
  selectedReport: Object,      // Denuncia seleccionada (para resaltar su marcador)
  expanded: Boolean,           // Modo expandido o compacto (afecta pan)
  mapKey: String               // Clave reactiva para forzar reinicio del mapa
})

const map = ref(null)          // Referencia al mapa Leaflet
let markers = []               // Lista de marcadores actuales

// Inicializa el mapa al montar el componente
function initMap() {
  map.value = L.map('map').setView(
    props.selectedReport?.latitude && props.selectedReport?.longitude
      ? [props.selectedReport.latitude, props.selectedReport.longitude]
      : [props.baseLat, props.baseLng],
    props.zoom
  )

  // Capa de tiles 
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map.value)

  addMarkers()
}

// Ejecutar al montar el componente
onMounted(() => initMap())

// Si cambian las denuncias, se actualizan los marcadores
watch(() => props.reports, () => {
  if (map.value) {
    clearMarkers()
    addMarkers()
  }
})

// Si cambia mapKey, se reinicia el mapa (usado tras seleccionar reporte)
watch(() => props.mapKey, () => {
  if (map.value) map.value.remove()
  initMap()
})

// Añade marcadores al mapa
function addMarkers() {
  const defaultIcon = L.icon({
    iconUrl: '/images/Isotipo_Anivalientes_transparente.png',
    iconSize: [16, 16],
    iconAnchor: [8, 16],
    popupAnchor: [0, -16]
  })

  const selectedIcon = L.icon({
    iconUrl: '/images/Isotipo_rojo.png',
    iconSize: [20, 20],
    iconAnchor: [10, 20],
    popupAnchor: [0, -20]
  })

  markers = props.reports
    .filter(r => r.latitude && r.longitude)
    .map(r => {
      const icon = props.selectedReport?.id === r.id ? selectedIcon : defaultIcon
      return L.marker([r.latitude, r.longitude], { icon })
        .addTo(map.value)
        .bindPopup(r.description || 'Sin descripción')
    })
}

// Elimina todos los marcadores del mapa
function clearMarkers() {
  markers.forEach(m => m.remove())
  markers = []
}

// Centra el mapa en una localización concreta y ajusta el desplazamiento si no está expandido
async function panToLocation([lat, lng]) {
  if (!map.value) return

  await nextTick()
  map.value.invalidateSize()

  const latLng = L.latLng(lat, lng)
  map.value.setView(latLng, props.zoom || 9, { animate: true })

  if (!props.expanded) {
    // Desplaza el mapa hacia arriba y un poco a la izquierda si no está expandido
    setTimeout(() => {
      const offsetY = map.value.getSize().y * 0.25
      const offsetX = map.value.getSize().x * 0.01
      map.value.panBy([-offsetX, +offsetY], { animate: true })
    }, 400)
  }
}

// Expone funciones para el componente padre
defineExpose({
  map,
  panToLocation
})
</script>

<template>
  <!-- Contenedor del mapa -->
  <div id="map" class="w-full h-full rounded z-0"></div>
</template>

<style scoped>
#map {
  width: 100%;
  height: 100%;
  min-height: 650px; 
}
</style>
