/**
 *
  * Copyright since 2023 Moneda Solutions Ltd.
  *
   NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License version 3.0
  * that is bundled with this package in the file LICENSE.md.
  * It is also available through the world-wide-web at this URL:
  * https://opensource.org/licenses/AFL-3.0
  * If you did not receive a copy of the license and are unable to
  * obtain it through the world-wide-web, please send an email
  * to license@prestashop.com so we can send you a copy immediately.
  *
  * @author  Moneda Solutions Ltd.
  * @copyright Since 2023 Moneda Solutions Ltd.
  * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0

 */

INSERT IGNORE INTO ps_carrier_zone (id_carrier, id_zone)
SELECT c.id_carrier, z.id_zone
FROM ps_carrier c
         JOIN ps_zone z;

-- ###############################################################
-- PLN currency creation / activation
INSERT IGNORE INTO ps_currency (name, iso_code, numeric_iso_code, `precision`, conversion_rate, deleted, active, unofficial, modified)
VALUES ('', 'PLN', 985, 2, 1.000000, 0, 1, 0, 0);

-- Add PLN currency language data for all active languages
INSERT IGNORE INTO ps_currency_lang (id_currency, id_lang, name, symbol)
SELECT c.id_currency, l.id_lang, 'Polish Zloty', 'zł'
FROM ps_currency c
CROSS JOIN ps_lang l
WHERE c.iso_code = 'PLN' AND l.active = 1;

-- Add PLN currency to shop association
INSERT IGNORE INTO ps_currency_shop (id_currency, id_shop, conversion_rate)
SELECT c.id_currency, 1, 1.0
FROM ps_currency c
WHERE c.iso_code = 'PLN';

-- Module-currency linking
INSERT IGNORE INTO ps_module_currency (id_module, id_currency)
SELECT m.id_module, c.id_currency
FROM ps_module m
CROSS JOIN ps_currency c
WHERE m.name = 'tiltupcryptopaymentsmodule' AND c.iso_code = 'PLN';

-- Set PLN as default currency
UPDATE ps_configuration
SET value = (SELECT id_currency FROM ps_currency WHERE iso_code = 'PLN' LIMIT 1)
WHERE name = 'PS_CURRENCY_DEFAULT';


-- ###############################################################
-- Add GBP currency
INSERT INTO ps_currency (name, iso_code, numeric_iso_code, `precision`, conversion_rate, deleted, active, unofficial, modified)
VALUES ('', 'GBP', 826, 2, 0.200000, 0, 1, 0, 0);

-- Add GBP currency language data for all active languages
INSERT INTO ps_currency_lang (id_currency, id_lang, name, symbol)
SELECT c.id_currency, l.id_lang, 'British Pound', '£'
FROM ps_currency c
         CROSS JOIN ps_lang l
WHERE c.iso_code = 'GBP' AND l.active = 1;

-- Add GBP currency to shop association
INSERT INTO ps_currency_shop (id_currency, id_shop, conversion_rate)
SELECT c.id_currency, 1, 0.20
FROM ps_currency c
WHERE c.iso_code = 'GBP';
