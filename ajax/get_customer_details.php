<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	if(isset($_POST['code']))
	{
		$code = $_POST['code'];
		echo $code;
		$quantity = $_POST['quantity'];
		echo $quantity;
	}

	$stock_array=array('code' => array(), 'stock' => array());

	$sql="SELECT code, quantity FROM stock";
	$result = mysqli_query($dbConnected, $sql);

	if($result)
	{

	}	else{
		die("SQL STOCK ERROR");
	}

	while($row=mysqli_fetch_assoc($result))
	{
		$stock_array['code'][] = $row['code'];
		$stock_array['stock'][] = $row['stock'];
	}

	for($i=0;$i<sizeof($stock_array['code']);$i++)
	{
		if($code == $stock_array['code'][$i])
		{
			$_code =  $stock_array['code'][$i];
			$stock_result = mysqli_query($dbConnected, "UPDATE stock SET 
				quantity = quantity - '$quantity' WHERE code = '$_code'");
			break;
		}
	
	}

?>