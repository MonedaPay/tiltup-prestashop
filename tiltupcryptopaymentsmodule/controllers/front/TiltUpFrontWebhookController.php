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
require_once __DIR__ . '/TiltUpFrontController.php';

abstract class TiltUpFrontWebhookController extends TiltUpFrontController
{
    protected function getHmac(): string
    {
        $headers = array_change_key_case(getallheaders());

        return $headers['tiltup-hmac'] ?? '';
    }

    protected function handleInvalidToken(): void
    {
        header('HTTP/1.1 401 Unauthorized', true, 401);

        exit;
    }

    protected function handleOrderNotFound(): void
    {
        header('HTTP/1.1 404 Not Found', true, 404);

        exit;
    }
}
