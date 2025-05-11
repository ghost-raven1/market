<template>
  <div class="relative">
    <div class="relative">
      <input
        type="text"
        :value="modelValue"
        @input="handleInput"
        @focus="showSuggestions = true"
        @blur="handleBlur"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        :placeholder="placeholder"
      />
      <div class="absolute inset-y-0 right-0 flex items-center pr-3">
        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
      </div>
    </div>

    <!-- Suggestions dropdown -->
    <div
      v-if="showSuggestions && suggestions.length > 0"
      class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
    >
      <div
        v-for="suggestion in suggestions"
        :key="suggestion.id"
        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-50"
        @mousedown="selectSuggestion(suggestion)"
      >
        <div class="flex items-center">
          <span class="truncate">{{ suggestion.title }}</span>
          <span class="ml-2 text-sm text-gray-500">{{ formatPrice(suggestion.price) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'
import debounce from 'lodash/debounce'

interface Props {
  modelValue: string
  placeholder?: string
  endpoint: string
}

interface Suggestion {
  id: number
  title: string
  price: number
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Поиск...'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'select', suggestion: Suggestion): void
}>()

const suggestions = ref<Suggestion[]>([])
const showSuggestions = ref(false)

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB'
  }).format(price)
}

const fetchSuggestions = debounce(async (query: string) => {
  if (!query) {
    suggestions.value = []
    return
  }

  try {
    const response = await axios.get(props.endpoint, {
      params: { query }
    })
    suggestions.value = response.data.data
  } catch (error) {
    console.error('Error fetching suggestions:', error)
    suggestions.value = []
  }
}, 300)

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
  fetchSuggestions(target.value)
}

const handleBlur = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

const selectSuggestion = (suggestion: Suggestion) => {
  emit('update:modelValue', suggestion.title)
  emit('select', suggestion)
  showSuggestions.value = false
}

watch(() => props.modelValue, (newValue) => {
  if (!newValue) {
    suggestions.value = []
  }
})
</script> 