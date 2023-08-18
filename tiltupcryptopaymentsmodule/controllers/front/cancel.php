<?php

require_once __DIR__ . '/TiltUpFrontController.php';

class TiltUpCryptoPaymentsModuleCancelModuleFrontController extends TiltUpFrontController
{
    public function initContent()
    {
        parent::initContent();
    }

    protected function handleRequest(Order $order)
    {
        $this->updateOrderState($order, (int)Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_CANCELLED_STATUS_CONFIG));
        $customer = new Customer($order->id_customer);

        Tools::redirect(
            $this->context->link->getPageLink('guest-tracking', null, null, [
                'order_reference' => $order->reference,
                'email' => $customer->email
            ])
        );
    }
}
