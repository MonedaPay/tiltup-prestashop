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
class TiltUpCryptoPaymentsModuleConfigurator
{
    private TiltUpCryptoPaymentsModule $parentModule;
    private string $moduleInstallationTable;

    public function __construct(TiltUpCryptoPaymentsModule $parentModule, string $moduleInstallationTable)
    {
        $this->parentModule = $parentModule;
        $this->moduleInstallationTable = $moduleInstallationTable;
    }

    public function getConfigurationForm(): string
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->parentModule->name)) {
            // retrieve the value set by the user
            $merchantId = (string) Tools::getValue(TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG);
            $shopId = (string) Tools::getValue(TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG);
            $env = (string) Tools::getValue(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG);
            $encryptionKey = (string) Tools::getValue(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG);

            // check that the value is valid
            if (empty($merchantId) || empty($shopId) || empty($encryptionKey)) {
                // invalid value, show an error
                $output = $this->parentModule->displayError($this->parentModule->l('Mandatory TiltUp configuration items missing'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue(TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG, $merchantId);
                Configuration::updateValue(TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG, $shopId);
                Configuration::updateValue(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG, $env);
                Configuration::updateValue(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG, $encryptionKey);

                $output = $this->parentModule->displayConfirmation($this->parentModule->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }

    /**
     * Builds the configuration form
     *
     * @return string HTML code
     */
    private function displayForm(): string
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->parentModule->l('TiltUp Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->parentModule->l('TiltUp Merchant ID'),
                        'name' => TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG,
                        'size' => 32,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->parentModule->l('TiltUp Shop ID'),
                        'name' => TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG,
                        'size' => 32,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->parentModule->l('Encryption Secret'),
                        'name' => TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG,
                        'size' => 32,
                        'required' => true,
                    ],
                    [
                        'type' => 'select',                              // This is a <select> tag.
                        'label' => $this->parentModule->l('TiltUp Environment:'),         // The <label> for this <select> tag.
                        'desc' => $this->parentModule->l('For testing purposes, select "Staging"'),  // A help text, displayed right next to the <select> tag.
                        'name' => TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG,                     // The content of the 'id' attribute of the <select> tag.
                        'required' => false,                              // If set to true, this option must be set.
                        'options' => [
                            'query' => [
                                ['id' => 'app', 'name' => 'Production'],                             // The value of the 'id' attribute of the <option> tag.
                                ['id' => 'dev', 'name' => 'Development'],                             // The value of the 'id' attribute of the <option> tag.
                                ['id' => 'staging', 'name' => 'Staging'],                             // The value of the 'id' attribute of the <option> tag., 'name' => 'Development'],                             // The value of the 'id' attribute of the <option> tag.
                            ],                           // $options contains the data itself.
                            'id' => 'id',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                            'name' => 'name',                               // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->parentModule->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->moduleInstallationTable;
        $helper->name_controller = $this->parentModule->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->parentModule->name]);
        $helper->submit_action = 'submit' . $this->parentModule->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value[TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG));
        $helper->fields_value[TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG));
        $helper->fields_value[TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG));
        $helper->fields_value[TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG));

        return $helper->generateForm([$form]);
    }
}
