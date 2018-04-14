<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");	

	if(isset($_POST["user_id"])) {
		$user_id = $_POST["user_id"];

		if($return = $thermox->getTmInvoiceNumbers($user_id)) {
			$ret["response"] = $return["data"];
			$ret["success"] = true;
		} else {
			$ret["message"] = "SQL query error!";
			echo json_encode($ret);
			return;
		}

	} else{
		$ret["message"] = "POST variables not set!";
	}

	echo json_encode($ret);
	return;
?>