<?php
/*
 * *
 *  * Copyright since 2007 PrestaShop SA and Contributors
 *  * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *  *
 *  * NOTICE OF LICENSE
 *  *
 *  * This source file is subject to the Academic Free License version 3.0
 *  * that is bundled with this package in the file LICENSE.md.
 *  * It is also available through the world-wide-web at this URL:
 *  * https://opensource.org/licenses/AFL-3.0
 *  * If you did not receive a copy of the license and are unable to
 *  * obtain it through the world-wide-web, please send an email
 *  * to license@prestashop.com so we can send you a copy immediately.
 *  *
 *  * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 *  * @copyright Since 2007 PrestaShop SA and Contributors
 *  * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 */

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$log = new Logger('name');
$log->pushHandler(new StreamHandler('var/logs/tiltup_module.log', Logger::INFO));
$GLOBALS['log'] = $log;

class TiltUpSimpleLogger
{
    public static function info(string $msg): void
    {
        global $log;
        $log->info($msg);
    }

    public static function error(string $msg): void
    {
        global $log;
        $log->error($msg);
    }
}
