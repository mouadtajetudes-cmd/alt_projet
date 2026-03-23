<template>
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    
    <div v-if="title || $slots.header" class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
      <slot name="header">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-bold text-dark flex items-center gap-2">
            <font-awesome-icon v-if="icon" :icon="icon" class="text-primary" />
            {{ title }}
          </h3>
          <div v-if="$slots.actions">
            <slot name="actions"></slot>
          </div>
        </div>
      </slot>
    </div>

    
    <div v-if="$slots.filters" class="px-6 py-4 bg-gray-50 border-b border-gray-200">
      <slot name="filters"></slot>
    </div>

    
    <div class="overflow-x-auto">
      <table class="w-full">
        
        <thead class="bg-gray-100 border-b border-gray-200">
          <tr>
            <th 
              v-for="column in columns" 
              :key="column.key"
              class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider"
              :class="column.headerClass"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>

        
        <tbody class="divide-y divide-gray-200">
          <slot name="body">
            <tr v-if="loading" class="hover:bg-gray-50">
              <td :colspan="columns.length" class="px-6 py-12 text-center">
                <div class="flex justify-center gap-2">
                  <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                  <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                  <div class="w-3 h-3 bg-primary rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
              </td>
            </tr>
            <tr v-else-if="!loading && rows.length === 0" class="hover:bg-gray-50">
              <td :colspan="columns.length" class="px-6 py-12 text-center text-gray-500">
                <font-awesome-icon icon="inbox" class="text-4xl mb-2 text-gray-300" />
                <p>{{ emptyMessage }}</p>
              </td>
            </tr>
            <tr 
              v-else
              v-for="(row, index) in rows" 
              :key="row[rowKey] || index"
              class="hover:bg-gray-50 transition-colors"
            >
              <td 
                v-for="column in columns" 
                :key="column.key"
                class="px-6 py-4 whitespace-nowrap text-sm text-dark"
                :class="column.cellClass"
              >
                <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                  {{ row[column.key] }}
                </slot>
              </td>
            </tr>
          </slot>
        </tbody>
      </table>
    </div>

    
    <div v-if="$slots.pagination" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
      <slot name="pagination"></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: null
  },
  icon: {
    type: String,
    default: null
  },
  columns: {
    type: Array,
    required: true
  },
  rows: {
    type: Array,
    default: () => []
  },
  rowKey: {
    type: String,
    default: 'id'
  },
  loading: {
    type: Boolean,
    default: false
  },
  emptyMessage: {
    type: String,
    default: 'Aucune donnée disponible'
  }
})
</script>
