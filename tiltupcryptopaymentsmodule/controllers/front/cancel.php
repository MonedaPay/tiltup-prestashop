<?php
/**
 * TiltUp_TiltUpCryptoPaymentsModule extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU Lesser General Public License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/lgpl-3.0.en.html
 *
 * @author         TiltUp Sp. z o. o.
 * @copyright      Copyright (c) 2023-2031
 * @license        https://www.gnu.org/licenses/lgpl-3.0.en.html GNU Lesser General Public License
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
