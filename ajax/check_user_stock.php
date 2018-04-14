<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");
	$data = json_decode(file_get_contents("php://input"),true);

	if(!isset($data['user_id']) || $data['user_id'] == "" || $data['user_id'] == null) {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	}

	foreach($data['items'] as $item) {
		$return = $thermox->checkUserStock($data['user_id'],$item['itemCode'],$item['itemQuantity']);
		if($return['data'][1] != 1) {
			$ret["response"] = $return['data']['material_name'];
			$ret["message"] = "Not enough stock for: ".$ret["response"];
			echo json_encode($ret);
			return;
		}
	} 

	$ret["success"] = true;
	echo json_encode($ret);
	return;

?>