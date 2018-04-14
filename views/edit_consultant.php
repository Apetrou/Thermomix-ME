<!DOCTYPE html>

<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php");

	$data = json_decode($_SESSION["selected_person"],true);
	// print_r($data);
?>

	<head>
		<title>Edit Consultant</title>
	</head>

	<body>
		<div class="title-bg" style="padding:10px;">
			<div class="pull-left"><h1>Edit Consultant</h1></div>
			<div class="pull-right">
				<!-- insert an image of a customer add -->
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="container" style="width:650px;">

			<form name="consultantForm" id="edit-consultant-form">

				<table class="table table-striped" id="edit-consultant-table">

					<div class="col-md-12">
						<td style="width:50%">
							<div class="form-group">
								<label class="control-label" for="consultant_title">Consultant Title</label>
								<select class="form-control input-sm" name="consultant_title" id="consultant_title">
									<option value="<?=$data['consultant_title']?>"><?=$data['consultant_title']?></option>
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
									<label class="control-label" for="consultant_first_name">Consultant First Name</label>
									<input type="text" name="consultant_first_name" id="consultant_first_name" value="<?=$data['consultant_first_name']?>" class="form-control customer-valid"/>
			    				</div>
							</td>
							<td>
								<div class="form-group">
									<label class="control-label" for="consultant_last_name">Consultant Last Name</label>
									<input type="text" name="consultant_last_name" id="consultant_last_name" value="<?=$data['consultant_last_name']?>" class="form-control customer-valid"/>
								</div>
							</td>
						</tr>
						<tr>
							<td><div class="form-group">
									<label class="control-label" for="consultant_city">City</label>	
									<select class="form-control input-sm customer-valid" id="consultant_city">
										<option value="<?=$data['consultant_city']?>"><?=$data['consultant_city']?></option>
										<option value="Dubai">Dubai</option>
										<option value="Abu Dhabi">Abu Dhabi</option>
										<option value="Other">Other</option>
									</select>
								</div>
							</td>
							<td><div class="form-group">
									<label class="control-label" for="consultant_country">Country</label>
									<input type="text" name="customer-country" id="customer-country" class="form-control customer-valid" value="<?=$data['consultant_country']?>"/>
								</div>
							</td>
							
						</tr>
						<tr>
							<td>
								<div class="form-group">
								<label class="control-label" for="consultant_tel_no">Telephone</label>
									<input type="text" name="consultant_tel_no" id="consultant_tel_no" class="form-control customer-valid" value="<?=$data['consultant_tel_no']?>"/>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label class="control-label" for="consultant_email">Email</label>
									<input type="text" name="consultant_email" id="consultant_email" value="<?=$data['consultant_email']?>" class="form-control customer-valid"
									data-bv-emailaddress="true"
			        				data-bv-emailaddress-message="The input is not a valid email address" />
								</div>
			    			</td>
						</tr>

					</table>
		</form>
	

			<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" id="save-edit"><span class="glyphicon glyphicon-floppy-saved" data-id="<?=$data['consultant_id']?>" data-form="consultantForm"></span> Save</button>
			<button type="button" class="btn btn-default pull-right" style="margin-right:10px;" id="restore-consultant-details"><span class="glyphicon glyphicon-refresh"></span> Restore Default</button>

		</div>

	</body>
</html>



