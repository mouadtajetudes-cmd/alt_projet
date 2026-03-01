<template>
  <div 
    class="bg-white rounded-2xl shadow-lg p-6 transition-all duration-200 hover:shadow-xl hover:-translate-y-1"
    :class="[clickable ? 'cursor-pointer' : '', customClass]"
    @click="handleClick"
  >
    
    <div v-if="$slots.header" class="mb-4">
      <slot name="header"></slot>
    </div>

    
    <div v-if="icon || image" class="mb-4">
      <div 
        v-if="icon && !image"
        class="w-12 h-12 rounded-full flex items-center justify-center"
        :class="iconBgClass"
      >
        <font-awesome-icon :icon="icon" class="text-xl" :class="iconColorClass" />
      </div>
      <img 
        v-else-if="image"
        :src="image"
        :alt="title"
        class="w-full h-48 object-cover rounded-lg"
      />
    </div>

    
    <h3 v-if="title" class="text-xl font-bold text-dark mb-2">
      {{ title }}
    </h3>

    
    <p v-if="subtitle" class="text-sm text-dark-muted mb-3">
      {{ subtitle }}
    </p>

    
    <div class="text-dark">
      <slot></slot>
    </div>

    
    <div v-if="$slots.footer" class="mt-4 pt-4 border-t border-gray-200">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: null
  },
  subtitle: {
    type: String,
    default: null
  },
  icon: {
    type: String,
    default: null
  },
  image: {
    type: String,
    default: null
  },
  clickable: {
    type: Boolean,
    default: false
  },
  iconBgClass: {
    type: String,
    default: 'bg-gradient-to-br from-primary to-primary-light'
  },
  iconColorClass: {
    type: String,
    default: 'text-white'
  },
  customClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['click'])

const handleClick = () => {
  if (props.clickable) {
    emit('click')
  }
}
</script>
