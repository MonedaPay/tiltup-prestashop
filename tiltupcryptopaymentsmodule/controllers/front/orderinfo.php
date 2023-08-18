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
require_once __DIR__ . '/TiltUpFrontWebhookController.php';

class TiltUpCryptoPaymentsModuleOrderInfoModuleFrontController extends TiltUpFrontWebhookController
{
    protected function handleRequest(Order $order)
    {
        $customer = new Customer($order->id_customer);
        $currency = new Currency($order->id_currency);

        try {
            if (Validate::isLoadedObject($customer) && Validate::isLoadedObject($currency)) {
                header('HTTP/1.1 200 Ok', true, 200);
                header('Content-Type: application/json; charset=utf-8');

                $res = [
                    'merchantOrderId' => $order->id,
                    'merchantCustomerId' => $customer->id,
                    'fromCurrency' => $currency->iso_code,
                    'fromAmount' => $order->getOrdersTotalPaid(),
                    'email' => $customer->email,
                    'firstName' => $customer->firstname,
                    'lastName' => $customer->lastname,
                ];

                echo json_encode($res);
            } else {
                header('HTTP/1.1 500 Internal Server Error', true, 500);
            }
        } catch (Exception $exception) {
            header('HTTP/1.1 500 Internal Server Error', true, 500);
        }

        exit;
    }
}
