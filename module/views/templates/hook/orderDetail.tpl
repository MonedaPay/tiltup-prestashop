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
