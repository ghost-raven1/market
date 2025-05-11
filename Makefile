.PHONY: help install build start stop restart logs clean backup migrate test lint deploy ssl cdn status remaining meta analytics monitoring tracing

# Variables
DOCKER_COMPOSE = docker-compose
BACKEND_DIR = backend
FRONTEND_DIR = frontend

help: ## Показать справку по командам
	@echo "Доступные команды:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Установить зависимости
	@echo "Установка зависимостей..."
	cd $(BACKEND_DIR) && composer install
	cd $(FRONTEND_DIR) && npm install

build: ## Собрать Docker контейнеры
	@echo "Сборка контейнеров..."
	$(DOCKER_COMPOSE) build

start: ## Запустить приложение
	@echo "Запуск приложения..."
	$(DOCKER_COMPOSE) up -d

stop: ## Остановить приложение
	@echo "Остановка приложения..."
	$(DOCKER_COMPOSE) down

restart: stop start ## Перезапустить приложение

logs: ## Показать логи
	@echo "Просмотр логов..."
	$(DOCKER_COMPOSE) logs -f

clean: ## Очистить кэш и временные файлы
	@echo "Очистка кэша..."
	$(DOCKER_COMPOSE) exec backend php artisan cache:clear
	$(DOCKER_COMPOSE) exec backend php artisan config:clear
	$(DOCKER_COMPOSE) exec backend php artisan route:clear
	$(DOCKER_COMPOSE) exec backend php artisan view:clear
	cd $(FRONTEND_DIR) && rm -rf node_modules/.cache

backup: ## Создать резервную копию
	@echo "Создание резервной копии..."
	$(DOCKER_COMPOSE) exec backend /var/www/html/scripts/backup.sh

migrate: ## Запустить миграции базы данных
	@echo "Запуск миграций..."
	$(DOCKER_COMPOSE) exec backend php artisan migrate

test: ## Запустить тесты
	@echo "Запуск тестов..."
	$(DOCKER_COMPOSE) exec backend php artisan test
	cd $(FRONTEND_DIR) && npm run test

lint: ## Проверить код линтером
	@echo "Проверка кода..."
	$(DOCKER_COMPOSE) exec backend composer run-script lint
	cd $(FRONTEND_DIR) && npm run lint

deploy: ## Развернуть приложение
	@echo "Развертывание приложения..."
	git pull
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) up -d --build
	$(DOCKER_COMPOSE) exec backend php artisan migrate --force
	$(DOCKER_COMPOSE) exec backend php artisan config:cache
	$(DOCKER_COMPOSE) exec backend php artisan route:cache

ssl: ## Настроить SSL сертификаты (не работает)
	@echo "Настройка SSL сертификатов..."
	chmod +x scripts/init-letsencrypt.sh
	./scripts/init-letsencrypt.sh

cdn: ## Настроить CDN
	@echo "Настройка CDN..."
	chmod +x scripts/setup-cdn.sh
	./scripts/setup-cdn.sh

monitor: ## Открыть мониторинг
	@echo "Открытие Grafana..."
	open http://localhost:3000
	@echo "Открытие Prometheus..."
	open http://localhost:9090

db: ## Открыть консоль базы данных
	@echo "Открытие консоли MySQL..."
	$(DOCKER_COMPOSE) exec db mysql -u market -p

shell: ## Открыть shell в backend контейнере
	@echo "Открытие shell в backend контейнере..."
	$(DOCKER_COMPOSE) exec backend sh

status: ## Показать статус проекта
	@echo "\033[1;34m=== Статус контейнеров ===\033[0m"
	@$(DOCKER_COMPOSE) ps
	@echo "\n\033[1;34m=== Использование ресурсов ===\033[0m"
	@docker stats --no-stream
	@echo "\n\033[1;34m=== Последние логи ===\033[0m"
	@$(DOCKER_COMPOSE) logs --tail=20
	@echo "\n\033[1;34m=== Статус базы данных ===\033[0m"
	@$(DOCKER_COMPOSE) exec -T db mysql -u market -pmarket -e "SHOW STATUS LIKE 'Threads_connected';" market
	@echo "\n\033[1;34m=== Статус бэкапов ===\033[0m"
	@ls -lh ./backups 2>/dev/null || echo "Нет доступных бэкапов"
	@echo "\n\033[1;34m=== Статус SSL ===\033[0m"
	@$(DOCKER_COMPOSE) exec nginx nginx -t
	@echo "\n\033[1;34m=== Статус мониторинга ===\033[0m"
	@curl -s http://localhost:9090/-/healthy || echo "Prometheus недоступен"
	@curl -s http://localhost:3000/api/health || echo "Grafana недоступна"

analytics: ## Настроить аналитику
	@echo "\033[1;34m=== Настройка аналитики ===\033[0m"
	@echo "1. Добавление Google Analytics..."
	@echo "2. Настройка целей и событий..."
	@echo "3. Настройка e-commerce отслеживания..."
	@echo "\nДля применения изменений выполните:"
	@echo "make build && make restart"

monitoring: ## Настроить мониторинг
	@echo "\033[1;34m=== Настройка мониторинга ===\033[0m"
	@echo "1. Настройка алертов в Grafana..."
	@echo "   - CPU > 80%"
	@echo "   - Memory > 85%"
	@echo "   - Disk > 90%"
	@echo "   - Error rate > 5%"
	@echo "2. Настройка дашбордов..."
	@echo "3. Настройка уведомлений..."
	@echo "\nДля применения изменений выполните:"
	@echo "docker-compose restart grafana prometheus"

tracing: ## Настроить трейсинг и логирование
	@echo "\033[1;34m=== Настройка трейсинга и логирования ===\033[0m"
	@echo "1. Настройка логирования ошибок..."
	@echo "   - Добавление Sentry"
	@echo "   - Настройка ротации логов"
	@echo "2. Настройка трейсинга запросов..."
	@echo "   - Добавление Jaeger"
	@echo "   - Настройка sampling"
	@echo "3. Настройка метрик..."
	@echo "   - Request duration"
	@echo "   - Error rates"
	@echo "   - Database queries"
	@echo "\nДля применения изменений выполните:"
	@echo "make build && make restart"

.DEFAULT_GOAL := help 