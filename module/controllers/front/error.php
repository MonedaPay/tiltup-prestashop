<?php

class TiltUpCryptoPaymentsModuleCancelModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'error' => Tools::getValue('error'),
        ]);
        $this->setTemplate('module:tiltupcryptopayments/views/templates/front/error.tpl');
    }
}
