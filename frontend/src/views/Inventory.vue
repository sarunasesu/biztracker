<script lang="ts">
// ✅ Module-level cache shared between component instances (not reactive)
let productCache: any[] | null = null;
let categoryCache: string[] | null = null;
</script>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "../axios"; // Adjust path if necessary
import AddProductModal from "../components/AddProductModal.vue";
import {
  PlusIcon,
  MagnifyingGlassIcon,
  CubeIcon,
  ExclamationTriangleIcon,
  ChartBarIcon,
  CurrencyDollarIcon,
} from "@heroicons/vue/24/outline";

const products = ref<any[]>([]);
const isLoading = ref(true);
const token = localStorage.getItem("token");

onMounted(async () => {
  isLoading.value = true;

  if (productCache) {
    products.value = productCache;
  } else {
    try {
      const res = await axios.get("/api/products", {
        headers: { Authorization: `Bearer ${token}` },
      });
      products.value = res.data;
      productCache = res.data;
    } catch (err) {
      console.error("Failed to fetch products:", err);
    }
  }

  if (categoryCache) {
    categories.value = categoryCache;
  } else {
    try {
      const res = await axios.get("/api/categories", {
        headers: { Authorization: `Bearer ${token}` },
      });
      categories.value = res.data.map((c: any) => c.name);
      categoryCache = categories.value;
    } catch (err) {
      console.error("Failed to fetch categories:", err);
    }
  }

  isLoading.value = false;
});

// Modal state for adding new products
const showAddProductModal = ref(false);

const searchQuery = ref("");
const selectedCategory = ref("");

const categories = ref<string[]>([]);

onMounted(async () => {
  try {
    const res = await axios.get("/api/categories", {
      headers: { Authorization: `Bearer ${token}` },
    });
    categories.value = res.data.map((c: any) => c.name);
  } catch (err) {
    console.error("Failed to fetch categories:", err);
  }
});

const filteredProducts = computed(() =>
  products.value
    .map((p) => ({
      ...p,
      status: p.stock < 20 ? "Low Stock" : "In Stock",
    }))
    .filter((p) => {
      const name = p.name?.toLowerCase?.() ?? "";
      const sku = p.sku?.toLowerCase?.() ?? "";
      const matchCategory =
        !selectedCategory.value || p.category?.name === selectedCategory.value;
      const matchSearch =
        name.includes(searchQuery.value.toLowerCase()) ||
        sku.includes(searchQuery.value.toLowerCase());

      return matchCategory && matchSearch;
    })
);

const addProduct = () => {
  showAddProductModal.value = true;
};

const closeAddProductModal = () => {
  showAddProductModal.value = false;
};

const handleProductSubmit = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get("/api/products", {
      headers: { Authorization: `Bearer ${token}` },
    });
    products.value = res.data;
    productCache = res.data;
  } catch (err) {
    console.error("Failed to refresh products:", err);
  } finally {
    isLoading.value = false;
  }
};

// Helper function for status styling
const getStatusColor = (status: string) => {
  return status === "Low Stock" ? "status-low" : "status-good";
};

const totalProducts = computed(() => products.value.length);
const totalValue = computed(() =>
  products.value.reduce((sum, p) => sum + p.valuePrice * p.stock, 0)
);
const lowStockItems = computed(
  () => products.value.filter((p) => p.stock < 10).length
);
</script>

<template>
  <div class="inventory-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Inventory Management</h1>
        <p>Track and manage your product inventory</p>
      </div>
      <button @click="addProduct" class="btn-primary">
        <PlusIcon class="btn-icon" />
        Add Product
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
        <div class="stat-icon blue">
          <CubeIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Products</span>
          <span class="stat-value">{{ totalProducts }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon green">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Value</span>
          <span class="stat-value">${{ totalValue.toLocaleString() }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon red">
          <ExclamationTriangleIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Low Stock</span>
          <span class="stat-value">{{ lowStockItems }}</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon purple">
          <ChartBarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Categories</span>
          <span class="stat-value">{{ categories.length }}</span>
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
            placeholder="Search products..."
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

    <!-- Product List -->
    <div class="products-section">
      <div class="section-header" v-if="!isLoading">
        <h3>Inventory Items</h3>
        <span class="products-count">{{ filteredProducts.length }} items</span>
      </div>

      <!-- Skeleton Items -->
      <div v-if="isLoading" class="products-list">
        <div class="skeleton-item" v-for="n in 5" :key="n">
          <div class="skeleton-thumbnail"></div>
          <div class="skeleton-lines">
            <div class="skeleton-line short"></div>
            <div class="skeleton-line"></div>
          </div>
        </div>
      </div>

      <!-- Actual Product Items -->
      <div v-else class="products-list">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="product-item"
        >
          <div class="product-main">
            <div class="product-info">
              <h4 class="product-name">{{ product.name }}</h4>
              <span class="product-sku">{{ product.sku }}</span>
            </div>
            <div class="product-details">
              <span class="product-category">{{
                product.category?.name || "Uncategorized"
              }}</span>
              <span :class="['product-status', getStatusColor(product.status)]">
                {{ product.status }}
              </span>
            </div>
          </div>

          <div class="product-metrics">
            <div class="metric">
              <span class="metric-label">Quantity</span>
              <span
                :class="['metric-value', product.stock < 10 ? 'text-red' : '']"
              >
                {{ product.stock }}
              </span>
            </div>
            <div class="metric">
              <span class="metric-label">Price</span>
              <span class="metric-value">${{ product.valuePrice }}</span>
            </div>
            <div class="metric">
              <span class="metric-label">Total</span>
              <span class="metric-value total">
                ${{ (product.valuePrice * product.stock).toLocaleString() }}
              </span>
            </div>
          </div>

          <div class="product-actions">
            <button class="action-btn edit">Edit</button>
            <button class="action-btn delete">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Product Modal -->
    <AddProductModal
      :is-open="showAddProductModal"
      @close="closeAddProductModal"
      @submit="handleProductSubmit"
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
