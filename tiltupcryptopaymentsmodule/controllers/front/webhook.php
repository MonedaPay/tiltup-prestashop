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
require_once __DIR__ . '/TiltUpFrontWebhookController.php';
require_once __DIR__ . '/../../TiltUpAggregatedOrderStatus.php';
require_once __DIR__ . '/../../TiltUpSimpleLogger.php';

class TiltUpCryptoPaymentsModuleWebhookModuleFrontController extends TiltUpFrontWebhookController
{
    protected function handleRequest(Order $order)
    {
        TiltUpSimpleLogger::info('Received TiltUp webhook: ' . serialize(Tools::getAllValues()));

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
                return (int) Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_PENDING_STATUS_CONFIG);
            case TiltUpAggregatedOrderStatus::FAILURE:
                return (int) Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_FAILED_STATUS_CONFIG);
            case TiltUpAggregatedOrderStatus::SUCCESS:
            case TiltUpAggregatedOrderStatus::OVERPAID:
                return (int) Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_COMPLETED_STATUS_CONFIG);
            case TiltUpAggregatedOrderStatus::UNDERPAID:
                return (int) Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_PARTIALLY_COMPLETED_STATUS_CONFIG);
        }

        throw new Exception('Unknown TiltUp aggregated status: ' . $tiltUpAggregatedStatus);
    }
}
