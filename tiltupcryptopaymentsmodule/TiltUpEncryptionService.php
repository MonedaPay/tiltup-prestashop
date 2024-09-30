<?php
/**
 * TiltUp_TiltUpCryptoPaymentsModule extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU Lesser General Public License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/lgpl-3.0.en.html
 *
 * @author         Moneda Solutions Ltd.
 * @copyright      Copyright (c) 2023-2031
 * @license        https://www.gnu.org/licenses/lgpl-3.0.en.html GNU Lesser General Public License
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
