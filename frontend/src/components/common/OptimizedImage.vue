<template>
  <div class="relative" :class="containerClass">
    <!-- Placeholder -->
    <div
      v-if="!isLoaded"
      class="absolute inset-0 bg-gray-200 animate-pulse"
      :class="imageClass"
    ></div>

    <!-- Blurred low-quality image -->
    <img
      v-if="lowQualitySrc"
      :src="lowQualitySrc"
      :alt="alt"
      class="absolute inset-0 w-full h-full object-cover blur-lg scale-110"
      :class="imageClass"
    />

    <!-- Main image -->
    <img
      :src="src"
      :alt="alt"
      class="relative w-full h-full object-cover transition-opacity duration-300"
      :class="[imageClass, { 'opacity-0': !isLoaded }]"
      @load="handleLoad"
      @error="handleError"
      loading="lazy"
    />

    <!-- Error state -->
    <div
      v-if="hasError"
      class="absolute inset-0 flex items-center justify-center bg-gray-100"
      :class="imageClass"
    >
      <span class="text-gray-400">{{ errorText }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Props {
  src: string
  alt: string
  lowQualitySrc?: string
  containerClass?: string
  imageClass?: string
  errorText?: string
}

defineProps<Props>()

const isLoaded = ref(false)
const hasError = ref(false)

const handleLoad = () => {
  isLoaded.value = true
}

const handleError = () => {
  hasError.value = true
  isLoaded.value = true
}
</script> 