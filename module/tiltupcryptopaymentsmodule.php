<?php

require_once __DIR__ . '/EncryptionService.php';

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

    const SUCCESS_CONTROLLER = 'success';
    const CANCEL_CONTROLLER = 'cancel';
    const VALIDATE_CONTROLLER = 'validate';

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
        $this->controllers = [self::SUCCESS_CONTROLLER, self::CANCEL_CONTROLLER, self::VALIDATE_CONTROLLER];

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
            parent::install() && $this->registerHook('paymentOptions')
            && $this->registerHook('displayPaymentReturn') && $this->installOrderStates()
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
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $merchantId = (string)Tools::getValue(self::MERCHANT_ID_CONFIG);
            $shopId = (string)Tools::getValue(self::SHOP_ID_CONFIG);
            $env = (string)Tools::getValue(self::TILTUP_ENV_CONFIG);
            $encryptionKey = (string)Tools::getValue(self::ENCRYPTION_KEY_CONFIG);

            // check that the value is valid
            if (empty($merchantId) || empty($shopId) || empty($encryptionKey)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Mandatory TiltUp configuration items missing'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue(self::MERCHANT_ID_CONFIG, $merchantId);
                Configuration::updateValue(self::SHOP_ID_CONFIG, $shopId);
                Configuration::updateValue(self::TILTUP_ENV_CONFIG, $env);
                Configuration::updateValue(self::ENCRYPTION_KEY_CONFIG, $encryptionKey);

                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }

    /**
     * Builds the configuration form
     * @return string HTML code
     */
    public function displayForm(): string
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('TiltUp Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('TiltUp Merchant ID'),
                        'name' => self::MERCHANT_ID_CONFIG,
                        'size' => 32,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('TiltUp Shop ID'),
                        'name' => self::SHOP_ID_CONFIG,
                        'size' => 32,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Encryption Secret'),
                        'name' => self::ENCRYPTION_KEY_CONFIG,
                        'size' => 32,
                        'required' => true,
                    ],
                    [
                        'type' => 'select',                              // This is a <select> tag.
                        'label' => $this->l('TiltUp Environment:'),         // The <label> for this <select> tag.
                        'desc' => $this->l('For testing purposes, select "Staging"'),  // A help text, displayed right next to the <select> tag.
                        'name' => self::TILTUP_ENV_CONFIG,                     // The content of the 'id' attribute of the <select> tag.
                        'required' => false,                              // If set to true, this option must be set.
                        'options' => [
                            'query' => [
                                ['id' => 'app', 'name' => 'Production'],                             // The value of the 'id' attribute of the <option> tag.
                                ['id' => 'dev', 'name' => 'Development'],                             // The value of the 'id' attribute of the <option> tag.
                                ['id' => 'staging', 'name' => 'Staging'],                             // The value of the 'id' attribute of the <option> tag., 'name' => 'Development'],                             // The value of the 'id' attribute of the <option> tag.
                            ],                           // $options contains the data itself.
                            'id' => 'id',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                            'name' => 'name'                               // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                        ]
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value[self::MERCHANT_ID_CONFIG] = Tools::getValue(self::MERCHANT_ID_CONFIG, Configuration::get(self::MERCHANT_ID_CONFIG));
        $helper->fields_value[self::SHOP_ID_CONFIG] = Tools::getValue(self::SHOP_ID_CONFIG, Configuration::get(self::SHOP_ID_CONFIG));
        $helper->fields_value[self::TILTUP_ENV_CONFIG] = Tools::getValue(self::TILTUP_ENV_CONFIG, Configuration::get(self::TILTUP_ENV_CONFIG));
        $helper->fields_value[self::ENCRYPTION_KEY_CONFIG] = Tools::getValue(self::ENCRYPTION_KEY_CONFIG, Configuration::get(self::ENCRYPTION_KEY_CONFIG));

        return $helper->generateForm([$form]);
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $cart = $this->context->cart;
        $total = $this->context->getCurrentLocale()->formatPrice(
            $cart->getOrderTotal(true, Cart::BOTH),
            (new Currency($cart->id_currency))->iso_code
        );
        $taxLabel = '';
        if ($this->context->country->display_tax_label) {
            $taxLabel = $this->trans(
                '(tax incl.)',
                [],
                'Modules.Tiltupcryptopaymentsmodule.Admin'
            );
        }
        $this->smarty->assign([
            'totalPrice' => $total,
            'taxLabel' => $taxLabel
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
            return;
        }

        $merchantOrderId = $params['order']->id;
        $customer = new Customer($params['order']->id_customer);
        $tiltUpRedirectUrl = $this->buildTiltUpRedirectUrl($merchantOrderId, $params['order']->reference, $customer->email);

        $totalAmount = $params['order']->getOrdersTotalPaid();
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

    private function installOrderStates(): bool
    {
        return $this->installState(self::CRYPTO_PAYMENT_PENDING_STATUS_CONFIG, ['en' => 'Waiting for TiltUp Crypto Payment'], '#198754', false)
            && $this->installState(self::CRYPTO_PAYMENT_CANCELLED_STATUS_CONFIG, ['en' => 'TiltUp Crypto Payment Cancelled'], '#dc3545', false)
            && $this->installState(self::CRYPTO_PAYMENT_COMPLETED_STATUS_CONFIG, ['en' => 'TiltUp Crypto Payment Completed'], '#198754', true);
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function installState(string $configKey, array $namesByLangCode, string $color, bool $isPaid): bool
    {
        if (Configuration::getGlobalValue($configKey)) {
            $orderState = new OrderState((int)Configuration::getGlobalValue($configKey));
            if (Validate::isLoadedObject($orderState) && $this->name === $orderState->module_name) {
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
        $orderState->module_name = $this->name;
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
            $this->_errors[] = sprintf('Failed to create OrderState %s', $configurationKey);
            return false;
        }
        $result = Configuration::updateGlobalValue(
            $configurationKey,
            (int)$orderState->id
        );
        if (false === $result) {
            $this->_errors[] = sprintf('Failed to save OrderState %s to Configuration', $configurationKey);

            return false;
        }

        // TODO Add icons for all states.
        $orderStateImgPath = $this->getLocalPath() . 'views/img/orderstate/' . $configurationKey . '.png';
        if (false === Tools::file_exists_cache($orderStateImgPath)) {
            $this->_errors[] = sprintf(
                'Failed to find icon file of OrderState %s',
                $configurationKey
            );

            return false;
        }

        if (false === (bool)Tools::copy($orderStateImgPath, _PS_ORDER_STATE_IMG_DIR_ . $orderState->id . '.png')) {
            $this->_errors[] = sprintf(
                'Failed to copy icon of OrderState %s',
                $configurationKey
            );

            return false;
        }

        return true;
    }

    /**
     * @param $orderId
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
     * @param string $merchantOrderId
     * @param string $controllerName
     * @return void
     */
    private function buildReturnUrl(string $merchantOrderId, string $controllerName): string
    {
        return $this->context->link->getModuleLink(
            $this->name,
            $controllerName,
            ['merchantOrderId' => $merchantOrderId, 'shopId' => $this->context->shop->id, 'shopGroupId' => $this->context->shop->id_shop_group, 'hmac' => EncryptionService::generateHmac($merchantOrderId)],
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
