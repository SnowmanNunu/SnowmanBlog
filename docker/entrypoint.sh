#!/bin/sh
set -e

cd /var/www/html

# 安装 PHP 依赖（如果 vendor 不存在）
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# 生成应用密钥
if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate
fi

# 运行数据库迁移
echo "Running database migrations..."
php artisan migrate --force

# 缓存配置和路由
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 创建存储软链接
echo "Creating storage link..."
php artisan storage:link || true

# 设置权限
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# 启动 Nginx 和 PHP-FPM
echo "Starting services..."
nginx
php-fpm
