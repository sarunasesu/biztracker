<script setup lang="ts">
import { ref } from 'vue'
import MetricCard from '../components/MetricCard.vue'
import RevenueExpenseChart from '../components/RevenueExpenseChart.vue'
import {
  CurrencyDollarIcon,
  ArrowTrendingDownIcon,
  ArrowTrendingUpIcon,
  CubeIcon
} from '@heroicons/vue/24/outline'

const metrics = ref([
  {
    title: 'Monthly Revenue',
    value: '$28,000',
    icon: ArrowTrendingUpIcon,
    trend: 'up' as const,
    iconColor: 'text-green-500'
  },
  {
    title: 'Monthly Expenses',
    value: '$18,000',
    icon: ArrowTrendingDownIcon,
    trend: 'down' as const,
    iconColor: 'text-red-500'
  },
  {
    title: 'Monthly Net Profit',
    value: '$10,000',
    icon: CurrencyDollarIcon,
    trend: 'up' as const,
    iconColor: 'text-green-500'
  },
  {
    title: 'Products in Stock',
    value: '247',
    icon: CubeIcon,
    trend: 'neutral' as const,
    iconColor: 'text-blue-500'
  }
])

const recentActivities = ref([
  { type: 'revenue', description: 'Product Sales - Electronics', amount: 15000, date: '2024-01-15' },
  { type: 'expense', description: 'Office Rent Payment', amount: -3000, date: '2024-01-14' },
  { type: 'revenue', description: 'Consulting Services', amount: 8500, date: '2024-01-13' },
  { type: 'expense', description: 'Marketing Campaign', amount: -2500, date: '2024-01-12' },
])

const topProducts = ref([
  { name: 'Wireless Headphones', sales: 45, revenue: 5850 },
  { name: 'Bluetooth Speaker', sales: 32, revenue: 2880 },
  { name: 'Coffee Mug Set', sales: 28, revenue: 1106 },
  { name: 'Desk Lamp', sales: 24, revenue: 1080 },
])
</script>

<template>
  <div class="dashboard-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Dashboard</h1>
        <p>Overview of your business performance this month</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green">
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Monthly Revenue</span>
          <span class="stat-value">$28,000</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon red">
          <ArrowTrendingDownIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Monthly Expenses</span>
          <span class="stat-value">$18,000</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon blue">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Net Profit</span>
          <span class="stat-value">$10,000</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon purple">
          <CubeIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Products in Stock</span>
          <span class="stat-value">247</span>
        </div>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
      <RevenueExpenseChart />
    </div>

    <!-- Activity & Products Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Activity -->
      <div class="activity-section">
        <div class="section-header">
          <h3>Recent Activity</h3>
          <span class="activity-count">{{ recentActivities.length }} items</span>
        </div>
        
        <div class="activity-list">
          <div v-for="activity in recentActivities" :key="activity.description" class="activity-item">
            <div class="activity-main">
              <div class="activity-info">
                <h4 class="activity-name">{{ activity.description }}</h4>
                <span :class="['activity-type', activity.type === 'revenue' ? 'type-revenue' : 'type-expense']">
                  {{ activity.type }}
                </span>
              </div>
              <p class="activity-date">{{ activity.date }}</p>
            </div>
            
            <div class="activity-amount">
              <span :class="['amount-value', activity.amount > 0 ? 'positive' : 'negative']">
                {{ activity.amount > 0 ? '+' : '' }}${{ Math.abs(activity.amount).toLocaleString() }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="products-section">
        <div class="section-header">
          <h3>Top Products</h3>
          <span class="products-count">{{ topProducts.length }} items</span>
        </div>
        
        <div class="products-list">
          <div v-for="product in topProducts" :key="product.name" class="product-item">
            <div class="product-main">
              <h4 class="product-name">{{ product.name }}</h4>
            </div>
            
            <div class="product-metrics">
              <div class="metric">
                <span class="metric-label">Sales</span>
                <span class="metric-value">{{ product.sales }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Revenue</span>
                <span class="metric-value total">${{ product.revenue.toLocaleString() }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>