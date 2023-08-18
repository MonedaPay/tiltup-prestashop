{*
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
*}

{if $isPaymentIncomplete == true || $isPaymentCancelled == true}
    <section>
        {if $isPaymentIncomplete == true}
            <p>{l s='Looks like your order is still not fully paid. Click the button to complete your payment.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
        {/if}
        {if $isPaymentCancelled == true}
            <p>{l s='Looks like you cancelled your payment with TiltUp. Click the button to try again.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
        {/if}
        <input type="button" class="btn btn-primary" onclick="location.href='{$tiltUpRedirectUrl}';"
               value="Pay with TiltUp"/>
    </section>
{/if}
