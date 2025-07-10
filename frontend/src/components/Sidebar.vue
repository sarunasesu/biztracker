<script setup lang="ts">
import { inject, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import {
  HomeIcon,
  CubeIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  ChartBarIcon,
  DocumentTextIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const route = useRoute();
const sidebar = inject("sidebar") as any;

const navigation = [
  { name: "Dashboard", href: "/", icon: HomeIcon },
  { name: "Inventory", href: "/inventory", icon: CubeIcon },
  { name: "Revenue", href: "/revenue", icon: ArrowTrendingUpIcon },
  { name: "Expenses", href: "/expenses", icon: ArrowTrendingDownIcon },
  { name: "Analytics", href: "/analytics", icon: ChartBarIcon },
  { name: "Reports", href: "/reports", icon: DocumentTextIcon },
];

const isActive = (href: string) => {
  return route.path === href;
};

const navigate = (href: string) => {
  router.push(href);
  sidebar.close();
};

const logout = () => {
  localStorage.removeItem("token");
  router.push("/login");
  sidebar.close();
};
</script>

<template>
  <!-- Desktop sidebar -->
  <div :class="['sidebar', { open: sidebar.isOpen.value }]">
    <div class="flex min-h-0 flex-1 flex-col">
      <!-- Logo -->
      <div
        class="flex h-16 flex-shrink-0 items-center px-4 border-b border-gray-200"
      >
        <div class="flex items-center">
          <div
            class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center"
          >
            <svg
              class="w-5 h-5 text-white"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
              ></path>
            </svg>
          </div>
          <div class="ml-3">
            <h1 class="text-lg font-semibold text-gray-900">BizTracker</h1>
            <p class="text-xs text-gray-500">Business Management</p>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <button
          v-for="item in navigation"
          :key="item.name"
          @click="navigate(item.href)"
          :class="[isActive(item.href) ? 'active' : '']"
        >
          <component :is="item.icon" />
          {{ item.name }}
        </button>
      </nav>

      <!-- Logout Button -->
      <div class="mt-auto p-4 border-t border-gray-200">
        <button
          @click="logout"
          class="w-full flex items-center gap-2 px-4 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-100 transition"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5 text-red-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V5"
            />
          </svg>
          Logout
        </button>
      </div>
    </div>
  </div>
</template>
