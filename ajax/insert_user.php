<?php
header('Content-Type: application/json');
include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

$error_msg = "";

if (isset($_POST['user_name'], $_POST['email'])) {
    $username = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $tel_no = filter_input(INPUT_POST, 'telepone_number', FILTER_SANITIZE_STRING);
    $forename = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $user_type = filter_input(INPUT_POST, 'user_type_radio', FILTER_SANITIZE_NUMBER_INT);
    $register_date = date('Y-m-d');
    if(isset($_POST['parent_user']) && $_POST['user_type_radio'] == 3) {
        $parent_id = $_POST['parent_user'];
    }
    $locked = 1;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg .= 'The email address you entered is not valid';
        $ret["message"] = $error_msg;
        echo json_encode($ret);
        return;
    }
    
    $prep_stmt = "SELECT Id FROM user WHERE email = ? LIMIT 1";
    $stmt = $dbConnected->prepare($prep_stmt);
 
   // check existing email  
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            // A user with this email address already exists
            $error_msg .= 'A user with this email address already exists';
                        $stmt->close();
        }
    } else {
        $error_msg .= 'Database error Line 55';
                $stmt->close();
    }
 
    // check existing username
    $prep_stmt = "SELECT Id FROM user WHERE user_name = ? LIMIT 1";
    $stmt = $dbConnected->prepare($prep_stmt);
 
    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
 
                if ($stmt->num_rows == 1) {
                        // A user with this username already exists
                        $error_msg .= 'A user with this username already exists';
                        $stmt->close();
                }
        } else {
                $error_msg .= '>Database error line 74';
                $stmt->close();
        }   
 	
    if (empty($error_msg)) { 

        $hash = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
        $activate_date = date('Y-m-d H:i:s', strtotime("now"));  

        $return = $thermox->insertUser(array("username"=>$username,"forename"=>$forename,"surname"=>$surname,"email"=>$email,"tel_no"=>$tel_no,"country"=>$country,"city"=>$city,"user_type"=>$user_type,"register_date"=>$register_date,"parent_id"=>$parent_id,"locked"=>$locked,"hash"=>$hash,"activate_date"=>$activate_date));

        if($return) {
            $emails = array();
            $emails[] = $email;
            $actual_link = "http://tm-me.net?reg=".$hash;
            $subject = "User Registration Activation Email";
            $content = '<div class="panel panel-thermox">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tm-me Registration, Username: '.$username.'</h3>
                            </div>
                            <div class="panel-body">
                                <p>Click this link to activate your tm-me account. <a href="' . $actual_link . '">' . $actual_link . '</a>
                                </p>
                            </div>
                        </div>';
                            
            email($emails, "info@tm-me.net",$subject, $content, null, null, 1);
        }

        $ret["success"] = true;

    } else {
        $ret["message"] = $error_msg;
    }
}

echo json_encode($ret);
return;
?>