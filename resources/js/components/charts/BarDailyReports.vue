<script setup>
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

import { watchEffect, reactive, ref } from 'vue'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  }
})

// Datos del gráfico
const chartData = reactive({
  labels: [],
  datasets: [
    {
      label: 'Denuncias',
      backgroundColor: '#3B82F6',
      data: []
    }
  ]
})

// Clave para forzar render completo
const chartKey = ref(0)

watchEffect(() => {
  chartData.labels = props.data.map(d => d.name)
  chartData.datasets[0].data = props.data.map(d => d.reports)
  chartKey.value++
})
</script>

<template>
  <div style="width: 100%; height: 250px;">
    <Bar
      :data="chartData"
      :options="{
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => `${ctx.raw} denuncias`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }"
      :key="chartKey"
    />
  </div>
</template>
