# Share Space - Self-Hosted Backend
# Supports linux/amd64 and linux/arm64 (Raspberry Pi 4/5)
FROM php:8.2-apache

# Install system dependencies
# - libcurl4-openssl-dev: for PHP curl extension
# - libpng-dev, libjpeg-dev, libwebp-dev: for PHP GD extension (image processing)
# - libmagickwand-dev: for PHP Imagick extension (thumbnail generation)
# - libheif-dev, libheif-examples: for HEIC/HEIF image conversion
#   (heif-convert command used by maestroerror/php-heic-to-jpg)
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libmagickwand-dev \
    libheif-dev \
    libheif-examples \
    openssl \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install curl gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html/

# Install PHP dependencies (creates vendor/ and composer.json)
RUN cd /var/www/html && composer require maestroerror/php-heic-to-jpg --no-interaction --no-progress

# Create the data directory with correct ownership.
# This is overridden at runtime by the mounted volume, but the directory
# must exist in the image for the entrypoint script to reference it.
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data \
    && chown -R www-data:www-data /var/www/html

# Allow .htaccess overrides (needed for clean URL handling if added later)
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && a2enmod rewrite

# Entrypoint generates config.php from environment variables on each start
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
