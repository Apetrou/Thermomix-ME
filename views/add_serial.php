<!DOCTYPE html>
<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$data = json_decode(file_get_contents("php://input"),true);

 	$serials = $thermox->getUnregSerials($data["id"]);
	$count = $serials['data'][0]['count'];
?>

<html>
	<body>
		<div class="title-bg" style="padding:10px;">
			<div class="pull-left"><h1>Add TM Serial</h1></div>
			<div class="clearfix"></div>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title"><strong></strong></h3>
			</div>
			<div class="panel-body">
				<form class="form-inline" id="add-tm-serial" name="add-tm-serial">

				<?php for($i=0;$i<$count;$i++) { ?>
				 	<div class="col-md-12 col-xs-12">	
						<div class="form-group">
							<input autocomplete="off" type="text" class="form-control valid" name="tm-serial<?=$i+1?>" placeholder="Serial Number <?=$i+1?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				</form>
			</div>
		</div>	
		
		<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" data-customer-activity-id="<?=$data['id']?>" data-notification-id="<?=$data['notification_id']?>" id="add-tm-serial-number"><span class="glyphicon glyphicon-ok"></span> Add Serial(s)</button>
		<button type="button" class="btn btn-default pull-right" style="margin-right:10px;" id="cancel-fancybox">Cancel</button>

	</body>
</html>
