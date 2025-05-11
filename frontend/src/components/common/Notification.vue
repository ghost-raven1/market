<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="modelValue"
      class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5"
      :class="{
        'bg-green-50': type === 'success',
        'bg-blue-50': type === 'info',
        'bg-yellow-50': type === 'warning',
        'bg-red-50': type === 'error'
      }"
    >
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <CheckCircleIcon
              v-if="type === 'success'"
              class="h-6 w-6 text-green-400"
              aria-hidden="true"
            />
            <InformationCircleIcon
              v-else-if="type === 'info'"
              class="h-6 w-6 text-blue-400"
              aria-hidden="true"
            />
            <ExclamationTriangleIcon
              v-else-if="type === 'warning'"
              class="h-6 w-6 text-yellow-400"
              aria-hidden="true"
            />
            <XCircleIcon
              v-else-if="type === 'error'"
              class="h-6 w-6 text-red-400"
              aria-hidden="true"
            />
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p
              class="text-sm font-medium"
              :class="{
                'text-green-800': type === 'success',
                'text-blue-800': type === 'info',
                'text-yellow-800': type === 'warning',
                'text-red-800': type === 'error'
              }"
            >
              {{ title }}
            </p>
            <p
              class="mt-1 text-sm"
              :class="{
                'text-green-700': type === 'success',
                'text-blue-700': type === 'info',
                'text-yellow-700': type === 'warning',
                'text-red-700': type === 'error'
              }"
            >
              {{ message }}
            </p>
          </div>
          <div class="ml-4 flex flex-shrink-0">
            <button
              type="button"
              class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
              :class="{
                'text-green-400 hover:text-green-500 focus:ring-green-500': type === 'success',
                'text-blue-400 hover:text-blue-500 focus:ring-blue-500': type === 'info',
                'text-yellow-400 hover:text-yellow-500 focus:ring-yellow-500': type === 'warning',
                'text-red-400 hover:text-red-500 focus:ring-red-500': type === 'error'
              }"
              @click="$emit('update:modelValue', false)"
            >
              <span class="sr-only">Закрыть</span>
              <XMarkIcon class="h-5 w-5" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import {
  CheckCircleIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon,
  XCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

interface Props {
  modelValue: boolean
  title: string
  message: string
  type?: 'success' | 'info' | 'warning' | 'error'
}

withDefaults(defineProps<Props>(), {
  type: 'info'
})

defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()
</script> 