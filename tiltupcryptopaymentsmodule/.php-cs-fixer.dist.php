<?php
/**
 *
  * Copyright since 2023 Moneda Solutions Ltd.
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
  * @author  Moneda Solutions Ltd.
  * @copyright Since 2023 Moneda Solutions Ltd.
  * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0

 */

$config = new PrestaShop\CodingStandards\CsFixer\Config();

/** @var \Symfony\Component\Finder\Finder $finder */
$finder = $config->setUsingCache(true)->getFinder();
$finder->in(__DIR__)->exclude('vendor');

return $config;
