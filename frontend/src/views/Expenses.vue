<script lang="ts">
// ✅ Module-level cache (outside component scope)
let expensesCache: any[] | null = null;
</script>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "../axios";
import AddExpensesModal from "../components/AddExpensesModal.vue";
import {
  PlusIcon,
  ArrowTrendingDownIcon,
  CurrencyDollarIcon,
  ExclamationTriangleIcon,
  MagnifyingGlassIcon,
  CalendarIcon,
} from "@heroicons/vue/24/outline";

const expenses = ref<any[]>([]);
const isLoading = ref(true);
const showAddExpensesModal = ref(false);
const searchQuery = ref("");
const selectedCategory = ref("");
const token = localStorage.getItem("token");

const categories = ["Rent", "Marketing", "Software", "Supplies", "Services"];

onMounted(async () => {
  await fetchExpensesIfNeeded();
});

const fetchExpensesIfNeeded = async () => {
  isLoading.value = true;

  if (expensesCache) {
    expenses.value = expensesCache;
  } else {
    try {
      const res = await axios.get("/api/expenses", {
        headers: { Authorization: `Bearer ${token}` },
      });
      expenses.value = res.data;
      expensesCache = res.data;
    } catch (err) {
      console.error("Failed to fetch expenses:", err);
    }
  }

  isLoading.value = false;
};

const filteredExpenses = computed(() =>
  expenses.value.filter((e) => {
    const matchCategory =
      !selectedCategory.value || e.category === selectedCategory.value;
    const matchSearch =
      e.description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      e.vendor?.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchCategory && matchSearch;
  })
);

const monthlyExpenses = computed(() => {
  const now = new Date();
  return expenses.value
    .filter((e) => {
      const d = new Date(e.date);
      return (
        d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
      );
    })
    .reduce((sum, e) => sum + e.amount, 0);
});

const averageExpenses = computed(() => {
  const sums = new Map<string, number>();
  expenses.value.forEach((e) => {
    const d = new Date(e.date);
    const key = `${d.getFullYear()}-${d.getMonth()}`;
    sums.set(key, (sums.get(key) || 0) + e.amount);
  });
  const total = Array.from(sums.values()).reduce((a, b) => a + b, 0);
  return sums.size > 0 ? total / sums.size : 0;
});

// TOTAL REVENUE CALCULATIONS
const totalExpenses = computed(() =>
  expenses.value.reduce((sum, r) => sum + r.amount, 0)
);

// TOTAL EXPENSES COUNT
const totalExpensesCount = computed(() => expenses.value.length);

const addExpenses = () => (showAddExpensesModal.value = true);
const closeAddExpensesModal = () => (showAddExpensesModal.value = false);

const handleExpensesSubmit = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get("/api/expenses", {
      headers: { Authorization: `Bearer ${token}` },
    });
    expenses.value = res.data;
    expensesCache = res.data;
  } catch (err) {
    console.error("Failed to refresh expenses list:", err);
  } finally {
    isLoading.value = false;
  }
};

const getCategoryColor = (category: string) => {
  const colors = {
    Rent: "category-rent",
    Marketing: "category-marketing",
    Software: "category-software",
    Supplies: "category-supplies",
    Services: "category-services",
  };
  return colors[category] || "category-default";
};

const formatDate = (dateStr: string): string => {
  const date = new Date(dateStr);
  return date.toLocaleDateString("en-GB", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};
</script>

<template>
  <div class="expenses-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Expenses Management</h1>
        <p>Track and control your business expenses</p>
      </div>
      <button @click="addExpenses" class="btn-primary">
        <PlusIcon class="btn-icon" />
        Add Expense
      </button>
    </div>

    <!-- Stats (with skeleton) -->
    <div v-if="isLoading" class="stats-grid">
      <div v-for="n in 4" :key="n" class="stat-card skeleton-card">
        <div class="stat-icon skeleton"></div>
        <div class="stat-content">
          <span class="stat-label skeleton-text"></span>
          <span class="stat-value skeleton-text"></span>
        </div>
      </div>
    </div>
    <div v-else class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon red"><CurrencyDollarIcon /></div>
        <div class="stat-content">
          <span class="stat-label">Total Expenses</span>
          <span class="stat-value"> ${{ totalExpenses.toLocaleString() }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange"><ArrowTrendingDownIcon /></div>
        <div class="stat-content">
          <span class="stat-label">This Month</span>
          <span class="stat-value"
            >${{ monthlyExpenses.toLocaleString() }}</span
          >
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><CurrencyDollarIcon /></div>
        <div class="stat-content">
          <span class="stat-label">Monthly Average</span>
          <span class="stat-value"
            >${{ averageExpenses.toLocaleString() }}</span
          >
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><ExclamationTriangleIcon /></div>
        <div class="stat-content">
          <span class="stat-label">Total Expenses</span>
          <span class="stat-value"> {{ totalExpensesCount }}</span>
        </div>
      </div>
    </div>

    <!-- Search -->
    <div class="search-section">
      <div class="search-container">
        <div class="search-input-wrapper">
          <MagnifyingGlassIcon class="search-icon" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search expenses..."
            class="search-input"
          />
        </div>
        <select v-model="selectedCategory" class="category-select">
          <option value="">All Categories</option>
          <option
            v-for="category in categories"
            :key="category"
            :value="category"
          >
            {{ category }}
          </option>
        </select>
      </div>
    </div>

    <!-- Expenses List (with skeleton) -->
    <div class="expenses-section">
      <div class="section-header" v-if="!isLoading">
        <h3>Expenses Entries</h3>
        <span class="expenses-count"
          >{{ filteredExpenses.length }} entries</span
        >
      </div>

      <div v-if="isLoading" class="expenses-list">
        <div v-for="n in 5" :key="n" class="expense-item skeleton-card">
          <div class="skeleton-line" style="width: 60%"></div>
          <div class="skeleton-line" style="width: 40%"></div>
        </div>
      </div>

      <div v-else class="expenses-list">
        <div
          v-for="expense in filteredExpenses"
          :key="expense.id"
          class="expense-item"
        >
          <div class="expense-main">
            <div class="expense-info">
              <h4 class="expense-name">{{ expense.description }}</h4>
              <span
                :class="[
                  'expense-category',
                  getCategoryColor(expense.category),
                ]"
              >
                {{ expense.category }}
              </span>
              <span v-if="expense.recurring" class="recurring-badge"
                >Recurring</span
              >
            </div>
            <div class="expense-details">
              <span class="expense-vendor">{{ expense.vendor }}</span>
              <span class="expense-date">{{ formatDate(expense.date) }}</span>
            </div>
          </div>

          <div class="expense-metrics">
            <div class="metric">
              <span class="metric-label">Amount</span>
              <span class="metric-value expense-amount"
                >${{ expense.amount.toLocaleString() }}</span
              >
            </div>
          </div>

          <div class="expense-actions">
            <button class="action-btn edit">Edit</button>
            <button class="action-btn delete">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <AddExpensesModal
      :is-open="showAddExpensesModal"
      @close="closeAddExpensesModal"
      @submit="handleExpensesSubmit"
    />
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
