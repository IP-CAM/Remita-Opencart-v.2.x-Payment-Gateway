<?php echo $header; ?><?php echo $column_left; ?>
<div style="text-align: center;">
<?php if($response_code == '00' || $response_code == '01') { ?>
<h2>Transaction Successful</h2>
<p><b>Your Payment Has Been Received</b></p>
<p>You can view your Purchase History from your "Account Page"</p>
<p><b>Remita Retrieval Reference: </b><?php echo $rrr; ?><p>
<div class="buttons">
     <div class="right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
</div>
<?php }else if($response_code == '021') { ?>
<h2>RRR Generated Successfully</h2>
<p>You can make payment for the RRR by visiting the nearest ATM or POS.</p>
<p><b>Remita Retrieval Reference: </b><?php echo $rrr; ?><p>
<div class="buttons">
     <div class="right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
</div>
<?php } else{ ?>
<h2>Your Transaction was not Successful</h2>
<p>Payment for this order was not received.</p>
<?php if ($rrr !=null){ ?>
  <p>Your Remita Retrieval Reference is <span><b><?php echo $rrr; ?></b></span><br />
<?php } ?> 
<?php if ($response_reason !=null) { ?>
  <p><b>Reason: </b><?php echo $response_reason; ?><p>
  <?php } ?> 
  <div class="buttons">
  	<div class="right"><a href="<?php echo $fail_continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
  </div>
</div>
<?php }?>
<?php echo $column_right; ?>
<?php echo $content_bottom; ?></div>
<?php echo $footer; ?>