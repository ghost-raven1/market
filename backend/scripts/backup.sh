#!/bin/bash

# Configuration
BACKUP_DIR="/backups"
DB_NAME="market"
DB_USER="market"
DB_PASSWORD="market"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=7

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/storage/app/public

# Remove old backups
find $BACKUP_DIR -type f -mtime +$RETENTION_DAYS -delete

# Log backup completion
echo "Backup completed at $(date)" >> $BACKUP_DIR/backup.log 