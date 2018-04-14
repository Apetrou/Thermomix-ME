<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	if(!isset($_POST["user_id"]) || $_POST["user_id"] == "" || $_POST["user_id"] == null) {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	} 

	if(!isset($_POST["code"]) || $_POST["code"] == "" || $_POST["code"] == null) {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	} 

	if(!isset($_POST["quantity"]) || $_POST["quantity"] == "" || $_POST["quantity"] == null) {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	} 

	$return = $thermox->deductConsultantStock($_POST); 

	if($return === true) {
		$ret["success"] = true;
	} else {
		$ret["message"] = $return;
	}

	echo json_encode($ret);
	return;

?>