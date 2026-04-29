FROM php:8.2-fpm-alpine

LABEL maintainer="SnowmanNunu"

# 安装系统依赖和 PHP 扩展
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    curl \
    git \
    unzip \
    tzdata \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        bcmath \
        intl \
        mbstring \
        xml \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 设置时区
ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 复制 PHP 配置
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# 复制 Nginx 配置
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# 创建工作目录
WORKDIR /var/www/html

# 复制项目代码
COPY . /var/www/html

# 设置权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# 复制入口脚本
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 暴露端口
EXPOSE 80

# 入口脚本
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
