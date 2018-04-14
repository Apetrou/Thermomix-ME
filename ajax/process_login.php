<?php
    header('Content-Type: application/json');
    include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

    $ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

    if (isset($_POST['username'], $_POST['p'])) {
        $username = $_POST['username'];
        $password = $_POST['p']; 

        $return = $thermox->login($username, $password, $dbConnected);

        echo $return;
    } else {
        $ret["response"] = "POST variables not set!";
        echo json_encode($ret);
    }

    return;

?>