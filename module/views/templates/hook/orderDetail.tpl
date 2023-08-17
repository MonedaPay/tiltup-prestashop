{if $isPaymentIncomplete == true}
    <section>
        <p>{l s='Looks like your order is still not fully paid. Click the button to complete your payment.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
        <input type="button" class="btn btn-primary" onclick="location.href='{$tiltUpRedirectUrl}';"
               value="Pay with TiltUp"/>
    </section>
{/if}
