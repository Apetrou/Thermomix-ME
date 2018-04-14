<?php	
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");	

	$data = json_decode(file_get_contents("php://input"),true);
	
	if(isset($data['name'])) {
		$name = $data['name'];
	} else {
		$name  = null;
	}

	if(isset($data['id'])) {
		$customer_id = $data['id'];
	} 

	$data = $thermox->getPersons($name,$customer_id);

	if($data["success"]) {
		if(sizeof($data['data']) < 1) {
			$ret["response"] = 0;
		} else {
			$ret["response"] = $data['data'];
		}
		$ret["success"] = true;	
	} else {
		$ret["message"] = $data;
	}
	
	echo json_encode($ret);

?>
