FROM prestashop/prestashop:1.7.8.9-7.4-apache

# Use development php.ini
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# XDebug extension
RUN pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug

# Enable XDebug
RUN echo '[XDebug] \n\
xdebug.mode=develop,debug \n\
xdebug.discover_client_host=1 \n\
xdebug.client_host=host.docker.internal \n\
xdebug.start_with_request=yes' >> /usr/local/etc/php/php.ini