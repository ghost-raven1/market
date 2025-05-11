<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Управление категориями</h1>
        <button
          @click="openCreateModal"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
        >
          Добавить категорию
        </button>
      </div>

      <!-- Дерево категорий -->
      <div class="bg-white rounded-lg shadow p-6">
        <div v-if="loading" class="flex justify-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        <div v-else class="space-y-4">
          <div
            v-for="category in categories"
            :key="category.id"
            class="category-item"
          >
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
              <div class="flex items-center space-x-4">
                <button
                  v-if="category.children?.length"
                  @click="toggleCategory(category)"
                  class="text-gray-500 hover:text-gray-700"
                >
                  <svg
                    class="w-5 h-5 transform transition-transform"
                    :class="{ 'rotate-90': category.isExpanded }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </button>
                <span class="font-medium">{{ category.name }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  @click="openEditModal(category)"
                  class="p-1 text-gray-500 hover:text-blue-600"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    />
                  </svg>
                </button>
                <button
                  @click="confirmDelete(category)"
                  class="p-1 text-gray-500 hover:text-red-600"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                </button>
              </div>
            </div>
            <!-- Подкатегории -->
            <div
              v-if="category.isExpanded && category.children?.length"
              class="ml-8 mt-2 space-y-2"
            >
              <div
                v-for="child in category.children"
                :key="child.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <span>{{ child.name }}</span>
                <div class="flex items-center space-x-2">
                  <button
                    @click="openEditModal(child)"
                    class="p-1 text-gray-500 hover:text-blue-600"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                  </button>
                  <button
                    @click="confirmDelete(child)"
                    class="p-1 text-gray-500 hover:text-red-600"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно создания/редактирования -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">
          {{ editingCategory ? 'Редактировать категорию' : 'Новая категория' }}
        </h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Название
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Родительская категория
            </label>
            <select
              v-model="form.parent_id"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option :value="null">Нет</option>
              <option
                v-for="category in availableParents"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-gray-700 hover:text-gray-900"
            >
              Отмена
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
              :disabled="loading"
            >
              {{ loading ? 'Сохранение...' : 'Сохранить' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Подтверждение удаления</h2>
        <p class="mb-6">
          Вы уверены, что хотите удалить категорию "{{ categoryToDelete?.name }}"?
          {{ categoryToDelete?.children?.length ? 'Все подкатегории также будут удалены.' : '' }}
        </p>
        <div class="flex justify-end space-x-4">
          <button
            @click="closeDeleteModal"
            class="px-4 py-2 text-gray-700 hover:text-gray-900"
          >
            Отмена
          </button>
          <button
            @click="handleDelete"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
            :disabled="loading"
          >
            {{ loading ? 'Удаление...' : 'Удалить' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useNotification } from '@/composables/useNotification'

interface Category {
  id: number
  name: string
  parent_id: number | null
  children?: Category[]
  isExpanded?: boolean
}

interface FormData {
  name: string
  parent_id: number | null
}

const notification = useNotification()
const loading = ref(false)
const categories = ref<Category[]>([])
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingCategory = ref<Category | null>(null)
const categoryToDelete = ref<Category | null>(null)

const form = ref<FormData>({
  name: '',
  parent_id: null
})

// Доступные родительские категории (исключая текущую и её подкатегории)
const availableParents = computed(() => {
  if (!editingCategory.value) return categories.value

  const excludeIds = new Set<number>()
  const addToExclude = (category: Category) => {
    excludeIds.add(category.id)
    category.children?.forEach(addToExclude)
  }
  addToExclude(editingCategory.value)

  return categories.value.filter(category => !excludeIds.has(category.id))
})

// Загрузка категорий
const loadCategories = async () => {
  try {
    loading.value = true
    const response = await fetch(`api/categories`)
    if (!response.ok) throw new Error('Failed to load categories')
    const data = await response.json()
    categories.value = buildCategoryTree(data)
  } catch (error) {
    notification.error('Ошибка', 'Не удалось загрузить категории')
    console.error('Error loading categories:', error)
  } finally {
    loading.value = false
  }
}

// Построение дерева категорий
const buildCategoryTree = (categories: Category[]): Category[] => {
  const categoryMap = new Map<number, Category>()
  const tree: Category[] = []

  categories.forEach(category => {
    categoryMap.set(category.id, { ...category, children: [], isExpanded: false })
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

// Открытие модального окна создания
const openCreateModal = () => {
  editingCategory.value = null
  form.value = {
    name: '',
    parent_id: null
  }
  showModal.value = true
}

// Открытие модального окна редактирования
const openEditModal = (category: Category) => {
  editingCategory.value = category
  form.value = {
    name: category.name,
    parent_id: category.parent_id
  }
  showModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
  showModal.value = false
  editingCategory.value = null
  form.value = {
    name: '',
    parent_id: null
  }
}

// Подтверждение удаления
const confirmDelete = (category: Category) => {
  categoryToDelete.value = category
  showDeleteModal.value = true
}

// Закрытие модального окна удаления
const closeDeleteModal = () => {
  showDeleteModal.value = false
  categoryToDelete.value = null
}

// Переключение отображения подкатегорий
const toggleCategory = (category: Category) => {
  category.isExpanded = !category.isExpanded
}

// Отправка формы
const handleSubmit = async () => {
  try {
    loading.value = true
    const url = editingCategory.value
      ? `api/categories/${editingCategory.value.id}`
      : `api/categories`
    const method = editingCategory.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(form.value)
    })

    if (!response.ok) throw new Error('Failed to save category')

    notification.success(
      'Успешно',
      `Категория ${editingCategory.value ? 'обновлена' : 'создана'}`
    )
    closeModal()
    loadCategories()
  } catch (error) {
    notification.error('Ошибка', 'Не удалось сохранить категорию')
    console.error('Error saving category:', error)
  } finally {
    loading.value = false
  }
}

// Удаление категории
const handleDelete = async () => {
  if (!categoryToDelete.value) return

  try {
    loading.value = true
    const response = await fetch(`api/categories/${categoryToDelete.value.id}`, {
      method: 'DELETE'
    })

    if (!response.ok) throw new Error('Failed to delete category')

    notification.success('Успешно', 'Категория удалена')
    closeDeleteModal()
    loadCategories()
  } catch (error) {
    notification.error('Ошибка', 'Не удалось удалить категорию')
    console.error('Error deleting category:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.category-item {
  position: relative;
}
</style>
