<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = $thermox->getUserNotifications();

	if(!$data['success']) {
		$ret["message"] = $data;
	} else {
		$ret["response"] = $data['data'];
		$ret["success"] = true;
	}

	echo json_encode($ret);
	return;

?>

