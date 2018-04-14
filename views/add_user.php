<?php 
	$team_leaders =  $thermox->getTeamLeaders($dbConnected);
?>
    <h2>Add User</h2>

    <form id="add_user_form">
        <div class="panel panel-thermox">
    		<div class="panel-heading">
	    		<h3 class="panel-title top">User Details</h3>
	  		</div>
		  	<div class="panel-body">

				<div class="col-md-12">
					<div class="form-group">
					    <label>User Type</label>
						<div class="btn-group" data-toggle="buttons"  style="display:block;">
						  	<label class="btn btn-default" style="width:50%;">
						    	<input type="radio" class="mandatory-field" name="user_type_radio" id="option1" autocomplete="off" value="3">Consultant
						  	</label>
						  	<label class="btn btn-default" style="width:50%;">
						    	<input type="radio" class="mandatory-field" name="user_type_radio" id="option2" autocomplete="off" value="2">Team Leader
						  	</label>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>

				<div class="col-md-6 col-xs-6 hidden" id="parent_user_cont">
  					<div class="form-group">
					    <label for="user_type">Select consultants team leader</label>
					    <select class="form-control input-sm mandatory-field" id="parent_user" name="parent_user">
					    <option value="">Please Select...</option>
					    <?php foreach($team_leaders as $leaders) { ?>
					    	<option value="<?=$leaders['id']?>"><?=$leaders['forename']." ".$leaders['surname']?></option>
					    <?php } ?>
						</select>
			  		</div>
				</div>
				
		  		<div class="clearfix"></div>
			
		  		<div class="col-md-6 col-xs-6">
  					<div class="form-group">
					    <label for="first_name">First Name</label>
					    <input type="text" class="form-control mandatory-field" id="first_name" name="first_name">
			  		</div>
		  		</div>
				<div class="col-md-6 col-xs-6">
  					<div class="form-group">
					    <label for="last_name">Last Name</label>
					    <input type="text" class="form-control mandatory-field" id="last_name" name="last_name">
			  		</div>
				</div>
				<div class="col-md-6 col-xs-6">
  					<div class="form-group">
					    <label for="email">Email</label>
					    <input type="text" class="form-control mandatory-field" id="email" name="email">
			  		</div>
				</div>
				<div class="col-md-6 col-xs-6">
  					<div class="form-group">
					    <label for="telepone_number">Telephone Number</label>
					    <input type="text" class="form-control" id="telepone_number" name="telepone_number">
			  		</div>
				</div>
				<div class="col-md-6 col-xs-6">
  					<div class="form-group">
					    <label for="country">Country</label>
					    <input type="text" class="form-control" id="country" name="country">
			  		</div>
				</div>
				<div class="col-md-6 col-xs-6">
  					<div class="form-group">
					    <label for="city">City</label>
					    <input type="text" class="form-control" id="city" name="city">
			  		</div>
				</div>
				<div class="col-md-6 col-xs-6">
  					<div class="form-group">
					    <label for="user_name">User Name</label>
					    <input type="text" class="form-control mandatory-field" id="user_name" name="user_name" readonly>
			  		</div>
				</div>
		  	</div>
		</div>
</form>


	<div class="action-buttons">
		<button class="btn btn-success pull-right main-bottom-margin" id="add-user" style="margin-right:5px;"><span class="glyphicon glyphicon-ok"></span> Add User</button>
		<button class="btn btn-warning pull-right main-bottom-margin" id="refresh-user-form" style="margin-right:5px;"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
	</div>

