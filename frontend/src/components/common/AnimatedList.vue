<template>
  <TransitionGroup
    tag="div"
    :class="containerClass"
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="transform opacity-0 scale-95"
    enter-to-class="transform opacity-100 scale-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="transform opacity-100 scale-100"
    leave-to-class="transform opacity-0 scale-95"
    move-class="transition duration-300 ease-out"
  >
    <slot />
  </TransitionGroup>
</template>

<script setup lang="ts">
interface Props {
  containerClass?: string
}

withDefaults(defineProps<Props>(), {
  containerClass: 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'
})
</script> 