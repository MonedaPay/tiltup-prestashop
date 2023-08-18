{if $isModuleConfigured == true}
    <section>
        <p>{l s='You will be redirected to TiltUp to have your payment processed. Please prepare your crypto wallet.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
    </section>
{else}
    <section class="alert alert-danger">
        <p>{l s='Oops, looks like you forgot to provide your TiltUp configuration. Configure the module in the Admin panel first.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
    </section>
{/if}