FROM laradock/php-fpm:latest-8.0

RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
      apt-utils \
      gnupg2 \
      git \
      libzip-dev zip unzip && \
    docker-php-ext-configure zip; \
    docker-php-ext-install zip && \
    php -m | grep -q 'zip'


RUN pecl install -o -f redis \
	&& rm -rf /tmp/pear \
    && docker-php-ext-enable redis

RUN docker-php-ext-install bcmath exif opcache mysqli

# Copy opcache configration
COPY ./dev-config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

RUN apt-get install -yqq jpegoptim optipng pngquant gifsicle

# install imagick
USER root
RUN apt-get install -yqq libmagickwand-dev imagemagick && \
	cd /tmp && \
	git clone https://github.com/Imagick/imagick && \
	cd imagick && \
	phpize && \
	./configure && \
	make && \
	make install && \
	rm -r /tmp/imagick && \
    docker-php-ext-enable imagick; \
    php -m | grep -q 'imagick'

# Install cachetool
RUN curl http://gordalina.github.io/cachetool/downloads/cachetool-3.2.1.phar -o cachetool.phar && \
	chmod +x cachetool.phar && \
    mv cachetool.phar /usr/local/bin/cachetool

# Copy php config
COPY ./dev-config/php.ini /usr/local/etc/php/
COPY ./dev-config/laravel.ini /usr/local/etc/php/conf.d
COPY ./dev-config/xlaravel.pool.conf /usr/local/etc/php-fpm.d/

# Install Composer
RUN curl --silent --show-error "https://getcomposer.org/installer" | php -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel Envoy
RUN composer global require "laravel/envoy=~1.0"

# install node
RUN curl -s https://deb.nodesource.com/setup_16.x | bash
RUN apt install nodejs -y

USER root

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Configure locale.
ARG LOCALE=POSIX
ENV LC_ALL ${LOCALE}

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
