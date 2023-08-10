<?php

class TiltUpCryptoPaymentsModuleCancelModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->setTemplate('module:tiltupcryptopaymentsmodule/views/templates/front/cancel.tpl');
    }
}
