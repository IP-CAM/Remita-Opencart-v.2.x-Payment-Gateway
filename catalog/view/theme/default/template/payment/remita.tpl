 <form name="webpayform" method="post" action="<?php echo $gateway_url; ?>">
<input id="merchantId" name="merchantId" value="<?php echo $remita_mercid; ?>" type="hidden"/>
<input id="serviceTypeId" name="serviceTypeId" value="<?php echo $remita_servicetypeid; ?>" type="hidden"/>
<input id="amt" name="amt" value="<?php echo $totalAmount; ?>" type="hidden"/>
<input id="responseurl" name="responseurl" value="<?php echo $returnurl; ?>" type="hidden"/>
<input id="hash" name="hash" value="<?php echo $hash; ?>" type="hidden"/>
<input id="payerName" name="payerName" value="<?php echo $payerName; ?>" type="hidden"/>
<input id="payerEmail" name="payerEmail" value="<?php echo $payerEmail; ?>" type="hidden"/>
<input id="payerPhone" name="payerPhone" value="<?php echo $payerPhone; ?>" type="hidden"/>
<input id="orderId" name="orderId" value="<?php echo $orderid; ?>" type="hidden"/>
	<div class="form-group">
<label class="col-sm-3 control-label" for="input-cc-type">Payment Type</label>
<div class="col-sm-7">
  <select name="paymenttype" id="input-cc-type" class="form-control">
		<option>-- Select Payment Type --</option>
								<?php  foreach( $paymentOptions as $key => $value ) { ?>
									<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
								<?php } ?>
	 </select>
</div>
</div>
<div class="buttons">
  <div class="pull-right">
    <input type="submit" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
  </div>
</div>
</form>
