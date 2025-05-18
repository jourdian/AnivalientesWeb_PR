<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'

import { ref, watch, onMounted, computed, nextTick } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

import { ChevronLeft, ChevronRight, Expand, Minimize } from 'lucide-vue-next'

import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

import Heatmap from '@/components/Heatmap.vue'
import Map from '@/components/Map.vue'

const toastMessage = ref('')
const toastType = ref('success') 
const showToast = ref(false)

const flashMessage = ref('')
const tab = ref('heatmap')

const reportSummary = ref([])
const reportList = ref([])

const isMapExpanded = ref(false)
const mapRef = ref(null)

const newNotification = ref('')
const showNotificationModal = ref(false)

const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)
const baseLat = computed(() => user.value?.administration?.latitude ?? 39.4947639)
const baseLng = computed(() => user.value?.administration?.longitude ?? -0.6857103)

const highlightedDates = computed(() =>
  props.reports.data.map(r => new Date(r.created_at))
)

const calendarDate = ref({
  month: new Date().getMonth(),
  year: new Date().getFullYear()
})

const props = defineProps({
  reports: Object,
  filters: Object,
})

const selectedReport = ref(null)
const responseMessage = ref('')
const status = ref('pending')
const severity = ref('medium')

const search = ref(props.filters.search || '')
const filterStatus = ref(props.filters.status || '')

const mapKey = ref('init')

/**
 * Carga todos los datos de la denuncia cuando se selecciona una de la lista
 */
async function selectReport(report) {
  try {
    const res = await fetch(`/reports/${report.id}`);
    const fullReport = await res.json();

    selectedReport.value = fullReport;
    responseMessage.value = fullReport.response || '';
    status.value = fullReport.status;
    severity.value = fullReport.severity || 'medium';
    mapKey.value = 'selected-' + report.id; 
  } catch (error) {
    console.error('Error cargando detalles del reporte:', error);
  }
}

/**
 * Formateamos fecha
 */
function formatDate(dateStr) {
  const date = new Date(dateStr)
  return date.toLocaleString()
}

//Cargamos datos del Heatmap
/** El heatmap es un componente que se ha diseñado específicamente
 * para este proyecto. Muestra en una especie de calendario los
 * días del mes con un color u otro dependiendo de la cantidad de 
 * denuncias de ese día. De ese modo, de un solo vistazo se puede saber
 * los días más conflictivos e, incluso, de podrían establecer patrones.
 */
onMounted(() => {
  loadHeatmap()
})

/**
 * Centramos el mapa cuando se selecciona una denuncia para
 * mostrar el punto exacto del abandono.
 */
watch(selectedReport, (report) => {
  if (report?.latitude && report?.longitude) {
    nextTick(() => {
      setTimeout(() => {
        mapRef.value?.map?.invalidateSize?.()
        mapRef.value?.panToLocation?.([report.latitude, report.longitude])
      }, 250)
    })
  }
})

/**
 * Mensaje Toast
 */
function showToastMessage(message, type = 'success') {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 4000)
}

/**
 * Envío de notificación al ciudadano
 */

async function sendNotification() {
  if (!selectedReport.value || !newNotification.value.trim()) return

  console.log("Selected report:", selectedReport.value.id);
    console.log("Message:", newNotification.value);

router.post(`api/reports/${selectedReport.value.id}/notifications`, {    message: newNotification.value
  }, {
    preserveScroll: true,
    onSuccess: (page) => {
      const notification = page.props.notification 
      selectedReport.value.notifications = [
        ...(selectedReport.value.notifications || []),
        notification,
      ]
      newNotification.value = ''
      showToastMessage('Notificación enviada correctamente.', 'success')
    },
    onError: () => {
      showToastMessage('Error al enviar la notificación.', 'warning')
    }
  }) 
}


/**
 * Actualizamos la denuncia
 */
function updateReport() {
  if (!selectedReport.value) return;

  router.put(`/reports/${selectedReport.value.id}`, {
    response: responseMessage.value,
    status: status.value,
    severity: severity.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      selectedReport.value.status = status.value;
      selectedReport.value.severity = severity.value;
      selectedReport.value.response = responseMessage.value;
      showToastMessage('Denuncia actualizada correctamente.', 'success');
    },
    onError: () => {
      showToastMessage('Error al actualizar la denuncia.', 'error');
    }
  });
}

/**
 * Etiquetas del desplegable de urgencia(severity)
 */
function getSeverityLabel(severity) {
  return {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    critical: 'Crítica',
  }[severity] || severity
}

/**
 * Mapeamos los colores para las etiquetas de urgencia
 */
function getSeverityClass(severity) {
  return {
    'text-green-600': severity === 'low',
    'text-yellow-600': severity === 'medium',
    'text-orange-600': severity === 'high',
    'text-red-600': severity === 'critical',
  }
}

watch([search, filterStatus], () => {
  router.get('/reports', {
    search: search.value,
    status: filterStatus.value,
  }, { preserveState: true, replace: true })
})

/**
 * Obtenemos etiquetas de estado de denuncia.
 * Suelo desarrollar en inglés y luego adapto
 * a diferentes idiomas si es necesario (i18n)
 * Pero en este caso, siendo un ejercicio académico,
 * he optado por esta solución, más simple.
 */
function getStatusLabel(status) {
  return {
    pending: 'Pendiente',
    resolved: 'Resuelto',
    reviewing: 'En proceso',
    dismissed: 'Desestimado',
  }[status] || status
}

/**
 * Cada estado tiene una clase asociada para que se muestre un color determinado
 */
function getStatusClass(status) {
  return {
    'text-yellow-600': status === 'pending',
    'text-green-600': status === 'resolved',
    'text-blue-600': status === 'dismissed',
  }
}

/**
 * Cargamos datos mensuales
 */
async function loadHeatmap() {
  const month = calendarDate.value.month + 1
  const year = calendarDate.value.year
  try {
    const res = await fetch(`/admin/api/reports/heatmap?month=${month}&year=${year}`, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin'
    })
    reportSummary.value = await res.json()
  } catch (error) {
    console.error('Error cargando heatmap:', error)
  }
}
</script>

<template>
  <!-- Página principal de denuncias con layout administrativo -->
  <AdminLayout title="Denuncias">

    <div class="relative flex flex-col xl:flex-row gap-6">
      <div class="flex-1 space-y-6">

        <!-- FILTROS de búsqueda -->
        <div class="bg-white rounded-2xl p-4 shadow flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <!-- Búsqueda por nombre del denunciante -->
          <input type="text" v-model="search" class="border rounded px-3 py-2 text-sm w-full sm:w-1/3"
            placeholder="Buscar por denunciante..." />
          <!-- Filtro por estado de la denuncia -->
          <select v-model="filterStatus" class="border rounded px-3 py-2 text-sm w-full sm:w-40">
            <option value="">Todas</option>
            <option value="pending">Pendientes</option>
            <option value="reviewing">En proceso</option>
            <option value="resolved">Resueltas</option>
            <option value="dismissed">Desestimadas</option>
          </select>
        </div>

        <!-- SECCIÓN DE ACTIVIDAD: heatmap y mapa -->
        <div class="bg-white rounded-2xl p-4 shadow w-full">
          <h2 class="font-semibold mb-4">📌 Actividad reciente</h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">

            <!-- Heatmap por mes -->
            <div class="space-y-2 col-span-1">
              <label class="text-sm text-gray-600 block mb-1">Mes:</label>
              <Datepicker v-model="calendarDate" :month-picker="true" auto-apply :format="'MMMM yyyy'"
                input-class-name="border px-2 py-1 rounded text-sm w-[140px]"
                @update:model-value="loadHeatmap" />
              <div class="min-h-[100px]">
                <Heatmap :reportSummary="reportSummary" :selectedDate="calendarDate" />
              </div>
            </div>

            <!-- MAPA de denuncias con opción de expandir -->
            <div class="col-span-1 md:col-span-2 relative">
              <div :class="[
                isMapExpanded ? 'h-[600px]' : 'h-[300px]',
                'relative transition-all duration-300 overflow-hidden w-full'
              ]">
                <Map ref="mapRef" :reports="reports.data" :baseLat="baseLat" :baseLng="baseLng" :zoom="9"
                  :selectedReport="selectedReport" :mapKey="mapKey" :expanded="isMapExpanded" class="w-full h-full" />

                <!-- Botón de expansión del mapa -->
                <button @click="isMapExpanded = !isMapExpanded"
                  class="absolute top-2 right-2 bg-white/80 hover:bg-white text-gray-700 p-1 rounded shadow z-10"
                  title="Expandir vertical">
                  <component :is="isMapExpanded ? Minimize : Expand" class="w-4 h-4" />
                </button>
              </div>
            </div>

          </div>
        </div>

        <!-- TABLA de denuncias -->
        <div class="bg-white rounded-2xl shadow overflow-x-auto">
          <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-3 text-left">Fecha</th>
                <th class="p-3 text-left">Título</th>
                <th class="p-3 text-left">Denunciante</th>
                <th class="p-3 text-left">Gravedad</th>
                <th class="p-3 text-left">Estado</th>
              </tr>
            </thead>
            <tbody>
              <!-- Fila por cada denuncia -->
              <tr v-for="r in props.reports.data" :key="r.id" @click="selectReport(r)" :class="[
                'cursor-pointer hover:bg-gray-50 border-t',
                selectedReport?.id === r.id ? 'bg-yellow-50' : ''
              ]">
                <td class="p-3">{{ new Date(r.created_at).toLocaleDateString() }}</td>
                <td class="p-3">{{ r.title }}</td>
                <td class="p-3">{{ r.user?.first_name }} {{ r.user?.last_name }}</td>
                <td class="p-3 font-semibold" :class="getSeverityClass(r.severity)">
                  {{ getSeverityLabel(r.severity) }}
                </td>
                <td class="p-3 font-semibold" :class="getStatusClass(r.status)">
                  {{ getStatusLabel(r.status) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación inferior -->
        <div class="mt-4 text-sm flex flex-wrap gap-1 items-center">
          <Link v-for="link in props.reports.links" :key="link.label" :href="link.url ?? ''"
            class="px-3 py-1 border rounded flex items-center gap-1" :class="{
              'bg-[#003D34] text-white': link.active,
              'text-gray-400 cursor-not-allowed': !link.url,
            }">
            <template v-if="link.label.includes('Previous')">
              <ChevronLeft class="w-4 h-4" />
            </template>
            <template v-else-if="link.label.includes('Next')">
              <ChevronRight class="w-4 h-4" />
            </template>
            <template v-else>
              {{ link.label }}
            </template>
          </Link>
        </div>
      </div>

      <!-- PANEL LATERAL con el detalle de la denuncia seleccionada -->
      <Transition name="slide">
        <div v-if="selectedReport"
          class="bg-white w-full xl:w-[400px] rounded-2xl p-6 shadow-xl absolute xl:static right-0 top-0 h-full xl:h-auto overflow-auto z-10">

          <!-- Cabecera -->
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Detalle de denuncia</h2>
            <button @click="selectedReport = null" class="text-gray-400 hover:text-black text-sm">✕</button>
          </div>

          <!-- Imagen de la denuncia -->
          <img v-if="selectedReport.image_path" :src="'/storage/' + selectedReport.image_path" alt="Foto denuncia"
            class="rounded w-full object-cover mb-3" />

          <!-- Datos personales -->
          <div class="text-sm space-y-1">
            <p><strong>Denunciante:</strong> {{ selectedReport.user?.first_name }} {{ selectedReport.user?.last_name }}</p>
            <p><strong>Teléfono:</strong> {{ selectedReport.user?.phone }}</p>
            <p><strong>Email:</strong> {{ selectedReport.user?.email }}</p>
            <p><strong>Dirección:</strong> {{ selectedReport.address }}</p>
          </div>

          <!-- Descripción -->
          <div class="bg-gray-50 p-3 rounded text-sm text-gray-800 my-3">
            {{ selectedReport.description }}
          </div>

          <!-- Respuesta administrativa -->
          <label class="block text-sm font-semibold mt-3 mb-1">Solución aplicada:</label>
          <textarea v-model="responseMessage"
            placeholder="Resumen de la actuación realizada por la administración"
            class="w-full border rounded p-2 text-sm" rows="3"></textarea>

          <!-- Selectores de estado y gravedad -->
          <div class="flex items-center gap-2 mt-2">
            <select v-model="status" class="border text-sm rounded px-2 py-1">
              <option value="pending">Pendiente</option>
              <option value="reviewing">En proceso</option>
              <option value="resolved">Resuelto</option>
              <option value="dismissed">Desestimado</option>
            </select>
            <select v-model="severity" class="border text-sm rounded px-2 py-1">
              <option value="low">Baja</option>
              <option value="medium">Media</option>
              <option value="high">Alta</option>
              <option value="critical">Crítica</option>
            </select>
            <button @click="updateReport" class="bg-[#003D34] text-white px-4 py-1 rounded text-sm">Actualizar</button>
          </div>

          <!-- Enviar nueva notificación -->
          <div class="mt-4">
            <label class="block text-sm font-semibold mb-1">Enviar notificación:</label>
            <textarea v-model="newNotification" placeholder="Mensaje para el denunciante"
              class="w-full border rounded p-2 text-sm" rows="2"></textarea>
            <div class="flex gap-2 mt-2">
              <button class="bg-[#003D34] text-white px-3 py-1 rounded text-sm" @click="sendNotification">
                Enviar notificación
              </button>

              <!-- Feedback visual + botón para ver historial -->
              <p v-if="flashMessage"
                class="mt-3 text-sm bg-green-100 text-green-800 border border-green-300 rounded p-2">
                {{ flashMessage }}
              </p>
              <button class="border text-sm px-3 py-1 rounded text-gray-700" @click="showNotificationModal = true">
                Ver anteriores
              </button>
            </div>
          </div>

        </div>
      </Transition>
    </div>
  </AdminLayout>

  <!-- MODAL de notificaciones anteriores -->
  <Teleport to="body">
    <div v-if="showNotificationModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-xl w-full max-w-md max-h-[80vh] overflow-auto shadow-xl">
        <div class="flex justify-between items-center mb-3">
          <h2 class="text-lg font-semibold text-black">Notificaciones anteriores</h2>
          <button @click="showNotificationModal = false" class="text-gray-400 hover:text-black text-sm">✕</button>
        </div>
        <div v-if="selectedReport.notifications?.length" class="mt-4">
          <label class="block text-sm font-semibold mb-1">Notificaciones enviadas:</label>
          <ul class="text-sm space-y-1 max-h-40 overflow-y-auto pr-1">
            <li v-for="n in selectedReport.notifications" :key="n.id" class="border-b pb-1">
              <p class="text-gray-700">{{ n.message }}</p>
              <p class="text-xs text-gray-400">{{ new Date(n.created_at).toLocaleString() }}</p>
            </li>
          </ul>
        </div>
        <div v-else class="text-sm text-gray-500">No hay notificaciones aún.</div>
      </div>
    </div>
  </Teleport>

  <!-- TOAST superior derecho -->
  <Teleport to="body">
    <transition name="fade">
      <div
        v-if="showToast"
        class="fixed top-5 right-5 px-4 py-2 rounded shadow-lg z-50 text-white"
        :class="[
          toastType === 'success' && 'bg-green-600',
          toastType === 'error' && 'bg-red-600',
          toastType === 'warning' && 'bg-yellow-500 text-black',
          toastType === 'info' && 'bg-blue-600'
        ]"
      >
        {{ toastMessage }}
      </div>
    </transition>
  </Teleport>
</template>
<style scoped>
/* Transición del panel lateral */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

/* Transición del toast */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
