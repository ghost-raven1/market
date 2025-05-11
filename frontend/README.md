# Market Frontend

## Установка и настройка

### Требования
- Node.js 16 или выше
- npm или yarn
- Docker и Docker Compose (для production)

### Настройка окружения

1. Скопируйте файл `.env.example` в `.env`:
```bash
cp .env.example .env
```

2. Настройте переменные окружения в файле `.env`:
```
VITE_API_URL=http://localhost/api
```

### Разработка

1. Установите зависимости:
```bash
npm install
# или
yarn install
```

2. Запустите сервер разработки:
```bash
npm run dev
# или
yarn dev
```

### Сборка для production

1. Соберите проект:
```bash
npm run build
# или
yarn build
```

2. Запустите через Docker:
```bash
docker-compose up -d
```

## Структура проекта

```
frontend/
├── src/
│   ├── assets/
│   │   ├── common/
│   │   └── layout/
│   ├── components/
│   │   ├── common/
│   │   └── layout/
│   ├── composables/
│   ├── router/
│   ├── services/
│   ├── stores/
│   ├── types/
│   └── views/
├── public/
└── docker/
    └── nginx.conf
```

## Компоненты

### Общие компоненты
- `NotificationContainer.vue` - Контейнер для уведомлений
- `LoadingSpinner.vue` - Индикатор загрузки
- `ErrorBoundary.vue` - Обработчик ошибок

### Представления
- `HomeView.vue` - Главная страница
- `LoginView.vue` - Страница входа
- `RegisterView.vue` - Страница регистрации
- `ProfileView.vue` - Профиль пользователя
- `AdvertisementView.vue` - Просмотр объявления
- `CreateAdvertisementView.vue` - Создание объявления
- `EditAdvertisementView.vue` - Редактирование объявления

## Состояние приложения

### Хранилища (Stores)
- `auth.ts` - Управление аутентификацией
- `advertisements.ts` - Управление объявлениями

### Composables
- `useNotifications.ts` - Управление уведомлениями
- `useAuth.ts` - Хуки для аутентификации
- `useAdvertisements.ts` - Хуки для работы с объявлениями

## Стилизация

Проект использует:
- Tailwind CSS для стилей
- Heroicons для иконок
- Vue 3 Composition API
- TypeScript

## Тестирование

### Запуск тестов
```bash
npm run test
# или
yarn test
```

### Линтинг
```bash
npm run lint
# или
yarn lint
```

### Форматирование кода
```bash
npm run format
# или
yarn format
```

## Развертывание

### Production сборка
1. Соберите проект:
```bash
npm run build
```

2. Настройте Nginx:
- Скопируйте `nginx.conf` в контейнер
- Настройте SSL сертификаты (опционально)

3. Запустите через Docker:
```bash
docker-compose up -d
```

### SSL Настройка
Для настройки HTTPS следуйте инструкциям в документации бэкенда.

## Лицензия

MIT 