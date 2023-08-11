<?php

require_once __DIR__ . '/../../EncryptionService.php';

abstract class TiltUpFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        parent::postProcess();

        $hmac = Tools::getValue('hmac');
        $orderId = Tools::getValue('orderId');

        if (!EncryptionService::isValidHmac($orderId, $hmac)) {
            Tools::redirect($this->context->link->getModuleLink($this->module->name, 'error', ['error' => $this->module->l('Invalid TiltUp security token - unable to process payment request')]));
        }

        if (isset($orderId)) {
            // TODO Check this is enough in multistore context.
            $order = new Order($orderId);

            if (Validate::isLoadedObject($order)) {
                $this->handlePaymentUpdate($order);
            } else {
                $this->handleOrderNotFound();
            }
        } else {
            $this->redirectToPaymentScreen();
        }
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

    protected function updateOrderState(Order $order, int $newStateId)
    {
        $currentOrderStateId = $order->getCurrentState();

        if ($currentOrderStateId !== $newStateId
            && false === (bool)$order->hasBeenShipped()
            && false === (bool)$order->hasBeenDelivered()
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
