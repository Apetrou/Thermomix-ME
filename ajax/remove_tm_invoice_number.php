<?php	
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = json_decode(file_get_contents("php://input"),true);

	if(!isset($data["invoice_number"]) || $data["invoice_number"] == "" || $data["invoice_number"] == null) {
		// $ret["message"] = "POST variables not set!";
		// echo json_encode($ret);
		return;
	} 
	
	if($thermox->removeTMInoviceNumber($data["invoice_number"],$data["user"])) {
		$ret["success"] = true;
	} else {
		$ret["message"] = "SQL query error!";
	}	

	echo json_encode($ret);
	return;
?>