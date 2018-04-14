<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = json_decode(file_get_contents("php://input"),true);

	if(!isset($data['type']) || $data['type'] == null || $data['type'] == '') {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	} else {
		if($return = $thermox->getSales($data['type'])) {
			$ret["response"] = $return["data"];
			$ret["success"] = true;
		} else {
			$ret["message"] = "SQL query error";
		}
	}

	echo json_encode($ret);
	return;

?>
