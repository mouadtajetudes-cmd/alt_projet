<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
    @click.self="$emit('close')"
  >
    <div 
      class="bg-white rounded-2xl shadow-2xl w-full animate-slide-down"
      :class="sizeClass"
    >
      
      <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark flex items-center gap-2">
          <font-awesome-icon v-if="icon" :icon="icon" class="text-primary" />
          {{ title }}
        </h2>
        <button 
          @click="$emit('close')"
          class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <font-awesome-icon icon="times" class="text-gray-600" />
        </button>
      </div>

      
      <div class="p-6">
        <slot></slot>
      </div>

      
      <div v-if="$slots.footer" class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-2xl">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import '../views/views.css'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    default: null
  },
  size: {
    type: String,
    default: 'md', 
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  }
})

defineEmits(['close'])

const sizeClass = computed(() => {
  const sizes = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl'
  }
  return sizes[props.size]
})
</script>
