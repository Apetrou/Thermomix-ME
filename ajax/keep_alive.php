<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	$ret = array("success" => false, "message" => "", "refresh" => true, "response" => "");
	//Default to logged out and refresh

	if (isset($_SESSION['last_activity']) && isset($_SESSION['expire_time'])) {
		if (time() < ($_SESSION['last_activity'] + $_SESSION['expire_time'])){
			$_SESSION['last_activity'] = time();
			$_SESSION['expire_time'] = 60 * 60;
			$ret["success"] = true;
			$ret["refresh"] = false;
		} else {
			$_SESSION = "";
			session_destroy();
			$ret["message"] = "You have been logged out due to inactivity";
		}
	} else {
		
		$ret["message"] = "User not logged in";
		$_SESSION = "";
		session_destroy();	
	}

	echo json_encode($ret);
	return;
?>