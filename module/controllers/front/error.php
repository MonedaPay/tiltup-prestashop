<?php

class TiltUpCryptoPaymentsModuleErrorModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            'error' => Tools::getValue('error'),
        ]);
        $this->setTemplate('module:tiltupcryptopaymentsmodule/views/templates/front/error.tpl');
    }
}
