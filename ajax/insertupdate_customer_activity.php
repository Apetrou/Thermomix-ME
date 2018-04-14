<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = json_decode(file_get_contents("php://input"),true);

	if($return = $thermox->insertUpdateActivity($data)) {
		$ret["success"] = true;
		$ret["response"] = $return["id"];
	} else { 
		$ret["message"] = "SQL query error";
	}

	echo json_encode($ret);
	return;	

?>

