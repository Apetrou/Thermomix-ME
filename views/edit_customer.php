<!DOCTYPE html>

<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php");

	$data = json_decode($_SESSION["selected_person"],true);
?>

	<head>
		<title>Edit Customer</title>
	</head>

	<body>
		<div class="title-bg" style="padding:10px;">
			<div class="pull-left"><h1>Edit Customer</h1></div>
			<div class="pull-right">
				<!-- insert an image of a customer add -->
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="container" style="width:650px;">

			<form name="edit-customer-form" id="edit-customer-form">

				<table class="table table-striped" id="edit-consultant-table">

					<div class="col-md-12">
						<td style="width:50%">
							<div class="form-group">
								<label class="control-label" for="customer_title">Customer Title</label>
								<select class="form-control input-sm" name="customer_title" id="customer_title">
									<option value="<?=$data['title']?>"><?=$data['title']?></option>
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
									<input type="text" name="customer_first_name" id="customer_first_name" value="<?=$data['first_name']?>" class="form-control customer-valid"/>
			    				</div>
							</td>
							<td>
								<div class="form-group">
									<label class="control-label" for="customer_last_name">Customer Last Name</label>
									<input type="text" name="customer_last_name" id="customer_last_name" value="<?=$data['last_name']?>" class="form-control customer-valid"/>
								</div>
							</td>
						</tr>
						<tr>
							<td><div class="form-group">
									<label class="control-label" for="customer_city">City</label>	
									<select class="form-control input-sm customer-valid" id="customer_city">
										<option value="<?=$data['city']?>"><?=$data['city']?></option>
										<option value="Dubai">Dubai</option>
										<option value="Abu Dhabi">Abu Dhabi</option>
										<option value="Other">Other</option>
									</select>
								</div>
							</td>
							<td><div class="form-group">
									<label class="control-label" for="customer_country">Country</label>
									<input type="text" name="customer_country" id="customer_country" class="form-control customer-valid" value="<?=$data['country']?>"/>
								</div>
							</td>
							
						</tr>
						<tr>
							<td>
								<div class="form-group">
								<label class="control-label" for="customer_tel_no">Telephone</label>
									<input type="text" name="customer_tel_no" id="customer_tel_no" class="form-control customer-valid" value="<?=$data['tel_no']?>"/>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label class="control-label" for="customer_email">Email</label>
									<input type="text" name="customer_email" id="customer_email" value="<?=$data['email']?>" class="form-control customer-valid"
									data-bv-emailaddress="true"
			        				data-bv-emailaddress-message="The input is not a valid email address" />
								</div>
			    			</td>
						</tr>

					</table>

					<input type="hidden" name="id" id="id" value="<?=$data['id']?>"/>	
		</form>
	

			<button type="button" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:30px;" id="save-edit" data-id="<?=$data['customer_id']?>" data-form="customerForm"><span class="glyphicon glyphicon-floppy-saved"></span> Save</button>
			<button type="button" class="btn btn-default pull-right" style="margin-right:10px;" id="restore-details"><span class="glyphicon glyphicon-refresh"></span> Restore Default</button>

		</div>

	</body>
</html>



