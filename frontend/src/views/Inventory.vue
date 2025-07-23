<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useInventoryStore } from "../stores/inventoryStore.ts";
import axios from "../axios";
import AddProductModal from "../components/AddProductModal.vue";
import Swal from "sweetalert2";
import {
  PlusIcon,
  MagnifyingGlassIcon,
  CubeIcon,
  ExclamationTriangleIcon,
  ChartBarIcon,
  CurrencyDollarIcon,
} from "@heroicons/vue/24/outline";

const token = localStorage.getItem("token") || "";
const store = useInventoryStore();
const isLoading = ref(true);

const showAddProductModal = ref(false);
const searchQuery = ref("");
const selectedCategory = ref("");

onMounted(async () => {
  isLoading.value = true;
  await Promise.all([store.fetchProducts(token), store.fetchCategories(token)]);
  isLoading.value = false;
});

const filteredProducts = computed(() =>
  (store.products || [])
    .map((p) => ({
      ...p,
      status: p.stock < 10 ? "Low Stock" : "In Stock",
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

const handleProductSubmit = async (newProduct?: any) => {
  if (newProduct) {
    // Show SweetAlert loading modal
    Swal.fire({
      title: "Adding product...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    try {
      await store.addNewProductFromApi(token, newProduct);
      Swal.close(); // Close loading modal on success
      showAddProductModal.value = false;
      Swal.fire("Added!", "The product was added successfully.", "success");
    } catch (err) {
      console.error("Failed to add product:", err);
      Swal.fire("Error", "Failed to add product. Try again.", "error");
    }
  } else {
    isLoading.value = true;
    try {
      await store.fetchProducts(token);
    } catch (err) {
      console.error("Failed to refresh products:", err);
    } finally {
      isLoading.value = false;
    }
  }
};

const deleteProduct = async (id: number) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "This action cannot be undone!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  });

  if (result.isConfirmed) {
    Swal.fire({
      title: "Deleting...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    try {
      await axios.delete(`/api/products/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      });

      store.deleteProductLocally(id);

      Swal.fire("Deleted!", "The product has been deleted.", "success");
    } catch (err) {
      console.error("Failed to delete product:", err);
      Swal.fire("Error", "An error occurred while deleting.", "error");
    }
  }
};

const getStatusColor = (status: string) => {
  return status === "Low Stock" ? "status-low" : "status-good";
};

const totalProducts = computed(() => store.products.length);
const totalValue = computed(() =>
  store.products.reduce((sum, p) => sum + p.valuePrice * p.stock, 0)
);
const lowStockItems = computed(
  () => store.products.filter((p) => p.stock < 10).length
);
const categories = computed(() => store.categories);
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
      <div
        v-for="n in 4"
        :key="`skeleton-${n}`"
        class="stat-card skeleton-card"
      >
        <div class="stat-icon skeleton"></div>
        <div class="stat-content">
          <span class="stat-label skeleton-text"></span>
          <span class="stat-value skeleton-text"></span>
        </div>
      </div>
    </div>
    <div v-else class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue"><CubeIcon /></div>
        <div class="stat-content">
          <span class="stat-label">Total Products</span>
          <span class="stat-value">{{ totalProducts }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><CurrencyDollarIcon /></div>
        <div class="stat-content">
          <span class="stat-label">Total Value</span>
          <span class="stat-value">${{ totalValue.toLocaleString() }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red"><ExclamationTriangleIcon /></div>
        <div class="stat-content">
          <span class="stat-label">Low Stock</span>
          <span class="stat-value">{{ lowStockItems }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple"><ChartBarIcon /></div>
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
            :key="`cat-${category}`"
            :value="category"
          >
            {{ category }}
          </option>
        </select>
      </div>
    </div>

    <!-- Product List -->
    <div class="products-section">
      <div v-if="!isLoading" class="section-header">
        <h3>Inventory Items</h3>
        <span class="products-count">{{ filteredProducts.length }} items</span>
      </div>

      <!-- Skeleton Items -->
      <div v-if="isLoading" class="products-list">
        <div
          class="skeleton-item"
          v-for="n in 5"
          :key="`skeleton-product-${n}`"
        >
          <div class="skeleton-thumbnail"></div>
          <div class="skeleton-lines">
            <div class="skeleton-line short"></div>
            <div class="skeleton-line"></div>
          </div>
        </div>
      </div>

      <!-- Actual Product Items with transition -->
      <template v-if="!isLoading && filteredProducts.length">
        <transition-group
          name="fade-slide"
          tag="div"
          class="products-list"
          appear
        >
          <div
            v-for="product in filteredProducts"
            :key="`product-${product.id}`"
            class="product-item"
          >
            <div class="product-main">
              <div class="product-info">
                <h4 class="product-name">{{ product.name }}</h4>
                <span class="product-sku">{{ product.sku }}</span>
              </div>
              <div class="product-details">
                <span class="product-category">
                  {{ product.category?.name || "Uncategorized" }}
                </span>
                <span
                  :class="['product-status', getStatusColor(product.status)]"
                >
                  {{ product.status }}
                </span>
              </div>
            </div>

            <div class="product-metrics">
              <div class="metric">
                <span class="metric-label">Quantity</span>
                <span
                  :class="[
                    'metric-value',
                    product.stock < 10 ? 'text-red' : '',
                  ]"
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
              <!-- <button class="action-btn edit">Edit</button> -->
              <button
                class="action-btn delete"
                @click="deleteProduct(product.id)"
              >
                Delete
              </button>
            </div>
          </div>
        </transition-group>
      </template>
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

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-slide-enter-to {
  opacity: 1;
  transform: translateY(0);
}

.fade-slide-leave-from {
  opacity: 1;
  transform: translateY(0);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
