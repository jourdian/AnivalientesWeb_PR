<script setup>
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'

import { watchEffect, reactive, ref } from 'vue'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  data: {
    type: Object,
    default: () => ({})
  }
})

const chartData = reactive({
  labels: [],
  datasets: [
    {
      label: 'Casos',
      backgroundColor: ['#F87171', '#FBBF24', '#60A5FA', '#34D399'],
      data: []
    }
  ]
})

// Nueva clave para forzar render del componente
const chartKey = ref(0)

watchEffect(() => {
  const entries = Object.entries(props.data ?? {})
  const statusLabels = {
  pending: 'Pendiente',
  resolved: 'Resuelta',
  reviewing: 'En proceso',
  dismissed: 'Desestimada'
}
chartData.labels = entries.map(([key]) => statusLabels[key] ?? key)
  chartData.datasets[0].data = entries.map(([_, val]) => val)
  chartKey.value++ 
})
</script>

<template>
    <div class="flex justify-center items-center w-full h-full">
      <Pie
        :data="chartData"
        :options="{
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              align: 'center',
              labels: {
                boxWidth: 20,
                padding: 15
              }
            }
          }
        }"
        :key="chartKey"
      />
    </div>
  </template>
  
