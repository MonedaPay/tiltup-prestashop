<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class TiltUpEncryptionService
{
    public function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public static function generateHmac(string $message): string
    {
        $encryptionKey = Configuration::get(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG);

        if (empty($encryptionKey)) {
            throw new Exception('TiltUp encryption key config item not set');
        }

        return hash_hmac('sha256', $message, Configuration::get(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG));
    }

    public static function isValidHmac(string $message, string $hmac): bool
    {
        try {
            return hash_equals(self::generateHmac($message), $hmac);
        } catch (Exception $exception) {
            syslog(LOG_ERR, 'Error when trying to compare hmacs: ' . $exception->getMessage());

            return false;
        }
    }
}
