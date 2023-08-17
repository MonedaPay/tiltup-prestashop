<?php

require_once __DIR__ . '/TiltUpFrontWebhookController.php';

class TiltUpCryptoPaymentsModuleWebhookModuleFrontController extends TiltUpFrontWebhookController
{
    protected function handleRequest(Order $order)
    {
        $tiltUpAggregatedStatus = Tools::getValue('aggregatedStatus');
        $this->updateOrderState($order, $this->mapTiltUpToPrestaOrderState($tiltUpAggregatedStatus));
        header('HTTP/1.1 200 OK', true, 200);

        exit;
    }

    /**
     * @throws Exception
     */
    private function mapTiltUpToPrestaOrderState(string $tiltUpAggregatedStatus): int
    {
        switch ($tiltUpAggregatedStatus) {
            case TiltUpAggregatedOrderStatus::CREATED:
            case TiltUpAggregatedOrderStatus::IN_PROGRESS:
                return (int)Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_PENDING_STATUS_CONFIG);
            case TiltUpAggregatedOrderStatus::FAILURE:
                return (int)Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_FAILED_STATUS_CONFIG);
            case TiltUpAggregatedOrderStatus::SUCCESS:
            case TiltUpAggregatedOrderStatus::OVERPAID:
                return (int)Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_COMPLETED_STATUS_CONFIG);
            case TiltUpAggregatedOrderStatus::UNDERPAID:
                return (int)Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_PARTIALLY_COMPLETED_STATUS_CONFIG);
        }

        throw new Exception('Unknown TiltUp aggregated status: ' . $tiltUpAggregatedStatus);
    }
}
