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

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('var/logs/tiltup_module.log', Logger::INFO));
$GLOBALS['tiltUpLogger'] = $logger;

class TiltUpSimpleLogger
{
    public static function info(string $msg, array $context = []): void
    {
        $GLOBALS['tiltUpLogger']->info($msg, $context);
    }

    public static function error(string $msg, array $context = []): void
    {
        $GLOBALS['tiltUpLogger']->error($msg, $context);
    }
}
