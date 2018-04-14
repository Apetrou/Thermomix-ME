<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	if(isset($_POST['material_name']))
	{
		$part_name = $_POST['material_name'];
	}

	$sql="SELECT code FROM stock WHERE material_name = '$part_name'";
	$result = mysqli_query($dbConnected, $sql);

	if($result) {
		$ret["success"] = true;
	} else {
		$ret["message"] = "SQL Query error";
	}

	$row=mysqli_fetch_assoc($result);

	$ret["response"] = $row['code'];

	echo json_encode($ret);

	mysqli_free_result($result);
	
	return;

?>