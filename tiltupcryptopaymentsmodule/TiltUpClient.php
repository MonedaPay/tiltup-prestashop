<?php

require_once __DIR__ . '/TiltUpDefaults.php';
require_once __DIR__ . '/TiltUpSimpleLogger.php';

use GuzzleHttp\Client;

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
class TiltUpClient
{
    const HTTP_TIMEOUT = 5000;

    /**
     * @var Client
     */
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_url' => self::generateTiltUpBaseUrl(),
            'timeout' => self::HTTP_TIMEOUT,
        ]);
    }

    public function getSupportedCurrencies(): array
    {
        try {
            return $this->tryGetSupportedCurrencies();
        } catch (Exception $ex) {
            TiltUpSimpleLogger::error('Failed to retrieve supported currencies from TiltUp BE. Returning defaults', ['err' => $ex->getMessage()]);

            return TiltUpDefaults::TILTUP_DEFAULT_SUPPORTED_CURRENCIES;
        }
    }

    private function tryGetSupportedCurrencies(): array
    {
        $query = <<< EOD
        query GetMeta {
            metadata {
                supportedCurrencies {
                    fiat
                }
            }
        }
        EOD;

        $res = $this->httpClient->post(null, [
            'json' => [
                'query' => $query,
                'variables' => null,
            ],
        ]);

        if ($res->getStatusCode() === 200) {
            $jsonRes = $res->json();

            if (isset($jsonRes->errors)) {
                TiltUpSimpleLogger::error('Failed to retrieve supported currencies from TiltUp BE. Returning defaults', $jsonRes->errors);

                return TiltUpDefaults::TILTUP_DEFAULT_SUPPORTED_CURRENCIES;
            }

            $supportedCurrencies = $jsonRes['data']['metadata']['supportedCurrencies']['fiat'];

            if (isset($supportedCurrencies)) {
                return $supportedCurrencies;
            }
        }

        return TiltUpDefaults::TILTUP_DEFAULT_SUPPORTED_CURRENCIES;
    }

    private static function generateTiltUpBaseUrl(): string
    {
        $env = Configuration::get(TiltUpCryptoPaymentsModule::TILTUP_ENV_CONFIG);

        return 'https://be.' . $env . '.tiltup.io/graphql';
    }
}
