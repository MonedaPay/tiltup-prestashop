<?php

class TiltUpCryptoPaymentsModuleInstaller
{
    private TiltUpCryptoPaymentsModule $parentModule;

    public function __construct(TiltUpCryptoPaymentsModule $parentModule)
    {
        $this->parentModule = $parentModule;
    }

    public function installOrderStates(): bool
    {
        return $this->installState(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_PENDING_STATUS_CONFIG, ['en' => 'Waiting for TiltUp Crypto Payment'], '#ffc107', false)
            && $this->installState(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_CANCELLED_STATUS_CONFIG, ['en' => 'TiltUp Crypto Payment Cancelled'], '#dc3545', false)
            && $this->installState(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_FAILED_STATUS_CONFIG, ['en' => 'TiltUp Crypto Payment Failed'], '#dc3545', false)
            && $this->installState(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_PARTIALLY_COMPLETED_STATUS_CONFIG, ['en' => 'TiltUp Crypto Payment Partially Completed'], '#20c997', false)
            && $this->installState(TiltUpCryptoPaymentsModule::CRYPTO_PAYMENT_COMPLETED_STATUS_CONFIG, ['en' => 'TiltUp Crypto Payment Completed'], '#198754', true);
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function installState(string $configKey, array $namesByLangCode, string $color, bool $isPaid): bool
    {
        if (Configuration::getGlobalValue($configKey)) {
            $orderState = new OrderState((int)Configuration::getGlobalValue($configKey));
            // OrderState already installed, no need to do anything.
            if (Validate::isLoadedObject($orderState) && $this->parentModule->name === $orderState->module_name) {
                return true;
            }
        }

        return $this->createTiltUpOrderState(
            $configKey,
            $namesByLangCode,
            $color,
            false,
            $isPaid
        );
    }

    private function createTiltUpOrderState($configurationKey, array $nameByLangIsoCode, $color, $isLogable = false, $isPaid = false, $isInvoice = false, $isShipped = false, $isDelivery = false, $isPdfDelivery = false, $isPdfInvoice = false, $isSendEmail = false, $template = '', $isHidden = false, $isUnremovable = true, $isDeleted = false): bool
    {
        $tabNameByLangId = [];

        foreach ($nameByLangIsoCode as $langIsoCode => $name) {
            foreach (Language::getLanguages(false) as $language) {
                if (Tools::strtolower($language['iso_code']) === $langIsoCode) {
                    $tabNameByLangId[(int)$language['id_lang']] = $name;
                } elseif (isset($nameByLangIsoCode['en'])) {
                    $tabNameByLangId[(int)$language['id_lang']] =
                        $nameByLangIsoCode['en'];
                }
            }
        }

        $orderState = new OrderState();
        $orderState->module_name = $this->parentModule->name;
        $orderState->name = $tabNameByLangId;
        $orderState->color = $color;
        $orderState->logable = $isLogable;
        $orderState->paid = $isPaid;
        $orderState->invoice = $isInvoice;
        $orderState->shipped = $isShipped;
        $orderState->delivery = $isDelivery;
        $orderState->pdf_delivery = $isPdfDelivery;
        $orderState->pdf_invoice = $isPdfInvoice;
        $orderState->send_email = $isSendEmail;
        $orderState->hidden = $isHidden;
        $orderState->unremovable = $isUnremovable;
        $orderState->template = $template;
        $orderState->deleted = $isDeleted;
        $result = (bool)$orderState->add();

        if (false === $result) {
            $this->parentModule->displayError(sprintf('Failed to create OrderState %s', $configurationKey));
            return false;
        }
        $result = Configuration::updateGlobalValue(
            $configurationKey,
            (int)$orderState->id
        );
        if (false === $result) {
            $this->parentModule->displayError(sprintf('Failed to save OrderState %s to Configuration', $configurationKey));

            return false;
        }

        // TODO Add icons for all states.
        $orderStateImgPath = $this->parentModule->getLocalPath() . 'views/img/orderstate/' . $configurationKey . '.png';
        if (false === Tools::file_exists_cache($orderStateImgPath)) {
            $this->parentModule->displayError(sprintf(
                'Failed to find icon file of OrderState %s',
                $configurationKey
            ));

            return false;
        }

        if (false === (bool)Tools::copy($orderStateImgPath, _PS_ORDER_STATE_IMG_DIR_ . $orderState->id . '.png')) {
            $this->parentModule->displayError(sprintf(
                'Failed to copy icon of OrderState %s',
                $configurationKey
            ));

            return false;
        }

        return true;
    }
}
