#!/bin/bash

# Update system packages
apt-get update && apt-get upgrade -y

# Install required dependencies
apt-get install -y \
    nginx \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    mariadb-server \
    composer \
    ffmpeg \
    supervisor \
    git

# Configure MySQL
mysql -e "CREATE DATABASE IF NOT EXISTS rtsp_gateway;"
mysql -e "CREATE USER IF NOT EXISTS 'rtsp_user'@'localhost' IDENTIFIED BY 'rtsp_password';"
mysql -e "GRANT ALL PRIVILEGES ON rtsp_gateway.* TO 'rtsp_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Configure PHP
sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.2/fpm/php.ini
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 64M/' /etc/php/8.2/fpm/php.ini

# Install Laravel dependencies
composer install --no-dev --optimize-autoloader

# Set up environment file
cp .env.example .env
php artisan key:generate

# Configure environment variables
sed -i 's/DB_DATABASE=.*/DB_DATABASE=rtsp_gateway/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=rtsp_user/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=rtsp_password/' .env

# Set up application
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set up Supervisor for FFmpeg processes
cat > /etc/supervisor/conf.d/rtsp-gateway.conf << EOL
[program:rtsp-gateway-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/rtsp-gateway/artisan queue:work
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/rtsp-gateway/storage/logs/worker.log
stopwaitsecs=3600
EOL

# Set up log rotation
cat > /etc/logrotate.d/rtsp-gateway << EOL
/var/www/rtsp-gateway/storage/logs/*.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
EOL

# Restart services
systemctl restart php8.2-fpm
systemctl restart nginx
supervisorctl reread
supervisorctl update
supervisorctl start all

echo "Installation completed successfully!"