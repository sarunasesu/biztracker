<script lang="ts">
// ✅ Module-level cache (outside component scope)
let revenueCache: any[] | null = null;
</script>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "../axios";
import AddRevenueModal from "../components/AddRevenueModal.vue";
import {
  PlusIcon,
  ArrowTrendingUpIcon,
  CurrencyDollarIcon,
  CalendarIcon,
  MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";

const revenues = ref<any[]>([]);
const isLoading = ref(true);
const searchQuery = ref("");
const selectedCategory = ref("");
const showAddRevenueModal = ref(false);
const token = localStorage.getItem("token");

const categories = [
  "Product Sales",
  "Services",
  "Consulting",
  "Licensing",
  "Training",
  "Other",
];

// ✅ Fetch once on mount if cache is empty
onMounted(async () => {
  await fetchRevenueIfNeeded();
});

const fetchRevenueIfNeeded = async () => {
  isLoading.value = true;

  if (revenueCache) {
    revenues.value = revenueCache;
  } else {
    try {
      const res = await axios.get("/api/revenue", {
        headers: { Authorization: `Bearer ${token}` },
      });
      revenues.value = res.data;
      revenueCache = res.data;
    } catch (err) {
      console.error("Failed to fetch revenues:", err);
    }
  }

  isLoading.value = false;
};

const filteredRevenues = computed(() =>
  revenues.value.filter((r) => {
    const matchCategory =
      !selectedCategory.value || r.category === selectedCategory.value;
    const matchSearch =
      r.description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      r.customer?.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchCategory && matchSearch;
  })
);

// TOTAL REVENUE CALCULATIONS
const totalRevenue = computed(() =>
  revenues.value.reduce((sum, r) => sum + r.amount, 0)
);
// MONTHLY REVENUE CALCULATIONS
const monthlyRevenue = computed(() => {
  const now = new Date();
  const currentMonth = now.getMonth();
  const currentYear = now.getFullYear();

  return revenues.value
    .filter((r) => {
      const revDate = new Date(r.date);
      return (
        revDate.getMonth() === currentMonth &&
        revDate.getFullYear() === currentYear
      );
    })
    .reduce((sum, r) => sum + r.amount, 0);
});
// AVERAGE REVENUE CALCULATIONS
const averageRevenue = computed(() => {
  const monthSums = new Map<string, number>();

  revenues.value.forEach((r) => {
    const d = new Date(r.date);
    const key = `${d.getFullYear()}-${d.getMonth()}`; // e.g., "2025-6"
    monthSums.set(key, (monthSums.get(key) || 0) + r.amount);
  });

  const monthsCount = monthSums.size || 1; // Avoid division by 0
  const total = Array.from(monthSums.values()).reduce((sum, v) => sum + v, 0);
  return total / monthsCount;
});
// TOTAL TRANSACTION COUNT
const totalTransactions = computed(() => revenues.value.length);

const addRevenue = () => {
  showAddRevenueModal.value = true;
};

const closeAddRevenueModal = () => {
  showAddRevenueModal.value = false;
};

const handleRevenueSubmit = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get("/api/revenue", {
      headers: { Authorization: `Bearer ${token}` },
    });
    revenues.value = res.data;
    revenueCache = res.data; // ✅ update cache after POST
  } catch (err) {
    console.error("Failed to refresh revenue list:", err);
  } finally {
    isLoading.value = false;
  }
};

const getCategoryColor = (category: string) => {
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

// ✅ Helper function for formatting date
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
  <div class="revenue-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Revenue Tracking</h1>
        <p>Monitor your business revenue streams and growth</p>
      </div>
      <button @click="addRevenue" class="btn-primary">
        <PlusIcon class="btn-icon" />
        Record Revenue
      </button>
    </div>

    <!-- Stats Cards with Skeleton -->
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
        <div class="stat-icon green">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Revenue</span>
          <span class="stat-value">${{ totalRevenue.toLocaleString() }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon blue">
          <CalendarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">This Month</span>
          <span class="stat-value">${{ monthlyRevenue.toLocaleString() }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple">
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Monthly Average</span>
          <span class="stat-value">${{ averageRevenue.toLocaleString() }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon green">
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Transactions</span>
          <span class="stat-value">{{ totalTransactions }}</span>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="search-section">
      <div class="search-container">
        <div class="search-input-wrapper">
          <MagnifyingGlassIcon class="search-icon" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search revenue entries..."
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

    <!-- Revenue List -->
    <div class="revenue-section">
      <div class="section-header" v-if="!isLoading">
        <h3>Revenue Entries</h3>
        <span class="revenue-count">{{ filteredRevenues.length }} entries</span>
      </div>

      <!-- Skeletons -->
      <div v-if="isLoading" class="revenue-list">
        <div class="skeleton-item" v-for="n in 5" :key="n">
          <div class="skeleton-thumbnail"></div>
          <div class="skeleton-lines">
            <div class="skeleton-line short"></div>
            <div class="skeleton-line"></div>
          </div>
        </div>
      </div>

      <!-- Actual Items -->
      <div v-else class="revenue-list">
        <div
          v-for="revenue in filteredRevenues"
          :key="revenue.id"
          class="revenue-item"
        >
          <div class="revenue-main">
            <div class="revenue-info">
              <h4 class="revenue-name">{{ revenue.description }}</h4>
              <span
                :class="[
                  'revenue-category',
                  getCategoryColor(revenue.category),
                ]"
              >
                {{ revenue.category }}
              </span>
            </div>
            <div class="revenue-details">
              <span class="revenue-client">{{ revenue.customer }}</span>
              <span class="revenue-date">{{ formatDate(revenue.date) }}</span>
            </div>
          </div>

          <div class="revenue-metrics">
            <div class="metric">
              <span class="metric-label">Amount</span>
              <span class="metric-value total">
                ${{ revenue.amount.toLocaleString() }}
              </span>
            </div>
          </div>

          <div class="revenue-actions">
            <button class="action-btn edit">Edit</button>
            <button class="action-btn delete">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Revenue Modal -->
    <AddRevenueModal
      :is-open="showAddRevenueModal"
      @close="closeAddRevenueModal"
      @submit="handleRevenueSubmit"
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
