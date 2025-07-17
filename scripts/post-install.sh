#!/bin/sh

echo "Installing Tiltup Crypto Payments Module"
php bin/console prestashop:module install tiltupcryptopaymentsmodule
echo "Configuring Tiltup Crypto Payments Module"
php bin/console prestashop:module configure tiltupcryptopaymentsmodule

echo "Uninstalling Customer Reassurance module, because it was showing some annoying messages"
php bin/console prestashop:module uninstall blockreassurance

echo "Cleaning up cache and logs..."
php bin/console cache:clear
rm -rf var/cache/*
rm -rf var/logs

echo "post-install script completed successfully"
