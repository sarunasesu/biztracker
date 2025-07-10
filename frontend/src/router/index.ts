// index.ts
import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Inventory from "../views/Inventory.vue";
import Revenue from "../views/Revenue.vue";
import Expenses from "../views/Expenses.vue";
import Analytics from "../views/Analytics.vue";
import Reports from "../views/Reports.vue";
import Login from "../views/Login.vue";
import NotFound from "../views/NotFound.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      name: "Dashboard",
      component: Dashboard,
      meta: { requiresAuth: true },
    },
    {
      path: "/inventory",
      name: "Inventory",
      component: Inventory,
      meta: { requiresAuth: true },
    },
    {
      path: "/revenue",
      name: "Revenue",
      component: Revenue,
      meta: { requiresAuth: true },
    },
    {
      path: "/expenses",
      name: "Expenses",
      component: Expenses,
      meta: { requiresAuth: true },
    },
    {
      path: "/analytics",
      name: "Analytics",
      component: Analytics,
      meta: { requiresAuth: true },
    },
    {
      path: "/reports",
      name: "Reports",
      component: Reports,
      meta: { requiresAuth: true },
    },
    {
      path: "/login",
      name: "Login",
      component: Login,
      meta: { layout: false },
    },
    {
      //Must be last route to catch all unmatched paths
      path: "/:pathMatch(.*)*",
      name: "NotFound",
      component: NotFound,
      meta: { layout: false }, // ⬅️ disable layout for 404
    },
  ],
});

// ✅ Navigation Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  if (to.meta.requiresAuth && !token) {
    return next("/login");
  }
  if (to.path === "/login" && token) {
    return next("/");
  }
  next();
});

export default router;
