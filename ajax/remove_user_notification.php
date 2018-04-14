<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = json_decode(file_get_contents("php://input"),true);


	if(!isset($data["id"]) || $data["id"] == '') {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	}

	$return = $thermox->removeUserNotification($data["id"]);

	if($return == true) {
		$ret["success"] = true;
	} else {
		$ret["message"] = $return;
	}
	
	echo json_encode($ret);
	return;

?>

