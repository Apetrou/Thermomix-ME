<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");	
	
	if(!isset($_POST)) {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	}

	if($return = $thermox->insertUpdateCustomerPurchase($_POST)) {
		$ret["success"] = true;
		$ret["response"] = $return["id"];
	} else {
		$ret["message"] = "SQL query error!";
	}

	echo json_encode($ret);
	return;

?>