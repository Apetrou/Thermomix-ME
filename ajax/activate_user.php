<?php
header('Content-Type: application/json');
include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

$error_msg = "";
 
if (isset($_POST['p'], $_POST['identifier'])) {
  
    $identifier = filter_input(INPUT_POST, 'identifier', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
 	
    if (empty($error_msg)) { 
        $password = password_hash($password, PASSWORD_BCRYPT);

        $dbConnected->query("SET SQL_SAFE_UPDATES = 0;");
        if ($insert_stmt = $dbConnected->prepare("UPDATE user SET locked = 0, password = ?, reset_activate_date = NULL, reset_activate_hash = NULL WHERE reset_activate_hash = ?")) {
            $insert_stmt->bind_param('ss', $password, $identifier);
            if (! $insert_stmt->execute()) {
            	$ret["message"] = "SQL Query error";
                return json_encode($ret);
            } else {
                $user_id = $insert_stmt->insert_id;
            }

        }
        $ret["success"] = true;
    } else {
        $ret["message"] = $error_msg;
    }
}

echo json_encode($ret);
return;
?>