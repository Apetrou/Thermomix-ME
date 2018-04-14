<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php");  
	
  	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");	

  	$person_details = json_decode($_SESSION['selected_person'],true);
  	
  	if(!isset($_SESSION['selected_person']) && !isset($_POST['type'])){
  		$ret["message"] = "404";
  		echo json_encode($ret);
  		return;
  	}

  	if(isset($_POST['type']) && $_POST['type'] == 'muser') {
  		$cons = true;
  		$muser = true;
  		if($return = $thermox->getUserTimeline()) {
  			$data = $return["data"];
  		} else {
  			$ret["message"] = "SQL query error!";
  			echo json_encode($ret);
  			return;
  		}
  	} else if ($person_details['flag'] == 'user') {
  		$cons = true;
  		$uid = $person_details['id'];
  		if($return = $thermox->getUserTimeline($uid)) {
  			$data = $return["data"];
  		} else {
  			$ret["message"] = "SQL query error!";
  			echo json_encode($ret);
  			return;
  		}
  	} else {
  		$cust = true;
  		if($return = $thermox->getCustomerTimeline($person_details["id"])) {
  			$data = $return["data"];
  		} else {
  			$ret["message"] = "SQL query error!";
  			echo json_encode($ret);
  			return;
  		}
  	}

	$content  = '';

	for($i=0;$i<sizeof($data);$i++) {

		if($data[$i]['activity_type'] == "TM Purchase") {
			$type = "cd-purchase";
		} else if ($data[$i]['activity_type'] == "Repair") {
			$type = "cd-repair";
		} else if($data[$i]['activity_type'] == "BP Purchase"){
			$type = "cd-books-parts";
		}

		if(date("d F") == date("d F",$data[$i]['activity_date'])) {
			$date = "Today";
		} else if (date("d F", strtotime('-1 day')) == date("d F",$data[$i]['activity_date'])) {
			$date = "Yesterday";
		} else {
			$date = date("dS F",$data[$i]['activity_date']);
		}
		
		$content .= '<li class="'.$type.'">';
		$content .=  	'<div class="timeline-time">';
		$content .=  		'<span class="date">'.$date.'</span>';
		$content .=  		'<span class="year">'.date("Y",$data[$i]['activity_date']).'</span>';
		$content .=  	'</div>';
		$content .= 	'<div class="timeline-icon">';
		if($cons) {
		$content .= 		'<a class="ignore getPerson hoverable" data-customer="'.$data[$i]['customer_id'].'" data-id="'.$data[$i]['customer_activity_id'].'"></a>';
		} else {
		$content .= 		'<a class="ignore"></a>';
		}
		$content .= 	'</div>';
		$content .= 	'<div class="timeline-body">';
		$content .= 		'<div class="timeline-content">';
		$content .= 			'<div class="row">';
		$content .=					'<div class="pull-left">';
		$content .= 					'<span class="activity-type">'.$data[$i]["activity_notes"].'</span>';
		$content .=						'<p class="activity-description">'.$data[$i]["customer_formatted_name"].'</p>';
		$content .=					'</div>';
		$content .=					'<div class="pull-right col-md-4">';
		$content .=						'<div>';
		$content .=							'<button class="btn btn-default btn-block generate-invoice" data-customer-id="'.$data[$i]['customer_id'].'" data-customer-activity-id="'.$data[$i]['customer_activity_id'].'" data-invoice-number="'.$data[$i]['invoice_num'].'">Download Invoice ';
		$content .=								'<span class="glyphicon glyphicon-download-alt"></span>';
		$content .=							'</button>';
		$content .=							'<button class="btn btn-default btn-block view-activity-details" data-serial-num="'.$data[$i]['serial_num'].'" data-customer-id="'.$data[$i]['customer_id'].'" data-customer-activity-id="'.$data[$i]['customer_activity_id'].'" data-invoice-number="'.$data[$i]['invoice_num'].'">View Details ';
		$content .=								'<span class="glyphicon glyphicon-list-alt"></span>';
		$content .=							'</button>';
		$content .=						'</div>';
		$content .=					'</div>';
		$content .= 			'</div>';
		$content .= 		'</div>';
		$content .= 		'<div class="timeline-footer">';
		$content .=				'<div class="row">';
		$content .=                '<a href="#" class="ignore m-r-15"><i class="glyphicon glyphicon-user"></i> User: '.$data[$i]['username'].'</a>';
		$content .=          	'</div>';
		$content .=          '</div>';
		$content .=		'</div>';
		$content .= '</li>';

	}

	if(sizeof($data) == 0) {
		$content = '<li>';
		$content .= 	'<div class="timeline-icon">';
		$content .= 		'<a class="ignore"></a>';
		$content .= 	'</div>';
		$content .= 	'<div class="timeline-body">';
		$content .= 		'<div class="timeline-content">';
		$content .= 			'<div class="row">';
		$content .=					'<div class="pull-left">';
		$content .= 					'<span class="activity-type">No timeline content to display</span>';
		$content .=					'</div>';
		$content .= 			'</div>';
		$content .= 		'</div>';
		$content .=		'</div>';
		$content .= '</li>'; 
	}

	if ($cust) {
		$content .= '<div class="action-buttons">';
		$content .= '<button id="add-customer-activity" class="btn btn-success" style="margin-right:5px;"><span class="glyphicon glyphicon-plus"></span> Add Purchase or Repair</button>';
		if($_SESSION['user']['user_type'] == 1) {
			$content .= '<button id="edit-customer" class="btn btn-warning" style="margin-right:5px;">Edit Customer</button>';
			$content .= '</div>';
		}
	} else if ($_SESSION['user']['user_type'] == 1 && !$muser) {
		$content .= '<div class="action-buttons">';
		if($user_id != $_SESSION['user']['user_id']) {
			// $content .= '<button id="add-consultant-host-gift-dialogue" class="btn btn-default" style="margin-right:5px;" data-id="'.$consultant_id.'"><span class="glyphicon glyphicon-gift"></span> Add Gift</button>';
		
			$content .= '<button id="add-consultant-inventory-dialogue" class="btn btn-success" style="margin-right:5px;"><span class="glyphicon glyphicon-plus"></span> Add Invetory</button>';
		}
		if($_SESSION["user"]["user_type"] == 1) {
			// $content .= '<button id="edit-consultant" class="btn btn-warning" style="margin-right:5px;" data-id="'.$consultant_id.'">Edit Consultant</button>';
		}
		$content .= '</div>';
	}


	$ret["response"] = $content;
	$ret["success"] = true;

	if(isset($_POST['type']) && $_POST['type'] != null && $_POST['type'] != "") {
		$ret["message"] = "cons";
	} else {
		$ret["message"] = $person_details['flag'];
	}
	
	echo json_encode($ret);
	return;

?>