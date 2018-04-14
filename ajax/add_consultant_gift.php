<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	if(isset($_POST['consultant_id'])) {
		$id = $_POST['consultant_id'];
		$items = $_POST['items'];
	} else {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	}

	if($thermox->addConsultantGift($dbConnected,$_POST['id'],$_POST['items'])) {
		$ret["success"] = true;
	}

	$ret["success"] = true;
	$ret["response"] = mysqli_insert_id($dbConnected); 
	echo json_encode($ret);
	return;

?>