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
require_once __DIR__ . '/TiltUpFrontController.php';

class TiltUpCryptoPaymentsModuleCancelModuleFrontController extends TiltUpFrontController
{
    public function initContent()
    {
        parent::initContent();
    }

    protected function handleRequest(Order $order)
    {
        $this->updateOrderState($order, (int) Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_CANCELLED_STATUS_CONFIG));
        $customer = new Customer($order->id_customer);

        Tools::redirect(
            $this->context->link->getPageLink('guest-tracking', null, null, [
                'order_reference' => $order->reference,
                'email' => $customer->email,
            ])
        );
    }
}
