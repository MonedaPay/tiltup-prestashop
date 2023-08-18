<?php
/**
 *
  * Copyright since 2023 TiltUp Sp z o. o.
  *
   NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License version 3.0
  * that is bundled with this package in the file LICENSE.md.
  * It is also available through the world-wide-web at this URL:
  * https://opensource.org/licenses/AFL-3.0
  * If you did not receive a copy of the license and are unable to
  * obtain it through the world-wide-web, please send an email
  * to license@prestashop.com so we can send you a copy immediately.
  *
  * @author  TiltUp Sp. z o.o
  * @copyright Since 2023 TiltUp Sp z o. o.
  * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0

 */
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
