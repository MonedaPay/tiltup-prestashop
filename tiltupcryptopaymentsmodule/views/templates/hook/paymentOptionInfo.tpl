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

{if $isModuleConfigured == true}
    <section>
        <p>{l s='You will be redirected to Ari10 to have your payment processed. Please prepare your crypto wallet.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
    </section>
{else}
    <section class="alert alert-danger">
        <p>{l s='Oops, looks like you forgot to provide your Ari10 configuration. Configure the module in the Admin panel first.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
    </section>
{/if}