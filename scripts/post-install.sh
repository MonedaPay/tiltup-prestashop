#!/bin/sh

php -d memory_limit=256M
echo "Installing Tiltup Crypto Payments Module"
php bin/console prestashop:module install tiltupcryptopaymentsmodule
echo "Configuring Tiltup Crypto Payments Module"
php bin/console prestashop:module configure tiltupcryptopaymentsmodule

# Enable Polish Zloty (PLN) if not already active
if ! mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT 1 FROM ps_currency WHERE iso_code='PLN' AND active=1" | grep -q 1; then
  echo "Activating PLN currency…"
  php /var/www/html/bin/console prestashop:currency:add PLN --activate --no-interaction
fi

# Enable tiltupcryptopaymentsmodule for PLN currency
PLN_CURRENCY_ID=$(mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_currency FROM ps_currency WHERE iso_code='PLN' AND active=1" | grep -v id_currency)
TILTUP_MODULE_ID=$(mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_module FROM ps_module WHERE name='tiltupcryptopaymentsmodule'" | grep -v id_module)

if [ -n "$PLN_CURRENCY_ID" ] && [ -n "$TILTUP_MODULE_ID" ]; then
  if ! mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT 1 FROM ps_module_currency WHERE id_module='$TILTUP_MODULE_ID' AND id_currency='$PLN_CURRENCY_ID'" | grep -q 1; then
    echo "Enabling tiltupcryptopaymentsmodule for PLN currency…"
    mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "INSERT INTO ps_module_currency (id_module, id_shop, id_currency) VALUES ('$TILTUP_MODULE_ID', 1, '$PLN_CURRENCY_ID')"
  fi
fi

rm -rf /var/www/html/var/cache/dev
rm -rf /var/www/html/var/logs
