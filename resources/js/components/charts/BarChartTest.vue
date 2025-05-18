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

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const data = {
  labels: ['Lu', 'Ma', 'Mi', 'Ju', 'Vi'],
  datasets: [
    {
      label: 'Denuncias',
      backgroundColor: '#3B82F6',
      data: [1, 3, 2, 4, 1]
    }
  ]
}

const options = {
  responsive: true,
  plugins: {
    legend: { display: false }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { stepSize: 1 }
    }
  }
}
</script>

<template>
  <div style="width: 100%; height: 250px;">
    <Bar :data="data" :options="options" />
  </div>
</template>
