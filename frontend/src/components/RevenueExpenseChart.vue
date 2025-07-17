<script setup lang="ts">
import { ref, computed } from "vue";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";
import { Bar } from "vue-chartjs";

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

const props = defineProps<{
  revenues: { amount: number; date: string }[];
  expenses: { amount: number; date: string }[];
}>();

function formatMonth(dateStr: string): string {
  const d = new Date(dateStr);
  return d.toLocaleString("default", { month: "short", year: "numeric" }); // e.g. "Jan 2025"
}

function groupByMonth(entries: { amount: number; date: string }[]) {
  return entries.reduce((acc, entry) => {
    const month = formatMonth(entry.date);
    acc[month] = (acc[month] || 0) + entry.amount;
    return acc;
  }, {} as Record<string, number>);
}

const revenueByMonth = computed(() => groupByMonth(props.revenues));
const expenseByMonth = computed(() => groupByMonth(props.expenses));

const allMonths = computed(() => {
  const uniqueMonths = new Set([
    ...Object.keys(revenueByMonth.value),
    ...Object.keys(expenseByMonth.value),
  ]);
  return Array.from(uniqueMonths).sort(
    (a, b) => new Date(a).getTime() - new Date(b).getTime()
  );
});

const chartData = computed(() => ({
  labels: allMonths.value,
  datasets: [
    {
      label: "Revenue",
      backgroundColor: "#10b981",
      data: allMonths.value.map((m) => revenueByMonth.value[m] || 0),
    },
    {
      label: "Expenses",
      backgroundColor: "#ef4444",
      data: allMonths.value.map((m) => expenseByMonth.value[m] || 0),
    },
  ],
}));

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: "bottom" as const,
    },
    title: {
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        callback: function (value: any) {
          return "$" + value.toLocaleString();
        },
      },
    },
  },
});
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
