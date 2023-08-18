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
    const CRYPTO_PAYMENT_PARTIALLY_COMPLETED_STATUS_CONFIG = 'TILTUP_CRYPTO_PAYMENT_PARTIALLY_COMPLETED_STATUS';
    const CRYPTO_PAYMENT_FAILED_STATUS_CONFIG = 'TILTUP_CRYPTO_PAYMENT_FAILED_STATUS';

    const TILTUP_ECOMMERCE_TYPE = 'PRESTASHOP';
    const TILTUP_SUPPORTED_CURRENCIES = ['EUR', 'USD', 'PLN'];

    const CANCEL_CONTROLLER = 'cancel';
    const VALIDATE_CONTROLLER = 'validate';

    const MODULE_HOOKS = ['paymentOptions', 'displayPaymentReturn', 'displayOrderDetail'];

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

        return
            parent::install()
            && $this->registerHook(self::MODULE_HOOKS)
            && (new TiltUpCryptoPaymentsModuleInstaller($this))->installOrderStates();
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
     *
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

    public function hookDisplayPaymentReturn($params): string
    {
        if (!$this->active) {
            return '';
        }

        $tiltUpRedirectUrl = $this->buildTiltUpRedirectUrl($params['order']);
        $this->smarty->assign([
            'tiltUpRedirectUrl' => $tiltUpRedirectUrl,
        ]);

        return $this->fetch('module:tiltupcryptopaymentsmodule/views/templates/hook/postPaymentInfo.tpl');
    }

    public function hookDisplayOrderDetail($params): string
    {
        if (!$this->active) {
            return '';
        }

        $tiltUpRedirectUrl = $this->buildTiltUpRedirectUrl($params['order']);
        $this->smarty->assign([
            'isPaymentIncomplete' => $params['order']->current_state === Configuration::getGlobalValue(self::CRYPTO_PAYMENT_PARTIALLY_COMPLETED_STATUS_CONFIG)
                || $params['order']->current_state === Configuration::getGlobalValue(self::CRYPTO_PAYMENT_PENDING_STATUS_CONFIG),
            'isPaymentCancelled' => $params['order']->current_state === Configuration::getGlobalValue(self::CRYPTO_PAYMENT_CANCELLED_STATUS_CONFIG),
            'tiltUpRedirectUrl' => $tiltUpRedirectUrl,
        ]);

        return $this->fetch('module:tiltupcryptopaymentsmodule/views/templates/hook/orderDetail.tpl');
    }

    private function checkCurrency($cart): bool
    {
        $orderCurrency = new Currency((int) $cart->id_currency);
        $moduleCurrencies = $this->getCurrency((int) $cart->id_currency);

        if (is_array($moduleCurrencies)) {
            foreach ($moduleCurrencies as $moduleCurrency) {
                if ($orderCurrency->id == $moduleCurrency['id_currency'] && in_array($orderCurrency->iso_code, self::TILTUP_SUPPORTED_CURRENCIES)) {
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
     * @param Order $order
     *
     * @return string
     *
     * @throws Exception
     */
    private function buildTiltUpRedirectUrl(Order $order): string
    {
        $customer = new Customer($order->id_customer);

        $merchantId = Configuration::get(self::MERCHANT_ID_CONFIG);
        $shopId = Configuration::get(self::SHOP_ID_CONFIG);
        $env = Configuration::get(self::TILTUP_ENV_CONFIG);
        $callbackUrl = $this->buildCallbackUrl($order->reference, $customer->email);
        $cancelUrl = $this->buildCancelUrl($order);

        return 'https://payment.' . $env . '.tiltup.io/ecommerce?' . http_build_query([
                'merchantId' => $merchantId,
                'shopId' => $shopId,
                'merchantOrderId' => $order->id,
                'type' => self::TILTUP_ECOMMERCE_TYPE,
                'callbackUrl' => $callbackUrl,
                'cancelUrl' => $cancelUrl,
            ]);
    }

    /**
     * @param Order $order
     *
     * @return void
     *
     * @throws Exception
     */
    private function buildCancelUrl(Order $order): string
    {
        return $this->context->link->getModuleLink(
            $this->name,
            self::CANCEL_CONTROLLER,
            ['orderId' => $order->id, 'shopId' => $this->context->shop->id, 'shopGroupId' => $this->context->shop->id_shop_group, 'hmac' => EncryptionService::generateHmac($order->id)],
            true
        );
    }

    /**
     * @param string $orderReference
     * @param string $customerEmail
     *
     * @return void
     */
    private function buildCallbackUrl(string $orderReference, string $customerEmail): string
    {
        return $this->context->link->getPageLink('guest-tracking', null, null, [
            'order_reference' => $orderReference,
            'email' => $customerEmail,
        ]);
    }
}
