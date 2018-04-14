<!DOCTYPE html>

<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php");
	include_once("../ajax/get_consultants.php");

	$users = $ret["response"];

	$customer_details = json_decode($_SESSION['selected_person'],true);
	// print_r($customer_details);	

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	  $data = json_decode(file_get_contents("php://input"),true);

	  $action = $data["action"];
	  $view = $data["id"];
	  $uid = $data["user_id"];
	  $nid = $data["notification_id"];

	  	if(isset($view)) {
		  	$row = json_decode($thermox->getActivityData($dbConnected,$view),true);
		  	$row = $row[0];

		  	foreach($row as $key=>$value) {
		  		if($value == "") {
		  			?>
		  				<script type="text/javascript">
		  					$('select[name="<?php echo $key; ?>"]').addClass('error-radcheck');
		  					$('input[name="<?php echo $key; ?>"]').addClass('error-radcheck');
		  				</script>
		  			<?php
		  		}
		  	}
	  	}
	
	 	if($action == "add-activity") {
	 		$add = 1;
	 	} else if($action == "edit-customer") {
	 		$edit = 1;
	 	} else if($action == "add-details") {
	 		$add_detail = 1;
	 	}

	}

?>



	<head>
		<title>Add Customer</title>
	</head>

	<body>
		<div class="title-bg" style="padding:10px;">
			<div class="pull-left"><h1><?php if($add){ ?> Add Purchase <?php } else if($add_detail) { ?> Edit Purchase Details <?php } else { ?> Add Customer<?php } ?></h1></div>
			<div class="pull-right">
				<!-- insert an image of a customer add -->
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="container" style="width:650px;">
			<form name="customerForm" id="customerForm">
				<table class="table table-striped" id="add-customer-table">
					<?php if(isset($_SESSION["user"]["user_type"]) && $_SESSION["user"]["user_type"] == 1) { ?>
					<tr>
						<td>
							<div class="form-group">
								<label style="margin-top:20px">The purchase will be under:</label>
								<select class="form-control input-sm form-inline" name="user" id="user" <?php if(isset($view)) { ?> disabled <?php } ?>>
									<option value="<?=$_SESSION['user']['user_id']?>"><?=$_SESSION['user']['user_formatted_name']?></option>
								  <?php
								    for($i=0;$i<sizeof($users['id']);$i++) { ?>
								      <option value="<?= $users['id'][$i] ?>"><?=$users['full_name'][$i]?></option>
								  <?php } ?>
								</select> 
							</div>
						</td>
						<td></td>
					</tr>
					<?php } else { ?>
						<input type="hidden" id="user" name="user" value="<?=$_SESSION['user']['user_id']?>"/>
					<?php } ?>
					<tr>
						<td colspan="2">
						<?php if(!$add_detail) {
							 if(isset($_SESSION["user"]["user_type"]) && $_SESSION["user"]["user_type"] != 1) { ?>
							<div class="form-group">
							    <label></label>
								<div class="btn-group" data-toggle="buttons"  style="display:block;">
								  	<label class="btn btn-default <?php if (isset($view)) { if ("Yes" == $row["patient_taking_anti_coagulants"]) { ?> active <?php } } ?>" style="width:50%;">
								    	<input type="radio" class="mandatory-field" data-type="TM Purchase" name="customer_activity" id="option1" autocomplete="off" <?php if (isset($view)) { if ("TM5 Domestic Purchase" == $row["activity_notes"]) { ?> checked <?php } } ?> value="TM5 Domestic Purchase">TM5 Domestic Purchase
								  	</label>
									<label class="btn btn-default <?php if (isset($view)) { if ("Books/Parts Purchase" == $row["activity_notes"]) { ?> active <?php } } ?>" style="width:50%;">
								    	<input type="radio" class="mandatory-field" name="customer_activity" id="option2" autocomplete="off" <?php if (isset($view)) { if ("Books/Parts Purchase" == $row["activity_notes"]) { ?> checked <?php } } ?> value="Books/Parts Purchase">Purchase Books and Parts
								  	</label>
								</div>
								<div class="clearfix"></div>
							</div>
						<?php } else { ?>
							<div class="form-group">
							    <label></label>
								<div class="btn-group" data-toggle="buttons"  style="display:block;">
								  	<label class="btn btn-default <?php if (isset($view)) { if ("Yes" == $row["patient_taking_anti_coagulants"]) { ?> active <?php } } ?>" style="width:50%;">
								    	<input type="radio" class="mandatory-field" data-type="TM Purchase" name="customer_activity" id="option1" autocomplete="off" <?php if (isset($view)) { if ("TM5 Domestic Purchase" == $row["activity_notes"]) { ?> checked <?php } } ?> value="TM5 Domestic Purchase">TM5 Domestic Purchase
								  	</label>
								  	<label class="btn btn-default <?php if (isset($view)) { if ("TM5 Commercial Purchase" == $row["activity_notes"]) { ?> active <?php } } ?>" style="width:50%;">
								    	<input type="radio" class="mandatory-field" name="customer_activity" id="option2" autocomplete="off" <?php if (isset($view)) { if ("TM5 Commercial Purchase" == $row["activity_notes"]) { ?> checked <?php } } ?> value="TM5 Commercial Purchase">TM5 Commercial Purchase
								  	</label>
							  		<label class="btn btn-default <?php if (isset($view)) { if ("Repaired Machine Not Under Warranty" == $row["activity_notes"]) { ?> active <?php } } ?>" style="width:50%;">
								    	<input type="radio" class="mandatory-field" name="customer_activity" id="option2" autocomplete="off" <?php if (isset($view)) { if ("Repaired Machine Not Under Warranty" == $row["activity_notes"]) { ?> checked <?php } } ?> value="Repaired Machine Not Under Warranty">Repair
								  	</label>
								  	<label class="btn btn-default <?php if (isset($view)) { if ("Repaired Machine Under Warranty" == $row["activity_notes"]) { ?> active <?php } } ?>" style="width:50%;">
								    	<input type="radio" class="mandatory-field" name="customer_activity" id="option2" autocomplete="off" <?php if (isset($view)) { if ("Repaired Machine Not Under Warranty" == $row["activity_notes"]) { ?> checked <?php } } ?> value="Repaired Machine Under Warranty">Repair under Warranty
								  	</label>
								  	<label class="btn btn-default <?php if (isset($view)) { if ("Books/Parts Purchase" == $row["activity_notes"]) { ?> active <?php } } ?>" style="width:100%;">
								    	<input type="radio" class="mandatory-field" name="customer_activity" id="option2" autocomplete="off" <?php if (isset($view)) { if ("Books/Parts Purchase" == $row["activity_notes"]) { ?> checked <?php } } ?> value="Books/Parts Purchase">Purchase Books and Parts
								  	</label>
								</div>
								<div class="clearfix"></div>
							</div>
						<?php } } ?>
										
						</td>
					</tr>
			<?php
				if(!$add && !$add_detail) {
			?>
					<tr>
						<td style="width:50%">
							<div class="form-group">
								<label class="control-label" for="customer_title">Customer Title</label>
								<select class="form-control input-sm" name="customer_title" id="customer_title">
									<option value="">Please select...</option>
									<option value="">No Title</option>
									<option value="Mrs">Mrs</option>
									<option value="Mr">Mr</option>
									<option value="Miss">Miss</option>
									<option value="Dr">Dr</option>
								</select>
							</div>
						</td>
						
					<tr>
						<td>
							<div class="form-group">
								<label class="control-label" for="customer_first_name">Customer First Name</label>
								<input type="text" name="customer_first_name" id="customer_first_name" class="form-control mandatory-field"
									data-bv-notempty="true"
	                				data-bv-notempty-message="The first name is required and cannot be empty"/>
            				</div>
						</td>
						<td>
							<div class="form-group">
								<label class="control-label" for="customer_last_name">Customer Last Name</label>
								<input type="text" name="customer_last_name" id="customer_last_name" class="form-control"/>
							</div>
						</td>
					</tr>
					<tr>
						<td><div class="form-group">
								<label class="control-label" for="customer_city">City</label>	
								<select class="form-control input-sm" id="customer_city" name="customer_city">
									<option value="">Please select...</option>
									<option value="Dubai">Dubai</option>
									<option value="Abu Dhabi">Abu Dhabi</option>
									<option value="Other">Other</option>
								</select>
							</div>
						</td>
						<td><div class="form-group">
								<label class="control-label" for="customer_country">Country</label>
								<input type="text" name="customer_country" id="customer_country" class="form-control valid" value="UAE"/>
							</div>
						</td>
						
					</tr>
					<tr>
						<td colspan="2"><strong>
							<div class="form-group">
								<label class="control-label" for="customer_address">Address</label>
								<textarea class="form-control" rows="3" name="customer_address" id="customer_address"></textarea>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="form-group">
							<label class="control-label" for="customer_tel_no">Telephone</label>
								<input type="text" name="customer_tel_no" id="customer_tel_no" class="form-control"/>
							</div>
						</td>
						<td>
							<div class="form-group">
								<label class="control-label" for="customer_email-email">Email</label>
								<input type="text" name="customer_email" id="customer_email" class="form-control mandatory-field"
								data-bv-emailaddress="true"
                				data-bv-emailaddress-message="The input is not a valid email address" />
        					</div>
            			</td>
					</tr>

			<?php } ?>	
					<tr>
						<td>
							<div class="form-group">
								<label class="control-label" for="payment_method">Payment Method</label>
								<select class="form-control input-sm" id="payment-method" name="payment_method">
									<option value="">Please select...</option>
									<option value="cash" <?php if(isset($view) && $row['payment_method'] == "cash") { ?> selected <?php } ?>>Cash</option>
									<option value="credit_debit_card" <?php if(isset($view) && $row['payment_method'] == "credit_debit_card") { ?> selected <?php } ?>>Credit/Debit Card</option>
									<option value="cheque" <?php if(isset($view) && $row['payment_method'] == "cheque") { ?> selected <?php } ?>>Cheque</option>
									<option value="transfer" <?php if(isset($view) && $row['payment_method'] == "transfer") { ?> selected <?php } ?>>Transfer</option>
									<option value="warranty" <?php if(isset($view) && $row['payment_method'] == "warranty") { ?> selected <?php } ?>>Warranty</option>
								</select>
							</div>
						</td>
						<td style="width:50%">
							<div class="form-group" id="invoice_number_cont">
								<!-- <label class="control-label" for="invoice_number">Invoice Number</label> -->
								<!-- <input type="text" name="invoice_number" id="invoice_number" class="form-control mandatory-field" value="<?php if(isset($view)) { echo $row['invoice_num']; }?>"/> -->
							</div>
						</td>
					</tr>
					
		
				</table>
				<input type="hidden" id="user_name" name="user_name" value="<?=$_SESSION['user']['user_formatted_name']?>"/>
				<input type="hidden" id="user_type" name="user_type" value="<?=$_SESSION['user']['user_type']?>"/>
				<input type="hidden" name="activity_type" id="activity_type" value=""/>
			<?php if($add) { ?>
				<input type="hidden" name="customer_name" value="<?=$customer_details['full_name']?>"/>
				<input type="hidden" name="has_serial" id="has_serial" value="<?php if(sizeof($customer_details['serial_numbers']) > 0) { echo 1; } ?>"/>
			<?php } ?>

		  </form>
	
		<?php if(!$add_detail) { ?>

		<?php if(isset($_SESSION["user"]["user_type"]) && $_SESSION["user"]["user_type"] == 1) { ?>

			<div class="panel panel-thermox">
				<div class="panel-heading">
					<h3 class="panel-title top"><strong>Purchase(s)</strong></h3>
				</div>
				<div class="panel-body">
					<form class="" id="add-contact-form" name="add-contact-form">
						<div class="col-md-12 col-sm-12">
							<div class="alert alert-info">
							  <span class="glyphicon glyphicon glyphicon-info-sign"></span> You can only select one TM at a time.
							</div>
						</div>
						<div class="form-group col-md-6 col-sm-6">
							<label class="sr-only" for="add-item">Item Search</label>
							<input autocomplete="off" type="text" class="form-control" style="width= 100%" id="item-search" name="item-search" placeholder="Item Search">
						</div>
						<div class="form-group col-md-3 col-sm-3">
							<label class="sr-only" for="add-quantity">Quantity</label>
							<input type="number" class="form-control" style="width= 100%" id="add-quantity" name="add-quantity" placeholder="Quantity">
						</div>
					<!-- <?php if($add && isset($customer_details['serial_numbers']) && sizeof($customer_details['serial_numbers']) > 0) { ?> -->
						<!-- <div class="form-group col-md-3 col-sm-3" style="display: none;">
							<select class="form-control input-sm" name="serial_number" id="serial_number_select">
							<?php foreach($customer_details['serial_numbers'] as $serial) { 
								echo '<option value="'.$serial['serial_num'].'">'.$serial['serial_num'].'</option>';
							} ?>
							</select>
						</div> -->
					<!-- <?php } ?> -->
						<div class="form-group col-md-3 col-sm-3" style="display: none;">
							<label class="sr-only" for="add-tm-serial">Serial No</label>
							<input type="text" class="form-control" style="width= 100%" id="add-tm-serial" name="add-tm-serial" placeholder="Serial No" disabled>
						</div>
						<div class="form-group col-md-3 col-sm-3">
							<button type="button" class="btn btn-default puill-right" id="add-item" style="width: 35px; padding: 6px 6px;"><span class="glyphicon glyphicon-plus"></span></button>
						</div>
					</form>
				</div>
				<table class="table contacts-table table-striped" id="item-table" style="margin-bottom:2px;">
					<tr id="header">
						<th style="width:20px;"></th>
						<th>Item Code</th>
						<th>Item Name</th>
						<th>Quantity</th>
						<th>Serial</th>
					</tr>
					
			  	</table>
				</div>	
			</div>
		<?php } else { ?>
			<div class="panel panel-thermox">
				<div class="panel-heading">
					<h3 class="panel-title top"><strong>Purchase(s)</strong></h3>
				</div>
				<div class="panel-body">
					<form class="" id="add-contact-form" name="add-contact-form">
						<div class="col-md-12 col-sm-12">
							<div class="alert alert-info">
							  <span class="glyphicon glyphicon glyphicon-info-sign"></span> You can only select one TM at a time.
							</div>
						</div>
						<div class="form-group col-md-6 col-sm-6">
							<label class="sr-only" for="add-item">Item Search</label>
							<input autocomplete="off" type="text" class="form-control" id="item-search" name="item-search" placeholder="Item Search">
						</div>
						<div class="form-group col-md-3 col-sm-3">
							<label class="sr-only" for="add-quantity">Quantity</label>
							<input type="number" class="form-control" style="width= 100%" id="add-quantity" name="add-quantity" placeholder="Quantity">
						</div>
						<div class="form-group col-md-3 col-sm-3">
							<button type="button" class="btn btn-default" id="add-item" style="width: 35px; padding: 6px 6px;"><span class="glyphicon glyphicon-plus"></span></button>
						</div>
					</form>
				</div>
				<table class="table contacts-table table-striped" id="item-table" style="margin-bottom:2px;">
					<tr id="header">
						<th style="width:20px;"></th>
						<th>Item Code</th>
						<th>Item Name</th>
						<th>Quantity</th>
					</tr>
					
			  	</table>
				</div>	
			</div>

		<?php } ?>

		<?php } ?>

		<input type="hidden" name="item_id" id="item_id"/>

		<?php if($add) { ?>
			<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" id="add-purchase" data-id="<?=$customer_details['id']?>"><span class="glyphicon glyphicon-ok"></span> Add Purchase</button>
		<?php } else if ($add_detail){ ?>
			<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" id="add-details" data-id="<?=$data['id']?>" data-user-id="<?=$uid?>" data-notif-id="<?=$nid?>"><span class="glyphicon glyphicon-ok"></span> Add Details</button>
		<?php } else { ?>
			<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" id="add-customer"><span class="glyphicon glyphicon-ok"></span> Add Customer</button>
		<?php } ?>
			<button type="button" class="btn btn-default pull-right" style="margin-right:10px;" id="cancel-fancybox">Cancel</button>

		</div>

	</body>
</html>



