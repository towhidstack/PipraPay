# syntax=docker/dockerfile:1

# PipraPay — Dokploy production (Build type: Dockerfile, Port: 8080)
# See DOKPLOY.md. Link MariaDB and set DB_* in Environment (or use web installer).

FROM php:8.3-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        gettext-base \
        git \
        imagemagick \
        libmagickwand-dev \
        libmagickcore-dev \
        libgomp1 \
        libcurl4-openssl-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libwebp-dev \
        libzip-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql zip bcmath opcache \
    && rm -f /etc/nginx/sites-enabled/default

COPY docker/php/install-imagick.sh /tmp/install-imagick.sh
RUN chmod +x /tmp/install-imagick.sh \
    && bash /tmp/install-imagick.sh \
    && rm -f /tmp/install-imagick.sh \
    && apt-get purge -y --auto-remove \
        $PHPIZE_DEPS \
        git \
        libmagickwand-dev \
        libmagickcore-dev \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php/99-piprapay.ini /usr/local/etc/php/conf.d/99-piprapay.ini
COPY docker/php/zz-www.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY docker/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

WORKDIR /app

COPY . .

RUN mkdir -p pp-media/storage \
    && chmod +x /usr/local/bin/docker-entrypoint.sh \
        docker/fix-storage-permissions.sh \
        docker/production-start.sh \
        docker/coolify-start.sh \
        docker/write-pp-config-from-env.sh \
    && chown -R www-data:www-data /app

EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
