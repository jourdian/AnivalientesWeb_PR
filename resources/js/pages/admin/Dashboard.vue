<script setup>
// Importación de funciones reactivas y del ciclo de vida desde Vue
import { ref, onMounted, watch, computed } from 'vue'

// Importación del layout principal para la vista administrativa
import AdminLayout from '@/layouts/AdminLayout.vue'

// Importación de componentes de gráficas personalizados
import PieCaseDistribution from '@/components/charts/PieCaseDistribution.vue'
import BarDailyReports from '@/components/charts/BarDailyReports.vue'
import PieCaseTest from '@/components/charts/PieCaseTest.vue' // (Posiblemente en pruebas)
import BarChartTest from '@/components/charts/BarChartTest.vue' // (Posiblemente en pruebas)
import { Pie } from 'vue-chartjs'

// Rango de fechas seleccionado ('week' o 'month')
const range = ref('week')

// Datos reactivos para la distribución por estado y los reportes por día
const statusDistribution = ref({})
const reportsByDay = ref([])

// Cálculo del total de denuncias en el rango actual
const totalReports = computed(() =>
  reportsByDay.value.reduce((acc, r) => acc + r.reports, 0)
)

// Cálculo del total de denuncias en la "semana pasada" (primeros 7 días)
const totalLastWeek = computed(() => {
  // Simulamos como si los primeros 7 días fueran la semana anterior
  return reportsByDay.value
    .slice(0, 7)
    .reduce((acc, r) => acc + r.reports, 0)
})

// Porcentaje de incremento de denuncias con respecto a la semana pasada
const incrementPercent = computed(() => {
  const last = totalLastWeek.value || 1 
  return ((totalReports.value - last) / last * 100).toFixed(1)
})

// Número total de denuncias resueltas
const resolved = computed(() => statusDistribution.value.resolved ?? 0)

// Total de denuncias por estado (resueltas + pendientes)
const totalStatus = computed(() =>
  Object.values(statusDistribution.value).reduce((a, b) => a + b, 0)
)

// Denuncias que ya han sido gestionadas (no están pendientes)
const attended = computed(() =>
  totalStatus.value - (statusDistribution.value.pending ?? 0)
)

// Porcentaje de denuncias gestionadas (tasa de atención)
const attentionRate = computed(() =>
  totalStatus.value > 0
    ? ((attended.value / totalStatus.value) * 100).toFixed(1)
    : '0.0'
)

// Porcentaje de denuncias resueltas (ratio de confianza)
const confidenceRate = computed(() =>
  totalStatus.value > 0
    ? ((resolved.value / totalStatus.value) * 100).toFixed(1)
    : '0.0'
)

/**
 * Carga los datos estadísticos del dashboard desde la API.
 * Llama a `/admin/api/dashboard` pasando el rango como parámetro.
 */
async function loadDashboardData() {
  try {
    const res = await fetch(`/admin/api/dashboard?range=${range.value}`, {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin'
    })
    const data = await res.json()

    // Logs para depuración
    console.log('Distribución de estados:', data.statusDistribution)
    console.log('Denuncias por día:', data.reportsByDay)

    // Actualización de datos reactivos
    statusDistribution.value = data.statusDistribution
    reportsByDay.value = data.reportsByDay
  } catch (error) {
    console.error('Error al cargar métricas:', error)
  }
}

// Ejecutar la carga inicial al montar el componente
onMounted(loadDashboardData)

// Volver a cargar datos cuando cambia el rango seleccionado
watch(range, loadDashboardData)
</script>

<template>
  <AdminLayout title="Dashboard">
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <!-- MÉTRICAS: Cuatro tarjetas con indicadores clave -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

      <!-- Tarjeta 1: Total de denuncias -->
      <div class="bg-white rounded-2xl p-4 shadow">
        <div class="flex items-center justify-between">
          <h2 class="text-sm text-gray-500 flex items-center gap-1">
            Denuncias totales
            <span
              class="text-gray-400 cursor-help"
              title="Cantidad total de denuncias registradas en el rango actual."
            >
              ℹ️
            </span>
          </h2>
        </div>
        <p class="text-2xl font-semibold">{{ totalReports }}</p>
        <p class="text-xs text-green-600 mt-1">▲ {{ incrementPercent }}% desde la semana pasada</p>
      </div>

      <!-- Tarjeta 2: Incremento semanal -->
      <div class="bg-white rounded-2xl p-4 shadow">
        <div class="flex items-center justify-between">
          <h2 class="text-sm text-gray-500 flex items-center gap-1">
            Incremento semanal
            <span
              class="text-gray-400 cursor-help"
              title="Variación porcentual de denuncias respecto a la semana anterior."
            >
              ℹ️
            </span>
          </h2>
        </div>
        <p class="text-2xl font-semibold">{{ incrementPercent }}%</p>
        <p class="text-xs text-green-600 mt-1">▲ con respecto a la semana pasada</p>
      </div>

      <!-- Tarjeta 3: Ratio de confianza -->
      <div class="bg-white rounded-2xl p-4 shadow">
        <div class="flex items-center justify-between">
          <h2 class="text-sm text-gray-500 flex items-center gap-1">
            Ratio de confianza
            <span
              class="text-gray-400 cursor-help"
              title="Porcentaje de denuncias que han sido resueltas."
            >
              ℹ️
            </span>
          </h2>
        </div>
        <p class="text-2xl font-semibold">{{ confidenceRate }}%</p>
        <p class="text-xs text-green-600 mt-1">✔ % de denuncias resueltas</p>
      </div>

      <!-- Tarjeta 4: Tasa de atención -->
      <div class="bg-white rounded-2xl p-4 shadow">
        <div class="flex items-center justify-between">
          <h2 class="text-sm text-gray-500 flex items-center gap-1">
            Tasa de atención
            <span
              class="text-gray-400 cursor-help"
              title="Porcentaje de denuncias que ya no están pendientes."
            >
              ℹ️
            </span>
          </h2>
        </div>
        <p class="text-2xl font-semibold">{{ attentionRate }}%</p>
        <p class="text-xs text-green-600 mt-1">✔ % de denuncias gestionadas</p>
      </div>
    </div>

    <!-- GRÁFICAS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Gráfico circular de distribución por estado -->
      <div class="bg-white rounded-2xl p-6 shadow">
        <h2 class="text-lg font-semibold mb-4">Distribución total de denuncias</h2>
        <div style="position: relative; width: 100%; height: 250px;">
          <PieCaseDistribution :data="statusDistribution" />
        </div>
      </div>

      <!-- Gráfico de barras por día -->
      <div class="bg-white rounded-2xl p-6 shadow">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold">Denuncias diarias</h2>
          <!-- Selector de rango de tiempo -->
          <select v-model="range" class="border text-sm rounded px-2 py-1">
            <option value="week">Semana</option>
            <option value="month">Mes</option>
          </select>
        </div>
        <div style="position: relative; width: 100%; height: 250px;">
          <BarDailyReports :data="reportsByDay" />
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
