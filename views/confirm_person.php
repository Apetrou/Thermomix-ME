<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$content = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST')	{
		
		$person_details = json_decode(file_get_contents("php://input"),true);
		$user_type = json_decode($thermox->getUserType($person_details["user_type"],$dbConnected),true);
		$person_details["user_type_name"] = $user_type[0]["user_type"];
		$thermox->storePersonSession($person_details);
	}

	if(isset($_GET["action"])) {
		$action = $_GET["action"];
	}

	if(isset($_GET["id"])) {
		$activityID = $_GET["id"];
	}

	if(isset($_GET["uid"])) {
		$uid = $_GET["uid"];
	}

	if(isset($_GET["nid"])) {
		$nid = $_GET["nid"];
	}

	if($person_details['flag'] == "cust") {

		$content = '<div style="width:300px;">';
		$content .= '	<h2 class="text-center">Is this the correct customer?</h2>';
		$content .= '	<div class="col-xs-12 pull pull-left">';
		$content .= '		<ul class="list-group">';
		$content .= '			<li class="list-group-item"><b>Title:</b> '.$person_details['title'].'</li>';
		$content .= '			<li class="list-group-item"><b>First Name:</b> '.$person_details['first_name'].'</li>';
		$content .= '			<li class="list-group-item"><b>Last Name:</b> '.$person_details['last_name'].'</li>';
		$content .= '			<li class="list-group-item"><b>Date of Registration:</b> '.$person_details['register_date'].'</li>';
		$content .= '			<li class="list-group-item"><b>Email Address:</b> '.$person_details['email'].'</li>';
		$content .= '			<li class="list-group-item"><b>Customer Number:</b> '.$person_details['id'].'</li>';
		$content .= '		</ul>';
		$content .= '	</div>';
		$content .= '	<div class="clearfix">';
		$content .= '	</div>';
		$content .= '	<div  class="col-xs-12" style="padding-top:10px">';
		$content .= '		<button type="button" class="btn btn-danger pull-left incorrect-person-search" title="Incorrect Customer"><span class="glyphicon glyphicon-remove"></span> Incorrect</button>';
		$content .= '		<button type="button" class="btn btn-success pull-right correct-person-search" data-user-id="'.$uid.'" data-notification-id="'.$nid.'" data-activity-id="'.$activityID.'" data-action="'.$action.'" data-person-type="'.$person_details['flag'].'" data-person-name="'.$person_details['full_name'].'" data-person-id="'.$person_details["id"].'" data-person-email="'.$person_details["customer_email"].'" data-person-reg="'.$person_details["register_date"].'" title="Correct Customer"><span class="glyphicon glyphicon-ok"></span> Correct</button>';
		$content .= '	</div>';
		$content .= '</div>';
	} else {

		$content = '<div style="width:300px;">';
		$content .= '	<h2 class="text-center">Is this the correct user?</h2>';
		$content .= '	<div class="col-xs-12 pull pull-left">';
		$content .= '		<ul class="list-group">';
		// $content .= '			<li class="list-group-item"><b>Title:</b><br/> '.$person_details['consultant_title'].'</li>';
		$content .= '			<li class="list-group-item"><b>First Name:</b> '.$person_details['first_name'].'</li>';
		$content .= '			<li class="list-group-item"><b>Last Name:</b> '.$person_details['last_name'].'</li>';
		$content .= '			<li class="list-group-item"><b>Date of Registration:</b> '.$person_details['register_date'].'</li>';
		$content .= '			<li class="list-group-item"><b>Email Address:</b> '.$person_details['email'].'</li>';
		$content .= '			<li class="list-group-item"><b>User Type:</b> '.$person_details['user_type'].'</li>';
		$content .= '			<li class="list-group-item"><b>User Number:</b> '.$person_details['id'].'</li>';
		$content .= '		</ul>';
		$content .= '	</div>';
		$content .= '	<div class="clearfix">';
		$content .= '	</div>';
		$content .= '	<div  class="col-xs-12" style="padding-top:10px">';
		$content .= '		<button type="button" class="btn btn-danger pull-left incorrect-person-search" title="Incorrect User"><span class="glyphicon glyphicon-remove"></span> Incorrect</button>';
		$content .= '		<button type="button" class="btn btn-success pull-right correct-person-search" data-action="'.$action.'" data-person-type="'.$person_details['flag'].'" data-person-name="'.$person_details['full_name'].'" data-person-id="'.$person_details["id"].'" data-person-email="'.$person_details["email"].'" data-person-reg="'.$person_details["register_date"].'" title="Correct User"><span class="glyphicon glyphicon-ok"></span> Correct</button>';
		$content .= '	</div>';
		$content .= '</div>';
	}

	echo $content;

	return;

?>