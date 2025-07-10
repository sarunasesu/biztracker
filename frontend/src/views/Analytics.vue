<script setup lang="ts">
import { ref } from 'vue'
import RevenueExpenseChart from '../components/RevenueExpenseChart.vue'
import { ChartBarIcon, ArrowTrendingUpIcon, CurrencyDollarIcon, CalculatorIcon } from '@heroicons/vue/24/outline'

const keyMetrics = ref([
  { label: 'Profit Margin', value: '35.7%', change: '+2.3%', trend: 'up' },
  { label: 'Growth Rate', value: '12.4%', change: 'Monthly', trend: 'up' },
  { label: 'Break Even', value: '$15,000', change: 'Monthly target', trend: 'neutral' },
  { label: 'ROI', value: '156%', change: 'Return on investment', trend: 'up' }
])

const performanceTrends = ref([
  { label: 'Revenue Growth', value: '+18%', period: 'vs last quarter', type: 'positive' },
  { label: 'Expense Reduction', value: '-5%', period: 'vs last quarter', type: 'positive' },
  { label: 'Profit Increase', value: '+23%', period: 'vs last quarter', type: 'positive' }
])

const expenseCategories = ref([
  { name: 'Rent', amount: 3000, percentage: 40 },
  { name: 'Marketing', amount: 2500, percentage: 33 },
  { name: 'Software', amount: 800, percentage: 11 },
  { name: 'Services', amount: 1200, percentage: 16 }
])
</script>

<template>
  <div class="analytics-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Analytics</h1>
        <p>Deep insights into your business performance</p>
      </div>
    </div>

    <!-- Key Metrics Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon green">
          <CurrencyDollarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Profit Margin</span>
          <span class="stat-value">35.7%</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon blue">
          <ArrowTrendingUpIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Growth Rate</span>
          <span class="stat-value">12.4%</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon purple">
          <CalculatorIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Break Even</span>
          <span class="stat-value">$15,000</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon orange">
          <ChartBarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">ROI</span>
          <span class="stat-value">156%</span>
        </div>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
      <RevenueExpenseChart />
    </div>

    <!-- Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Expense Categories -->
      <div class="categories-section">
        <div class="section-header">
          <h3>Expense Categories</h3>
          <span class="categories-count">{{ expenseCategories.length }} categories</span>
        </div>
        
        <div class="categories-list">
          <div v-for="category in expenseCategories" :key="category.name" class="category-item">
            <div class="category-main">
              <h4 class="category-name">{{ category.name }}</h4>
              <div class="category-progress">
                <div class="progress-bar">
                  <div 
                    class="progress-fill" 
                    :style="{ width: category.percentage + '%' }"
                  ></div>
                </div>
                <span class="progress-text">{{ category.percentage }}%</span>
              </div>
            </div>
            
            <div class="category-amount">
              <span class="amount-value">${{ category.amount.toLocaleString() }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Trends -->
      <div class="trends-section">
        <div class="section-header">
          <h3>Performance Trends</h3>
          <span class="trends-count">{{ performanceTrends.length }} metrics</span>
        </div>
        
        <div class="trends-list">
          <div v-for="trend in performanceTrends" :key="trend.label" class="trend-item">
            <div class="trend-main">
              <h4 class="trend-label">{{ trend.label }}</h4>
              <p class="trend-period">{{ trend.period }}</p>
            </div>
            
            <div class="trend-value">
              <span :class="['value-text', trend.type === 'positive' ? 'positive' : 'negative']">
                {{ trend.value }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>