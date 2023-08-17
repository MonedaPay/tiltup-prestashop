<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('name');
$log->pushHandler(new StreamHandler('var/logs/tiltup_payments_module.log', Logger::INFO));
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
