<template>
  <div
    class="bg-white rounded-lg shadow overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
    :class="[customClass, { 'border-2 border-yellow-400': isVip }]"
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
interface Props {
  customClass?: string
  isVip?: boolean
}

withDefaults(defineProps<Props>(), {
  customClass: '',
  isVip: false
})
</script> 