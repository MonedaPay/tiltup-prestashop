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
