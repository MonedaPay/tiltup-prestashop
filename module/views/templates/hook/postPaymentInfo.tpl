<section>
    <p>{l s='Your order is now confirmed. Click the button to complete your payment.' d='Modules.Tiltupcryptopaymentsmodule.Shop'}</p>
    <ul>
        <li>OrderID: {$orderId}</li>
        <li>Total Amount: {$totalAmount}</li>
    </ul>
    <input type="button" class="btn btn-primary" onclick="location.href='{$tiltUpRedirectUrl}';"
           value="Pay with TiltUp"/>
</section>