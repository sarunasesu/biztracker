<script setup lang="ts">
import { computed, provide, ref } from "vue";
import { useRoute } from "vue-router";
import Sidebar from "./components/Sidebar.vue";

const route = useRoute();
const sidebarOpen = ref(false);

// Provide sidebar toggle state/functions
provide("sidebar", {
  isOpen: sidebarOpen,
  toggle: () => (sidebarOpen.value = !sidebarOpen.value),
  close: () => (sidebarOpen.value = false),
});

// Track auth and route layout
const isLoggedIn = ref(!!localStorage.getItem("token"));
const showLayout = computed(() => {
  const noLayout = route.meta.layout === false;
  return isLoggedIn.value && !noLayout;
});

// Provide auth state for injection in Login.vue etc.

provide("auth", { isLoggedIn });
</script>

<template>
  <!-- ✅ Layout-enabled routes -->
  <div v-if="showLayout" class="min-h-screen flex bg-gray-50">
    <!-- Mobile backdrop -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black bg-opacity-25 z-40"
      @click="sidebarOpen = false"
    />
    <!-- Sidebar -->
    <Sidebar />
    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <div
        class="mobile-header p-4 lg:hidden flex justify-between items-center border-b"
      >
        <h1 class="text-lg font-semibold">BizTracker</h1>
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            />
          </svg>
        </button>
      </div>
      <main class="p-4 lg:p-8">
        <router-view />
      </main>
    </div>
  </div>

  <!-- ❌ Login / 404 / no-layout pages -->
  <router-view v-else />
</template>
