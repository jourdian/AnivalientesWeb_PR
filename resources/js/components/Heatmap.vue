<template>
    <div class="min-h-[80px]">
      <!-- Cabecera con los días de la semana -->
      <div class="grid grid-cols-7 gap-2 text-xs text-center mb-2 text-gray-500">
        <div>Lu</div>
        <div>Ma</div>
        <div>Mi</div>
        <div>Ju</div>
        <div>Vi</div>
        <div>Sá</div>
        <div>Do</div>
      </div>
  
      <!-- Cuadrícula con los días del mes actual -->
      <div class="grid grid-cols-7 gap-1 sm:gap-1.5 md:gap-2">
        <!-- Recorre todos los días del mes generado dinámicamente -->
        <template v-for="(day, index) in calendarDays" :key="index">
          <div
            :title="`${day.date}: ${day.count} denuncia(s)`"
            class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 rounded transition-colors flex items-center justify-center text-[9px] sm:text-[10px] md:text-xs font-medium text-grey"
            :class="getColor(day.count)"
          >
            <!-- Muestra el número del día en el centro del cuadrado -->
            {{ new Date(day.date).getDate() }}
          </div>
        </template>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  
  /**
   * Props esperadas:
   * - reportSummary: Array con objetos tipo { date: 'YYYY-MM-DD', count: number }
   * - selectedDate: Objeto con el mes y año seleccionado (ej. { month: 4, year: 2025 })
   */
  const props = defineProps({
    reportSummary: Array,
    selectedDate: Object
  })
  
  /**
   * Computed que genera una lista de días del mes actual con su número de denuncias.
   * Devuelve un array de objetos con:
   * - date (YYYY-MM-DD)
   * - count (número de denuncias ese día)
   */
  const calendarDays = computed(() => {
    const list = []
    const year = props.selectedDate.year
    const month = props.selectedDate.month 
  
    const daysInMonth = new Date(year, month + 1, 0).getDate()
  
    for (let i = 1; i <= daysInMonth; i++) {
      const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`
      const count = props.reportSummary.find(d => d.date === dateStr)?.count || 0
      list.push({ date: dateStr, count })
    }
  
    return list
  })
  
  /**
   * Devuelve una clase de color según el número de denuncias:
   * - 0: gris claro
   * - 1-2: amarillo claro
   * - 3-5: naranja
   * - 6-10: rojo
   * - 11-20: amarillo oscuro
   * - >20: amarillo intenso
   */
  function getColor(count) {
    if (count === 0) return 'bg-gray-200'
    if (count <= 2) return 'bg-yellow-200'
    if (count <= 5) return 'bg-orange-300'
    if (count <= 10) return 'bg-red-400'
    if (count <= 20) return 'bg-yellow-500'
    return 'bg-yellow-600'
  }
  </script>
  