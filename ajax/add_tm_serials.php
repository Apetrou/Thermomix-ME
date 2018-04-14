<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");
	$success = true;

	if(!isset($_POST["customer_activity_id"]) || $_POST["customer_activity_id"] == "" || $_POST["customer_activity_id"] == null) {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	} 

	foreach($_POST['serial_nos'] as $serial) {
		$return = $thermox->addCustomerSerial($_POST['customer_activity_id'],$serial);
		if(!$return) {
			$success = false;
		}
	}

	if($success) {
		$thermox->removeUserNotification($_POST['notification_id']);
		$ret["success"] = true;
	}

	echo json_encode($ret);
	return;

?>
