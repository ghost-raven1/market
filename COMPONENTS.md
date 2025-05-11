# Документация по компонентам

## Frontend компоненты

### AdvertisementForm.vue
Компонент для создания и редактирования объявлений.

**Props:**
- `advertisement` (Object, optional) - Существующее объявление для редактирования
- `categories` (Array) - Список категорий

**Methods:**
- `handleSubmit()` - Обработка отправки формы
- `handleImageUpload()` - Загрузка изображений
- `removeImage()` - Удаление изображения

### AdvertisementList.vue
Компонент для отображения списка объявлений.

**Props:**
- `filters` (Object) - Параметры фильтрации
- `sortBy` (String) - Поле для сортировки
- `sortOrder` (String) - Порядок сортировки

**Methods:**
- `loadAdvertisements()` - Загрузка объявлений
- `handleFiltersUpdate()` - Обновление фильтров
- `handleSort()` - Сортировка списка

### CategoryManager.vue
Компонент для управления категориями.

**Props:**
- `categories` (Array) - Список категорий

**Methods:**
- `createCategory()` - Создание категории
- `updateCategory()` - Обновление категории
- `deleteCategory()` - Удаление категории
- `validateHierarchy()` - Проверка иерархии категорий

### NotificationList.vue
Компонент для отображения уведомлений.

**Props:**
- `notifications` (Array) - Список уведомлений

**Methods:**
- `markAsRead()` - Отметить уведомление как прочитанное
- `deleteNotification()` - Удалить уведомление

### Statistics.vue
Компонент для отображения статистики.

**Props:**
- `period` (String) - Период статистики
- `metrics` (Object) - Метрики для отображения

**Methods:**
- `loadStatistics()` - Загрузка статистики
- `updatePeriod()` - Обновление периода
- `exportData()` - Экспорт данных

### ContentModeration.vue
Компонент для модерации контента.

**Props:**
- `items` (Array) - Список элементов для модерации
- `filters` (Object) - Параметры фильтрации

**Methods:**
- `approveItem()` - Одобрить элемент
- `rejectItem()` - Отклонить элемент
- `applyFilters()` - Применить фильтры

### AdvancedFilters.vue
Компонент для расширенной фильтрации.

**Props:**
- `filters` (Object) - Текущие фильтры

**Methods:**
- `updateFilters()` - Обновление фильтров
- `resetFilters()` - Сброс фильтров
- `applyFilters()` - Применение фильтров

## Backend компоненты

### Controllers

#### AdvertisementController
Управление объявлениями.

**Methods:**
- `index()` - Получение списка объявлений
- `store()` - Создание объявления
- `show()` - Просмотр объявления
- `update()` - Обновление объявления
- `destroy()` - Удаление объявления

#### CategoryController
Управление категориями.

**Methods:**
- `index()` - Получение списка категорий
- `store()` - Создание категории
- `show()` - Просмотр категории
- `update()` - Обновление категории
- `destroy()` - Удаление категории

#### NotificationController
Управление уведомлениями.

**Methods:**
- `index()` - Получение списка уведомлений
- `markAsRead()` - Отметить как прочитанное
- `destroy()` - Удалить уведомление

#### StatisticsController
Управление статистикой.

**Methods:**
- `index()` - Получение статистики
- `export()` - Экспорт данных

#### ModerationController
Управление модерацией.

**Methods:**
- `index()` - Получение списка для модерации
- `approve()` - Одобрить элемент
- `reject()` - Отклонить элемент

### Models

#### Advertisement
Модель объявления.

**Relations:**
- `category` - Связь с категорией
- `user` - Связь с пользователем
- `images` - Связь с изображениями

#### Category
Модель категории.

**Relations:**
- `parent` - Связь с родительской категорией
- `children` - Связь с дочерними категориями
- `advertisements` - Связь с объявлениями

#### Notification
Модель уведомления.

**Relations:**
- `user` - Связь с пользователем

#### Image
Модель изображения.

**Relations:**
- `advertisement` - Связь с объявлением

### Middleware

#### AuthMiddleware
Проверка аутентификации.

#### RoleMiddleware
Проверка ролей пользователя.

#### ValidationMiddleware
Проверка валидации запросов.

### Services

#### AdvertisementService
Сервис для работы с объявлениями.

**Methods:**
- `create()` - Создание объявления
- `update()` - Обновление объявления
- `delete()` - Удаление объявления
- `search()` - Поиск объявлений

#### CategoryService
Сервис для работы с категориями.

**Methods:**
- `create()` - Создание категории
- `update()` - Обновление категории
- `delete()` - Удаление категории
- `validateHierarchy()` - Проверка иерархии

#### NotificationService
Сервис для работы с уведомлениями.

**Methods:**
- `create()` - Создание уведомления
- `markAsRead()` - Отметить как прочитанное
- `delete()` - Удалить уведомление

#### StatisticsService
Сервис для работы со статистикой.

**Methods:**
- `getMetrics()` - Получение метрик
- `generateReport()` - Генерация отчета
- `exportData()` - Экспорт данных

#### ModerationService
Сервис для работы с модерацией.

**Methods:**
- `getItems()` - Получение элементов
- `approve()` - Одобрение элемента
- `reject()` - Отклонение элемента 