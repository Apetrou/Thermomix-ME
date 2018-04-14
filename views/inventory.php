<?php
 	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 		 

	$stock_content = "";


	// $return = $thermox->getInventory($_SESSION['user']['user_type']);
	// var_dump($return);

	$sql = "SELECT code, material_name, quantity FROM stock ORDER BY code";
	$result = mysqli_query($dbConnected, $sql);

	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
		$data[] = $row;
		if($data["quantity"] < 5) {
			// $data[]["flag"] = "low";
			// mail($master_email,"Low stock on item - ".$data["material_name"],"Stock has fallen below 5, please order more stock for item code - ".$data["code"]);
		}
	}
	

	// $thermox->getInventory($_SESSION['user']['user_type']);
?>
    <h2>Inventory Menu</h2>
	<div class="panel panel-thermox">
		<div class="panel-heading">
			<h3 class="panel-title top">Search For Item <span class="pull-right glyphicon glyphicon-minus show-hide-panel hoverable"></span></h3>
		</div>
		<div class="panel-body">
		<!-- <div class="container-fluid responsive-utilities"  id="parts" style="width:800px; margin:0 auto;"> -->
			<div class="row">
		 		<div class="input-group" style="padding-top:20px;padding-bottom:20px;">
		 			<input type="text" id="material-search" class="col-md-12 form-control input-lg" id="item-search" placeholder="Search for item by name or serial number"></input>
		 			<span class="input-group-btn">
						<button class="btn btn-default" style="height:46px;" id="btn-tm5-search" type="submit"><i class="fa fa-search"></i></button>
					</span>
		 		</div>
		 	</div>
		 	<a href="#" class="ignore search dropdown-toggle" title="Search" data-toggle="dropdown"></a>
		 	</div>
		 </div>
		 <div class="panel panel-thermox main-bottom-margin">
		 	<div class="panel-heading">
				<h3 class="panel-title top">Inventory Levels</h3>
			</div>
		 	<div class="panel-body">
		 	<table class="table table-striped table-hover" width="10" id="inventory_table">
				<tr class="bg-default">
					<th width="15%">Serial no.</th>
			 		<th width="50%">Part name</th>
					<th>Quantity</th>
				</tr>
						
			<?php
							
			for($i=0;$i<sizeof($data);$i++){ 
				if($data[$i]["quantity"] < 5) { ?>
				<tr class="alert alert-warning">
				<?php } else { ?>
				<tr>
				<?php } ?>
					<td><?php echo $data[$i]['code']; ?></td>
					<td><?php echo $data[$i]['material_name']; ?></td>
					<td><input class="col-md-12 form-control input-lg" type="number" data-code="<?=$data[$i]['code']?>" name="stock-levels" style=" text-align: center "value="<?=$data[$i]['quantity']?>" readonly></input></td>
				</tr>

			<?php }	 ?>

			</table>
		</div>
	</div>

<?php if($_SESSION['user']['user_type'] == 1) { ?>
	<div class="action-buttons">
		<button id="reset-stock" class="btn btn-warning" style="margin-right:5px;">Reset</button>
		<button id="edit-stock" class="btn btn-success" style="margin-right:5px;">Edit inventory</button>
		<button id="save-stock" class="btn btn-success" style="margin-right:5px;" disabled>Save inventory</button>
	</div>
<?php } ?>

		
				


                        
                            
                           
                                
                            
                        
                   	 

				
						
							
	
							
						

					
						
						
						
					
