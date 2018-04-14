<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$sql="SELECT invoice_num FROM customer_activity WHERE NOT (activity_type = 'Purchase') ORDER BY invoice_num DESC LIMIT 1";
	$result = mysqli_query($dbConnected, $sql);

	if(!$result) {
		die("SQL ERROR");
	}	
	
	$row=mysqli_fetch_assoc($result);
	
	$data = $row['invoice_num'];

	echo $data;

	mysqli_free_result($result);


?>