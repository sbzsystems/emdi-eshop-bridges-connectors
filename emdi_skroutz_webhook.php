<?php
/*------------------------------------------------------------------------
		# SKROUTZ webhook by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2021 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');


$orderid=$_REQUEST['orderid'];
$apikey='';




$message = json_decode(file_get_contents('php://input'), true);

if ($message) {
	


	if (isset($message['event_type'])) {
		
		switch ($message['event_type']) {			
		case 'new_order':
			//$message['order'];
			break;			
		case 'order_updated':			
			// $message['changes']);			
			break;
		}

		$event_time = $message['event_time'];

		if(isset($message['changes'])) {
			$changes = $message['changes']; 
		}

	}
	
	header("HTTP/1.1 200 Ok");
	
}





// LOGS
//file_put_contents('smart_cart.log', $message['event_type'].'|'.$message['event_time'].'|'.$message['order']['code'].'|'.$message['order']['state'].'|'."\n", FILE_APPEND | LOCK_EX);








/*



CREATE TABLE `bustolin_bd`.`sbz_skroutz_docs` ( 

`code` VARCHAR(20) NOT NULL , 
`event_type` VARCHAR(20) NOT NULL , 
`state` VARCHAR(20) NOT NULL , 
`customer_id` VARCHAR(20) NOT NULL , 
`customer_first_name` VARCHAR(50) NOT NULL , 
`customer_last_name` VARCHAR(50) NOT NULL , 

`customer_address_street_name` VARCHAR(50) NOT NULL , 
`customer_address_street_number` VARCHAR(20) NOT NULL , 
`customer_address_street_zip` VARCHAR(20) NOT NULL , 
`customer_address_street_city` VARCHAR(50) NOT NULL , 
`customer_address_street_region` VARCHAR(50) NOT NULL , 
`customer_address_street_pickup_from_collection_point` VARCHAR(200) NOT NULL , 

`invoice` VARCHAR(50) NOT NULL , 

`comments` VARCHAR(500) NOT NULL , 
`courier` VARCHAR(50) NOT NULL , 
`courier_voucher` VARCHAR(500) NOT NULL , 
`courier_tracking_codes` VARCHAR(50) NOT NULL , 

`created_at` TIMESTAMP NOT NULL  , 
`expires_at` TIMESTAMP NOT NULL  , 
`dispatch_until` TIMESTAMP NOT NULL  , 

`invoice_company` VARCHAR(100) NOT NULL , 
`invoice_profession` VARCHAR(100) NOT NULL , 
`invoice_vat_number` VARCHAR(20) NOT NULL , 
`invoice_doy` VARCHAR(50) NOT NULL , 
`invoice_street_name` VARCHAR(50) NOT NULL , 
`invoice_street_number` VARCHAR(20) NOT NULL , 
`invoice_zip` VARCHAR(20) NOT NULL , 
`invoice_city` VARCHAR(50) NOT NULL , 
`invoice_region` VARCHAR(50) NOT NULL , 
`invoice_vat_exclusion` VARCHAR(10) NOT NULL ,  

PRIMARY KEY (`code`)

) ENGINE = MyISAM CHARSET=utf8 COLLATE utf8_unicode_ci;

 

 
CREATE TABLE `bustolin_bd`.`sbz_skroutz_lines` ( 

`code` VARCHAR(20) NOT NULL , 
`id` VARCHAR(20) NOT NULL , 
`shop_uid` VARCHAR(20) NOT NULL , 
`product_name` VARCHAR(200) NOT NULL , 
`quantity` FLOAT NOT NULL , 
`size_label` VARCHAR(50) NOT NULL , 
`size_value` VARCHAR(50) NOT NULL , 
`shop_value` VARCHAR(50) NOT NULL , 
`unit_price` FLOAT NOT NULL , 
`total_price` FLOAT NOT NULL , 
`price_includes_vat` FLOAT NOT NULL , 

PRIMARY KEY (`id`),
INDEX `shop_uid` (`shop_uid`),
INDEX `code` (`code`)

) ENGINE = MyISAM CHARSET=utf8 COLLATE utf8_unicode_ci;


*/








require 'config.php';
$host = DB_HOSTNAME;
$user = DB_USERNAME;
$password = DB_PASSWORD;
$db = DB_DATABASE;

$link=mysqli_connect("$host", $user, $password) or die(mysqli_error($link));
mysqli_select_db($link,"$db") or die(mysqli_error($link));
mysqli_set_charset($link,'utf8'); 


if (!$orderid) {
	$orderid=$message['order']['code'];
	//echo 'ok';
}


$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://api.skroutz.gr/merchants/ecommerce/orders/'.$orderid,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'GET',
CURLOPT_HTTPHEADER => array(
'Authorization: Bearer '.$apikey,
'Accept: application/vnd.skroutz+json; version=3.0'
),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;



$alldata=json_decode($response);
//print_r ($alldata);



$query="
		
		INSERT INTO `sbz_skroutz_docs` (`code`, `state`, `customer_id`, `customer_first_name`, `customer_last_name`, 
										`customer_address_street_name`, `customer_address_street_number`, `customer_address_street_zip`, `customer_address_street_city`, 
										`customer_address_street_region`, `customer_address_street_pickup_from_collection_point`, 
										`invoice`, `comments`, `courier`, `courier_voucher`, `courier_tracking_codes`, 
										`created_at`, `expires_at`, `dispatch_until`, `event_type`, 
										
										`invoice_company`, `invoice_profession`, `invoice_vat_number`, 
										`invoice_doy`, `invoice_street_name`, `invoice_street_number`, 
										`invoice_zip`, `invoice_city`, `invoice_region`, 
										`invoice_vat_exclusion`
										) 
								VALUES ('".$alldata->order->code."', '".$alldata->order->state."', '".$alldata->order->customer->id."', '".$alldata->order->customer->first_name."', '".$alldata->order->customer->last_name."', 
										'".$alldata->order->customer->address->street_name."','".$alldata->order->customer->address->street_number."', '".$alldata->order->customer->address->zip."','".$alldata->order->customer->address->city."', 
										'".$alldata->order->customer->address->region."','".$alldata->order->customer->address->collection_point_address."', 
										'".$alldata->order->invoice."', '".$alldata->order->comments."','".$alldata->order->courier."', '".$alldata->order->courier_voucher."', '".$alldata->order->courier_tracking_codes[0]."', 
										'".date('Y-m-d H:i:s', strtotime($alldata->order->created_at))."','".date('Y-m-d H:i:s', strtotime($alldata->order->expires_at))."','".date('Y-m-d H:i:s', strtotime($alldata->order->dispatch_until))."'
										,'".$message['event_type']."'
										
										, '".$alldata->order->invoice_details->company."', '".$alldata->order->invoice_details->profession."', '".$alldata->order->invoice_details->vat_number."'
										, '".$alldata->order->invoice_details->doy."', '".$alldata->order->invoice_details->address->street_name."', '".$alldata->order->invoice_details->address->street_number."'
										, '".$alldata->order->invoice_details->address->zip."', '".$alldata->order->invoice_details->address->city."', '".$alldata->order->invoice_details->address->region."'
										, '".$alldata->order->invoice_details->vat_exclusion_requested."'
										)
		
		ON DUPLICATE KEY UPDATE `state`='".$alldata->order->state."',`courier`='".$alldata->order->courier."', `courier_voucher`='".$alldata->order->courier_voucher."', 
		                        `courier_tracking_codes`='".$alldata->order->courier_tracking_codes[0]."',`event_type`='".$message['event_type']."'
		
		";
		
		
		//echo $query;
		
/////////////
$data = mysqli_query($link,$query) or die(mysqli_error($link));;


foreach ($alldata->order->line_items as $value) {
	
	
	
	$query="
		
		INSERT INTO `sbz_skroutz_lines` (`code`, `id`, `shop_uid`, `product_name`, `quantity`, `size_label`, `size_value` , `shop_value`, `unit_price` , `total_price` , `price_includes_vat` ) 
							     VALUES ('".$alldata->order->code."', '".$value->id."', '".$value->shop_uid."', '".$value->product_name."', ".$value->quantity.", 
								         '".$value->size->label."','".$value->size->value."','".$value->size->shop_value."',".$value->unit_price.",".$value->total_price.",".$value->price_includes_vat." 
										) 
		ON DUPLICATE KEY UPDATE `code`='".$alldata->order->code."'
		
		";
	/////////////
	$data = mysqli_query($link,$query) or die(mysqli_error($link));;
	
	

}
mysqli_close($link);































//echo 'ok';


?> 					