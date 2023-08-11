<?php

require_once __DIR__ . '/TiltUpFrontController.php';

class TiltUpCryptoPaymentsModuleSuccessModuleFrontController extends TiltUpFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->setTemplate('module:tiltupcryptopaymentsmodule/views/templates/front/success.tpl');
    }

    protected function handlePaymentUpdate(Order $order)
    {
        // NOOP
    }
}
