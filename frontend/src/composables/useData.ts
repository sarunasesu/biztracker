import { computed } from "vue";
import { useDataStore } from "../stores/dataStore";

// Composable for easy access to data store
export function useData() {
  const store = useDataStore();

  return {
    // State
    products: computed(() => store.products),
    categories: computed(() => store.categories),
    revenues: computed(() => store.revenues),
    expenses: computed(() => store.expenses),

    // Loading states
    loading: computed(() => store.loading),

    // Getters
    totalProducts: computed(() => store.totalProducts),
    lowStockProducts: computed(() => store.lowStockProducts),
    totalInventoryValue: computed(() => store.totalInventoryValue),
    categoryNames: computed(() => store.categoryNames),
    totalRevenue: computed(() => store.totalRevenue),
    monthlyRevenue: computed(() => store.monthlyRevenue),
    totalExpenses: computed(() => store.totalExpenses),
    monthlyExpenses: computed(() => store.monthlyExpenses),
    netProfit: computed(() => store.netProfit),
    monthlyNetProfit: computed(() => store.monthlyNetProfit),
    recentActivity: computed(() => store.recentActivity),

    // Actions
    ...store,
  };
}

// Specific composables for different data types
export function useProducts() {
  const store = useDataStore();

  return {
    products: computed(() => store.products),
    loading: computed(() => store.loading.products),
    totalProducts: computed(() => store.totalProducts),
    lowStockProducts: computed(() => store.lowStockProducts),
    totalInventoryValue: computed(() => store.totalInventoryValue),

    fetchProducts: store.fetchProducts,
    addProduct: store.addProduct,
    updateProduct: store.updateProduct,
    deleteProduct: store.deleteProduct,
  };
}

export function useCategories() {
  const store = useDataStore();

  return {
    categories: computed(() => store.categories),
    categoryNames: computed(() => store.categoryNames),
    loading: computed(() => store.loading.categories),

    fetchCategories: store.fetchCategories,
    addCategory: store.addCategory,
    deleteCategory: store.deleteCategory,
  };
}

export function useRevenues() {
  const store = useDataStore();

  return {
    revenues: computed(() => store.revenues),
    loading: computed(() => store.loading.revenues),
    totalRevenue: computed(() => store.totalRevenue),
    monthlyRevenue: computed(() => store.monthlyRevenue),

    fetchRevenues: store.fetchRevenues,
    addRevenue: store.addRevenue,
    deleteRevenue: store.deleteRevenue,
  };
}

export function useExpenses() {
  const store = useDataStore();

  return {
    expenses: computed(() => store.expenses),
    loading: computed(() => store.loading.expenses),
    totalExpenses: computed(() => store.totalExpenses),
    monthlyExpenses: computed(() => store.monthlyExpenses),

    fetchExpenses: store.fetchExpenses,
    addExpense: store.addExpense,
    deleteExpense: store.deleteExpense,
  };
}
