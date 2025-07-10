<script setup lang="ts">
import { ref, onMounted } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
)

const chartData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [
    {
      label: 'Revenue',
      backgroundColor: '#10b981',
      data: [12000, 15000, 18000, 22000, 25000, 28000]
    },
    {
      label: 'Expenses',
      backgroundColor: '#ef4444',
      data: [8000, 9500, 11000, 13500, 16000, 18000]
    }
  ]
})

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom' as const,
    },
    title: {
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        callback: function(value: any) {
          return '$' + value.toLocaleString()
        }
      }
    }
  },
})
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Monthly Overview</h3>
      <p class="text-sm text-gray-600">Revenue vs Expenses comparison</p>
    </div>
    <div class="h-80">
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>