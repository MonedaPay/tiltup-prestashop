#!/bin/sh

echo "Installing Tiltup Crypto Payments Module"
php bin/console prestashop:module install tiltupcryptopaymentsmodule
rm -rf /var/www/html/var/cache/dev
rm -rf /var/www/html/var/logs