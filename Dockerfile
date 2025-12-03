# --- stage 1: build frontend assets (Vite + Tailwind) ---
FROM node:18 AS nodebuild
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# --- stage 2: php + nginx ---
FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html

COPY . /var/www/html
COPY --from=nodebuild /app/public/build /var/www/html/public/build

RUN composer install --no-dev --optimize-autoloader

# set permission laravel
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan config:cache
# RUN php artisan route:cache   # aktifkan lagi setelah route duplikat beres
RUN php artisan view:cache

ENV WEBROOT=/var/www/html/public
