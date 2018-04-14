<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

	$sql = "SELECT Id, forename, surname FROM user";
	$result = mysqli_query($dbConnected, $sql);

	$user_array = array("id" => array(), "full_name" => array());

	while($row=mysqli_fetch_assoc($result))	{

		$user_array['id'][] = $row['Id'];
		$user_array['full_name'][] = $row['forename']." ".$row['surname'];
	}

	if($result) {
		$ret["success"] = true;
	} else {
		$ret["message"] = "SQL Query error";
	}

	$ret["response"] = $user_array;

	mysqli_free_result($result);

	return;

?>