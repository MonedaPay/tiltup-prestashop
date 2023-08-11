<?php

class TiltUpCryptoPaymentsModuleOrderInfoModuleFrontController extends TiltUpFrontController
{
    protected function handleOrderNotFound(): void
    {
        header('HTTP/1.1 404 Not Found', true, 404);

        exit;
    }

    protected function handleRequest(Order $order)
    {
        $customer = new Customer($order->id_customer);
        $currency = new Currency($order->id_currency);

        try {
            if (Validate::isLoadedObject($customer) && Validate::isLoadedObject($currency)) {
                header('HTTP/1.1 200 Ok', true, 200);
                header('Content-Type: application/json; charset=utf-8');

                $res = [
                    'orderId' => $order->id,
                    'fromCurrency' => $currency->iso_code,
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
