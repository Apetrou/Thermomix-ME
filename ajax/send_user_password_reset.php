<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = json_decode(file_get_contents("php://input"),true);

	if(!isset($data["email"]) || $data["email"] == '') {
		$ret["message"] = "Please enter an email address";
		echo json_encode($ret);
		return;
	}

	$return = $thermox->forgotPasswordEmail($dbConnected,$data["email"]);
	
	if($return === true) {
		$ret["success"] = true;
	} else {
		$ret["message"] = $return;
	}

	echo json_encode($ret);
	return;

?>