<?php	
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$current_date = date('Y-m-d');

	$data = $_POST["purchase_details"];

	if(isset($_POST['customer_id']) && $_POST['customer_id'] != null){
		$customer_id = $_POST['customer_id'];
		$serial_number = $data[11]['value'];
		$invoice_number = $data[10]['value'];
		$payment_method = $data[12]['value'];
		$user_id = $data[0]['value'];
		$activity = "";
	}	else if (isset($_POST['add_activity'])){
		$customer_details = json_decode($_SESSION['selected_person'],true);
		$customer_id = $customer_details['customer_id']; 
		$invoice_number = $data[2]['value'];
		$payment_method = $data[4]['value'];
		$user_id = $data[0]['value'];
		$serial_number = $data[3]['value'];
	} else {
		$ret["message"] = "POST variables not set!";
		echo json_encode($ret);
		return;
	}

	$activity_notes = "Default";

	if(isset($data[1]['value'])){

		if($data[1]['value'] == "purchase_tm5"){

			$activity_notes = "Domestic Sale TM5";

			$activity = "Sale";

		} else if($data[1]['value'] == "purchase_commercial_tm5"){

			$activity_notes = "Commercial Sale TM5";

			$activity = "Sale";

		} else if($data[1]['value'] == "repair"){

			$activity_notes = "Repaired Machine Not Under Warranty";

			$activity = "Repair";

		} else if($data[1]['value'] == "repair_under_Warranty"){

			$activity_notes = "Repaired Machine Under Warranty";

			$activity = "Repair";

		} else if($data[1]['value'] == "purchase_books_parts"){

			$activity_notes = "Books and Parts Sale";

			$activity = "Books/Parts Sale";
		}
	}

	if(isset($invoice_number)) {
		$thermox->removeTMInoviceNumber($invoice_number,$user_id);
	}

	$sql = "INSERT INTO customer_activity(customer_id,activity_type,date_of_activity,activity_notes, invoice_num, serial_num, payment_method, user_id)
			VALUES ('$customer_id','$activity','$current_date','$activity_notes','$invoice_number', '$serial_number', '$payment_method','$user_id')";
	$result = mysqli_query($dbConnected, $sql);

	$last_activity_id = mysqli_insert_id($dbConnected);

	if($result)	{
		$ret["success"] = true;
		$ret["response"] = array("activity_id"=>$last_activity_id,"customer_id"=>(int)$customer_id);
		$thermox->addAuditEvent($dbConnected,2,3,$_POST);
	}	else {
		$ret["message"] = "SQL Query error";
	}

	echo json_encode($ret);
	mysqli_free_result($result);
	return;
?>