<?php

require_once __DIR__ . '/EncryptionService.php';
require_once __DIR__ . '/TiltUpCryptoPaymentsModuleConfigurator.php';
require_once __DIR__ . '/TiltUpCryptoPaymentsModuleInstaller.php';

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TiltUpCryptoPaymentsModule extends PaymentModule
{
    const MERCHANT_ID_CONFIG = 'TILTUP_MERCHANT_ID';
    const SHOP_ID_CONFIG = 'TILTUP_SHOP_ID';
    const TILTUP_ENV_CONFIG = 'TILTUP_ENV';
    const ENCRYPTION_KEY_CONFIG = 'TILTUP_ENCRYPTION_KEY';

    const CRYPTO_PAYMENT_PENDING_STATUS_CONFIG = 'TILTUP_AWAITING_CRYPTO_PAYMENT_STATUS';
    const CRYPTO_PAYMENT_CANCELLED_STATUS_CONFIG = 'TILTUP_CRYPTO_PAYMENT_CANCELLED_STATUS';
    const CRYPTO_PAYMENT_COMPLETED_STATUS_CONFIG = 'TILTUP_CRYPTO_PAYMENT_COMPLETED_STATUS';

    const TILTUP_ECOMMERCE_TYPE = 'PRESTASHOP';

    const CANCEL_CONTROLLER = 'cancel';
    const VALIDATE_CONTROLLER = 'validate';

    const MODULE_HOOKS = ['paymentOptions', 'displayPaymentReturn'];

    public function __construct()
    {
        $this->name = 'tiltupcryptopaymentsmodule';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'TiltUp Engineering';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->controllers = [self::CANCEL_CONTROLLER, self::VALIDATE_CONTROLLER];

        parent::__construct();

        $this->displayName = $this->l('TiltUp Crypto Payments');
        $this->description = $this->l('One-click fast and secure crypto payments.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install(): bool
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (
            parent::install()
            && $this->registerHook(self::MODULE_HOOKS)
            && (new TiltUpCryptoPaymentsModuleInstaller($this))->installOrderStates()
        );
    }

    public function uninstall(): bool
    {
        return Configuration::deleteByName(static::MERCHANT_ID_CONFIG)
            && Configuration::deleteByName(static::SHOP_ID_CONFIG)
            && Configuration::deleteByName(static::TILTUP_ENV_CONFIG)
            && Configuration::deleteByName(static::ENCRYPTION_KEY_CONFIG);
    }


    /**
     * This method handles the module's configuration page
     * @return string The page's HTML content
     */
    public function getContent(): string
    {
        return (new TiltUpCryptoPaymentsModuleConfigurator($this, $this->table))->getConfigurationForm();
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active || !$this->checkCurrency($params['cart'])) {
            return [];
        }

        $this->smarty->assign([
            'isModuleConfigured' => $this->isModuleConfigured(),
        ]);
        $cryptoPaymentOption = new PaymentOption();
        $cryptoPaymentOption->setModuleName($this->name)
            ->setCallToActionText($this->trans(
                'Pay with TiltUp',
                [],
                'Modules.Tiltupcryptopaymentsmodule.Admin'
            ))
            ->setAction($this->context->link->getModuleLink($this->name, 'validate', [], true))
            ->setAdditionalInformation($this->fetch('module:tiltupcryptopaymentsmodule/views/templates/hook/paymentOptionInfo.tpl'))
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/logo.png'));

        return [$cryptoPaymentOption];
    }

    public function hookDisplayPaymentReturn($params)
    {
        if (!$this->active) {
            return '';
        }

        $merchantOrderId = $params['order']->id;
        $customer = new Customer($params['order']->id_customer);
        $tiltUpRedirectUrl = $this->buildTiltUpRedirectUrl($merchantOrderId, $params['order']->reference, $customer->email);

        $this->smarty->assign([
            'tiltUpRedirectUrl' => $tiltUpRedirectUrl
        ]);

        return $this->fetch('module:tiltupcryptopaymentsmodule/views/templates/hook/postPaymentInfo.tpl');
    }

    // TODO Add check for supported currencies
    private function checkCurrency($cart): bool
    {
        $currency_order = new Currency((int)($cart->
        id_currency));
        $currencies_module = $this->getCurrency((int)$cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id ==
                    $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isModuleConfigured(): bool
    {
        return Configuration::get(static::MERCHANT_ID_CONFIG)
            && Configuration::get(static::SHOP_ID_CONFIG)
            && Configuration::get(static::TILTUP_ENV_CONFIG)
            && Configuration::get(static::ENCRYPTION_KEY_CONFIG);
    }

    /**
     * @param string $orderId
     * @param string $orderReference
     * @param string $customerEmail
     * @return string
     */
    private function buildTiltUpRedirectUrl(string $orderId, string $orderReference, string $customerEmail): string
    {
        $merchantId = Configuration::get(self::MERCHANT_ID_CONFIG);
        $shopId = Configuration::get(self::SHOP_ID_CONFIG);
        $env = Configuration::get(self::TILTUP_ENV_CONFIG);
        $callbackUrl = $this->buildCallbackUrl($orderReference, $customerEmail);
        $cancelUrl = $this->buildReturnUrl($orderId, self::CANCEL_CONTROLLER);

        return 'https://payment.' . $env . '.tiltup.io/ecommerce?' . http_build_query([
                'merchantId' => $merchantId,
                'shopId' => $shopId,
                'merchantOrderId' => $orderId,
                'type' => self::TILTUP_ECOMMERCE_TYPE,
                'callbackUrl' => $callbackUrl,
                'cancelUrl' => $cancelUrl,
            ]);
    }

    /**
     * @param string $orderId
     * @param string $controllerName
     * @return void
     */
    private function buildReturnUrl(string $orderId, string $controllerName): string
    {
        return $this->context->link->getModuleLink(
            $this->name,
            $controllerName,
            ['orderId' => $orderId, 'shopId' => $this->context->shop->id, 'shopGroupId' => $this->context->shop->id_shop_group, 'hmac' => EncryptionService::generateHmac($orderId)],
            true
        );
    }

    /**
     * @param string $orderReference
     * @param string $customerEmail
     * @return void
     */
    private function buildCallbackUrl(string $orderReference, string $customerEmail): string
    {
        return $this->context->link->getPageLink('guest-tracking', null, null, [
            'order_reference' => $orderReference,
            'email' => $customerEmail
        ]);
    }
}
