<?php

	class thermox{

		public $con;
		public $dbConnected;
		public $enforce_key_url_validation;

		public function __construct(){ 
			
		}

		public function encryptString( $string, $action = 'e' ) {
		    $secret_key = 'my_simple_secret_key';
		    $secret_iv = 'my_simple_secret_iv';
		 
		    $output = false;
		    $encrypt_method = "AES-256-CBC";
		    $key = hash( 'sha256', $secret_key );
		    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		 
		    if( $action == 'e' ) {
		        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		    }
		    else if( $action == 'd' ){
		        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		    }
		 
		    return $output;
		}

		public function storePersonSession($arr){
			// if($arr['flag'] == "cust") {
			// 	$return = $this->getCustomerSerials($arr['id']);
			// }
			$_SESSION['selected_person'] = $arr;
			// if($return["success"]) {
			// 	$_SESSION['selected_person']['serial_numbers'] = $return['data'];		
			// }
			$_SESSION['selected_person'] = json_encode($_SESSION['selected_person']);

			// $audit_array = array(
   //           "action" => "2", 
   //           "actioned_by" => $_SESSION['user']['username'],
   //           "data_change" => $_SESSION['selected_person']
   //      	); 
   //      	$this->addAuditEvent($audit_array);

        	return;
		}

		public function errorLog($function, $message){
            $line = date("d/m/Y H:i:s"). " | " .$function. " | " .$_SESSION['user']['username']. " | "  .print_r($message,true). "\r\n";
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/logs/error_log.log', $line, FILE_APPEND);
        }

		// public function addAuditEvent($params) {

  //       	$actioned_by = $_SESSION['user']['username'];
  //       	$action = (isset($params["action"]) && $params["action"] != "" ? $params["action"]  : NULL);
  //       	$data_change = (isset($params['data_change']) && $params['data_change'] != "" ? $params['data_change']  : NULL);

  //       	unset($stmt);

  //       	try { 
  //       		//  $query = $this->con->prepare('set session wait_timeout=10000,interactive_timeout=10000,net_read_timeout=10000');
  //       		//  $query->execute();
  //       		// $this->con->query("SET wait_timeout=1200;");
	 //        	$stmt = $this->con->prepare('CALL add_audit(:actioned_by,:action,:data)');
	 //        	$stmt->bindValue(':actioned_by', $actioned_by, PDO::PARAM_STR);
	 //        	$stmt->bindValue(':action', $action, PDO::PARAM_INT);
	 //        	$stmt->bindValue(':data', $data_change);
	 //        	$stmt->execute();
	 //        } catch(PDOException $errors){ 
	 //        	$error = array(
	 //             "user" => $actioned_by, 
	 //             "action" => $action,
	 //             "data_change" => $data_change
	 //        	); 
	 //        	$this->errorLog("addAuditEvent", $error);
	 //        	$this->errorLog("addAuditEvent", $errors->getMessage());
		//        	return $errors; 
		//     }

		//     return true;

		// }

		public function addAuditEvent($params) {
			$actioned_by = $_SESSION['user']['username'];
        	$action = (isset($params["action"]) && $params["action"] != "" ? $params["action"]  : NULL);
        	$data_change = (isset($params['data_change']) && $params['data_change'] != "" ? $params['data_change']  : NULL);

        	$stmt = $this->dbConnected->prepare('CALL add_audit(?,?,?)');
        	$stmt->bind_param('sss', $actioned_by,$action,$data_change); 

        	if(!$stmt->execute()) {
        		$error = array(
	             "user" => $actioned_by, 
	             "action" => $action,
	             "data_change" => $data_change
	        	); 
	        	$this->errorLog("addAuditEvent", $error);
	        	$this->errorLog("addAuditEvent", $stmt->error);
		       	return $dbConnected->errno; 
        	}
        	return true;

		}

		public function login($username, $password, $con) {

			$ret = array("success" => false, "message" =>"", "response" => ""); 

		    if ($stmt = $con->prepare("SELECT id, forename, surname, user_name, password, user_type, locked
		        FROM user
		       WHERE user_name = ?
		        LIMIT 1")) {
		        $stmt->bind_param('s', $username);  
		        $stmt->execute();   
		        $stmt->store_result();
		 
		        $stmt->bind_result($user_id, $forename, $surname, $username, $db_password, $user_type, $locked);
		        $stmt->fetch();
		 
		        if ($stmt->num_rows == 1) {
		            // If the user exists we check if the account is locked
		            // from too many login attempts 
		 			
		        	if($locked == 1) {
		        		$ret["response"] = "This account has been locked, please contact your administator.";
		        	} else if ($this->checkbrute($user_id, $con) == true) {
		                // Account is locked 
		                // Send an email to user saying their account is locked
		                $ret["response"] = "This account has been locked, please contact your administator.";
		            } else {
		                // Check if the password in the database matches
		                // the password the user submitted. We are using
		                // the password_verify function to avoid timing attacks.
		                if (password_verify($password, $db_password)) {
		                    // Password is correct!
		                    // Get the user-agent string of the user.
		                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
		                    // XSS protection as we might print this value
		                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
		                    $_SESSION['user_id'] = $user_id;
		                    // XSS protection as we might print this value
		                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
		                                                                "", 
		                                                                $username);
		                    $_SESSION['user']['username'] = $username;
		                    $_SESSION['login_string'] = hash('sha512', 
		                              $db_password . $user_browser);
		                    $_SESSION['user']['user_type'] = $user_type;
		                    $_SESSION['user']['user_id'] = $user_id;
		                    $_SESSION['user']['user_formatted_name'] = $forename." ".$surname;

		                    $ret["response"] = $user_id;
 		                    $ret["success"] = true;

 		                    $audit_array = array(
				             "action" => "13", 
				             "data_change" => $_SERVER['HTTP_USER_AGENT']
				        	); 
				        	$this->addAuditEvent($audit_array);

		                } else {
		                    // Password is not correct
		                    // We record this attempt in the database
		                    $now = time();
		                    $con->query("INSERT INTO login_attempts(user_id, time)
		                                    VALUES ('$user_id', '$now')");
		                    $ret["response"] = "Incorrect password!";
		                }
		            }
		        } else {
		            // No user exists.
		            $ret["response"] = "The user does not exist!";
		        }
		    }

		    return json_encode($ret);
		}

		public function checkbrute($user_id, $con) {
		    // Get timestamp of current time 
		    $now = time();
		 
		    // All login attempts are counted from the past 2 hours. 
		    $valid_attempts = $now - (2 * 60 * 60);
		 
		    if ($stmt = $con->prepare("SELECT time 
		                             FROM login_attempts 
		                             WHERE user_id = ? 
		                            AND time > '$valid_attempts'")) {
		        $stmt->bind_param('i', $user_id);
		 
		        // Execute the prepared query. 
		        $stmt->execute();
		        $stmt->store_result();
		 
		        // If there have been more than 5 failed logins 
		        if ($stmt->num_rows > 5) {
		            return true;
		        } else {
		            return false;
		        }
		    }
		}

		// public function storeUserDetails($con) {

		// 	$username = $_SESSION['user'];

		// 	$sqlUser="SELECT user_name, forename, surname, email, tel_no, priority FROM user WHERE user_name = '$username'";
		// 	$result = mysqli_query($con, $sqlUser);

		// 	if(!$result) {	
  //       		$errors = mysqli_error($this->con);
  //               $this->errorLog("storeUserDetails", $errors);
  //               $error_code = $errors[0]["code"];
  //               return $error_code;
  //       	}
			
		// 	$row=mysqli_fetch_assoc($result);
			
		// 	if(mysqli_num_rows($result) > 0) {

		// 		$_SESSION["username"] = $row["user_name"];
		// 		$_SESSION["forename"] = $row["forename"];
		//  		$_SESSION["surname"] = $row["surname"];
		// 		$_SESSION["email"] = $row["email"];
		// 		$_SESSION["tel_no"] = $row["tel_no"];
		// 		$_SESSION["priority"] = $row["priority"];
		// 		$_SESSION["user_full_name"] = $row["forename"]." ".$row["surname"];
		// 	} 

		// 	return;
		// }

		// public function getCustDetails($con,$id) {

		// 	$sql = "SELECT * FROM customer_details WHERE customer_id = '$id'";
	 //    	$result = mysqli_query($con, $sql);

	 //    	if($result) {
	 //    		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	 //    			$json[] = $row;
	 //    		}
	 //    		$ret["success"] = true;
		// 		$ret["response"] = $json;
	 //    	} else {
	 //    		$errors = mysqli_error($this->con);
  //               $this->errorLog("getCustDetails", $errors);
  //               $error_code = $errors[0]["code"];
  //               return $error_code;
	 //    	}

	 //    	return json_encode($ret);
		// }

	 	public function getViewDetails($view_name) {

	 		try {
 				$stmt = $this->con->prepare("CALL get_view_details(:view_name)");
				$stmt->bindValue(":view_name", $view_name, PDO::PARAM_STR);
				$stmt->execute();
 			} catch(PDOException $errors){ 
     			$this->errorLog("getViewDetails", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

     		$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();

	        return array("success"=>true,"data"=>$data[0]);
	    }

	   //  public function getConsultantName($con,$id) {

	   //  	$sql_cons = "SELECT consultant_id FROM customer_activity WHERE customer_activity_id = '$id'";
	   //  	$result_cons = mysqli_query($con,$sql_cons);

	   //  	while($row=mysqli_fetch_array($result_cons,MYSQLI_ASSOC)) {
	   //  		$this->consultant_id[] = $row;
	   //  	}

	   //  	$this->consultant_id = $this->consultant_id[0]['consultant_id'];

	   //  	$sql = "SELECT consultant_first_name, consultant_last_name FROM consultant_details WHERE consultant_id = '$this->consultant_id'";
	   //  	$result = mysqli_query($this->con,$sql);

	   //  	if($result) {
	   //  		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	   //  			$data[] = $row;
	   //  		}
	   //  		$ret["success"] = true;
				// $ret["response"] = $data;
	   //  	} else {
	   //  		$ret["message"] = "SQL error";
	   //  	}

	   //  	mysqli_free_result($result);

	   //  	return json_encode($ret);
	   //  }

	    // public function getCustomerName($con,$id) {

	    // 	$sql = "SELECT customer_title, customer_first_name, customer_last_name FROM customer_details WHERE customer_id = '$id'";
	    // 	$result = mysqli_query($con,$sql);

	    // 	while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	    // 		$data[] = $row;
	    // 	}

	    // 	$customer_name = $data[0]['customer_title']." ".$data[0]['customer_first_name']." ".$data[0]['customer_last_name'];

	    // 	mysqli_free_result($result);

	    // 	return json_encode($customer_name);
	    	
	    // }

	    public function getPurchaseDetails($id) {
	    
        	try {
        		$stmt = $this->con->prepare("CALL get_purchase_details(:activityid)");
	        	$stmt->bindValue(":activityid", $id, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
     			$this->errorLog("getPurchaseDetails", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

        	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();
           
            return array("success"=>true,"data"=>$data);
	    }

	  //   public function getActivityDetails($this->con,$id) {
	    	
	  //   	$sql = "SELECT p.purchase_material_code, p.purchase_material_name, p.purchase_material_quantity, p.purchase_date, p.customer_activity_id, s.sell_price, s.sub_dist
			// FROM customer_purchase as p
			// 	INNER JOIN stock as s
			// 	ON p.purchase_material_code = s.code
			// WHERE p.customer_activity_id = '$id'";

			// $result = mysqli_query($this->con, $sql);

			// while($row=mysqli_fetch_assoc($result)){
			// 	$data[] = $row;
			// }

			// mysqli_free_result($result);

			// return json_encode($data);
	  //   }

	    public function removeTMInoviceNumber($invoice_no,$id) {
	    
        	try {
        		$stmt = $this->con->prepare("CALL remove_invoice_number(:id,:invoice_no)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->bindValue(":invoice_no", $invoice_no, PDO::PARAM_STR);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
     			$this->errorLog("removeTMInoviceNumber", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

       		$audit_array = array(
             "action" => "3", 
             "data_change" => json_encode($invoice_num." - user: ".$id)
        	); 
        	$this->addAuditEvent($audit_array);
           
            return array("success"=>true);	
	    }

	  //   public function loadConsultantDetails($id,$con) {
	  //   	$sql = "SELECT * FROM consultant_details WHERE consultant_id = '$id'";
			// $result = mysqli_query($this->con, $sql);
			// while($row=mysqli_fetch_assoc($result)){
			// 	$data[] = $row;
			// }

			// mysqli_free_result($result);
			// return json_encode($data);
	  //   }

        public function getUserType($id,$con) {
        	$sql = "SELECT user_type FROM user_type WHERE id = '$id'";
        	$result = mysqli_query($con, $sql);
			while($row=mysqli_fetch_assoc($result)){
				$data[] = $row;
			}
			mysqli_free_result($result);
			return json_encode($data);
        }

        // public function printPost($data) {
        // 	return $data;
        // }


        public function getTeamLeaders($con) {
        	$stmt = $con->query('CALL get_team_leaders();');

        	if(!$stmt) {
        		$this->errorLog("getTeamLeaders", $con->error);
		       	return $con->errno; 
        	} else {
        		while ($row = $stmt->fetch_assoc()){
			        $data[] = $row;
			    }
				return $data; 
        	}
        		


   //      	$sql = "SELECT a.id, a.forename, a.surname FROM user a JOIN user_type b ON a.user_type = b.id WHERE b.id = 2";
	  //    	$result = mysqli_query($con, $sql);
	  //    	while($row=mysqli_fetch_assoc($result)){
			// 	$data[] = $row;
			// }
	      
	  //       mysqli_free_result($result);
	  //       return json_encode($data);
        	// $test = 'tes';

	  //       try {
			// 	$stmt = $this->con->prepare("CALL get_team_leaders(:test)");
			// 	$stmt->bindValue(":test", $test, PDO::PARAM_STR);
			// 	$stmt->execute();
			// } catch(PDOException $errors){ 
   //   			$this->errorLog("getTeamLeaders", $errors->getMessage());
   //   			// $this->con->rollback();
		 //       	return $errors->getMessage();
   //   		}

   //   		$data = array();
   //         	$data = $stmt->fetchAll();
   //         	$stmt->closeCursor();
           	
   //          return array("success"=>true,"data"=>$data[0]);
        }

        public function getPersons($name,$customer_id) {
        	$user_id = $_SESSION["user"]["user_id"];
        	$customer_id = (isset($customer_id) && $customer_id != "" ? $customer_id  : NULL);

			try {
				$stmt = $this->con->prepare("CALL get_persons(:name,:user_id,:customer_id)");
				$stmt->bindValue(":name", $name, PDO::PARAM_STR);
				$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
				$stmt->bindValue(":customer_id", $customer_id, PDO::PARAM_INT);
				$stmt->execute();
			} catch(PDOException $errors){ 
     			$this->errorLog("getPersons", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

     		$data = array();
           	if($id == NULL) {
           		$data = $stmt->fetchAll();
           		$stmt->closeCursor();
           	}

			// $audit_array = array(
   //           "action" => "5", 
   //           "data_change" => json_encode($params)
   //      	); 
   //      	$this->addAuditEvent($audit_array);

            return array("success"=>true,"data"=>$data);
        }

        public function getUserNotifications() {
        	$user_id = $_SESSION["user"]["user_id"];

        	try {
				$stmt = $this->con->prepare("CALL get_notifications(:user_id)");
				$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
				$stmt->execute();
			} catch(PDOException $errors){ 
     			$this->errorLog("getUserNotifications", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

     		$data = array();
           	if($id == NULL) {
           		$data = $stmt->fetchAll();
           		$stmt->closeCursor();
           	}
           	
           	return array("success"=>true,"data"=>$data);
        }

        public function addUserNotification($description,$customer_id,$action_id,$customer_activity_id) {
			$username = $_SESSION["user"]["username"];
        	$user_id = $_SESSION["user"]["user_id"];
       
        	try { 
        		$this->con->beginTransaction();

	        	$stmt = $this->con->prepare("CALL add_notifications(:user_id,:username,:description,:customer_id,:action_id,:customer_activity_id)");
	        	$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
	        	$stmt->bindValue(":username", $username, PDO::PARAM_STR);
	        	$stmt->bindValue(":description", $description, PDO::PARAM_STR);
	        	$stmt->bindValue(":customer_id", $customer_id, PDO::PARAM_INT);
	        	$stmt->bindValue(":action_id", $action_id, PDO::PARAM_INT);
	        	$stmt->bindValue(":customer_activity_id", $customer_activity_id, PDO::PARAM_INT);
	        	$stmt->execute();
	        
	        } catch(PDOException $errors){ 
	        	$this->errorLog("addUserNotification", $errors->getMessage());
	        	$this->con->rollback();
		       	return $errors;
		    }

		    $audit_array = array(
             "action" => "4", 
             "data_change" => json_encode($description." customer: ".$customer_id. " activity_id: ".$customer_activity_id)
        	); 
        	$this->addAuditEvent($audit_array);

		    $this->con->commit();
		    return true;
      
        }

        public function getActivityData($con,$id) {
        	$stmt = mysqli_query($con,"CALL get_activity_details($id)");

        	if( $stmt === false ) {       
	            $errors = mysqli_error($this->con);
                $this->errorLog("getActivityData", $errors);
                $error_code = $errors[0]["code"];
                return $error_code;
	        } else {
	            $data = array();
	            while ($row = mysqli_fetch_array($stmt, MYSQLI_ASSOC)) {
	                $data[] = $row;
	            }
	            return json_encode($data);
	        }
        }

        public function getUserTimeline($id=null) {
        	if($id==null) {
        		$id = $_SESSION["user"]["user_id"];
        	}
        	$stmt = $this->con->prepare("CALL get_user_timeline(:id)");
        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
        	$stmt->execute();

        	if( $stmt === false ) {       
	            $errors = $this->con->errorInfo();
                $this->errorLog("getUserTimeline", $errors);
                $error_code = $errors[0]["code"];
                return $error_code;
	        } else {
	           	$data = array();
           		$data = $stmt->fetchAll();
           		$stmt->closeCursor();
	           
	            return array("success"=>true,"data"=>$data);
	        }
        }

        public function getCustomerTimeline($id) {
        	$stmt = $this->con->prepare("CALL get_customer_timeline(:id)");
        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
        	$stmt->execute();

        	if( $stmt === false ) {       
	            $errors = $this->con->errorInfo();
                $this->errorLog("getCustomerTimeline", $errors);
                $error_code = $errors[0]["code"];
                return $error_code;
	        } else {
	           	$data = array();
           		$data = $stmt->fetchAll();
           		$stmt->closeCursor();
	           
	            return array("success"=>true,"data"=>$data);
	        }
        }

        public function insertUpdateCustomerDetails($params) {

        	$id = (isset($params["id"]) && $params["id"] != "" ? $params["id"]  : NULL);
 			$title = (isset($params["customer_title"]) && $params["customer_title"] != "" ? $params["customer_title"]  : NULL);
 			$first_name = (isset($params["customer_first_name"]) && $params["customer_first_name"] != "" ? $params["customer_first_name"]  : NULL);
 			$last_name = (isset($params["customer_last_name"]) && $params["customer_last_name"] != "" ? $params["customer_last_name"]  : NULL);
 			$email = (isset($params["customer_email"]) && $params["customer_email"] != "" ? $params["customer_email"]  : NULL);
 			$tel_no = (isset($params["customer_tel_no"]) && $params["customer_tel_no"] != "" ? $params["customer_tel_no"]  : NULL);
 			$address = (isset($params["customer_address"]) && $params["customer_address"] != "" ? $params["customer_address"]  : NULL);
 			$city = (isset($params["customer_city"]) && $params["customer_city"] != "" ? $params["customer_city"]  : NULL);
			$country = (isset($params["customer_country"]) && $params["customer_country"] != "" ? $params["customer_country"]  : NULL);

			try {
				$stmt = $this->con->prepare("CALL insertupdate_customer_details(:id,:title,:first_name,:last_name,:email,:tel_no,:address,:city,:country)");
				$stmt->bindValue(":id", $id, PDO::PARAM_INT);
				$stmt->bindValue(":title", $title, PDO::PARAM_STR);
				$stmt->bindValue(":first_name", $first_name, PDO::PARAM_STR);
				$stmt->bindValue(":last_name", $last_name, PDO::PARAM_STR);
				$stmt->bindValue(":email", $email, PDO::PARAM_STR);
				$stmt->bindValue(":tel_no", $tel_no, PDO::PARAM_STR);
				$stmt->bindValue(":address", $address, PDO::PARAM_STR);
				$stmt->bindValue(":city", $city, PDO::PARAM_STR);
				$stmt->bindValue(":country", $country, PDO::PARAM_STR);
				$stmt->execute();
			} catch(PDOException $errors){ 
     			$this->errorLog("insertUpdateCustomerDetails", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

     		$data = array();
           	if($id == NULL) {
           		$data = $stmt->fetchAll();
           		// $stmt->closeCursor();
           	}

			$audit_array = array(
             "action" => "5", 
             "data_change" => json_encode($params)
        	); 
        	$this->addAuditEvent($audit_array);

            return array("success"=>true,"id"=>$data[0][0]);

        }

        public function insertUpdateActivity($params) {
 			$id = (isset($params["id"]) && $params["id"] != "" ? $params["id"]  : NULL);
 			$customer_id = (isset($params["customer_id"]) && $params["customer_id"] != "" ? $params["customer_id"]  : NULL);
 			$activity_type = (isset($params["activity_type"]) && $params["activity_type"] != "" ? $params["activity_type"]  : NULL);
 			$activity_notes = (isset($params["customer_activity"]) && $params["customer_activity"] != "" ? $params["customer_activity"]  : NULL);
 			$invoice_num = (isset($params["invoice_number"]) && $params["invoice_number"] != "" ? $params["invoice_number"]  : NULL);
 			$payment_method = (isset($params["payment_method"]) && $params["payment_method"] != "" ? $params["payment_method"]  : NULL);
			$user_id = (isset($params["user"]) && $params["user"] != "" ? $params["user"]  : NULL);

			try {
				 $stmt = $this->con->prepare("CALL insertupdate_customer_activity(:id,:customer_id,:activity_type,:activity_notes,:invoice_number,:payment_method,:user_id)");

				$stmt->bindValue(":id", $id, PDO::PARAM_INT);
				$stmt->bindValue(":customer_id", $customer_id, PDO::PARAM_INT);
				$stmt->bindValue(":activity_type", $activity_type, PDO::PARAM_STR);
				$stmt->bindValue(":activity_notes", $activity_notes, PDO::PARAM_STR);
				$stmt->bindValue(":invoice_number", $invoice_num, PDO::PARAM_STR);
				$stmt->bindValue(":payment_method", $payment_method, PDO::PARAM_STR);
				$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
				$stmt->execute();
			} catch(PDOException $errors){ 
     			$this->errorLog("insertUpdateActivity", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

 			$data = array();
           	if($id == NULL) {
           		$data = $stmt->fetchAll();
           		$stmt->closeCursor();
           	}

           	$audit_array = array(
             "action" => "6", 
             "data_change" => json_encode($params)
        	); 
        	$this->addAuditEvent($audit_array);

            return array("success"=>true,"id"=>$data[0][0]);
        }

        public function insertUpdateCustomerPurchase($params) {
 			$id = (isset($params["id"]) && $params["id"] != "" ? $params["id"]  : NULL);
 			$customer_id = (isset($params["customer_id"]) && $params["customer_id"] != "" ? $params["customer_id"]  : NULL);
 			$purchase_material_code = (isset($params["material_code"]) && $params["material_code"] != "" ? $params["material_code"]  : NULL);
 			$purchase_material_name = (isset($params["material_name"]) && $params["material_name"] != "" ? $params["material_name"]  : NULL); 
 			$purchase_material_quantity = (isset($params["material_quantity"]) && $params["material_quantity"] != "" ? $params["material_quantity"]  : NULL);
 			$customer_activity_id = (isset($params["customer_activity_id"]) && $params["customer_activity_id"] != "" ? $params["customer_activity_id"]  : NULL);
 			$serial_number = (isset($params["serial_number"]) && $params["serial_number"] != "" ? $params["serial_number"]  : NULL);
 			
 			try {
 				 $stmt = $this->con->prepare("CALL insert_update_customer_purchase(:id,:customer_id,:purchase_material_code, :purchase_material_name, :purchase_material_quantity,:customer_activity_id,:serial_number)");

				$stmt->bindValue(":id", $id, PDO::PARAM_INT);
				$stmt->bindValue(":customer_id", $customer_id, PDO::PARAM_INT);
				$stmt->bindValue(":purchase_material_code", $purchase_material_code, PDO::PARAM_STR);
				$stmt->bindValue(":purchase_material_name", $purchase_material_name, PDO::PARAM_STR);
				$stmt->bindValue(":purchase_material_quantity", $purchase_material_quantity, PDO::PARAM_INT);
				$stmt->bindValue(":customer_activity_id", $customer_activity_id, PDO::PARAM_INT);
				$stmt->bindValue(":serial_number", $serial_number, PDO::PARAM_STR);
				$stmt->execute();
 			} catch(PDOException $errors){ 
     			$this->errorLog("insertUpdateCustomerPurchase", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}
	       	$data = array();
           	if($id == NULL) {
           		$data = $stmt->fetchAll();
           		$stmt->closeCursor();
           	}

           	$audit_array = array(
             "action" => "7", 
             "data_change" => json_encode($params)
        	); 
        	$this->addAuditEvent($audit_array);

            return array("success"=>true,"id"=>$data[0][0]);
        }

        public function deductStock($params) {
 			$item_code = (isset($params["code"]) && $params["code"] != "" ? $params["code"]  : NULL);
 			$item_quantity = (isset($params["quantity"]) && $params["quantity"] != "" ? $params["quantity"]  : NULL);
 			
 			try {
 				$stmt = $this->con->prepare("CALL deduct_stock(:item_code,:item_quantity)");

				$stmt->bindValue(":item_code", $item_code, PDO::PARAM_STR);
				$stmt->bindValue(":item_quantity", $item_quantity, PDO::PARAM_INT);
				$stmt->execute();
 			} catch(PDOException $errors){ 
     			$this->errorLog("deductStock", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

           	$audit_array = array(
		     "action" => "11", 
		     "data_change" => json_encode($params)
			); 
			$this->addAuditEvent($audit_array);

            return true;
        }

         public function deductConsultantStock($params) {
         	$user_id = (isset($params["user_id"]) && $params["user_id"] != "" ? $params["user_id"]  : NULL);
 			$item_code = (isset($params["code"]) && $params["code"] != "" ? $params["code"]  : NULL);
 			$item_quantity = (isset($params["quantity"]) && $params["quantity"] != "" ? $params["quantity"]  : NULL);
 			
 			try {
 				$stmt = $this->con->prepare("CALL deduct_user_stock(:user_id,:item_code,:item_quantity)");

 				$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
				$stmt->bindValue(":item_code", $item_code, PDO::PARAM_STR);
				$stmt->bindValue(":item_quantity", $item_quantity, PDO::PARAM_INT);
				$stmt->execute();
 			} catch(PDOException $errors){ 
     			$this->errorLog("deductConsultantStock", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

           	$audit_array = array(
		     "action" => "12", 
		     "data_change" => json_encode($params)
			); 
			$this->addAuditEvent($audit_array);

            return true;
        }

        public function forgotPasswordEmail($dbConnected,$email) {
        	$ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");
        	$valid = false;
			$prep_stmt = "SELECT Id FROM user WHERE email = ? LIMIT 1";
			$stmt = $this->dbConnected->prepare($prep_stmt);
			 
		   	// check existing email  
		    if ($stmt) {
		        $stmt->bind_param('s', $email);
		        $stmt->execute();
		        $stmt->store_result();
		        if ($stmt->num_rows == 1) {
		            $valid = true;
                    $stmt->close();
		        } else {
		        	return "Email does not exist!";
		        }
		    } else {
                $stmt->close();
		    }

		    $hash = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
        	$reset_date = date('Y-m-d H:i:s', strtotime("now"));  

        	$this->resetPassword(array("hash"=>$hash,"reset_date"=>$reset_date,"email"=>$email));

		    $emails = array();
            $emails[] = $email;
            $actual_link = "http://tm-me.net?ref=".$hash;
            $subject = "Tm-me Password Reset";
            $content = '<div class="panel panel-thermox">
                            <div class="panel-heading">
                                <h3 class="panel-title">Tm-me Reset</h3>
                            </div>
                            <div class="panel-body">
                                <p>Click this link to reset your password. <a href="' . $actual_link . '">' . $actual_link . '</a>
                                </p>
                            </div>
                        </div>';            

		    $emails = array();
		    $emails[] = $email;

		    if($valid) {
		    	if(!email($emails, "info@tm-me.net",$subject, $content, null, null, 1)) {
		            return "Email error";
		        } 
		    }

        	return true;
        } 

     	public function removeUserNotification($id) {

     		try {
     			// $this->con->beginTransaction();

     			$stmt = $this->con->prepare("CALL remove_user_notification(:id)");
				$stmt->bindValue(":id", $id, PDO::PARAM_INT);
				$stmt->execute();
     		} catch(PDOException $errors){ 
     			$this->errorLog("removeUserNotification", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage();
     		}

     		$audit_array = array(
             "action" => "8", 
             "data_change" => json_encode("notification_id:".$id)
        	); 
        	$this->addAuditEvent($audit_array);

          	return true;
        }

        public function addConsultantGift($con,$id,$items) { //need to contact anthie regarding this
        	$stmt = mysqli_query($con,"CALL add_consultant_gift('$id','$items')");

        	if( $stmt === false ) {       
	            $errors = mysqli_error($this->con);
                $this->errorLog("addConsultantGift", $errors);
                $error_code = $errors[0]["code"];
                return $error_code;
	        } else {

	        	// $audit_array = array(
	         //     "action" => "9", 
	         //     "actioned_by" => $_SESSION['user']['username'],
	         //     "data_change" => json_encode($id." ".$items)
	        	// ); 
	        	// $this->addAuditEvent($audit_array);

	         //  	return true;
	        }
        }

        public function getCustomerInvoiceData($customer_id,$customer_activity_id) {

        	try {
        		$stmt = $this->con->prepare("CALL get_customer_invoice_data(:customer_id,:customer_activity_id)");
	        	$stmt->bindValue(":customer_id", $customer_id, PDO::PARAM_INT);
	        	$stmt->bindValue(":customer_activity_id", $customer_activity_id, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
     			$this->errorLog("getCustomerInvoiceData", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage(); 
        	}

        	$data = array();
        	$data = $stmt->fetchAll();
       		$stmt->closeCursor();
           
            return json_encode($data);
        }

        public function getUserStock($id) {
        	
        	try {
        		$stmt = $this->con->prepare("CALL get_user_stock(:id)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
     			$this->errorLog("getUserStock", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage(); 
        	}

        	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();

       		return array("success"=>true,"data"=>$data);
        }

        public function getTmInvoiceNumbers($id) {
        
        	try {
        		$stmt = $this->con->prepare("CALL get_tm_invoice_numbers(:id)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
     			$this->errorLog("getTmInvoiceNumbers", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage(); 
        	}
    
           	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();
           
            return array("success"=>true,"data"=>$data);
        }

        public function getSales($type) {
        	$id = $_SESSION['user']['user_id'];

        	try {
        		$stmt = $this->con->prepare("CALL get_sales(:id,:type)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->bindValue(":type", $type, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
     			$this->errorLog("getSales", $errors->getMessage());
     			// $this->con->rollback();
		       	return $errors->getMessage(); 
        	}

           	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();
           
            return array("success"=>true,"data"=>$data);
        }

        public function getCustomerSerials($id) {

        	try {
	        	$stmt = $this->con->prepare("CALL get_tm_serials(:id)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->execute();
	        } catch(PDOException $errors){ 
	        	$this->errorLog("getCustomerSerials", $errors->getMessage());
		       	return $errors; 
		    }

           	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();

	        return array("success"=>true,"data"=>$data);
	        
        }

        public function getUserStockItems($searchVal) {
        	$id = $_SESSION['user']['user_id'];
        	$userLevel = $_SESSION['user']['user_type'];

        	try {
        		$stmt = $this->con->prepare("CALL get_user_stock_items(:id, :searchval, :userLevel)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->bindValue(":searchval", $searchVal, PDO::PARAM_STR);
	        	$stmt->bindValue(":userLevel", $userLevel, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
	        	$this->errorLog("getUserStockItems", $errors->getMessage());
		       	return $errors; 
		    }
	        
           	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();
	           
	        return array("success"=>true,"data"=>$data);
	        
        }

        public function getUnregSerials($id) {
        	try {
        		$stmt = $this->con->prepare("CALL get_customer_unreg_serials(:id)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
	        	$this->errorLog("getUnregSerials", $errors->getMessage());
		       	return $errors; 
		    }
	        
           	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();
	           
	        return array("success"=>true,"data"=>$data);
        }

        public function addCustomerSerial($id,$serial_number) {
        	try {
        		$stmt = $this->con->prepare("CALL add_customer_serial(:id,:serial_number)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->bindValue(":serial_number", $serial_number, PDO::PARAM_STR);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
	        	$this->errorLog("addCustomerSerial", $errors->getMessage());
		       	return $errors->getMessage(); 
		    }
	        
       		$audit_array = array(
             "action" => "14", 
             "data_change" => json_encode("customer:".$id." serial:".$serial_number)
        	); 
        	$this->addAuditEvent($audit_array);
	           
	        return true;
        }

        public function addUserStock($id,$code,$quantity) {
        	try {
        		$stmt = $this->con->prepare("CALL add_user_stock(:id,:code,:quantity)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->bindValue(":code", $code, PDO::PARAM_STR);
	        	$stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
	        	$this->errorLog("addUserStock", $errors->getMessage());
		       	return $errors->getMessage(); 
		    }
	        
       		$audit_array = array(
             "action" => "15", 
             "data_change" => json_encode("user:".$id." code:".$code." "."quantity: ".$quantity)
        	); 
        	$this->addAuditEvent($audit_array);
	           
	        return true;
        }

        public function addTmInvoiceNumbers($id,$invoice_number) {
        	try {
        		$stmt = $this->con->prepare("CALL add_tm_invoice_numbers(:id,:invoice_number)");
	        	$stmt->bindValue(":id", $id, PDO::PARAM_INT);
	        	$stmt->bindValue(":invoice_number", $invoice_number, PDO::PARAM_STR);
	        	$stmt->execute();
        	} catch(PDOException $errors){ 
	        	$this->errorLog("addTmInvoiceNumbers", $errors->getMessage());
		       	return $errors->getMessage(); 
		    }
	        
       		$audit_array = array(
             "action" => "16", 
             "data_change" => json_encode("user:".$id." invoice_number:".$invoice_number)
        	); 
        	$this->addAuditEvent($audit_array);
	           
	        return true;
        }

        public function checkUserStock($id,$code,$quantity) {

        	try {
	        	$stmt = $this->con->prepare("CALL check_user_stock(:user_id,:code,:quantity)");
	        	$stmt->bindValue(":user_id", $id, PDO::PARAM_INT);
	        	$stmt->bindValue(":code", $code, PDO::PARAM_STR);
	        	$stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
	        	$stmt->execute();
	        } catch(PDOException $errors){ 
	        	$this->errorLog("checkUserStock", $errors->getMessage());
		       	return $errors; 
		    }

           	$data = array();
       		$data = $stmt->fetchAll();
       		$stmt->closeCursor();

	        return array("success"=>true,"data"=>$data[0]);
	        
        }

        public function getInventory($access) {

     //       $stmt = mysqli_prepare($this->dbConnected, 'CALL get_main_inventory(?)');
		   // mysqli_stmt_bind_param($stmt, 'i', $access);

		   // mysqli_stmt_execute($stmt);

		   // $result = mysqli_store_result($this->dbConnected);

		   // while($row = mysqli_fetch_assoc($result)) {
		   // 	$data[] = $row;
		   // }


        	$stmt = $this->dbConnected->prepare("CALL get_main_inventory(?)");
        	$stmt->bind_param('s', $access); 
				$stmt->execute();        	
        	// if(!$stmt->execute()) {
	        // 	$this->errorLog("getInventory", $error);
	        // 	$this->errorLog("getInventory", $stmt->error);
		       // 	return $dbConnected->errno; 
        	// }

        	/* bind result variables */
  

        
        	$result = $stmt->store_result();
        	 // $data    = $stmt->fetch_array(MYSQLI_ASSOC);
        	while($row=$result->fetch_row()) {
        		$data[] = $row;
        	}

        	return array("success"=>true,"data"=>$data);	
        }

        public function resetPassword($params) {
        	$reset_hash = (isset($params["hash"]) && $params["hash"] != "" ? $params["hash"]  : NULL);
        	$reset_date = (isset($params["reset_date"]) && $params["reset_date"] != "" ? $params["reset_date"]  : NULL);
       		$email = (isset($params["email"]) && $params["email"] != "" ? $params["email"]  : NULL);

       		try {

	            $stmt = $this->con->prepare("UPDATE user SET reset_activate_hash = :hash, reset_activate_date = :reset_date, locked = 1 WHERE email = :email");

	            $stmt->bindValue(":hash", $reset_hash, PDO::PARAM_STR);
	            $stmt->bindValue(":reset_date", $reset_date, PDO::PARAM_STR);
	            $stmt->bindValue(":email", $email, PDO::PARAM_STR);

	            $stmt->execute();

	        } catch(PDOException $errors){ 
	            $this->errorLog("resetPassword", $errors->getMessage());
	            // $this->con->rollback();
	            // print_r($errors->getMessage());
	            return $errors->getMessage();
	        }

	        $audit_array = array(
             "action" => "18", 
             "data_change" => json_encode($params)
        	); 
        	$this->addAuditEvent($audit_array);

	    	return true;  
        }

        public function insertUser($params) {

        	$username = (isset($params["username"]) && $params["username"] != "" ? $params["username"]  : NULL);
        	$forename = (isset($params["forename"]) && $params["forename"] != "" ? $params["forename"]  : NULL);
        	$surname = (isset($params["surname"]) && $params["surname"] != "" ? $params["surname"]  : NULL);
        	$email = (isset($params["email"]) && $params["email"] != "" ? $params["email"]  : NULL);
        	$tel_no = (isset($params["tel_no"]) && $params["tel_no"] != "" ? $params["tel_no"]  : NULL);
        	$country = (isset($params["country"]) && $params["country"] != "" ? $params["country"]  : NULL);
        	$city = (isset($params["city"]) && $params["city"] != "" ? $params["city"]  : NULL);
        	$user_type = (isset($params["user_type"]) && $params["user_type"] != "" ? $params["user_type"]  : NULL);
        	$register_date = (isset($params["register_date"]) && $params["register_date"] != "" ? $params["register_date"]  : NULL);
        	$parent_id = (isset($params["parent_id"]) && $params["parent_id"] != "" ? $params["parent_id"]  : NULL);
        	$locked = (isset($params["locked"]) && $params["locked"] != "" ? $params["locked"]  : NULL);
        	$activate_hash = (isset($params["hash"]) && $params["hash"] != "" ? $params["hash"]  : NULL);
        	$activate_date = (isset($params["activate_date"]) && $params["activate_date"] != "" ? $params["activate_date"]  : NULL);

        	try {

	            $stmt = $this->con->prepare("INSERT INTO user (user_name, forename, surname, email, tel_no, country, city, user_type, register_date, parent_id, locked, reset_activate_hash, reset_activate_date) VALUES (:username,:forename,:surname,:email,:tel_no,:country,:city,:user_type,:register_date,:parent_id,:locked,:hash,:activate_date)");

	            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
	            $stmt->bindValue(":forename", $forename, PDO::PARAM_STR);
	            $stmt->bindValue(":surname", $surname, PDO::PARAM_STR);
	            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
	            $stmt->bindValue(":tel_no", $tel_no, PDO::PARAM_STR);
	            $stmt->bindValue(":country", $country, PDO::PARAM_STR);
	            $stmt->bindValue(":city", $city, PDO::PARAM_STR);
	            $stmt->bindValue(":user_type", $user_type, PDO::PARAM_INT);
	            $stmt->bindValue(":register_date", $register_date, PDO::PARAM_STR);
	            $stmt->bindValue(":parent_id", $parent_id, PDO::PARAM_INT);
	            $stmt->bindValue(":locked", $locked, PDO::PARAM_INT);
	            $stmt->bindValue(":hash", $activate_hash, PDO::PARAM_STR);
	            $stmt->bindValue(":activate_date", $activate_date, PDO::PARAM_STR);

	            $stmt->execute();

	        } catch(PDOException $errors){ 
	            $this->errorLog("insertUser", $errors->getMessage());
	            // $this->con->rollback();
	            // print_r($errors->getMessage());
	            return $errors->getMessage();
	        }

	        $audit_array = array(
             "action" => "17", 
             "data_change" => json_encode($params)
        	); 
        	$this->addAuditEvent($audit_array);

	    	return true;    
        }

	}

?>