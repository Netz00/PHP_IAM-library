FROM php:7.3.28-apache


RUN docker-php-ext-install mysqli

RUN a2enmod rewrite &&\
    service apache2 restart
