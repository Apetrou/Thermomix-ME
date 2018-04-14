<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 
	
	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$current_date = date('Y-m-d');

	if(isset($_POST)) {
		$data = $_POST["purchase_details"];

		$title = $data[2]['value'];
		$first_name = $data[3]['value'];
		$last_name = $data[4]['value'];
		$email = $data[9]['value'];
		$tel_no = $data[8]['value'];
		$address = $data[7]['value'];
		$city = $data[5]['value'];
		$country = $data[6]['value'];
		$full_name = $title." ".$first_name." ".$last_name;

		$sql="INSERT INTO customer_details(customer_title,customer_first_name,customer_last_name,customer_tel_no,customer_email,customer_address,customer_city,customer_country,register_date)
		  VALUES ('$title','$first_name','$last_name','$tel_no','$email','$address','$city','$country','$current_date')";
		$result = mysqli_query($dbConnected, $sql);
	} else {
		$ret["message"] = "POST variables not set!";
	}

	if($result) {
		$ret["success"] = true;
	} else {
		$ret["message"] = "SQL Query error";
	}

	$ret["response"] = mysqli_insert_id($dbConnected); //get last ID
	// $thermox->addAuditEvent($dbConnected,2,4,$_POST);

	echo json_encode($ret);

	mysqli_free_result($result);

	return;

?>