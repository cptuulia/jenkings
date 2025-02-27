# to ruiild, stop and delte this container and run
# > docker-compose build --no-cache
# > docker-compose up -d
FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/
COPY docker.conf /usr/local/etc/php-fpm.d/docker.conf
COPY .bashrc /root/

# Enable x-debug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
# to disable x-debug
# > vi /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#
# Put the first line into comments (#zend_extension=xdebug) in enable mode it is (zend_extension=xdebug)
# #zend_extension=xdebug
# xdebug.mode=develop,coverage,debug,profile
# xdebug.idekey=docker
# xdebug.start_with_request=yes
# xdebug.log=/dev/stdout
# xdebug.log_level=0
# xdebug.client_port=9003
# xdebug.client_host=host.docker.internal

#./scripts/phpunit.sh Tests/Integration/
# and restart the php container
#
# See more https://xdebug.org/docs/all_settingsß


RUN apt-get update \
  && apt-get install -y build-essential zlib1g-dev default-mysql-client curl gnupg procps vim git unzip libzip-dev libpq-dev \
  && docker-php-ext-install zip pdo_mysql pdo_pgsql pgsql

RUN apt-get install -y libicu-dev \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

# pcov
RUN pecl install pcov && docker-php-ext-enable pcov

WORKDIR /root
RUN git clone https://github.com/seebi/dircolors-solarized

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin
RUN composer config --global process-timeout 3600


RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli



EXPOSE 5173
WORKDIR /var/www


