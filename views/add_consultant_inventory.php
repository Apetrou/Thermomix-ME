<!DOCTYPE html>
<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 
	$data = json_decode($_SESSION['selected_person'],true);
	// print_r($data);
?>

<html>
	<body>
		<div class="title-bg" style="padding:10px;">
			<div class="pull-left"><h1>Add Inventory For User</h1></div>
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
				<form  id="add-contact-form" name="add-consultant-inventory">
					<div class="form-group col-md-6 col-sm-6">
						<label class="sr-only" for="add-item-cons">Item Search</label>
						<input autocomplete="off" type="text" class="form-control" style="width: 100%" id="item-search-cons" name="item-search-cons" placeholder="Item Search">
					</div>
					<div class="form-group col-md-3 col-sm-3">
						<label class="sr-only" for="add-quantity-cons">Quantity</label>
						<input type="number" class="form-control" style="width= 100%" id="add-quantity-cons" name="add-quantity-cons" placeholder="Quantity">
					</div>
					<div class="form-group col-md-3 col-sm-3">
						<button type="button" class="btn btn-default" id="add-item-cons" style="width: 35px; padding: 6px 6px;"><span class="glyphicon glyphicon-plus"></span></button>
					</div>	
				</form>
			</div>
			<table class="table contacts-table table-striped" id="item-table-cons" style="margin-bottom:2px;">
				<tr>
					<th style="width:20px;"></th>
					<th>Item Code</th>
					<th>Item Name</th>
					<th>Quantity</th>
				</tr>
				
		  	</table>
		</div>	
		
		<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" data-user-id="<?=$data['id'];?>" id="add-consultant-inventory"><span class="glyphicon glyphicon-ok"></span> Add Inventory</button>
		<button type="button" class="btn btn-default pull-right" style="margin-right:10px;" id="cancel-fancybox">Cancel</button>

	</body>
</html>


