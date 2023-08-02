<?php

class TiltUpCryptoPaymentsModuleValidateModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $cart = $this->context->cart;

        $queryParams = [
            // TODO Change to PRESTA once implemented
            'type' => 'SHOPIFY',
            'merchantOrderId' => '123',
        ];
        dump($this);

        echo 'ok';
    }
}
