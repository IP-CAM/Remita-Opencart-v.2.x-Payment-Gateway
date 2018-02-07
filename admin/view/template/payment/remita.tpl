<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-remita" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
     <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-remita" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-vendor"><?php echo $entry_mercid; ?></label>
            <div class="col-sm-10">
              <input type="text" name="remita_mercid" value="<?php echo $remita_mercid; ?>" placeholder="<?php echo $remita_mercid; ?>" id="input-vendor" class="form-control" />
              <?php if ($error_mercid) { ?>
              <div class="text-danger"><?php echo $error_mercid; ?></div>
              <?php } ?>
            </div>
          </div>
		    <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-vendor"><?php echo $entry_servicetypeid; ?></label>
            <div class="col-sm-10">
              <input type="text" name="remita_servicetypeid" value="<?php echo $remita_servicetypeid; ?>" placeholder="<?php echo $remita_servicetypeid; ?>" id="input-vendor" class="form-control" />
              <?php if ($error_servicetypeid) { ?>
              <div class="text-danger"><?php echo $error_servicetypeid; ?></div>
              <?php } ?>
            </div>
          </div>
		    <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-vendor"><?php echo $entry_apikey; ?></label>
            <div class="col-sm-10">
              <input type="text" name="remita_apikey" value="<?php echo $remita_apikey; ?>" placeholder="<?php echo $remita_apikey; ?>" id="input-vendor" class="form-control" />
              <?php if ($error_apikey) { ?>
              <div class="text-danger"><?php echo $error_apikey; ?></div>
              <?php } ?>
            </div>
          </div>
		    <div class="form-group">
            <label class="col-sm-2 control-label" for="input-test"><?php echo $entry_test; ?></label>
            <div class="col-sm-10">
              <select name="remita_mode" id="input-test" class="form-control">
                <?php if ($remita_mode == 'test') { ?>
                <option value="test" selected="selected"><?php echo $text_test; ?></option>
                <?php } else { ?>
                <option value="test"><?php echo $text_test; ?></option>
                <?php } ?>
                <?php if ($remita_mode == 'live') { ?>
                <option value="live" selected="selected"><?php echo $text_live; ?></option>
                <?php } else { ?>
                <option value="live"><?php echo $text_live; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
		      <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_paymentoptions; ?></label>
            <div class="col-sm-10">
              <select name="remita_paymentoptions[]" id="remita_paymentoptions" multiple class="form-control">
					<?php 
					foreach ($paymentOptions as $key=>$value) 
					{
					echo "<option value=".$key.">".$value."</option>";
						
					} 
				?>							
              </select>
            </div>
          </div>
		      <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_processed_status; ?></label>
            <div class="col-sm-10">
              <select name="remita_processed_status_id" id="input-order-status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $remita_processed_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="remita_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $remita_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
		     <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="remita_status" id="input-status" class="form-control">
                <?php if ($remita_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="remita_sort_order" value="<?php echo $remita_sort_order; ?>" placeholder="<?php echo $remita_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
		     <div class="form-group">
            <label class="col-sm-2 control-label" for="remita-token"><span data-toggle="tooltip" title="Make this long and hard to guess"><?php echo $entry_token; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="remita_token" value="<?php echo $remita_token; ?>" placeholder="<?php echo $entry_token; ?>" id="remita-token" class="form-control" />
              </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="notification-url"><span data-toggle="tooltip" title="Copy The Notification URL and Paste in your Remita Profile."><?php echo $entry_notification_url; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="notification_url" readonly value="<?php echo $remita_notification_url ?>" placeholder="<?php echo $entry_notification_url; ?>" id="input-cron-job-url" class="form-control" />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
<script type="text/javascript">
	$(function() {
	var data = "<?php $remita_paymentoptions; 
	$prefix = ''; 
	$paymentmodeList ='';
	foreach ($remita_paymentoptions as $code=>$name){
	$paymentmodeList .= $prefix . $name;
	$prefix = ',';
	}
	echo $paymentmodeList;
	?>";
	var dataarray = data.split(",");
	$("#remita_paymentoptions").val(dataarray);
		});
</script>
<?php echo $footer; ?> 