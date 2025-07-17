<script lang="ts">
// ✅ Module-level cache
let revenuesCache: any[] | null = null;
let expensesCache: any[] | null = null;
</script>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import RevenueExpenseChart from "../components/RevenueExpenseChart.vue";
import {
  ChartBarIcon,
  ArrowTrendingUpIcon,
  CurrencyDollarIcon,
  CalculatorIcon,
} from "@heroicons/vue/24/outline";

import axios from "../axios";

const revenues = ref<any[]>([]);
const expenses = ref<any[]>([]);

const allRevenues = ref<any[]>([]);
const allExpenses = ref<any[]>([]);

const isLoading = ref(true);

const fetchAnalyticsData = async () => {
  isLoading.value = true;

  if (revenuesCache && expensesCache) {
    allRevenues.value = revenuesCache;
    allExpenses.value = expensesCache;

    revenues.value = [...revenuesCache].slice(0, 5);
    expenses.value = [...expensesCache].slice(0, 5);
  } else {
    try {
      const [revRes, expRes] = await Promise.all([
        axios.get("/api/revenue"),
        axios.get("/api/expenses"),
      ]);

      const sortedRevenues = [...revRes.data].sort(
        (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
      );

      const sortedExpenses = [...expRes.data].sort(
        (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
      );

      allRevenues.value = sortedRevenues;
      allExpenses.value = sortedExpenses;

      revenues.value = sortedRevenues.slice(0, 5);
      expenses.value = sortedExpenses.slice(0, 5);

      revenuesCache = sortedRevenues;
      expensesCache = sortedExpenses;
    } catch (err) {
      console.error("Failed to load analytics data:", err);
    }
  }

  isLoading.value = false;
};

const getCategoryColorRevenue = (category: string) => {
  const colors = {
    "Product Sales": "category-sales",
    Services: "category-services",
    Consulting: "category-consulting",
    Licensing: "category-licensing",
    Training: "category-training",
    Other: "category-other",
  };
  return colors[category] || "category-default";
};

const getCategoryColorExpenses = (category: string) => {
  const colors = {
    Rent: "category-rent",
    Marketing: "category-marketing",
    Software: "category-software",
    Supplies: "category-supplies",
    Services: "category-services",
  };
  return colors[category] || "category-default";
};

// ---------- Stats Calculations ----------

const totalRevenue = computed(() =>
  allRevenues.value.reduce((sum, r) => sum + r.amount, 0)
);

const totalExpenses = computed(() =>
  allExpenses.value.reduce((sum, e) => sum + e.amount, 0)
);

const profitMargin = computed(() => {
  if (totalRevenue.value === 0) return 0;
  const profit = totalRevenue.value - totalExpenses.value;
  return ((profit / totalRevenue.value) * 100).toFixed(1);
});

const roi = computed(() => {
  if (totalExpenses.value === 0) return 0;
  const profit = totalRevenue.value - totalExpenses.value;
  return ((profit / totalExpenses.value) * 100).toFixed(1);
});

const netProfit = computed(() => {
  return totalRevenue.value - totalExpenses.value;
});

function getMonthKey(dateStr: string): string {
  const d = new Date(dateStr);
  return `${d.getFullYear()}-${d.getMonth()}`;
}

const revenueByMonth = computed(() => {
  const map = new Map<string, number>();
  for (const r of allRevenues.value) {
    const key = getMonthKey(r.date);
    map.set(key, (map.get(key) || 0) + r.amount);
  }
  return map;
});

const growthRate = computed(() => {
  const months = [...revenueByMonth.value.keys()].sort();
  const len = months.length;
  if (len < 2) return 0;

  const current = revenueByMonth.value.get(months[len - 1]) || 0;
  const previous = revenueByMonth.value.get(months[len - 2]) || 0;
  if (previous === 0) return 100;
  return (((current - previous) / previous) * 100).toFixed(1);
});

onMounted(fetchAnalyticsData);
</script>

<template>
  <div class="analytics-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Analytics</h1>
        <p>Deep insights into your business performance</p>
      </div>
    </div>

    <!-- Key Metrics Stats -->
    <div class="stats-grid" v-if="isLoading">
      <div v-for="n in 4" :key="n" class="stat-card skeleton-card">
        <div class="stat-icon skeleton"></div>
        <div class="stat-content">
          <span class="stat-label skeleton-text"></span>
          <span class="stat-value skeleton-text"></span>
        </div>
      </div>
    </div>

    <div class="stats-grid" v-else>
      <div class="stat-card">
        <div class="stat-icon green">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Profit Margin</span>
          <span class="stat-value">{{ profitMargin }}%</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon blue">
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Growth Rate</span>
          <span class="stat-value">{{ growthRate }}%</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple">
          <CalculatorIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Net Profit</span>
          <span
            class="stat-value"
            :class="{
              'text-green-600': netProfit >= 0,
              'text-red-600': netProfit < 0,
            }"
          >
            ${{ netProfit.toLocaleString() }}
          </span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon orange">
          <ChartBarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">ROI</span>
          <span class="stat-value">{{ roi }}%</span>
        </div>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
      <RevenueExpenseChart :revenues="allRevenues" :expenses="allExpenses" />
    </div>

    <!-- Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Latest Revenues -->
      <div class="categories-section">
        <div class="section-header">
          <h3>Latest Revenue</h3>
          <span class="categories-count">{{ revenues.length }} entries</span>
        </div>
        <div v-if="isLoading" class="categories-list">
          <div
            v-for="n in 5"
            :key="'rev-skel-' + n"
            class="revenue-item skeleton-card"
          >
            <div class="skeleton-line" style="width: 70%; height: 16px"></div>
            <div class="skeleton-line" style="width: 40%; height: 14px"></div>
          </div>
        </div>

        <div v-else class="categories-list">
          <div v-for="r in revenues" :key="r.id" class="revenue-item">
            <div class="revenue-main">
              <div class="revenue-info">
                <h4 class="revenue-name">{{ r.description }}</h4>
                <span
                  :class="[
                    'revenue-category',
                    getCategoryColorRevenue(r.category),
                  ]"
                >
                  {{ r.category }}
                </span>
              </div>
              <div class="revenue-details">
                <span class="revenue-client">{{ r.customer || "N/A" }}</span>
                <span class="revenue-date">{{
                  new Date(r.date).toLocaleDateString()
                }}</span>
              </div>
            </div>

            <div class="revenue-metrics">
              <div class="metric">
                <span class="metric-label">Amount</span>
                <span class="metric-value total">
                  ${{ r.amount.toLocaleString() }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Latest Expenses -->
      <div class="trends-section">
        <div class="section-header">
          <h3>Latest Expenses</h3>
          <span class="trends-count">{{ expenses.length }} entries</span>
        </div>
        <div v-if="isLoading" class="trends-list">
          <div
            v-for="n in 5"
            :key="'exp-skel-' + n"
            class="expense-item skeleton-card"
          >
            <div class="skeleton-line" style="width: 70%; height: 16px"></div>
            <div class="skeleton-line" style="width: 40%; height: 14px"></div>
          </div>
        </div>

        <div v-else class="trends-list">
          <div v-for="e in expenses" :key="e.id" class="expense-item">
            <div class="expense-main">
              <div class="expense-info">
                <h4 class="expense-name">{{ e.description }}</h4>
                <span
                  :class="[
                    'expense-category',
                    getCategoryColorExpenses(e.category),
                  ]"
                >
                  {{ e.category }}
                </span>
                <span v-if="e.recurring" class="recurring-badge"
                  >Recurring</span
                >
              </div>
              <div class="expense-details">
                <span class="expense-vendor">{{ e.vendor || "N/A" }}</span>
                <span class="expense-date">{{
                  new Date(e.date).toLocaleDateString()
                }}</span>
              </div>
            </div>

            <div class="expense-metrics">
              <div class="metric">
                <span class="metric-label">Amount</span>
                <span class="metric-value expense-amount">
                  ${{ e.amount.toLocaleString() }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.skeleton-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  margin-bottom: 1rem;
  animation: pulse 1.5s infinite;
}
.skeleton-thumbnail {
  width: 60px;
  height: 60px;
  background: #e5e7eb;
  border-radius: 8px;
}
.skeleton-lines {
  flex: 1;
}
.skeleton-line {
  height: 14px;
  background: #e5e7eb;
  margin-bottom: 8px;
  border-radius: 4px;
}
.skeleton-line.short {
  width: 40%;
}
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.4;
  }
}
</style>
