<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = json_decode(file_get_contents("php://input"),true);

	if(!isset($data) || $data == "" || $data == null) {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	} 

	$return = $thermox->addUserNotification($data["description"],$data["customer_id"],$data["action"],$data["customer_activity_id"]);

	if($return) {
		$ret["success"] = true;
	} else {
		$ret["message"] = $return;
	}

	echo json_encode($ret);
	return;

?>
