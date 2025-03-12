FROM php:8.2-fpm


RUN apt-get update && apt-get install -y vim git unzip  default-mysql-client   && docker-php-ext-install pdo_mysql  
# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer


# enable mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

WORKDIR /var/www


