<?php

require_once __DIR__ . '/TiltUpFrontController.php';

class TiltUpCryptoPaymentsModuleCancelModuleFrontController extends TiltUpFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->setTemplate('module:tiltupcryptopaymentsmodule/views/templates/front/cancel.tpl');
    }

    protected function handleRequest(Order $order)
    {
        $this->updateOrderState($order, (int)Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_CANCELLED_STATUS_CONFIG));
    }
}
