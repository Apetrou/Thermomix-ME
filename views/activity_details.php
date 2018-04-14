<?php 
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	if ($_SERVER['REQUEST_METHOD'] == 'POST')	{
		$id = json_decode(file_get_contents("php://input"),true);
	}

	$data = $thermox->getPurchaseDetails($id);

	if($data["success"]) {
		$data = $data["data"];
	} else {
		die("There was an error.");
	}

?>

	<div class="panel panel-thermox">
		<div class="panel-heading">
			<h3 class="panel-title top">Details</h3>
		</div>
		<div class="panel-body">	
			<div class="col-md-12">
				<table class="table table-striped">
					<tr>
						<th></th>
						<th>Item</th>
						<th>Quantity</th>
					</tr>
				<?php
					foreach($data as $purchase) {
				?>
					<tr>
						<td><span class="glyphicon glyphicon-usd"></span></td>
						<td>
							<?php echo $purchase['purchase_material_name']; ?>
						</td>
						<td class="text-center">
							<?php echo $purchase['purchase_material_quantity']; ?>
						</td>
					</tr>
				<?php } ?>
				</table>
			</div>
		</div>
	</div>

