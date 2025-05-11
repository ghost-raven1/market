# Deployment Guide

## Prerequisites

- Docker and Docker Compose installed
- Domain name pointing to your server
- Cloudflare account (for CDN)
- Let's Encrypt account (for SSL)

## Initial Setup

1. Clone the repository:
```bash
git clone https://github.com/your-username/market.git
cd market
```

2. Configure environment variables:
```bash
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```

3. Update the following variables in the configuration files:
- `backend/.env`: Database credentials, app URL, etc.
- `frontend/.env`: API URL
- `nginx/conf.d/default.conf`: Your domain name
- `scripts/init-letsencrypt.sh`: Your domain and email
- `scripts/setup-cdn.sh`: Cloudflare API token and zone ID

## SSL Setup

1. Make the initialization script executable:
```bash
chmod +x scripts/init-letsencrypt.sh
```

2. Run the SSL initialization:
```bash
./scripts/init-letsencrypt.sh
```

## CDN Setup

1. Make the CDN setup script executable:
```bash
chmod +x scripts/setup-cdn.sh
```

2. Run the CDN configuration:
```bash
./scripts/setup-cdn.sh
```

## Starting the Application

1. Build and start the containers:
```bash
docker-compose up -d
```

2. Run database migrations:
```bash
docker-compose exec backend php artisan migrate
```

3. Generate application key:
```bash
docker-compose exec backend php artisan key:generate
```

4. Create storage link:
```bash
docker-compose exec backend php artisan storage:link
```

## Monitoring Setup

1. Access Grafana at `https://your-domain.com:3000`
   - Default credentials: admin/admin
   - Change the default password on first login

2. Access Prometheus at `https://your-domain.com:9090`

3. Configure alerts in Grafana:
   - Import the provided dashboard
   - Set up notification channels
   - Configure alert rules

## Backup Configuration

1. Make the backup script executable:
```bash
chmod +x backend/scripts/backup.sh
```

2. The backup script is configured to run daily at 2 AM
   - Backups are stored in the `./backups` directory
   - Retention period is set to 7 days

## Maintenance

### Updating the Application

1. Pull the latest changes:
```bash
git pull
```

2. Rebuild and restart containers:
```bash
docker-compose down
docker-compose up -d --build
```

### Monitoring Logs

```bash
# All containers
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f nginx
```

### Backup Management

1. Manual backup:
```bash
docker-compose exec backend /var/www/html/scripts/backup.sh
```

2. List backups:
```bash
ls -l ./backups
```

## Troubleshooting

### SSL Issues
- Check certbot logs: `docker-compose logs certbot`
- Verify SSL configuration: `docker-compose exec nginx nginx -t`

### Database Issues
- Check MySQL logs: `docker-compose logs db`
- Access MySQL console: `docker-compose exec db mysql -u market -p`

### Application Issues
- Check Laravel logs: `docker-compose exec backend tail -f storage/logs/laravel.log`
- Check Nginx logs: `docker-compose exec nginx tail -f /var/log/nginx/error.log`

## Security Considerations

1. Change all default passwords
2. Keep all dependencies updated
3. Regularly check security advisories
4. Monitor system logs for suspicious activity
5. Keep SSL certificates up to date
6. Regularly backup your data
7. Use strong firewall rules
8. Enable rate limiting
9. Use secure headers
10. Implement proper CORS policies 