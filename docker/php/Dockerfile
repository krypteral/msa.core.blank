FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
libbz2-dev \
libexif-dev \
libfreetype6-dev \
libjpeg62-turbo-dev \
libpng-dev \
git \
unzip \
libzip-dev \
&& docker-php-ext-install zip bz2 exif mysqli \
&& pecl install msgpack \
&& docker-php-ext-enable msgpack \
&& docker-php-ext-configure gd \
&& docker-php-ext-install -j$(nproc) gd

RUN apt-get install -y cron

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/php/cronfile.sh /tmp/cronfile.sh
RUN chmod +x /tmp/cronfile.sh && sed -i 's/\r//' /tmp/cronfile.sh && /tmp/cronfile.sh