import { defineStore } from "pinia";
import axios from "../axios";

// Types
interface Product {
  id: number;
  name: string;
  sku: string;
  photo?: string;
  costPrice: number;
  valuePrice: number;
  soldPrice?: number;
  category: Category;
  stock: number;
  comment?: string;
  purchaseDate: string;
  soldDate?: string;
  description?: string;
  user: any;
}

interface Category {
  id: number;
  name: string;
}

interface Revenue {
  id: number;
  description: string;
  amount: number;
  category: string;
  date: string;
  paymentMethod?: string;
  customer?: string;
  invoiceNumber?: string;
  notes?: string;
  createdAt: string;
}

interface Expense {
  id: number;
  description: string;
  amount: number;
  category: string;
  date: string;
  paymentMethod?: string;
  vendor?: string;
  receiptNumber?: string;
  notes?: string;
  createdAt: string;
}

// Main data store
export const useDataStore = defineStore("data", {
  state: () => ({
    // Data
    products: [] as Product[],
    categories: [] as Category[],
    revenues: [] as Revenue[],
    expenses: [] as Expense[],

    // Loading states
    loading: {
      products: false,
      categories: false,
      revenues: false,
      expenses: false,
    },

    // Cache timestamps
    lastFetched: {
      products: null as Date | null,
      categories: null as Date | null,
      revenues: null as Date | null,
      expenses: null as Date | null,
    },

    // Cache duration (5 minutes)
    cacheTimeout: 5 * 60 * 1000,
  }),

  getters: {
    // Products
    totalProducts: (state) => state.products.length,
    lowStockProducts: (state) => state.products.filter((p) => p.stock < 10),
    totalInventoryValue: (state) =>
      state.products.reduce((sum, p) => sum + p.valuePrice * p.stock, 0),

    // Categories
    categoryNames: (state) => state.categories.map((c) => c.name),

    // Revenues
    totalRevenue: (state) =>
      state.revenues.reduce((sum, r) => sum + r.amount, 0),
    monthlyRevenue: (state) => {
      const now = new Date();
      return state.revenues
        .filter((r) => {
          const date = new Date(r.date);
          return (
            date.getMonth() === now.getMonth() &&
            date.getFullYear() === now.getFullYear()
          );
        })
        .reduce((sum, r) => sum + r.amount, 0);
    },

    // Expenses
    totalExpenses: (state) =>
      state.expenses.reduce((sum, e) => sum + e.amount, 0),
    monthlyExpenses: (state) => {
      const now = new Date();
      return state.expenses
        .filter((e) => {
          const date = new Date(e.date);
          return (
            date.getMonth() === now.getMonth() &&
            date.getFullYear() === now.getFullYear()
          );
        })
        .reduce((sum, e) => sum + e.amount, 0);
    },

    // Combined
    netProfit: (state) => {
      const totalRev = state.revenues.reduce((sum, r) => sum + r.amount, 0);
      const totalExp = state.expenses.reduce((sum, e) => sum + e.amount, 0);
      return totalRev - totalExp;
    },

    monthlyNetProfit(): number {
      return this.monthlyRevenue - this.monthlyExpenses;
    },

    // Recent activity (last 10 items)
    recentActivity: (state) => {
      const revenueActivity = state.revenues.map((r) => ({
        type: "revenue" as const,
        id: r.id,
        description: r.description,
        amount: r.amount,
        date: r.date,
        category: r.category,
      }));

      const expenseActivity = state.expenses.map((e) => ({
        type: "expense" as const,
        id: e.id,
        description: e.description,
        amount: -e.amount,
        date: e.date,
        category: e.category,
      }));

      return [...revenueActivity, ...expenseActivity]
        .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
        .slice(0, 10);
    },
  },

  actions: {
    // Check if data needs refresh
    needsRefresh(dataType: keyof typeof this.lastFetched): boolean {
      const lastFetch = this.lastFetched[dataType];
      if (!lastFetch) return true;
      return Date.now() - lastFetch.getTime() > this.cacheTimeout;
    },

    // Products
    async fetchProducts(force = false) {
      if (
        !force &&
        !this.needsRefresh("products") &&
        this.products.length > 0
      ) {
        return this.products;
      }

      this.loading.products = true;
      try {
        const response = await axios.get("/api/products");
        this.products = response.data;
        this.lastFetched.products = new Date();
        return this.products;
      } catch (error) {
        console.error("Failed to fetch products:", error);
        throw error;
      } finally {
        this.loading.products = false;
      }
    },

    async addProduct(productData: Partial<Product>) {
      try {
        const response = await axios.post("/api/products", productData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        // Fetch the complete product data
        const fullProduct = await axios.get(
          `/api/products/${response.data.id}`
        );
        this.products.unshift(fullProduct.data);

        return fullProduct.data;
      } catch (error) {
        console.error("Failed to add product:", error);
        throw error;
      }
    },

    async updateProduct(id: number, productData: Partial<Product>) {
      try {
        await axios.put(`/api/products/${id}`, productData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        // Fetch updated product
        const response = await axios.get(`/api/products/${id}`);
        const index = this.products.findIndex((p) => p.id === id);
        if (index !== -1) {
          this.products[index] = response.data;
        }

        return response.data;
      } catch (error) {
        console.error("Failed to update product:", error);
        throw error;
      }
    },

    async deleteProduct(id: number) {
      try {
        await axios.delete(`/api/products/${id}`);
        this.products = this.products.filter((p) => p.id !== id);
      } catch (error) {
        console.error("Failed to delete product:", error);
        throw error;
      }
    },

    // Categories
    async fetchCategories(force = false) {
      if (
        !force &&
        !this.needsRefresh("categories") &&
        this.categories.length > 0
      ) {
        return this.categories;
      }

      this.loading.categories = true;
      try {
        const response = await axios.get("/api/categories");
        this.categories = response.data;
        this.lastFetched.categories = new Date();
        return this.categories;
      } catch (error) {
        console.error("Failed to fetch categories:", error);
        throw error;
      } finally {
        this.loading.categories = false;
      }
    },

    async addCategory(name: string) {
      try {
        const response = await axios.post("/api/categories", { name });
        this.categories.push(response.data);
        return response.data;
      } catch (error) {
        console.error("Failed to add category:", error);
        throw error;
      }
    },

    async deleteCategory(id: number) {
      try {
        await axios.delete(`/api/categories/${id}`);
        this.categories = this.categories.filter((c) => c.id !== id);
      } catch (error) {
        console.error("Failed to delete category:", error);
        throw error;
      }
    },

    // Revenues
    async fetchRevenues(force = false) {
      if (
        !force &&
        !this.needsRefresh("revenues") &&
        this.revenues.length > 0
      ) {
        return this.revenues;
      }

      this.loading.revenues = true;
      try {
        const response = await axios.get("/api/revenue");
        this.revenues = response.data.sort(
          (a: Revenue, b: Revenue) =>
            new Date(b.date).getTime() - new Date(a.date).getTime()
        );
        this.lastFetched.revenues = new Date();
        return this.revenues;
      } catch (error) {
        console.error("Failed to fetch revenues:", error);
        throw error;
      } finally {
        this.loading.revenues = false;
      }
    },

    async addRevenue(revenueData: Partial<Revenue>) {
      try {
        const response = await axios.post("/api/revenue", revenueData);
        this.revenues.unshift(response.data);
        return response.data;
      } catch (error) {
        console.error("Failed to add revenue:", error);
        throw error;
      }
    },

    async deleteRevenue(id: number) {
      try {
        await axios.delete(`/api/revenue/${id}`);
        this.revenues = this.revenues.filter((r) => r.id !== id);
      } catch (error) {
        console.error("Failed to delete revenue:", error);
        throw error;
      }
    },

    // Expenses
    async fetchExpenses(force = false) {
      if (
        !force &&
        !this.needsRefresh("expenses") &&
        this.expenses.length > 0
      ) {
        return this.expenses;
      }

      this.loading.expenses = true;
      try {
        const response = await axios.get("/api/expenses");
        this.expenses = response.data.sort(
          (a: Expense, b: Expense) =>
            new Date(b.date).getTime() - new Date(a.date).getTime()
        );
        this.lastFetched.expenses = new Date();
        return this.expenses;
      } catch (error) {
        console.error("Failed to fetch expenses:", error);
        throw error;
      } finally {
        this.loading.expenses = false;
      }
    },

    async addExpense(expenseData: Partial<Expense>) {
      try {
        const response = await axios.post("/api/expenses", expenseData);
        this.expenses.unshift(response.data);
        return response.data;
      } catch (error) {
        console.error("Failed to add expense:", error);
        throw error;
      }
    },

    async deleteExpense(id: number) {
      try {
        await axios.delete(`/api/expenses/${id}`);
        this.expenses = this.expenses.filter((e) => e.id !== id);
      } catch (error) {
        console.error("Failed to delete expense:", error);
        throw error;
      }
    },

    // Initialize all data
    async initializeData() {
      await Promise.all([
        this.fetchProducts(),
        this.fetchCategories(),
        this.fetchRevenues(),
        this.fetchExpenses(),
      ]);
    },

    // Force refresh all data
    async refreshAllData() {
      await Promise.all([
        this.fetchProducts(true),
        this.fetchCategories(true),
        this.fetchRevenues(true),
        this.fetchExpenses(true),
      ]);
    },

    // Clear cache
    clearCache() {
      this.lastFetched = {
        products: null,
        categories: null,
        revenues: null,
        expenses: null,
      };
    },
  },
});
