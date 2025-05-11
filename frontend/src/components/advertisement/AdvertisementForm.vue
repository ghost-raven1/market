<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <h1 class="text-2xl font-bold mb-6">
        {{ isEditing ? 'Редактировать объявление' : 'Создать объявление' }}
      </h1>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
            Заголовок
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.title }"
          />
          <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
            Описание
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="4"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.description }"
          ></textarea>
          <p v-if="errors.description" class="mt-1 text-sm text-red-600">
            {{ errors.description }}
          </p>
        </div>

        <!-- Price -->
        <div>
          <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
            Цена
          </label>
          <div class="relative">
            <input
              id="price"
              v-model.number="form.price"
              type="number"
              min="0"
              required
              class="w-full rounded-lg border border-gray-300 pl-4 pr-12 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.price }"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <span class="text-gray-500">₽</span>
            </div>
          </div>
          <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price }}</p>
        </div>

        <!-- Category -->
        <div>
          <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
            Категория
          </label>
          <select
            id="category"
            v-model="form.category_id"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{ 'border-red-500': errors.category_id }"
          >
            <option value="">Выберите категорию</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">
            {{ errors.category_id }}
          </p>
        </div>

        <!-- Images -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Изображения
          </label>
          <div class="space-y-4">
            <!-- Загрузка изображений -->
            <div
              class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center"
              :class="{ 'border-blue-500': isDragging }"
              @dragover.prevent="isDragging = true"
              @dragleave.prevent="isDragging = false"
              @drop.prevent="handleDrop"
            >
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                multiple
                class="hidden"
                @change="handleFileSelect"
              />
              <div class="space-y-2">
                <svg
                  class="mx-auto h-12 w-12 text-gray-400"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 48 48"
                >
                  <path
                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                <div class="text-sm text-gray-600">
                  <button
                    type="button"
                    class="text-blue-600 hover:text-blue-700 font-medium"
                    @click="openFileInput"
                  >
                    Загрузить файлы
                  </button>
                  или перетащите их сюда
                </div>
                <p class="text-xs text-gray-500">
                  PNG, JPG, GIF до 5MB
                </p>
              </div>
            </div>

            <!-- Предпросмотр изображений -->
            <div v-if="previewImages.length > 0" class="grid grid-cols-4 gap-4">
              <div
                v-for="(image, index) in previewImages"
                :key="index"
                class="relative aspect-square group"
              >
                <img
                  :src="image.preview"
                  :alt="`Изображение ${index + 1}`"
                  class="w-full h-full object-cover rounded-lg"
                />
                <button
                  type="button"
                  class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                  @click="removeImage(index)"
                >
                  <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <p v-if="errors.images" class="mt-1 text-sm text-red-600">{{ errors.images }}</p>
        </div>

        <!-- Location -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Местоположение
          </label>
          <div class="space-y-4">
            <GMapAutocomplete
              v-model="form.address"
              placeholder="Введите адрес..."
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.address }"
              @place_changed="setPlace"
            />
            <div class="h-[300px] rounded-lg overflow-hidden">
              <GMapMap
                :center="mapCenter"
                :zoom="15"
                map-type-id="roadmap"
                class="w-full h-full"
                @click="handleMapClick"
              >
                <GMapMarker
                  v-if="form.latitude && form.longitude"
                  :position="{ lat: form.latitude, lng: form.longitude }"
                  :draggable="true"
                  @dragend="handleMarkerDragEnd"
                />
              </GMapMap>
            </div>
            <p v-if="errors.address" class="mt-1 text-sm text-red-600">
              {{ errors.address }}
            </p>
          </div>
        </div>

        <!-- VIP Status -->
        <div class="flex items-center">
          <input
            id="is_vip"
            v-model="form.is_vip"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <label for="is_vip" class="ml-2 block text-sm text-gray-700">
            Сделать VIP-объявлением
          </label>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
            :disabled="loading"
          >
            <span v-if="loading">Сохранение...</span>
            <span v-else>{{ isEditing ? 'Сохранить' : 'Создать' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useNotification } from '@/composables/useNotification'

// Google Maps type declarations
declare global {
  namespace google.maps {
    interface MapMouseEvent {
      latLng: LatLng | null
    }
    interface LatLng {
      lat(): number
      lng(): number
    }
    class Geocoder {
      geocode(
        request: { location: { lat: number; lng: number } },
        callback: (
          results: GeocoderResult[] | null,
          status: GeocoderStatus
        ) => void
      ): void
    }
    interface GeocoderResult {
      formatted_address: string
    }
    type GeocoderStatus = 'OK' | 'ZERO_RESULTS' | 'OVER_QUERY_LIMIT' | 'REQUEST_DENIED' | 'INVALID_REQUEST' | 'UNKNOWN_ERROR'
    namespace places {
      interface PlaceResult {
        geometry?: {
          location?: LatLng
        }
        formatted_address?: string
      }
    }
  }
}

interface Category {
  id: number
  name: string
  parent_id: number | null
  children?: Category[]
}

interface FormData {
  title: string
  description: string
  price: number
  category_id: number | null
  images: File[]
  address: string
  latitude: number | null
  longitude: number | null
  is_vip: boolean
}

const router = useRouter()
const route = useRoute()
const notification = useNotification()

const isEditing = computed(() => !!route.params.id)
const loading = ref(false)
const categories = ref<Category[]>([])
const previewImages = ref<{ file: File; preview: string }[]>([])
const errors = ref<Partial<Record<keyof FormData, string>>>({})

const form = ref<FormData>({
  title: '',
  description: '',
  price: 0,
  category_id: null,
  images: [],
  address: '',
  latitude: null,
  longitude: null,
  is_vip: false
})

const mapCenter = computed(() => ({
  lat: form.value.latitude || 55.7558,
  lng: form.value.longitude || 37.6173
}))

const fileInput = ref<HTMLInputElement | null>(null)
const isDragging = ref(false)

// Загрузка категорий
const loadCategories = async () => {
  try {
    const response = await fetch(`api/categories`)
    if (!response.ok) throw new Error('Failed to load categories')
    const data = await response.json()
    categories.value = buildCategoryTree(data)
  } catch (error) {
    notification.error('Ошибка', 'Не удалось загрузить категории')
    console.error('Error loading categories:', error)
  }
}

// Построение дерева категорий
const buildCategoryTree = (categories: Category[]): Category[] => {
  const categoryMap = new Map<number, Category>()
  const tree: Category[] = []

  categories.forEach(category => {
    categoryMap.set(category.id, { ...category, children: [] })
  })

  categories.forEach(category => {
    const node = categoryMap.get(category.id)!
    if (category.parent_id === null) {
      tree.push(node)
    } else {
      const parent = categoryMap.get(category.parent_id)
      if (parent) {
        parent.children = parent.children || []
        parent.children.push(node)
      }
    }
  })

  return tree
}

// Загрузка данных объявления при редактировании
const loadAdvertisement = async () => {
  if (!isEditing.value) return

  try {
    const response = await fetch(`api/advertisements/${route.params.id}`)
    if (!response.ok) throw new Error('Failed to load advertisement')
    const data = await response.json()

    form.value = {
      title: data.title,
      description: data.description,
      price: data.price,
      category_id: data.category_id,
      images: [],
      address: data.address,
      latitude: data.latitude,
      longitude: data.longitude,
      is_vip: data.is_vip
    }

    // Загружаем превью изображений
    previewImages.value = data.images.map((image: { path: string }) => ({
      file: new File([], ''),
      preview: image.path
    }))
  } catch (error) {
    notification.error('Ошибка', 'Не удалось загрузить объявление')
    console.error('Error loading advertisement:', error)
  }
}

// Обработка выбора файлов
const handleFileSelect = (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const files = Array.from(input.files)
  handleFiles(files)
}

// Обработка перетаскивания файлов
const handleDrop = (event: DragEvent) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer?.files || [])
  handleFiles(files)
}

// Обработка файлов
const handleFiles = (files: File[]) => {
  const validFiles = files.filter(file => {
    const isValidType = ['image/jpeg', 'image/png', 'image/gif'].includes(file.type)
    const isValidSize = file.size <= 5 * 1024 * 1024 // 5MB

    if (!isValidType) {
      notification.warning('Предупреждение', 'Поддерживаются только изображения (PNG, JPG, GIF)')
    }
    if (!isValidSize) {
      notification.warning('Предупреждение', 'Размер файла не должен превышать 5MB')
    }

    return isValidType && isValidSize
  })

  if (previewImages.value.length + validFiles.length > 5) {
    notification.warning('Предупреждение', 'Максимальное количество изображений - 5')
    return
  }

  validFiles.forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      previewImages.value.push({
        file,
        preview: e.target?.result as string
      })
    }
    reader.readAsDataURL(file)
  })
}

// Удаление изображения
const removeImage = (index: number) => {
  previewImages.value.splice(index, 1)
  form.value.images.splice(index, 1)
}

// Обработка выбора места на карте
const setPlace = (place: google.maps.places.PlaceResult) => {
  if (!place.geometry?.location) return

  const lat = place.geometry.location.lat()
  const lng = place.geometry.location.lng()

  form.value.latitude = lat
  form.value.longitude = lng
  form.value.address = place.formatted_address || ''
}

// Обработка клика по карте
const handleMapClick = (event: google.maps.MapMouseEvent) => {
  if (!event.latLng) return

  form.value.latitude = event.latLng.lat()
  form.value.longitude = event.latLng.lng()

  // Получаем адрес по координатам
  const geocoder = new google.maps.Geocoder()
  geocoder.geocode(
    { location: { lat: form.value.latitude, lng: form.value.longitude } },
    (results, status) => {
      if (status === 'OK' && results?.[0]) {
        form.value.address = results[0].formatted_address
      }
    }
  )
}

// Обработка перетаскивания маркера
const handleMarkerDragEnd = (event: google.maps.MapMouseEvent) => {
  if (!event.latLng) return

  form.value.latitude = event.latLng.lat()
  form.value.longitude = event.latLng.lng()

  // Получаем адрес по координатам
  const geocoder = new google.maps.Geocoder()
  geocoder.geocode(
    { location: { lat: form.value.latitude, lng: form.value.longitude } },
    (results, status) => {
      if (status === 'OK' && results?.[0]) {
        form.value.address = results[0].formatted_address
      }
    }
  )
}

// Валидация формы
const validateForm = (): boolean => {
  errors.value = {}

  if (!form.value.title.trim()) {
    errors.value.title = 'Введите заголовок'
  }

  if (!form.value.description.trim()) {
    errors.value.description = 'Введите описание'
  }

  if (form.value.price <= 0) {
    errors.value.price = 'Цена должна быть больше 0'
  }

  if (!form.value.category_id) {
    errors.value.category_id = 'Выберите категорию'
  }

  if (!isEditing.value && form.value.images.length === 0) {
    errors.value.images = 'Загрузите хотя бы одно изображение'
  }

  if (!form.value.address) {
    errors.value.address = 'Укажите местоположение'
  }

  return Object.keys(errors.value).length === 0
}

// Отправка формы
const handleSubmit = async () => {
  if (!validateForm()) return

  loading.value = true

  try {
    const formData = new FormData()
    formData.append('title', form.value.title)
    formData.append('description', form.value.description)
    formData.append('price', form.value.price.toString())
    if (form.value.category_id) {
      formData.append('category_id', form.value.category_id.toString())
    }
    formData.append('address', form.value.address)
    formData.append('latitude', form.value.latitude?.toString() || '')
    formData.append('longitude', form.value.longitude?.toString() || '')
    formData.append('is_vip', form.value.is_vip.toString())

    previewImages.value.forEach((image, index) => {
      if (image.file.size > 0) {
        formData.append(`images[${index}]`, image.file)
      }
    })

    const url = isEditing.value
      ? `api/advertisements/${route.params.id}`
      : `api/advertisements`
    const method = isEditing.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      body: formData
    })

    if (!response.ok) throw new Error('Failed to save advertisement')

    notification.success(
      'Успешно',
      `Объявление ${isEditing.value ? 'обновлено' : 'создано'}`
    )
    router.push('/advertisements')
  } catch (error) {
    notification.error('Ошибка', 'Не удалось сохранить объявление')
    console.error('Error saving advertisement:', error)
  } finally {
    loading.value = false
  }
}

// Открытие диалога выбора файлов
const openFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click()
  }
}

onMounted(() => {
  loadCategories()
  loadAdvertisement()
})
</script>
