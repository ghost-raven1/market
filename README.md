# Marketplace MVP

A marketplace platform similar to Avito, built with Vue 3 and Laravel.

## Project Structure

```
marketplace/
├── frontend/          # Vue 3 frontend application
├── backend/           # Laravel backend application
└── docker/           # Docker configuration files
```

## Features

- User authentication and authorization
- Advertisement management (CRUD operations)
- Image upload (up to 5 images per ad)
- Category management
- Favorites system
- Real-time chat
- Rating system
- User profiles
- Interactive map
- Admin panel
- VIP advertisements

## Tech Stack

### Frontend
- Vue 3 with Composition API
- TypeScript
- Pinia for state management
- Vue Router
- Vuetify/Tailwind CSS
- Axios for API requests
- Leaflet for maps

### Backend
- Laravel
- Laravel Orion for REST API
- Laravel Sanctum for authentication
- PostgreSQL database

### Infrastructure
- Docker & Docker Compose
- Nginx
- PostgreSQL

## Getting Started

1. Clone the repository
2. Copy `.env.example` to `.env` in both frontend and backend directories
3. Run `docker-compose up -d`
4. Access the application at `http://localhost:8080`

## Development

### Frontend Development
```bash
cd frontend
npm install
npm run dev
```

### Backend Development
```bash
cd backend
composer install
php artisan serve
```

## API Documentation

API documentation is available at `/api/documentation` when running the backend server.

## License

MIT 