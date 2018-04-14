<!DOCTYPE html>
<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	if(isset($_POST['quantity'])) {
		$no_fields = $_POST['quantity'];
		$user_id = $_POST['user_id'];
	} else {
		die("POST variables not set!");
	}
?>

<html>
	<body>
		<div class="title-bg" style="padding:10px;">
			<div class="pull-left"><h4><span class="glyphicon glyphicon-plus"></span> TM Invoice Numbers</h4></div>
			<div class="pull-right">
				<!-- insert an image of a customer add -->
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title"><strong></strong></h3>
			</div>
			<div class="panel-body">
				<form id="thermo_invoice_nos" name="thermo_invoice_nos">
					<?php
						for($i=0;$i<$no_fields;$i++) { 
					?>
						<label>Invoice number TM5 <?php echo $i+1; ?>: </label><input type="number" class="invoice_nos form-control col-md-12"  id="<?php echo $i; ?>" name="thermomix_invoice_number<?php echo $i;?>"/> 
						<div class="clearfix"></div>
						<br>
						<br>
					<?php 
						}
					?>
				</form>
			</div>
		
		</div>	
		
		<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" data-user-id="<?=$user_id?>" id="add-tm-invoice-num"><span class="glyphicon glyphicon-ok"></span> Add</button>

	</body>
</html>


