<?php

class EncryptionService
{
    public function __construct()
    {
    }

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
        return hash_equals(self::generateHmac($message), $hmac);
    }
}
