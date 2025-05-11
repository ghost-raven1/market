<template>
  <div class="bg-white shadow rounded-lg p-4 sm:p-6">
    <!-- Add Category Form -->
    <div class="mb-8">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Добавить категорию</h2>
      <form @submit.prevent="createCategory" class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Название</label>
          <input
            type="text"
            id="name"
            v-model="newCategory.name"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
          />
        </div>
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Описание</label>
          <textarea
            id="description"
            v-model="newCategory.description"
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          ></textarea>
        </div>
        <div class="flex justify-end">
          <button
            type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="loading"
          >
            {{ loading ? 'Сохранение...' : 'Добавить категорию' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Categories List -->
    <div>
      <h2 class="text-lg font-medium text-gray-900 mb-4">Список категорий</h2>
      <!-- Mobile view -->
      <div class="sm:hidden space-y-4">
        <div
          v-for="category in categories"
          :key="category.id"
          class="bg-white shadow rounded-lg p-4"
        >
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-sm font-medium text-gray-900">{{ category.name }}</h3>
              <p class="mt-1 text-sm text-gray-500">{{ category.description }}</p>
              <p class="mt-2 text-sm text-gray-500">
                Объявлений: {{ category.advertisements_count }}
              </p>
            </div>
            <div class="flex space-x-2">
              <button
                @click="editCategory(category)"
                class="text-indigo-600 hover:text-indigo-900"
              >
                <PencilIcon class="h-5 w-5" />
              </button>
              <button
                @click="deleteCategory(category)"
                class="text-red-600 hover:text-red-900"
                :disabled="category.advertisements_count > 0"
              >
                <TrashIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Desktop view -->
      <div class="hidden sm:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Название
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Описание
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Количество объявлений
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="category in categories" :key="category.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-500">{{ category.description }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ category.advertisements_count }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="editCategory(category)"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Редактировать
                </button>
                <button
                  @click="deleteCategory(category)"
                  class="text-red-600 hover:text-red-900"
                  :disabled="category.advertisements_count > 0"
                >
                  Удалить
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Edit Category Modal -->
    <div v-if="showEditModal && editingCategory" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg p-4 sm:p-6 max-w-md w-full">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Редактировать категорию</h3>
        <form @submit.prevent="updateCategory" class="space-y-4">
          <div>
            <label for="edit-name" class="block text-sm font-medium text-gray-700">Название</label>
            <input
              type="text"
              id="edit-name"
              v-model="editingCategory.name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              required
            />
          </div>
          <div>
            <label for="edit-description" class="block text-sm font-medium text-gray-700">Описание</label>
            <textarea
              id="edit-description"
              v-model="editingCategory.description"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="showEditModal = false"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Отмена
            </button>
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :disabled="loading"
            >
              {{ loading ? 'Сохранение...' : 'Сохранить' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmModal
      :is-visible="showDeleteModal"
      title="Удаление категории"
      message="Вы уверены, что хотите удалить эту категорию? Это действие нельзя будет отменить."
      type="warning"
      confirm-text="Удалить"
      :loading="loading"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useNotification } from '@/composables/useNotification'
import { PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import ConfirmModal from '@/components/common/ConfirmModal.vue'

interface Category {
  id: number
  name: string
  description: string
  advertisements_count: number
}

const categories = ref<Category[]>([])
const loading = ref(false)
const showEditModal = ref(false)
const editingCategory = ref<Category | null>(null)
const notification = useNotification()
const showDeleteModal = ref(false)
const categoryToDelete = ref<Category | null>(null)

const newCategory = ref({
  name: '',
  description: ''
})

const fetchCategories = async () => {
  try {
    const response = await axios.get(`api/categories`)
    categories.value = response.data.data
  } catch (err) {
    console.error('Error fetching categories:', err)
    notification.error(
      'Ошибка загрузки',
      'Не удалось загрузить список категорий'
    )
  }
}

const createCategory = async () => {
  try {
    loading.value = true
    await axios.post(`api/categories`, newCategory.value)
    await fetchCategories()
    newCategory.value = { name: '', description: '' }
    notification.success(
      'Категория создана',
      'Новая категория успешно добавлена'
    )
  } catch (err: any) {
    console.error('Error creating category:', err)
    if (err.response?.data?.errors) {
      notification.error(
        'Ошибка валидации',
        'Пожалуйста, проверьте введенные данные'
      )
    } else {
      notification.error(
        'Ошибка',
        'Не удалось создать категорию'
      )
    }
  } finally {
    loading.value = false
  }
}

const editCategory = (category: Category) => {
  editingCategory.value = { ...category }
  showEditModal.value = true
}

const updateCategory = async () => {
  if (!editingCategory.value) return

  try {
    loading.value = true
    await axios.patch(`api/categories/${editingCategory.value.id}`, {
      name: editingCategory.value.name,
      description: editingCategory.value.description
    })
    await fetchCategories()
    showEditModal.value = false
    notification.success(
      'Категория обновлена',
      'Категория успешно обновлена'
    )
  } catch (err: any) {
    console.error('Error updating category:', err)
    if (err.response?.data?.errors) {
      notification.error(
        'Ошибка валидации',
        'Пожалуйста, проверьте введенные данные'
      )
    } else {
      notification.error(
        'Ошибка',
        'Не удалось обновить категорию'
      )
    }
  } finally {
    loading.value = false
  }
}

const deleteCategory = (category: Category) => {
  categoryToDelete.value = category
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!categoryToDelete.value) return

  try {
    loading.value = true
    await axios.delete(`api/categories/${categoryToDelete.value.id}`)
    await fetchCategories()
    showDeleteModal.value = false
    categoryToDelete.value = null
    notification.success(
      'Категория удалена',
      'Категория успешно удалена'
    )
  } catch (err: any) {
    console.error('Error deleting category:', err)
    if (err.response?.status === 409) {
      notification.error(
        'Ошибка удаления',
        'Нельзя удалить категорию, содержащую объявления'
      )
    } else {
      notification.error(
        'Ошибка',
        'Не удалось удалить категорию'
      )
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCategories()
})
</script>
