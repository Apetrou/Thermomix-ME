<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");
	$success = true;

	if(isset($_POST['user_id'])) {
		$id = $_POST['user_id'];
		$items = $_POST['items'];
	} else {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	}

	foreach($items as $item) {
		$code = $item["itemCode"];
		$quantity = $item["itemQuantity"];
		$return = $thermox->addUserStock($id,$code,$quantity);
		if(!$return) {
			$ret["message"] = $return;
			echo json_encode($ret);
			return;
		}
	}

	$ret["success"] = true;
	return;
?>