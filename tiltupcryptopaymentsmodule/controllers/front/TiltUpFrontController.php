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
require_once __DIR__ . '/../../TiltUpEncryptionService.php';

abstract class TiltUpFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        parent::postProcess();

        $hmac = $this->getHmac();
        $orderId = Tools::getValue('orderId');

        if (!TiltUpEncryptionService::isValidHmac($orderId, $hmac)) {
            $this->handleInvalidToken();
        }

        if (isset($orderId)) {
            // TODO Check this is enough in multistore context.
            $order = new Order($orderId);

            if (Validate::isLoadedObject($order)) {
                $this->handleRequest($order);
            } else {
                $this->handleOrderNotFound();
            }
        } else {
            $this->redirectToPaymentScreen();
        }
    }

    abstract protected function handleRequest(Order $order);

    /**
     * @return string
     */
    protected function getHmac(): string
    {
        return Tools::getValue('hmac') ?? '';
    }

    /**
     * @return void
     */
    public function redirectToPaymentScreen(): void
    {
        Tools::redirect($this->context->link->getPageLink(
            'order',
            false,
            $this->context->language->id,
            [
                'step' => 4,
            ]
        ));
    }

    /**
     * @return void
     */
    protected function handleInvalidToken(): void
    {
        Tools::redirect($this->context->link->getModuleLink($this->module->name, 'error', ['error' => $this->module->l('Invalid TiltUp security token - unable to process payment request')]));
    }

    protected function updateOrderState(Order $order, int $newStateId)
    {
        $currentOrderStateId = (int) $order->getCurrentState();

        if ($currentOrderStateId !== $newStateId
            && false === (bool) $order->hasBeenShipped()
            && false === (bool) $order->hasBeenDelivered()
        ) {
            $orderHistory = new OrderHistory();
            $orderHistory->id_order = $order->id;
            $orderHistory->changeIdOrderState(
                $newStateId,
                $order->id
            );
            $orderHistory->addWithemail();
        }
    }

    /**
     * @return void
     */
    protected function handleOrderNotFound(): void
    {
        // TODO Add some more meaningful handling here.
        Tools::redirect($this->context->link->getPageLink('index'));
    }
}
