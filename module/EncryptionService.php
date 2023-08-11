<?php

class EncryptionService
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
