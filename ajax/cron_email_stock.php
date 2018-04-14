<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$data = array();
	$email_subject = "IMPORTANT: Low stock levels!";
	$email_body = "";

	$sql = "SELECT * FROM stock WHERE quantity < 5";
	$result = mysqli_query($dbConnected, $sql);

	while($row=mysqli_fetch_assoc($result)) {
		$data[] = $row;
	}

	$email_body.= "The following items have less than 5 in their inventory:";
	$email_body.= "<br/>";
	$email_body.= "<ul>";
	
	foreach($data as $item) {
		$email_body.= "<li>".$item['material_name']." - current inventory: ".$item['quantity']."</li>";
	}

	$email_body.= "</ul>";

	echo $email_body;

	email("petroualkis@gmail.com", "petroualkis@gmail.com", $email_subject, $email_body, $attachments = null, $cc=null, 1);

	// $ret["success"] = true;
	// echo json_encode($ret);
	// return;

?>
