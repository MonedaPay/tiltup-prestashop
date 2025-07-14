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
class TiltUpAggregatedOrderStatus
{
    public const CREATED = 'CREATED';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const UNDERPAID = 'UNDERPAID';
    public const OVERPAID = 'OVERPAID';
    public const SUCCESS = 'SUCCESS';
    public const FAILURE = 'FAILURE';
    public const AML_SCREENING = 'AML_SCREENING';
}
