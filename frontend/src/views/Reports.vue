<script setup lang="ts">
import { ref } from 'vue'
import { DocumentArrowDownIcon, DocumentTextIcon, CalendarIcon, ChartBarIcon } from '@heroicons/vue/24/outline'

const reportTypes = ref([
  {
    title: 'Monthly Financial Report',
    description: 'Complete overview of revenue, expenses, and profit',
    format: 'PDF',
    icon: DocumentTextIcon,
    color: 'blue'
  },
  {
    title: 'Inventory Report',
    description: 'Current stock levels and product details',
    format: 'Excel',
    icon: DocumentArrowDownIcon,
    color: 'green'
  },
  {
    title: 'Expense Breakdown',
    description: 'Detailed analysis of all business expenses',
    format: 'PDF',
    icon: DocumentTextIcon,
    color: 'red'
  },
  {
    title: 'Revenue Analysis',
    description: 'Revenue trends and performance metrics',
    format: 'Excel',
    icon: DocumentArrowDownIcon,
    color: 'purple'
  }
])

const reportsGenerated = ref(0)
const lastReport = ref('Never')
const availableFormats = ref('PDF & Excel')

const generateReport = (reportTitle: string) => {
  console.log(`Generating ${reportTitle}...`)
}

const getIconColor = (color: string) => {
  const colors = {
    'blue': 'icon-blue',
    'green': 'icon-green',
    'red': 'icon-red',
    'purple': 'icon-purple'
  }
  return colors[color] || 'icon-blue'
}
</script>

<template>
  <div class="reports-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>Reports</h1>
        <p>Generate and download business reports</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">
          <ChartBarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Reports Generated</span>
          <span class="stat-value">{{ reportsGenerated }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon purple">
          <CalendarIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Last Report</span>
          <span class="stat-value">{{ lastReport }}</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon green">
          <DocumentTextIcon />
        </div>
        <div class="stat-content">
          <span class="stat-label">Available Formats</span>
          <span class="stat-value">{{ availableFormats }}</span>
        </div>
      </div>
    </div>

    <!-- Report Types -->
    <div class="reports-section">
      <div class="section-header">
        <h3>Available Reports</h3>
        <span class="reports-count">{{ reportTypes.length }} report types</span>
      </div>
      
      <div class="reports-grid">
        <div 
          v-for="report in reportTypes" 
          :key="report.title"
          class="report-card"
        >
          <div class="report-header">
            <div :class="['report-icon', getIconColor(report.color)]">
              <component :is="report.icon" />
            </div>
            <span class="report-format">{{ report.format }}</span>
          </div>
          
          <div class="report-content">
            <h4 class="report-title">{{ report.title }}</h4>
            <p class="report-description">{{ report.description }}</p>
          </div>
          
          <div class="report-actions">
            <button
              @click="generateReport(report.title)"
              class="generate-btn"
            >
              Generate Report
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Custom Report Builder -->
    <div class="custom-report-section">
      <div class="section-header">
        <h3>Custom Report Builder</h3>
        <span class="custom-count">Build your own</span>
      </div>
      
      <div class="custom-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label">End Date</label>
            <input type="date" class="form-input" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Include Metrics</label>
          <div class="checkbox-grid">
            <label class="checkbox-item">
              <input type="checkbox" checked />
              <span>Revenue</span>
            </label>
            <label class="checkbox-item">
              <input type="checkbox" checked />
              <span>Expenses</span>
            </label>
            <label class="checkbox-item">
              <input type="checkbox" checked />
              <span>Profit</span>
            </label>
            <label class="checkbox-item">
              <input type="checkbox" />
              <span>Inventory</span>
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button class="btn-primary">
            <DocumentTextIcon class="btn-icon" />
            Generate PDF
          </button>
          <button class="btn-secondary">
            <DocumentArrowDownIcon class="btn-icon" />
            Generate Excel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>