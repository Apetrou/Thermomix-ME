<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" =>"", "response" => ""); 

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

	$return = $thermox->deductStock($_POST); 

	if($return === true) {
		$ret["success"] = true;
	} else {
		$ret["message"] = $return;
	}

	echo json_encode($ret);
	return;

?>