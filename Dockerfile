# syntax=docker/dockerfile:1

# =============================================================================
# Stage 1 — Build aset frontend (Vite + Tailwind + Alpine)
# Menghasilkan public/build yang dipakai oleh Laravel di runtime.
# =============================================================================
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# =============================================================================
# Stage 2 — Runtime PHP (Laravel 12 / PHP 8.3)
# =============================================================================
FROM php:8.3-cli-bookworm AS app

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    DEBIAN_FRONTEND=noninteractive

# Ekstensi PHP yang dibutuhkan:
#   pdo_mysql  -> koneksi MySQL
#   mbstring   -> Laravel + DomPDF + PhpSpreadsheet
#   zip        -> PhpSpreadsheet (baca/tulis xlsx)
#   gd         -> DomPDF (gambar) + PhpSpreadsheet
#   bcmath     -> perhitungan presisi (PhpSpreadsheet)
#   exif       -> validasi/proses gambar upload
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip \
        libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip gd bcmath exif \
    && rm -rf /var/lib/apt/lists/*

# Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Limit upload PHP (foto/dokumen dari HP bisa besar) — lihat docker/uploads.ini.
COPY docker/uploads.ini /usr/local/etc/php/conf.d/zz-uploads.ini

WORKDIR /var/www/html

# Kode aplikasi + dependency PHP (vendor dibangun ulang di dalam image)
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint (tunggu DB -> migrate/seed -> serve)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
