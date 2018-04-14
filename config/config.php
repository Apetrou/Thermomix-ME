<?php
	error_reporting(0);
	error_reporting(E_ERROR | E_PARSE);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('session.gc_maxlifetime', 7200);

	include_once($_SERVER['DOCUMENT_ROOT']."/libraries/PHPMailer-master/PHPMailerAutoload.php");
	include_once($_SERVER["DOCUMENT_ROOT"]."/Classes/thermox.php");
	include_once $_SERVER["DOCUMENT_ROOT"]."/Classes/PHPExcel/IOFactory.php";
	// echo $_SERVER["DOCUMENT_ROOT"];

	session_start();

	$servername="localhost"; 
	$username="root"; 
	$password="password"; 
	$dbname = "thermomix";
	$dbConnected = new mysqli($servername,$username,$password,$dbname);

	// $dsn = 'mysql:dbname=thermomix;host=127.0.0.1;port=4040';	// $link = new PDO('127.0.0.1:4040',$username,$password);
	// $link = new PDO($dsn, $username, $password);
	// if(!$link) {
	// die('d');
	// 	// die('Could not connect: '.mysql_error());
	// }


	$dbh = new PDO('mysql:host=localhost;dbname=thermomix', $username, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	$master_user = array();
	$master_user["email"] = "petroualkis@gmail.com";
	$master_user["username"] = "thermomix";
	$master_user["id"] = 1;

	if(!$dbConnected){
		die("connection error");
	} else {
		// echo "db connected";
	}

	/* check connection */
	// if ($mysqli->connect_errno) {
	//     printf("Connect failed: %s\n", $mysqli->connect_error);
	//     exit();
	// }

	$thermox = new thermox;
	$thermox->enforce_key_url_validation = false;
	$thermox->con = $dbh;
	$thermox->dbConnected = $dbConnected;

	function login_check($dbConnected) {
	    // Check if all session variables are set 
	    if (isset($_SESSION['user']['user_id'], 
	                        $_SESSION['user']['username'], 
	                        $_SESSION['login_string'])) {
	 
	        $user_id = $_SESSION['user']['user_id'];
	        $login_string = $_SESSION['login_string'];
	        $username = $_SESSION['user']['username'];
	 
	        // Get the user-agent string of the user.
	        $user_browser = $_SERVER['HTTP_USER_AGENT'];
	 
	        if ($stmt = $dbConnected->prepare("SELECT password 
	                                      FROM user 
	                                      WHERE Id = ? LIMIT 1")) {
	            // Bind "$user_id" to parameter. 
	            $stmt->bind_param('i', $user_id);
	            $stmt->execute();   // Execute the prepared query.
	            $stmt->store_result();
	 
	            if ($stmt->num_rows == 1) {
	                // If the user exists get variables from result.
	                $stmt->bind_result($password);
	                $stmt->fetch();
	                $login_check = hash('sha512', $password . $user_browser);
	 
	                if (hash_equals($login_check, $login_string) ){
	                    // Logged In!!!! 
	                    return true;
	                } else {
	                    // Not logged in 
	                    return false;
	                }
	            } else {
	                // Not logged in 
	                return false;
	            }
	        } else {
	            // Not logged in 
	            return false;
	        }
	    } else {
	        // Not logged in 
	        return false;
	    }
	}

	function esc_url($url) {
	    if ('' == $url) {
	        return $url;
	    }
	 
	    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
	 
	    $strip = array('%0d', '%0a', '%0D', '%0A');
	    $url = (string) $url;
	 
	    $count = 1;
	    while ($count) {
	        $url = str_replace($strip, '', $url, $count);
	    }
	 
	    $url = str_replace(';//', '://', $url);
	 
	    $url = htmlentities($url);
	 
	    $url = str_replace('&amp;', '&#038;', $url);
	    $url = str_replace("'", '&#039;', $url);
	 
	    if ($url[0] !== '/') {
	        // We're only interested in relative links from $_SERVER['PHP_SELF']
	        return '';
	    } else {
	        return $url;
	    }
	}


 	function email($recipients, $sender, $subject, $body, $attachments = null, $cc=null, $priority=null){

		$mail = new PHPMailer;

		// MAIL CONFIG
		// $mail->SMTPDebug   = 2;
		// $mail->DKIM_domain = '127.0.0.1';
		$mail->isSMTP();               // Set mailer to use SMTP
		// $mail->Mailer = "smtp";
		// $mail->SMTPDebug = 2;		
		// $mail->Host = "ssl://smtp.gmail.com";
		// // $mail->Host = "n3plcpnl0072.prod.ams3.secureserver.net";
		// $mail->Host = "localhost";
		// $mail->Port = 25;
		// // $mail->Port = 80;
		// $mail->SMTPAuth = true; // turn on SMTP authentication
		// // $mail->FromName = "info@tm-me.net"; 
		// $mail->Username = "	info@tm-me.net";
		// $mail->Password = "190792mp";


		$mail->Host = 'ssl://smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPAuth = true; // turn on SMTP authentication
		// $mail->Username = "info@tm-me.net";
		$mail->Username = "petroualkis@gmail.com";
		// $mail->Password = "190792mp";
		$mail->Password = "190792-mP";
		// $mail->Host = 'localhost'; // FOR DEV 
		$mail->SMTPSecure = 'ssl';


		// $mail->Username = "info@tm-me.net";
		// $mail->Password = "190792mp";

		// $mail->Host = 'localhost'; // FOR DEV 
		// $mail->SMTPSecure = 'ssl';
		// $mail->SMTPSecure = false;

		// $mail->Host = '10.31.18.10';  // Specify main and backup SMTP servers, OLD: '172.16.1.188'; 
		// $mail->Port = 25;

		// $mail->Priority = $priority;  
		

		// if($priority == 1) {
		// 	$mail->AddCustomHeader("X-MSMail-Priority: High");
		// 	$mail->AddCustomHeader("Importance: High");
		// }
		
		// $mail->setFrom($user_data['email'], $user_data['displayname']);

		// die(print_r(count($recipients)));

		$mail->setFrom('petroualkis@gmail.com');

		// $mail->setFrom($sender);
		for($i=0;$i<count($recipients);$i++){
			$mail->addAddress($recipients[$i]);
		}
		// $mail->addAddress("petroualkis@gmail.com");


		// $mail->addReplyTo('info@example.com', 'Information');
		// $mail->addCC('cc@example.com');
		// $mail->addBCC('bcc@example.com'); 
		for($i=0;$i<count($attachments);$i++){
			$mail->addAttachment($attachments[$i]);
		}       // Add attachments
		
		for($i=0;$i<count($cc);$i++){
            $mail->AddCC($cc[$i]);
        } // Add CC


		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = $body;
		// $mail->priority = 1;

		$ret = false;
		if($mail->send()) {
		    // echo 'Message has been sent';
		    $ret = true;
		}
		$mail->clearAttachments();
		return $ret;

	}

	// $thermox->con = $dbConnected;
	// $thermox->$enforce_key_url_validation = true;

?>
