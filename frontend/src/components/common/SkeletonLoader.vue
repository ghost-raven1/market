<template>
  <div
    class="animate-pulse"
    :class="[
      type === 'text' && 'h-4 bg-gray-200 rounded',
      type === 'circle' && 'rounded-full bg-gray-200',
      type === 'image' && 'bg-gray-200 rounded-lg',
      type === 'button' && 'h-10 bg-gray-200 rounded',
      type === 'card' && 'bg-gray-200 rounded-lg p-4'
    ]"
    :style="{
      width: type === 'circle' ? `${size}px` : '100%',
      height: type === 'circle' ? `${size}px` : type === 'text' ? '1rem' : type === 'image' ? `${size}px` : 'auto'
    }"
  ></div>
</template>

<script setup lang="ts">
defineProps<{
  type: 'text' | 'circle' | 'image' | 'button' | 'card'
  size?: number
}>()
</script> 