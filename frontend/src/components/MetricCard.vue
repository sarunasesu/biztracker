<script setup lang="ts">
interface Props {
  title: string
  value: string | number
  icon: any
  trend?: 'up' | 'down' | 'neutral'
  iconColor?: string
}

defineProps<Props>()
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600">{{ title }}</p>
        <p class="text-2xl font-semibold text-gray-900 mt-1">{{ value }}</p>
      </div>
      <div :class="[iconColor || 'text-primary-500', 'w-10 h-10 flex items-center justify-center rounded-lg bg-gray-50']">
        <component :is="icon" class="w-6 h-6" />
      </div>
    </div>
    <div v-if="trend" class="mt-4 flex items-center">
      <svg 
        v-if="trend === 'up'" 
        class="w-4 h-4 text-green-500" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
      </svg>
      <svg 
        v-else-if="trend === 'down'" 
        class="w-4 h-4 text-red-500" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"></path>
      </svg>
    </div>
  </div>
</template>