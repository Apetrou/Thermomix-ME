<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	  $stock_levels = json_decode(file_get_contents("php://input"),true);
	}

	for($i=0;$i<sizeof($stock_levels);$i++) {

		$item_code = $stock_levels[$i]['itemCode'];
		$item_quantity = $stock_levels[$i]['quantity'];

		$sql = "UPDATE stock
				SET quantity = '$item_quantity'
				WHERE code = '$item_code'";

		$result = mysqli_query($dbConnected, $sql);

		if($result) {
			$ret["success"] = true;
		} else {
			$ret["success"] = false;
			$ret["message"] = "SQL Query error";
		}

		mysqli_free_result($result);
	}

	$audit_array = array(
     "action" => "15", 
     "data_change" => json_encode($stock_levels)
	); 
	$thermox->addAuditEvent($dbh,$audit_array);

	echo json_encode($ret);

	return;

?>