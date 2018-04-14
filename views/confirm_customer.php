<?php
	include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 
	// require_once($_SERVER["DOCUMENT_ROOT"]."/Classes/thermox.php");

	$content = '';


	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$customer_details = json_decode(file_get_contents("php://input"),true);
		// $thermox->storeCustomerSession($customer_details);
		$_SESSION['selected_customer'] = json_encode($customer_details);
	  
	}
	
	$content = '<div style="width:400px;">';
	$content .= '	<h2 class="text-center">Is this the correct customer?</h2>';
	$content .= '	<div class="col-xs-12 pull pull-left">';
	$content .= '		<ul class="list-group">';
	$content .= '			<li class="list-group-item"><b>Title:</b><br/> '.$customer_details['customer_title'].'</li>';
	$content .= '			<li class="list-group-item"><b>First Name:</b><br/> '.$customer_details['customer_first_name'].'</li>';
	$content .= '			<li class="list-group-item"><b>Last Name:</b> <br/>'.$customer_details['customer_last_name'].'</li>';
	$content .= '			<li class="list-group-item"><b>Date of Registration:</b> <br/>'.$customer_details['customer_register_date'].'</li>';
	$content .= '			<li class="list-group-item"><b>Email Address:</b> <br/>'.$customer_details['customer_email'].'</li>';
	$content .= '			<li class="list-group-item"><b>Customer Number:</b> <br/>'.$customer_details['customer_id'].'</li>';
	$content .= '		</ul>';
	$content .= '	</div>';
	$content .= '	<div class="clearfix">';
	$content .= '	</div>';
	$content .= '	<div  class="col-xs-12" style="padding-top:10px">';
	$content .= '		<button type="button" class="btn btn-danger pull-left incorrect-customer-search" title="Incorrect Customer"><span class="glyphicon glyphicon-remove"></span> Incorrect Customer</button>';
	$content .= '		<button type="button" class="btn btn-success pull-right correct-customer-search" data-customer-name="'.$customer_details['customer_full_name'].'" data-customer-id="'.$customer_details["customer_id"].'" data-customer-email="'.$customer_details["customer_email"].'" data-customer-reg="'.$customer_details["register_date"].'" title="Correct Customer"><span class="glyphicon glyphicon-ok"></span> Correct Customer </button>';
	$content .= '	</div>';
	$content .= '</div>';

	echo $content;

	return;

?>