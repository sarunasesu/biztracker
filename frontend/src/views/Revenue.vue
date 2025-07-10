<script setup lang="ts">
import { ref } from 'vue'
import { PlusIcon, ArrowTrendingUpIcon, CurrencyDollarIcon, CalendarIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const revenues = ref([
  { id: 1, description: 'Product Sales - Electronics', amount: 15000, date: '2024-01-15', category: 'Sales', client: 'Tech Store Inc.' },
  { id: 2, description: 'Consulting Services', amount: 8500, date: '2024-01-10', category: 'Services', client: 'StartupCo' },
  { id: 3, description: 'Software License', amount: 2500, date: '2024-01-05', category: 'Licensing', client: 'Enterprise Corp' },
  { id: 4, description: 'Training Workshop', amount: 3200, date: '2024-01-08', category: 'Training', client: 'Learning Hub' },
  { id: 5, description: 'Product Sales - Clothing', amount: 4800, date: '2024-01-12', category: 'Sales', client: 'Fashion Retail' },
])

const searchQuery = ref('')
const selectedCategory = ref('')
const categories = ['Sales', 'Services', 'Licensing', 'Training']

const totalRevenue = ref(revenues.value.reduce((sum, revenue) => sum + revenue.amount, 0))
const monthlyRevenue = ref(28000)
const averageRevenue = ref(20000)
const revenueGrowth = ref(12.5)

const addRevenue = () => {
  console.log('Add revenue modal')
}

const getCategoryColor = (category: string) => {
  const colors = {
    'Sales': 'category-sales',
    'Services': 'category-services',
    'Licensing': 'category-licensing',
    'Training': 'category-training'
  }
  return colors[category] || 'category-default'
}

const filteredRevenues = ref(revenues.value)
</script>

<template>
  <div class="revenue-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Revenue Tracking</h1>
        <p>Monitor your business revenue streams and growth</p>
      </div>
      <button @click="addRevenue" class="btn-primary">
        <PlusIcon class="btn-icon" />
        Record Revenue
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Revenue</span>
          <span class="stat-value">${{ totalRevenue.toLocaleString() }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon blue">
          <CalendarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">This Month</span>
          <span class="stat-value">${{ monthlyRevenue.toLocaleString() }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon purple">
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Monthly Average</span>
          <span class="stat-value">${{ averageRevenue.toLocaleString() }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon green">
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Growth Rate</span>
          <span class="stat-value">+{{ revenueGrowth }}%</span>
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
            placeholder="Search revenue entries..."
            class="search-input"
          />
        </div>
        <select v-model="selectedCategory" class="category-select">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
        </select>
      </div>
    </div>

    <!-- Revenue List -->
    <div class="revenue-section">
      <div class="section-header">
        <h3>Revenue Entries</h3>
        <span class="revenue-count">{{ filteredRevenues.length }} entries</span>
      </div>
      
      <div class="revenue-list">
        <div v-for="revenue in filteredRevenues" :key="revenue.id" class="revenue-item">
          <div class="revenue-main">
            <div class="revenue-info">
              <h4 class="revenue-name">{{ revenue.description }}</h4>
              <span :class="['revenue-category', getCategoryColor(revenue.category)]">
                {{ revenue.category }}
              </span>
            </div>
            <div class="revenue-details">
              <span class="revenue-client">{{ revenue.client }}</span>
              <span class="revenue-date">{{ revenue.date }}</span>
            </div>
          </div>
          
          <div class="revenue-metrics">
            <div class="metric">
              <span class="metric-label">Amount</span>
              <span class="metric-value total">${{ revenue.amount.toLocaleString() }}</span>
            </div>
          </div>
          
          <div class="revenue-actions">
            <button class="action-btn edit">Edit</button>
            <button class="action-btn delete">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>