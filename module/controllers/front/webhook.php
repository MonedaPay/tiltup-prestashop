<?php

require_once __DIR__ . '/TiltUpFrontWebhookController.php';

class TiltUpCryptoPaymentsModuleWebhookModuleFrontController extends TiltUpFrontWebhookController
{
    protected function handleRequest(Order $order)
    {
        $this->updateOrderState($order, (int)Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_COMPLETED_STATUS_CONFIG));
        header('HTTP/1.1 200 OK', true, 200);

        exit;
    }
}
