FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Xdebug (for coverage in CI)
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/backend

# Copy composer files first (layer caching)
COPY composer.json ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader --no-dev --ignore-platform-reqs

# Copy application files
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --ignore-platform-reqs

# Set permissions
RUN chown -R www-data:www-data /var/www/backend/storage \
    && chmod -R 775 /var/www/backend/storage \
    && chown -R www-data:www-data /var/www/backend/bootstrap/cache

# PHP-FPM config
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000

CMD ["php-fpm"]
