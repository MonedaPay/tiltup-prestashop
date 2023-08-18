<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class TiltUpCryptoPaymentsModuleValidateModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (!($this->module instanceof Tiltupcryptopaymentsmodule)) {
            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'tiltupcryptopaymentsmodule') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            exit($this->trans(
                'This payment method is not available.',
                [],
                'Modules.Tiltupcryptopaymentsmodule.Shop'
            ));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');

            return;
        }

        $currency = $this->context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $mailVars = [];
        $orderStatusId =
            (int) Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_PENDING_STATUS_CONFIG);

        // This actually creates the order
        $this->module->validateOrder(
            (int) $cart->id,
            $orderStatusId,
            $total,
            $this->module->displayName,
            null,
            $mailVars,
            (int) $currency->id,
            false,
            $customer->secure_key
        );

        Tools::redirect('index.php?controller=order-confirmation&id_cart='
            . (int) $cart->id . '&id_module=' . (int) $this->module->id
            . '&id_order=' . $this->module->currentOrder . '&key='
            . $customer->secure_key);
    }
}
