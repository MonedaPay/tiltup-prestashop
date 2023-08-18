<?php
/**
 *
  * Copyright since 2023 TiltUp Sp z o. o.
  *
   NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License version 3.0
  * that is bundled with this package in the file LICENSE.md.
  * It is also available through the world-wide-web at this URL:
  * https://opensource.org/licenses/AFL-3.0
  * If you did not receive a copy of the license and are unable to
  * obtain it through the world-wide-web, please send an email
  * to license@prestashop.com so we can send you a copy immediately.
  *
  * @author  TiltUp Sp. z o.o
  * @copyright Since 2023 TiltUp Sp z o. o.
  * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0

 */

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('var/logs/tiltup_module.log', Logger::INFO));
$GLOBALS['tiltUpLogger'] = $logger;

class TiltUpSimpleLogger
{
    public static function info(string $msg): void
    {
        $GLOBALS['tiltUpLogger']->info($msg);
    }

    public static function error(string $msg): void
    {
        $GLOBALS['tiltUpLogger']->error($msg);
    }
}
