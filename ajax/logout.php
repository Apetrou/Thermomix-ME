<?php
session_start();
 
$_SESSION = array();
 
// // get session parameters 
// $params = session_get_cookie_params();
 
// setcookie(session_name(),
//         '', time() - 42000, 
//         $params["path"], 
//         $params["domain"], 
//         $params["secure"], 
//         $params["httponly"]);

unset($_SESSION);
session_destroy();
return;

?>