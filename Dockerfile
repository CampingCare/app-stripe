FROM composer:2.4.4 as build
WORKDIR /app
COPY . /app
RUN composer install && composer dumpautoload

FROM php:8.2-apache
RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 8000
COPY --from=build /app /var/www/
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN chmod 777 -R /var/www/storage/ && \
  echo "Listen 8000">>/etc/apache2/ports.conf && \
  chown -R www-data:www-data /var/www/ && \
  a2enmod rewrite
  