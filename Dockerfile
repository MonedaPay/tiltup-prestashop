FROM prestashop/prestashop:1.7.8.9-7.4-apache

COPY ./module /var/www/html/modules/tiltupcryptopaymentsmodule
COPY ./scripts /tmp/post-install-scripts