<?php
	header('Content-Type: application/json');
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

	if(isset($_POST["oa"])) {
		$ret["success"] = true;
		echo json_encode($ret);
		return;
	}

	$data = (json_decode($_GET["data"],true));

	$info = json_decode($thermox->getCustomerInvoiceData($data["customer_id"],$data["customer_activity_id"]),true);

	$customer_name = $info[0]["customer_formatted_name"];
	$customer_address = $info[0]["customer_address"];
	$customer_country = $info[0]["customer_country"];
	$customer_city = $info[0]["customer_city"];
	$customer_tel_no = $info[0]["customer_tel_no"];
	$customer_email = $info[0]["customer_email"];
	$consultant_name = $info[0]["consultant_formatted_name"];
	$invoice_number = $info[0]["invoice_number"];
	$serial_number = $info[0]["serial_number"];
	$purchase_material_descriptions = str_getcsv($info[0]["purchase_item_description"]);
	$purchase_material_quantities = str_getcsv($info[0]["purchase_item_quantity"]);
	$purchase_material_codes = str_getcsv($info[0]["purchase_item_code"]);
	$purchase_material_prices  = str_getcsv($info[0]["purchase_item_price"]);
	$purchase_date = date('Y-m-d', $info[0]["purchase_date"]);


	$objTpl = PHPExcel_IOFactory::load("../Classes/invoice.xls");
 	$objTpl->setActiveSheetIndex(0);

 	$objTpl->getActiveSheet()->setCellValue('V8', stripslashes($invoice_number));
 	$objTpl->getActiveSheet()->setCellValue('E10', stripslashes($consultant_name));
 	$objTpl->getActiveSheet()->setCellValue('R10', stripslashes($purchase_date));
 	$objTpl->getActiveSheet()->setCellValue('E12', stripslashes($customer_name));
	$objTpl->getActiveSheet()->setCellValue('E13', stripslashes($customer_address));
	$objTpl->getActiveSheet()->setCellValue('R14', stripslashes($customer_city));
 	$objTpl->getActiveSheet()->setCellValue('G15', stripslashes($customer_tel_no));
	$objTpl->getActiveSheet()->setCellValue('G16', stripslashes($customer_email));	
	$objTpl->getActiveSheet()->setCellValue('N40', stripslashes($serial_number));
	

 	for($i=0;$i<sizeof($purchase_material_descriptions);$i++) {

 		$code = $purchase_material_codes[$i];
 		$material_name = $purchase_material_descriptions[$i];
 		$purchase_material_quantity = $purchase_material_quantities[$i];
 		$material_price = $purchase_material_prices[$i];

 		$objTpl->getActiveSheet()->setCellValue('A2'.(int)($i+1), stripslashes($code));	
 		$objTpl->getActiveSheet()->setCellValue('N2'.(int)($i+1), stripslashes($purchase_material_quantity));
 		$objTpl->getActiveSheet()->setCellValue('E2'.(int)($i+1), stripslashes($material_name));
 		$objTpl->getActiveSheet()->setCellValue('P2'.(int)($i+1), stripslashes($material_price));
 	}
 		
	 
	
	$file_name = "invoice_".date("Y-m-d_H:i:s").".xls";

	$objWriter = PHPExcel_IOFactory::createWriter($objTpl, 'Excel5');  //downloadable file is in Excel 2003 format (.xls)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');
	 ob_end_clean();
	$objWriter->save("php://output");  //send it to user, of course you can save it to disk also!

	exit();

	  // $excelWriter = PHPExcel_IOFactory::createWriter($this->excel,'Excel2007');
   //  header('Content-Type:application/vnd.ms-excel');
   //  header('Content-Disposition:attachment;filename="'.$dept.'.xlsx"');
   //  header('Cache-Control:max-age=0');
   //  ob_end_clean();
   //  $excelWriter->save('php://output');
?>
