<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");
	
	// if(!isset($_POST["search_val"]) || $_POST["search_val"] == "" || $_POST["search_val"] == null) {
	// 	$ret["message"] = "POST variables not set!";
	// 	echo json_encode($ret);
	// 	return;
	// } 

	if($return = $thermox->getUserStockItems($_POST["search_val"])) {
		$ret["success"] = true;
		$ret["response"] = $return["data"];
	} else { 
		$ret["message"] = "SQL query error";
	}

	// print_r($ret["response"]);	

	echo json_encode($ret);
	return;	

?>

