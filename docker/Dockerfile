FROM wordpress:latest

COPY --chown=www-data:www-data ./bin/tawkto /var/www/html/wp-content/plugins/tawkto

WORKDIR /var/www/html

CMD ["apache2-foreground"]
