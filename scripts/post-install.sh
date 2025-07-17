#!/bin/sh

php -d memory_limit=256M
echo "Installing Tiltup Crypto Payments Module"
php bin/console prestashop:module install tiltupcryptopaymentsmodule
echo "Configuring Tiltup Crypto Payments Module"
php bin/console prestashop:module configure tiltupcryptopaymentsmodule


echo "Checking PLN currency status..."
if ! mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT 1 FROM ps_currency WHERE iso_code='PLN' AND active=1" | grep -q 1; then
  echo "PLN currency not found or inactive - checking if it exists..."

  # Check if PLN currency exists but is inactive
  if mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT 1 FROM ps_currency WHERE iso_code='PLN'" | grep -q 1; then
    echo "PLN currency exists but is inactive - activating it..."
    mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "UPDATE ps_currency SET active=1 WHERE iso_code='PLN'"
    echo "PLN currency has been activated"
  else
    echo "PLN currency does not exist - creating it..."
    mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "INSERT INTO ps_currency (name, iso_code, numeric_iso_code, \`precision\`, conversion_rate, deleted, active, unofficial, modified) VALUES ('', 'PLN', 985, 2, 4.250000, 0, 1, 0, 0)"

    # Get the ID of the newly created PLN currency
    PLN_ID=$(mysql -N -s -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_currency FROM ps_currency WHERE iso_code='PLN'")

    # Insert language-specific data for PLN currency
    echo "Adding PLN language-specific data..."
    # Get all available languages
    mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_lang FROM ps_lang WHERE active=1" | grep -v id_lang | while read lang_id; do
      mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "INSERT INTO ps_currency_lang (id_currency, id_lang, name, symbol) VALUES ($PLN_ID, $lang_id, 'Polish Zloty', 'zl')"
    done

    # Add PLN currency to shop association
    echo "Adding PLN currency to shop association..."
    mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "INSERT INTO ps_currency_shop (id_currency, id_shop, conversion_rate) VALUES ($PLN_ID, 1, 4.25)"

    echo "PLN currency has been created successfully with language data and shop association"
  fi
else
  echo "PLN currency is already active - skipping activation"
fi

echo "Configuring module-currency association for PLN..."
PLN_CURRENCY_ID=$(mysql -N -s -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_currency FROM ps_currency WHERE iso_code='PLN' AND active=1")
TILTUP_MODULE_ID=$(mysql -N -s -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "SELECT id_module FROM ps_module WHERE name='tiltupcryptopaymentsmodule'")

if [ -n "$PLN_CURRENCY_ID" ] && [ -n "$TILTUP_MODULE_ID" ]; then
  echo "Found PLN currency ID: $PLN_CURRENCY_ID"
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

# Set PLN as default currency if it exists and is active
if [ -n "$PLN_CURRENCY_ID" ]; then
  echo "Setting PLN as default currency..."
  mysql -h"$DB_SERVER" -u"$DB_USER" -p"$DB_PASSWD" "$DB_NAME" -e "UPDATE ps_configuration SET value = '$PLN_CURRENCY_ID' WHERE name = 'PS_CURRENCY_DEFAULT'"
  echo "PLN has been set as default currency"
fi

echo "Uninstalling Customer Reassurance module, because it was showing some annoying messages"
php bin/console prestashop:module uninstall blockreassurance

echo "Cleaning up cache and logs..."
php bin/console cache:clear
rm -rf var/cache/*
rm -rf var/logs

echo "post-install script completed successfully"
