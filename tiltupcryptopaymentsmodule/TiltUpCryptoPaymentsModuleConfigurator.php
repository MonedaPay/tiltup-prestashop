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
 * @author         Moneda Solutions Ltd.
 * @copyright      Copyright (c) 2023-2031
 * @license        https://www.gnu.org/licenses/lgpl-3.0.en.html GNU Lesser General Public License
 */
class TiltUpCryptoPaymentsModuleConfigurator
{
    /**
     * @var \TiltUpCryptoPaymentsModule
     */
    private $parentModule;
    /**
     * @var string
     */
    private $moduleInstallationTable;

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
            $redirectImmediate = (bool) Tools::getValue(TiltUpCryptoPaymentsModule::REDIRECT_IMMEDIATE_CONFIG);

            // check that the value is valid
            if (empty($merchantId) || empty($shopId) || empty($encryptionKey)) {
                // invalid value, show an error
                $output = $this->parentModule->displayError($this->parentModule->l('Mandatory Ari10 configuration items missing'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue(TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG, $merchantId);
                Configuration::updateValue(TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG, $shopId);
                Configuration::updateValue(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG, $env);
                Configuration::updateValue(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG, $encryptionKey);
                Configuration::updateValue(TiltUpCryptoPaymentsModule::REDIRECT_IMMEDIATE_CONFIG, $redirectImmediate);

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
                    'title' => $this->parentModule->l('Ari10 Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->parentModule->l('Ari10 Merchant ID'),
                        'name' => TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG,
                        'size' => 32,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->parentModule->l('Ari10 Shop ID'),
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
                        'type' => 'select',
                        'label' => $this->parentModule->l('Ari10 Environment:'),
                        'desc' => $this->parentModule->l('For testing purposes, select "Staging"'),
                        'name' => TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG,
                        'required' => false,
                        'options' => [
                            'query' => [
                                ['id' => 'app', 'name' => 'Production'],
                                ['id' => 'dev', 'name' => 'Development'],
                                ['id' => 'staging', 'name' => 'Staging'],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->parentModule->l('Immediate Redirect:'),
                        'desc' => $this->parentModule->l('If set to "Yes", the user will not see order confirmation page and will instead be redirected immediately to the Payment Gateway.'),
                        'name' => TiltUpCryptoPaymentsModule::REDIRECT_IMMEDIATE_CONFIG,
                        'required' => false,
                        'options' => [
                            'query' => [
                                ['id' => true, 'name' => 'Yes'],
                                ['id' => false, 'name' => 'No'],
                            ],
                            'id' => 'id',
                            'name' => 'name',
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
        $helper->show_cancel_button = true;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value[TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::MERCHANT_ID_CONFIG));
        $helper->fields_value[TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::SHOP_ID_CONFIG));
        $helper->fields_value[TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG));
        $helper->fields_value[TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::ENCRYPTION_KEY_CONFIG));
        $helper->fields_value[TiltUpCryptoPaymentsModule::REDIRECT_IMMEDIATE_CONFIG] = Tools::getValue(TiltUpCryptoPaymentsModule::REDIRECT_IMMEDIATE_CONFIG, Configuration::get(TiltUpCryptoPaymentsModule::REDIRECT_IMMEDIATE_CONFIG));

        return $helper->generateForm([$form]);
    }
}
