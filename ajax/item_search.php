<?php
    header('Content-Type: application/json');
    include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

    $ret = array("success" => false, "message" => "", "refresh" => false, "response" => "");

    if(isset($_POST['search_val'])){
      $search_val = $_POST['search_val'];

      $sql="SELECT material_name, code, quantity
        FROM stock 
        WHERE material_name 
          LIKE '%$search_val%' OR code LIKE '%$search_val%'"; 
    } else{
      
      $sql="SELECT material_name, code, quantity
        FROM stock 
        WHERE material_name"; 
    }

      $result = mysqli_query($dbConnected, $sql);

      if($result) {
        $ret["success"] = true;
      } else {
        $ret["message"] = "SQL Query error";
      }

      while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
        $name[]= $row['material_name'];
        $code[]= $row['code'];
        $quantity[] = $row['quantity'];
      }

      $ret["response"] = array("material_name"=>$name,"material_code"=>$code, "material_quantity"=>$quantity);

      echo json_encode($ret);

      mysqli_free_result($result);

      return;

    
?>