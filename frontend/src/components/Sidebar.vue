<script setup lang="ts">
import { inject, computed, onMounted, ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "../axios"; // your authenticated axios instance
import {
  HomeIcon,
  CubeIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
  ChartBarIcon,
  DocumentTextIcon,
  ArrowRightOnRectangleIcon,
  UserCircleIcon,
} from "@heroicons/vue/24/outline";

const router = useRouter();
const route = useRoute();
const sidebar = inject("sidebar") as any;
const userEmail = ref("");
const userName = ref("");

// Fetch email from backend on mount
onMounted(async () => {
  const token = localStorage.getItem("token");
  if (!token) return;

  try {
    const res = await axios.get("/api/user", {
      headers: { Authorization: `Bearer ${token}` },
    });
    userEmail.value = res.data.email;
    userName.value = res.data.fullName;
  } catch (error) {
    console.error("Failed to fetch user info:", error);
  }
});

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

      <!-- User Section -->
      <div class="mt-auto border-t border-gray-200">
        <!-- User Info -->
        <div class="user-info" v-if="userEmail">
          <div class="user-avatar">
            <UserCircleIcon />
          </div>
          <div class="user-details">
            <div class="user-name">{{ userName }}</div>
            <div class="user-email">{{ userEmail }}</div>
          </div>
        </div>

        <!-- Logout Button -->
        <div class="sidebar-nav logout-section">
          <button @click="logout" class="logout-btn">
            <ArrowRightOnRectangleIcon />
            Logout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
