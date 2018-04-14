<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	if(isset($_POST["user_id"])) {
		$data = $_POST["invoice_nos"];
		$user_id = $_POST["user_id"];
	} else {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	}


	foreach($data as $num) {
		$return = $thermox->addTmInvoiceNumbers($user_id,$num);
		if(!$return) {
			$ret["message"] = $return;
			echo json_encode($ret);
			return;
		}
	}

	$ret["success"] = true;
	echo json_encode($ret);
	return;

?>
