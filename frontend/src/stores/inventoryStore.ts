// stores/inventory.ts
import { defineStore } from "pinia";
import axios from "../axios";

export const useInventoryStore = defineStore("inventory", {
  state: () => ({
    products: [] as any[],
    categories: [] as string[],
    isLoaded: false,
  }),
  actions: {
    async fetchProducts(token: string) {
      if (this.products.length > 0 && this.isLoaded) return;
      const res = await axios.get("/api/products", {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.products = res.data;
      this.isLoaded = true;
    },
    async fetchCategories(token: string) {
      if (this.categories.length > 0) return;
      const res = await axios.get("/api/categories", {
        headers: { Authorization: `Bearer ${token}` },
      });
      this.categories = res.data.map((c: any) => c.name);
    },
    async addNewProductFromApi(token: string, rawProduct: any) {
      try {
        const res = await axios.get(`/api/products/${rawProduct.id}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.products.unshift(res.data);
      } catch (err) {
        console.error("Failed to fetch full product:", err);
      }
    },

    deleteProductLocally(id: number) {
      this.products = this.products.filter((p) => p.id !== id);
    },
  },
});
