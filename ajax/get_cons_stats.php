<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	if(isset($_POST['id']) && $_POST['id'] != null) {
		$user_id = $_POST['id'];
	} else {
		$cons = json_decode($_SESSION['selected_person'], true);
		$user_id = $cons['user_id'];
	}

	if($return = $thermox->getUserStock($user_id)) {
		
	} else {
		$ret["message"] = "SQL query error!";
		echo json_encode($ret);
		return;
	}

	if(empty($return["data"])) {
		$ret["response"] = "No stock";
		$ret["message"] = "Consultant does not have any stock to display!";
	} else {
		$ret["response"] = $return["data"];
	}

	$ret["success"] = true;
	echo json_encode($ret);

	return;

?>