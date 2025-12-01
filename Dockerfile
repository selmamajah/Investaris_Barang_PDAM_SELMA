# --- stage 1: build frontend assets (Vite + Tailwind) ---
FROM node:18 AS nodebuild
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# --- stage 2: php + nginx ---
FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html

COPY . /var/www/html

# copy hasil build Vite ke public/build
COPY --from=nodebuild /app/public/build /var/www/html/public/build

RUN composer install --no-dev --optimize-autoloader
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

ENV WEBROOT=/var/www/html/public
