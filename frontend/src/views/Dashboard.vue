<script lang="ts">
// ✅ Cache outside component scope
let revenueCache: any[] | null = null;
let expensesCache: any[] | null = null;
let productCache: any[] | null = null;
</script>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "../axios";
import RevenueExpenseChart from "../components/RevenueExpenseChart.vue";
import {
  CurrencyDollarIcon,
  ArrowTrendingDownIcon,
  ArrowTrendingUpIcon,
  CubeIcon,
} from "@heroicons/vue/24/outline";

const isLoading = ref(true);
const revenue = ref<any[]>([]);
const expenses = ref<any[]>([]);
const products = ref<any[]>([]);

const token = localStorage.getItem("token");

// Fetch revenue, expenses, products
onMounted(async () => {
  isLoading.value = true;
  try {
    if (!revenueCache) {
      const revRes = await axios.get("/api/revenue", {
        headers: { Authorization: `Bearer ${token}` },
      });
      revenue.value = revRes.data;
      revenueCache = revRes.data;
    } else {
      revenue.value = revenueCache;
    }

    if (!expensesCache) {
      const expRes = await axios.get("/api/expenses", {
        headers: { Authorization: `Bearer ${token}` },
      });
      expenses.value = expRes.data;
      expensesCache = expRes.data;
    } else {
      expenses.value = expensesCache;
    }

    if (!productCache) {
      const prodRes = await axios.get("/api/products", {
        headers: { Authorization: `Bearer ${token}` },
      });
      products.value = prodRes.data;
      productCache = prodRes.data;
    } else {
      products.value = productCache;
    }
  } catch (err) {
    console.error("Failed to fetch dashboard data:", err);
  } finally {
    isLoading.value = false;
  }
});

// Monthly metrics
const now = new Date();
const isThisMonth = (dateStr: string) => {
  const d = new Date(dateStr);
  return (
    d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
  );
};

const monthlyRevenue = computed(() =>
  revenue.value
    .filter((r) => isThisMonth(r.date))
    .reduce((sum, r) => sum + r.amount, 0)
);

const monthlyExpenses = computed(() =>
  expenses.value
    .filter((e) => isThisMonth(e.date))
    .reduce((sum, e) => sum + e.amount, 0)
);

const netProfit = computed(() => monthlyRevenue.value - monthlyExpenses.value);

// Products in stock
const totalProductsInStock = computed(() =>
  products.value.reduce((sum, p) => sum + (p.stock || 0), 0)
);

// Top Products (sold items)
const topProducts = computed(() => {
  return products.value
    .filter((p) => p.soldDate)
    .map((p) => ({
      name: p.name,
      sales: 1,
      revenue: p.soldPrice || p.valuePrice || 0,
    }))
    .sort((a, b) => b.revenue - a.revenue)
    .slice(0, 4);
});

// Recent activity
const recentActivities = computed(() => {
  const rev = revenue.value.map((r) => ({
    type: "revenue",
    description: r.description || "Revenue",
    amount: r.amount,
    date: r.date,
  }));
  const exp = expenses.value.map((e) => ({
    type: "expense",
    description: e.description || "Expense",
    amount: -e.amount,
    date: e.date,
  }));
  return [...rev, ...exp]
    .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
    .slice(0, 6);
});
</script>

<template>
  <div class="dashboard-page">
    <div class="page-header">
      <div class="header-content">
        <h1>Dashboard</h1>
        <p>Overview of your business performance this month</p>
      </div>
    </div>

    <!-- Stats Cards -->
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
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Monthly Revenue</span>
          <span class="stat-value">${{ monthlyRevenue.toLocaleString() }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon red">
          <ArrowTrendingDownIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Monthly Expenses</span>
          <span class="stat-value"
            >${{ monthlyExpenses.toLocaleString() }}</span
          >
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon blue">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Net Profit</span>
          <span class="stat-value">${{ netProfit.toLocaleString() }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple">
          <CubeIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Products in Stock</span>
          <span class="stat-value">{{ totalProductsInStock }}</span>
        </div>
      </div>
    </div>

    <!-- Chart -->
    <div class="chart-section mt-10">
      <RevenueExpenseChart
        :revenues="revenue"
        :expenses="expenses"
        :is-loading="isLoading"
      />
    </div>

    <!-- Activity & Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">
      <!-- Recent Activity -->
      <div class="activity-section">
        <div class="section-header">
          <h3>Recent Activity</h3>
          <span class="activity-count"
            >{{ recentActivities.length }} items</span
          >
        </div>

        <div v-if="isLoading" class="activity-list">
          <div
            v-for="n in 6"
            :key="'act-skel-' + n"
            class="activity-item skeleton-card"
          >
            <div class="skeleton-line" style="width: 60%; height: 14px"></div>
            <div class="skeleton-line" style="width: 40%; height: 12px"></div>
          </div>
        </div>

        <div v-else class="activity-list">
          <div
            v-for="activity in recentActivities"
            :key="activity.description + activity.date"
            class="activity-item"
          >
            <div class="activity-main">
              <div class="activity-info">
                <h4 class="activity-name">{{ activity.description }}</h4>
                <span
                  :class="[
                    'activity-type',
                    activity.type === 'revenue'
                      ? 'type-revenue'
                      : 'type-expense',
                  ]"
                >
                  {{ activity.type }}
                </span>
              </div>
              <p class="activity-date">
                {{ new Date(activity.date).toLocaleDateString() }}
              </p>
            </div>
            <div class="activity-amount">
              <span
                :class="[
                  'amount-value',
                  activity.amount > 0 ? 'positive' : 'negative',
                ]"
              >
                {{ activity.amount > 0 ? "+" : "-" }}${{
                  Math.abs(activity.amount).toLocaleString()
                }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="products-section">
        <div class="section-header">
          <h3>Top Products</h3>
          <span class="products-count">{{ topProducts.length }} items</span>
        </div>

        <div v-if="isLoading" class="products-list">
          <div
            v-for="n in 4"
            :key="'prod-skel-' + n"
            class="product-item skeleton-card"
          >
            <div class="skeleton-line" style="width: 50%; height: 14px"></div>
            <div class="skeleton-line" style="width: 60%; height: 12px"></div>
          </div>
        </div>

        <div v-else class="products-list">
          <div
            v-for="product in topProducts"
            :key="product.name"
            class="product-item"
          >
            <div class="product-main">
              <h4 class="product-name">{{ product.name }}</h4>
            </div>

            <div class="product-metrics">
              <div class="metric">
                <span class="metric-label">Sales</span>
                <span class="metric-value">{{ product.sales }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Revenue</span>
                <span class="metric-value total"
                  >${{ product.revenue.toLocaleString() }}</span
                >
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
