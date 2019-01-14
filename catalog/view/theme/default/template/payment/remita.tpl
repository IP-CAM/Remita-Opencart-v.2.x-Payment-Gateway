<?php if (!$remita_mode) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $text_testmode; ?></div>
<?php } ?>
<form name="webpayform" method="post" action="<?php echo $gateway_url; ?>">
<input id="publicKey" name="publicKey" value="<?php echo $remita_publickey; ?>" type="hidden"/>
<input id="secretKey" name="secretKey" value="<?php echo $remita_secretkey; ?>" type="hidden"/>
<input id="amt" name="amt" value="<?php echo $totalAmount; ?>" type="hidden"/>
<input id="responseurl" name="responseurl" value="<?php echo $returnurl; ?>" type="hidden"/>
<input id="txnHash" name="txnHash" value="<?php echo $txnHash; ?>" type="hidden"/>
<input id="payerName" name="payerName" value="<?php echo $payerName; ?>" type="hidden"/>
<input id="payment_firstname" name="payment_firstname" value="<?php echo $payment_firstname; ?>" type="hidden"/>
<input id="payment_lastname" name="payment_lastname" value="<?php echo $payment_lastname; ?>" type="hidden"/>
<input id="payerEmail" name="payerEmail" value="<?php echo $payerEmail; ?>" type="hidden"/>
<input id="payerPhone" name="payerPhone" value="<?php echo $payerPhone; ?>" type="hidden"/>
<input id="storeorderid" name="storeorderid" value="<?php echo $storeorderid; ?>" type="hidden"/>
<input id="totalAmount" name="totalAmount" value="<?php echo $totalAmount; ?>" type="hidden"/>
<input id="gateway_url" name="gateway_url" value="<?php echo $gateway_url; ?>" type="hidden"/>
<input id="returnurl" name="returnurl" value="<?php echo $returnurl; ?>" type="hidden"/>

<div class="buttons">

	<script src="<?php echo $gateway_url; ?>"></script>
	<div class="buttons">
		<div class="pull-right">
			<input type="button"  onclick="makePayment()" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
		</div>
	</div>
</div>
</form>

 <script>
     function makePayment() {
         var paymentEngine = RmPaymentEngine.init({
             key: "<?php echo $remita_publickey; ?>",
             customerId: "<?php echo $storeorderid; ?>",
             firstName: "<?php echo $payment_firstname; ?>",
             transactionId: "<?php echo $transactionId; ?>",
             lastName: "<?php echo $payment_lastname; ?>",
             narration: "bill pay",
             email: "<?php echo $payerEmail; ?>",
             amount: "<?php echo $totalAmount; ?>",
             onSuccess: function (response) {
                 window.location.href='<?php echo html_entity_decode($returnurl); ?>';
             },
             onError: function (response) {
                 console.log('callback Error Response', response);
             },
             onClose: function () {
                 console.log("closed");
             }
         });

         paymentEngine.showPaymentWidget();
     }
 </script>
