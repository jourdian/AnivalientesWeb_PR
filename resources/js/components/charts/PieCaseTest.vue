<script setup>
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const chartData = {
  labels: ['reviewing', 'dismissed', 'pending', 'resolved'],
  datasets: [
    {
      label: 'Casos',
      backgroundColor: ['#F87171', '#FBBF24', '#60A5FA', '#34D399'],
      data: [13, 9, 12, 10]
    }
  ]
}

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'bottom' }
  }
}
</script>

<template>
  <div style="width: 100%; height: 250px;">
    <Pie :data="chartData" :options="chartOptions" />
  </div>
</template>
