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
            case TiltUpAggregatedOrderStatus::AML_SCREENING:
                return (int) Configuration::getGlobalValue(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_AML_SCREENING);
        }

        throw new Exception('Unknown TiltUp aggregated status: ' . $tiltUpAggregatedStatus);
    }
}
