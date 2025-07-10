<script setup lang="ts">
import { ref } from 'vue'
import { PlusIcon, ArrowTrendingDownIcon, CurrencyDollarIcon, ExclamationTriangleIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const expenses = ref([
  { id: 1, description: 'Office Rent - Downtown Location', amount: 3000, date: '2024-01-01', category: 'Rent', vendor: 'Property Management Co.', recurring: true },
  { id: 2, description: 'Digital Marketing Campaign', amount: 2500, date: '2024-01-08', category: 'Marketing', vendor: 'AdTech Solutions', recurring: false },
  { id: 3, description: 'Software Licenses & Subscriptions', amount: 800, date: '2024-01-12', category: 'Software', vendor: 'Various Vendors', recurring: true },
  { id: 4, description: 'Office Supplies & Equipment', amount: 450, date: '2024-01-05', category: 'Supplies', vendor: 'Office Depot', recurring: false },
  { id: 5, description: 'Professional Services', amount: 1200, date: '2024-01-10', category: 'Services', vendor: 'Legal & Accounting', recurring: false },
])

const searchQuery = ref('')
const selectedCategory = ref('')
const categories = ['Rent', 'Marketing', 'Software', 'Supplies', 'Services']

const totalExpenses = ref(expenses.value.reduce((sum, expense) => sum + expense.amount, 0))
const monthlyExpenses = ref(18000)
const averageExpenses = ref(15000)
const recurringExpenses = ref(expenses.value.filter(e => e.recurring).reduce((sum, expense) => sum + expense.amount, 0))

const addExpense = () => {
  console.log('Add expense modal')
}

const getCategoryColor = (category: string) => {
  const colors = {
    'Rent': 'category-rent',
    'Marketing': 'category-marketing',
    'Software': 'category-software',
    'Supplies': 'category-supplies',
    'Services': 'category-services'
  }
  return colors[category] || 'category-default'
}

const filteredExpenses = ref(expenses.value)
</script>

<template>
  <div class="expenses-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Expense Management</h1>
        <p>Track and control your business expenses</p>
      </div>
      <button @click="addExpense" class="btn-primary">
        <PlusIcon class="btn-icon" />
        Add Expense
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon red">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Total Expenses</span>
          <span class="stat-value">${{ totalExpenses.toLocaleString() }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon orange">
          <ArrowTrendingDownIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">This Month</span>
          <span class="stat-value">${{ monthlyExpenses.toLocaleString() }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon blue">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Monthly Average</span>
          <span class="stat-value">${{ averageExpenses.toLocaleString() }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon purple">
          <ExclamationTriangleIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Recurring</span>
          <span class="stat-value">${{ recurringExpenses.toLocaleString() }}</span>
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
            placeholder="Search expenses..."
            class="search-input"
          />
        </div>
        <select v-model="selectedCategory" class="category-select">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
        </select>
      </div>
    </div>

    <!-- Expenses List -->
    <div class="expenses-section">
      <div class="section-header">
        <h3>Expense Entries</h3>
        <span class="expenses-count">{{ filteredExpenses.length }} entries</span>
      </div>
      
      <div class="expenses-list">
        <div v-for="expense in filteredExpenses" :key="expense.id" class="expense-item">
          <div class="expense-main">
            <div class="expense-info">
              <h4 class="expense-name">{{ expense.description }}</h4>
              <span :class="['expense-category', getCategoryColor(expense.category)]">
                {{ expense.category }}
              </span>
              <span v-if="expense.recurring" class="recurring-badge">
                Recurring
              </span>
            </div>
            <div class="expense-details">
              <span class="expense-vendor">{{ expense.vendor }}</span>
              <span class="expense-date">{{ expense.date }}</span>
            </div>
          </div>
          
          <div class="expense-metrics">
            <div class="metric">
              <span class="metric-label">Amount</span>
              <span class="metric-value expense-amount">${{ expense.amount.toLocaleString() }}</span>
            </div>
          </div>
          
          <div class="expense-actions">
            <button class="action-btn edit">Edit</button>
            <button class="action-btn delete">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>