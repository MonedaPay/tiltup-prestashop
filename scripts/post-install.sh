#!/bin/sh

php -d memory_limit=256M
echo "Installing Tiltup Crypto Payments Module"
php bin/console prestashop:module install tiltupcryptopaymentsmodule
echo "Configuring Tiltup Crypto Payments Module"
php bin/console prestashop:module configure tiltupcryptopaymentsmodule


echo "Checking PLN currency status..."
if ! mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT 1 FROM ps_currency WHERE iso_code='PLN' AND active=1" | grep -q 1; then
  echo "PLN currency not found or inactive - activating PLN currency..."
  php bin/console prestashop:currency:add PLN --activate --no-interaction
  echo "PLN currency has been successfully activated"
else
  echo "PLN currency is already active - skipping activation"
fi

echo "Configuring module-currency association for PLN..."
PLN_CURRENCY_ID=$(mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_currency FROM ps_currency WHERE iso_code='PLN' AND active=1" | grep -v id_currency)
TILTUP_MODULE_ID=$(mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_module FROM ps_module WHERE name='tiltupcryptopaymentsmodule'" | grep -v id_module)

if [ -n "$PLN_CURRENCY_ID" ] && [ -n "$TILTUP_MODULE_ID" ]; then
  echo "Found PLN currency ID: $PLN_CURRENCY_ID, setting it as default"
  mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "UPDATE ps_configuration SET value = $PLN_CURRENCY_ID WHERE name = 'PS_CURRENCY_DEFAULT';"

  echo "Found Tiltup module ID: $TILTUP_MODULE_ID"
  if ! mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT 1 FROM ps_module_currency WHERE id_module='$TILTUP_MODULE_ID' AND id_currency='$PLN_CURRENCY_ID'" | grep -q 1; then
    echo "Enabling crypto-payments-module for PLN currency..."
    mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "INSERT INTO ps_module_currency (id_module, id_shop, id_currency) VALUES ('$TILTUP_MODULE_ID', 1, '$PLN_CURRENCY_ID')"
    echo "Successfully enabled crypto-payments-module for PLN currency"
  else
    echo "crypto-payments-module is already enabled for PLN currency - skipping"
  fi
else
  echo "Warning: Could not find PLN currency ID or Tiltup module ID"
  [ -z "$PLN_CURRENCY_ID" ] && echo "  - PLN currency ID not found"
  [ -z "$TILTUP_MODULE_ID" ] && echo "  - module ID not found"
fi

echo "Cleaning up cache and logs..."
php bin/console cache:clear
rm -rf /var/www/html/var/cache/*
rm -rf /var/www/html/var/logs

echo "post-install script completed successfully"
