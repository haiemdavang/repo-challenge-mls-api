# ==============================================================================
# STAGE 1: BUILD (Biên dịch và Chuẩn bị Code)
# ==============================================================================
FROM php:8.2-fpm-alpine AS build

# 1. Cài các gói cần thiết để build extensions
RUN apk add --no-cache \
    bash \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip

# 2. Cài PHP Extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    mbstring \
    zip \
    opcache

# 3. Lấy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 4. Copy file cấu hình trước để tận dụng Docker Cache
COPY composer.json composer.lock ./

# 5. Cài đặt vendor (Lưu ý: BỎ cờ --classmap-authoritative ở đây)
# Bước này chỉ tải thư viện về, chưa cần map code vội.
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# 6. Copy toàn bộ Source Code vào image
COPY . .

# 7. [QUAN TRỌNG] Tạo lại danh sách class (Autoload) sau khi đã có code
# Lệnh này sẽ quét toàn bộ thư mục app/ và tạo map chính xác cho ForceJsonResponse
RUN composer dump-autoload --optimize --classmap-authoritative

# 8. Xử lý Cache và Discovery
# Xóa cache cũ lỡ copy từ máy host vào (để tránh lỗi xung đột)
RUN rm -rf bootstrap/cache/*.php
# Chạy discovery để Laravel nhận diện packages
RUN php artisan package:discover --ansi

# ==============================================================================
# STAGE 2: RUNTIME (Chạy ứng dụng - Nhẹ và Sạch)
# ==============================================================================
FROM php:8.2-fpm-alpine

# 1. Cài các thư viện runtime (không cần -dev)
RUN apk add --no-cache \
    icu \
    oniguruma \
    libzip \
    bash

# 2. Copy Extensions đã build từ Stage 1
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /var/www/html

# 3. Copy Source Code hoàn chỉnh từ Stage 1
COPY --from=build /app /var/www/html

# 4. Phân quyền cho thư mục storage và cache
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

# 5. Chuyển sang user non-root để chạy an toàn
USER www-data

EXPOSE 9000

CMD ["php-fpm"]