{**
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
*}

<section class="tiltup_order_confirmation_section">
    <p>{l s='Your order is now confirmed. Click the button to complete your payment.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
    <input type="button" class="tiltup_button" onclick="location.href='{$tiltUpRedirectUrl}';"
           value="{l s='Pay with Crypto using Ari10' d='Modules.Tiltupcryptopaymentsmodule.Shop'}"/>
</section>
