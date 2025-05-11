<template>
  <div class="relative h-[600px] w-full rounded-lg overflow-hidden">
    <div ref="mapContainer" class="w-full h-full"></div>

    <!-- Location Search -->
    <div class="absolute top-4 left-4 right-4">
      <input
        type="text"
        placeholder="Поиск по адресу..."
        v-model="searchQuery"
        @input="handleSearch"
        class="w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <!-- Map Controls -->
    <div class="absolute bottom-4 right-4 flex flex-col space-y-2">
      <button
        @click="centerOnUserLocation"
        class="p-2 bg-white rounded-lg shadow hover:bg-gray-50 transition-colors duration-200"
        title="Моё местоположение"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
      </button>
      <button
        @click="toggleRouteMode"
        class="p-2 bg-white rounded-lg shadow hover:bg-gray-50 transition-colors duration-200"
        :class="{ 'bg-blue-100': isRouteMode }"
        title="Построить маршрут"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
          />
        </svg>
      </button>
      <button
        @click="toggleDistanceMode"
        class="p-2 bg-white rounded-lg shadow hover:bg-gray-50 transition-colors duration-200"
        :class="{ 'bg-blue-100': isDistanceMode }"
        title="Измерить расстояние"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          />
        </svg>
      </button>
      <button
        @click="zoomIn"
        class="p-2 bg-white rounded-lg shadow hover:bg-gray-50 transition-colors duration-200"
        title="Приблизить"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
          />
        </svg>
      </button>
      <button
        @click="zoomOut"
        class="p-2 bg-white rounded-lg shadow hover:bg-gray-50 transition-colors duration-200"
        title="Отдалить"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M20 12H4"
          />
        </svg>
      </button>
    </div>

    <!-- Filters -->
    <div class="absolute top-4 right-4 bg-white rounded-lg shadow p-4">
      <h3 class="font-medium text-gray-900 mb-2">Фильтры</h3>
      <div class="space-y-2">
        <label class="flex items-center space-x-2">
          <input
            type="checkbox"
            v-model="filters.vipOnly"
            class="rounded text-blue-600 focus:ring-blue-500"
          />
          <span class="text-sm text-gray-700">Только VIP</span>
        </label>
        <div class="space-y-1">
          <label class="text-sm text-gray-700">Цена до:</label>
          <input
            type="number"
            v-model="filters.maxPrice"
            class="w-full px-2 py-1 text-sm border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Макс. цена"
          />
        </div>
      </div>
    </div>

    <!-- Route Info -->
    <div
      v-if="routeInfo"
      class="absolute bottom-4 left-4 bg-white rounded-lg shadow p-4 max-w-sm"
    >
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-medium text-gray-900">Маршрут</h3>
        <button
          @click="clearRoute"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
      <div class="text-sm text-gray-600">
        <p>Расстояние: {{ formatDistance(routeInfo.distance) }}</p>
        <p>Время в пути: {{ formatDuration(routeInfo.duration) }}</p>
      </div>
    </div>

    <!-- Distance Info -->
    <div
      v-if="distanceInfo"
      class="absolute bottom-4 left-4 bg-white rounded-lg shadow p-4 max-w-sm"
    >
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-medium text-gray-900">Расстояние</h3>
        <button
          @click="clearDistance"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
        </button>
      </div>
      <p class="text-sm text-gray-600">{{ formatDistance(distanceInfo) }}</p>
    </div>

    <!-- Popup -->
    <div
      v-if="selectedAdvertisement"
      class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-lg p-4 max-w-sm w-full"
    >
      <div class="flex items-start space-x-3">
        <img
          :src="selectedAdvertisement.images[0]?.path || '/placeholder.jpg'"
          :alt="selectedAdvertisement.title"
          class="w-20 h-20 object-cover rounded-lg"
        />
        <div class="flex-1">
          <div class="flex items-center justify-between">
            <h3 class="font-medium text-gray-900">{{ selectedAdvertisement.title }}</h3>
            <span
              v-if="selectedAdvertisement.is_vip"
              class="ml-2 px-2 py-1 bg-yellow-400 text-black text-xs font-medium rounded"
            >
              VIP
            </span>
          </div>
          <p class="text-sm text-gray-500 line-clamp-2">{{ selectedAdvertisement.description }}</p>
          <div class="mt-2 flex items-center justify-between">
            <span class="text-lg font-bold text-blue-600">
              {{ formatPrice(selectedAdvertisement.price) }} ₽
            </span>
            <div class="flex items-center space-x-2">
              <button
                @click="toggleFavorite(selectedAdvertisement)"
                class="text-gray-400 hover:text-red-500 transition-colors duration-200"
                :class="{ 'text-red-500': selectedAdvertisement.is_favorite }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <button
                @click="router.push(`/advertisements/${selectedAdvertisement.id}`)"
                class="text-sm text-blue-600 hover:text-blue-800"
              >
                Подробнее
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import maplibregl from 'maplibre-gl'
import 'maplibre-gl/dist/maplibre-gl.css'
import type { Advertisement } from '@/types/advertisement'
import type { Marker } from 'maplibre-gl'

interface Filters {
  vipOnly: boolean
  maxPrice: number | null
}

interface RouteInfo {
  distance: number
  duration: number
}

const props = defineProps<{
  advertisements: Advertisement[]
  center: { lat: number; lng: number }
  zoom: number
}>()

const router = useRouter()
const authStore = useAuthStore()
const notification = useNotification()

const mapContainer = ref<HTMLElement | null>(null)
let map: maplibregl.Map | null = null
const markers = ref<Marker[]>([])
const selectedAdvertisement = ref<Advertisement | null>(null)
const searchQuery = ref('')
const isRouteMode = ref(false)
const isDistanceMode = ref(false)
const routePoints = ref<[number, number][]>([])
const distancePoints = ref<[number, number][]>([])
const routeInfo = ref<RouteInfo | null>(null)
const distanceInfo = ref<number | null>(null)
const filters = ref<Filters>({
  vipOnly: false,
  maxPrice: null
})

// Фильтруем объявления
const filteredAdvertisements = computed(() => {
  return props.advertisements.filter(ad => {
    if (filters.value.vipOnly && !ad.is_vip) return false
    if (filters.value.maxPrice && ad.price > filters.value.maxPrice) return false
    return true
  })
})

const initMap = () => {
  if (!mapContainer.value) return

  map = new maplibregl.Map({
    container: mapContainer.value,
    style: `https://demotiles.maplibre.org/style.json`, // TODO: Доработать
    center: [props.center.lng, props.center.lat],
    zoom: props.zoom
  })

  map.addControl(new maplibregl.NavigationControl())

  // Добавляем источник данных для кластеризации
  map.on('load', () => {
    updateMapData()

    // Обработка клика по карте
    map?.on('click', (e) => {
      if (isRouteMode.value) {
        handleRoutePoint(e.lngLat)
      } else if (isDistanceMode.value) {
        handleDistancePoint(e.lngLat)
      }
    })
  })
}

const updateMapData = () => {
  const source = map?.getSource('advertisements') as maplibregl.GeoJSONSource
  if (source) {
    source.setData({
      type: 'FeatureCollection',
      features: filteredAdvertisements.value.map(ad => ({
        type: 'Feature',
        geometry: {
          type: 'Point',
          coordinates: [ad.longitude, ad.latitude]
        },
        properties: {
          id: ad.id,
          title: ad.title,
          description: ad.description,
          price: ad.price,
          is_vip: ad.is_vip,
          is_favorite: ad.is_favorite,
          image: ad.images[0]?.path || '/placeholder.jpg'
        }
      }))
    })
  }
}

const handleRoutePoint = (lngLat: maplibregl.LngLat) => {
  routePoints.value.push([lngLat.lng, lngLat.lat])

  if (routePoints.value.length === 2) {
    calculateRoute()
  }
}

const handleDistancePoint = (lngLat: maplibregl.LngLat) => {
  distancePoints.value.push([lngLat.lng, lngLat.lat])

  if (distancePoints.value.length === 2) {
    calculateDistance()
  }
}

const calculateRoute = async () => {
  try {
    const response = await fetch(
      `https://router.project-osrm.org/route/v1/driving/${routePoints.value[0][0]},${routePoints.value[0][1]};${routePoints.value[1][0]},${routePoints.value[1][1]}?overview=full&geometries=geojson`
    )

    if (!response.ok) throw new Error('Failed to calculate route')

    const data = await response.json()
    if (data.routes && data.routes.length > 0) {
      const route = data.routes[0]
      routeInfo.value = {
        distance: route.distance,
        duration: route.duration
      }

      // Отображаем маршрут на карте
      if (map) {
        map.addSource('route', {
          type: 'geojson',
          data: {
            type: 'Feature',
            properties: {},
            geometry: route.geometry
          }
        })

        map.addLayer({
          id: 'route',
          type: 'line',
          source: 'route',
          layout: {
            'line-join': 'round',
            'line-cap': 'round'
          },
          paint: {
            'line-color': '#3B82F6',
            'line-width': 4
          }
        })
      }
    }
  } catch (error) {
    notification.error('Ошибка', 'Не удалось построить маршрут')
    console.error('Error calculating route:', error)
  }
}

const calculateDistance = () => {
  const [point1, point2] = distancePoints.value
  const distance = calculateDistanceBetweenPoints(point1, point2)
  distanceInfo.value = distance
}

const calculateDistanceBetweenPoints = (point1: [number, number], point2: [number, number]): number => {
  const R = 6371e3 // радиус Земли в метрах
  const φ1 = (point1[1] * Math.PI) / 180
  const φ2 = (point2[1] * Math.PI) / 180
  const Δφ = ((point2[1] - point1[1]) * Math.PI) / 180
  const Δλ = ((point2[0] - point1[0]) * Math.PI) / 180

  const a =
    Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
    Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2)
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

  return R * c // расстояние в метрах
}

const toggleRouteMode = () => {
  isRouteMode.value = !isRouteMode.value
  isDistanceMode.value = false
  if (!isRouteMode.value) {
    clearRoute()
  }
}

const toggleDistanceMode = () => {
  isDistanceMode.value = !isDistanceMode.value
  isRouteMode.value = false
  if (!isDistanceMode.value) {
    clearDistance()
  }
}

const clearRoute = () => {
  routePoints.value = []
  routeInfo.value = null
  if (map) {
    if (map.getLayer('route')) {
      map.removeLayer('route')
    }
    if (map.getSource('route')) {
      map.removeSource('route')
    }
  }
}

const clearDistance = () => {
  distancePoints.value = []
  distanceInfo.value = null
}

const formatDistance = (meters: number): string => {
  if (meters < 1000) {
    return `${Math.round(meters)} м`
  }
  return `${(meters / 1000).toFixed(1)} км`
}

const formatDuration = (seconds: number): string => {
  const hours = Math.floor(seconds / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)

  if (hours > 0) {
    return `${hours} ч ${minutes} мин`
  }
  return `${minutes} мин`
}

const handleSearch = async () => {
  if (!searchQuery.value.trim()) return

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
        searchQuery.value
      )}&limit=1`
    )

    if (!response.ok) throw new Error('Failed to fetch location')

    const data = await response.json()

    if (data && data.length > 0) {
      const { lat, lon } = data[0]
      map?.flyTo({
        center: [parseFloat(lon), parseFloat(lat)],
        zoom: 15
      })
    } else {
      notification.error('Ошибка', 'Адрес не найден')
    }
  } catch (error) {
    notification.error('Ошибка', 'Не удалось найти адрес')
    console.error('Error searching location:', error)
  }
}

const centerOnUserLocation = () => {
  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        map?.flyTo({
          center: [position.coords.longitude, position.coords.latitude],
          zoom: 12
        })
      },
      (error) => {
        notification.error('Ошибка', 'Не удалось получить ваше местоположение')
        console.error('Error getting location:', error)
      }
    )
  } else {
    notification.error('Ошибка', 'Геолокация не поддерживается вашим браузером')
  }
}

const zoomIn = () => {
  const currentZoom = map?.getZoom() || 10
  map?.zoomTo(currentZoom + 1)
}

const zoomOut = () => {
  const currentZoom = map?.getZoom() || 10
  map?.zoomTo(currentZoom - 1)
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('ru-RU').format(price)
}

const toggleFavorite = async (advertisement: Advertisement) => {
  if (!authStore.isAuthenticated) {
    notification.error('Ошибка', 'Необходимо авторизоваться')
    return
  }

  try {
    const response = await fetch(`api/advertisements/${advertisement.id}/favorite`, {
      method: advertisement.is_favorite ? 'DELETE' : 'POST'
    })

    if (!response.ok) throw new Error('Failed to toggle favorite')

    advertisement.is_favorite = !advertisement.is_favorite
    notification.success(
      'Успешно',
      `Объявление ${advertisement.is_favorite ? 'добавлено в' : 'удалено из'} избранного`
    )
  } catch (error) {
    notification.error('Ошибка', 'Не удалось обновить избранное')
    console.error('Error toggling favorite:', error)
  }
}

// Обновляем маркеры при изменении фильтров
watch(
  [() => filters.value, () => props.advertisements],
  () => {
    updateMapData()
  },
  { deep: true }
)

watch(
  () => props.center,
  (newCenter) => {
    if (map) {
      map.flyTo({
        center: [newCenter.lng, newCenter.lat],
        zoom: props.zoom
      })
    }
  }
)

onMounted(() => {
  initMap()
})

onUnmounted(() => {
  markers.value.forEach(marker => marker.remove())
  markers.value = []
  if (map) {
    map.remove()
    map = null
  }
})
</script>

<style>
.marker {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
  transition: transform 0.2s;
}

.marker:hover {
  transform: scale(1.1);
}
</style>
