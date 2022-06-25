<?php
/*------------------------------------------------------------------------
		# SKROUTZ BRIDGE by SBZ systems - Solon Zenetzis - version 1.1
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2022 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');


$orderid=$_REQUEST['orderid'];
$apikey='';




function replace_bad($ch) {
	
	$ch=str_replace("'","`",$ch);
	
	return $ch;
}














//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/// create a cronjob to refresh pending orders over 24 hours:   emdi_skroutz_webhook.php?orderid=recheck_pending

if ($orderid == 'recheck_pending') {
	
	
	

	require 'config.php';

	$host                         = DB_HOSTNAME;
	$user                         = DB_USERNAME;
	$password                     = DB_PASSWORD;
	$db                           = DB_DATABASE;




	$link=mysqli_connect("$host", $user, $password) or die(mysqli_error($link));
	mysqli_select_db($link,"$db") or die(mysqli_error($link));
	mysqli_set_charset($link,'utf8'); 





	$data = mysqli_query($link,"
	SELECT * FROM `sbz_skroutz_docs`
	where 
	state='open' 
	and created_at>= NOW() - INTERVAL 1 DAY
	") or die(mysqli_error($link)); //


	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;ΧΡΗΣΤΗΣ;VOUCHER;ΚΑΤΑΣΤΑΣΗ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ ΑΠΟΣΤΟΛΗΣ;ΤΡΟΠΟΣ ΠΛΗΡΩΜΗΣ;ΤΡΟΠΟΣ ΑΠΟΣΤΟΛΗΣ;ΠΑΡΑΣΤΑΤΙΚΟ;<br>\n";

	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['code'];  	 	
		$userid= $alldata['customer_id']; 
		$hmera=$alldata['created_at'] ;
		$shipping=   str_replace('€','',       0);
		$comment=$alldata['comment'].''.$alldata['courier'];
		//$voucher=$alldata['courier_tracking'];
		$invoice=$alldata['invoice'];
		$courier_voucher=$alldata['courier_voucher'];
		$courier_tracking_codes=$alldata['courier_tracking_codes'];
		$voucher=$courier_tracking_codes.'|'.$courier_voucher;

		//$customer_invoice_code_prefix='IC';
		//$customer_code_prefix='AC';

		if ($invoice=='1') {
			$deliverycust=$customer_code_prefix.$userid;
			$maincust=$customer_invoice_code_prefix.$userid;	 
		} else {
			$deliverycust='';
			$maincust=$customer_code_prefix.$userid;
		}
		
		
		




		// call skroutz

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.skroutz.gr/merchants/ecommerce/orders/'.$id,
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
		
		
		$alldatacurl=json_decode($response);
		// print_r ($alldatacurl);





		$datacurl = mysqli_query($link,"
	update `sbz_skroutz_docs` set 
	`created_at`='".date('Y-m-d H:i:s', strtotime($alldatacurl->order->created_at))."',
	`expires_at`='".date('Y-m-d H:i:s', strtotime($alldatacurl->order->expires_at))."',	
	`dispatch_until`='".date('Y-m-d H:i:s', strtotime($alldatacurl->order->dispatch_until))."',
	`state`='".$order_state."',
	`courier`='".$alldatacurl->order->courier."', 
	`courier_voucher`='".$alldatacurl->order->courier_voucher."', 
	`courier_tracking_codes`='".$alldatacurl->order->courier_tracking_codes[0]."',
	`state`='".$alldatacurl->order->state."'
	where code='".$alldatacurl->order->code."'
	") or die(mysqli_error($link)); //

		//




		
		$rowtext= $created_at.'#'.$id.';'.$maincust.";0;0;0;".$hmera.";".$comment.";;".$voucher.";;".$deliverycust.";ΚΑΡΤΑ;COURIER SKROUTZ;;";		
		$rowtext = str_ireplace("&amp;", "&", $rowtext);
		$rowtext = str_ireplace("&quot;", "'", $rowtext);
		$rowtext = str_ireplace("&#039;", "'", $rowtext);
		$rowtext = str_ireplace("\n", "", $rowtext);
		echo $rowtext."<br>\n";
		
		
		
		
		

	}
	
	mysqli_close($link);
	exit;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
































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
file_put_contents('smart_cart.log', $message['event_type'].'|'.$message['event_time'].'|'.$message['order']['code'].'|'.$message['order']['state'].'|'."\n", FILE_APPEND | LOCK_EX);








/*
			{
					"id": "0ngeJNXMk1",
					"label": "Σοφοκλέους 146, Τ.Κ. 17672, Καλλιθέα, Αττική"
				},
				{
					"id": "rbgjjLQgeo",
					"label": "Αχιλλέως 16, Τ.Κ. 17674, Καλλιθέα, Αττική"
				}
				
				
				
CREATE TABLE `sbz_skroutz_docs` ( 
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
`invoice` VARCHAR(5) NOT NULL , 
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
`express` VARCHAR(5) NOT NULL , 
`gift_wrap` VARCHAR(5) NOT NULL , 
`fulfilled_by_skroutz` VARCHAR(5) NOT NULL , 
PRIMARY KEY (`code`)
) ENGINE = MyISAM CHARSET=utf8 COLLATE utf8_unicode_ci;
CREATE TABLE `sbz_skroutz_lines` ( 
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

$host                         = DB_HOSTNAME;
$user                         = DB_USERNAME;
$password                     = DB_PASSWORD;
$db                           = DB_DATABASE;




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




$order_state=$message['order']['state'];
if (!$order_state) { $order_state=$alldata->order->state; }
$event_type=$message['event_type'];
//if (!$event_type) { $event_type=$alldata->order->state; }




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

                                      	,`express`,`gift_wrap`,`fulfilled_by_skroutz`
										) 
								VALUES ('".$alldata->order->code."', '".$order_state."', '".$alldata->order->customer->id."', '".replace_bad($alldata->order->customer->first_name)."', '".replace_bad($alldata->order->customer->last_name)."', 
										'".replace_bad($alldata->order->customer->address->street_name)."','".$alldata->order->customer->address->street_number."', '".$alldata->order->customer->address->zip."','".replace_bad($alldata->order->customer->address->city)."', 
										'".replace_bad($alldata->order->customer->address->region)."','".replace_bad($alldata->order->customer->address->collection_point_address)."', 
										'".$alldata->order->invoice."', '".replace_bad($alldata->order->comments)."','".$alldata->order->courier."', '".$alldata->order->courier_voucher."', '".$alldata->order->courier_tracking_codes[0]."', 
										'".date('Y-m-d H:i:s', strtotime($alldata->order->created_at))."','".date('Y-m-d H:i:s', strtotime($alldata->order->expires_at))."','".date('Y-m-d H:i:s', strtotime($alldata->order->dispatch_until))."'
										,'".$event_type."'
										
										, '".replace_bad($alldata->order->invoice_details->company)."', '".replace_bad($alldata->order->invoice_details->profession)."', '".$alldata->order->invoice_details->vat_number."'
										, '".replace_bad($alldata->order->invoice_details->doy)."', '".replace_bad($alldata->order->invoice_details->address->street_name)."', '".$alldata->order->invoice_details->address->street_number."'
										, '".$alldata->order->invoice_details->address->zip."', '".replace_bad($alldata->order->invoice_details->address->city)."', '".replace_bad($alldata->order->invoice_details->address->region)."'
										, '".$alldata->order->invoice_details->vat_exclusion_requested."'
										
                                        ,'".$alldata->order->express."','".$alldata->order->gift_wrap."','".$alldata->order->fulfilled_by_skroutz."'
                                        )
		
		ON DUPLICATE KEY UPDATE `state`='".$order_state."',`courier`='".$alldata->order->courier."', `courier_voucher`='".$alldata->order->courier_voucher."', 
								`courier_tracking_codes`='".$alldata->order->courier_tracking_codes[0]."',`event_type`='".$event_type."'
		
		";


//echo $query;           $alldata->order->state

/////////////

file_put_contents('smart_cart.log',$query."\n", FILE_APPEND | LOCK_EX);


$data = mysqli_query($link,$query) or die(mysqli_error($link));;


foreach ($alldata->order->line_items as $value) {
	
	
	
	$query="
		
		INSERT INTO `sbz_skroutz_lines` (`code`, `id`, `shop_uid`, `product_name`, `quantity`, `size_label`, `size_value` , `shop_value`, `unit_price` , `total_price` , `price_includes_vat` ) 
								VALUES ('".$alldata->order->code."', '".$value->id."', '".$value->shop_uid."', '".replace_bad($value->product_name)."', ".$value->quantity.", 
										'".$value->size->label."','".$value->size->value."','".$value->size->shop_value."',".$value->unit_price.",".$value->total_price.",".$value->price_includes_vat." 
										) 
		ON DUPLICATE KEY UPDATE `code`='".$alldata->order->code."'
		
		";
	
	file_put_contents('smart_cart.log',$query."\n", FILE_APPEND | LOCK_EX);
	/////////////
	$data = mysqli_query($link,$query) or die(mysqli_error($link));;
	
	

}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ΟΤΑΝ ΕΧΕΙ ΑΚΥΡΩΘΕΙ Η ΑΡΧΙΚΗ ΠΑΡΑΓΓΕΛΙΑ
//echo '#'.$orderid.'#';


$tags = explode('-' , $orderid);
$num_tags = count($tags);
if ($num_tags>2) {


	$orderid=$tags[0].'-'.$tags[1];

	//echo '#'.$orderid.'#';




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




	$order_state=$message['order']['state'];
	if (!$order_state) { $order_state=$alldata->order->state; }
	$event_type=$message['event_type'];
	//if (!$event_type) { $event_type=$alldata->order->state; }




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

                                        ,`express`,`gift_wrap`,`fulfilled_by_skroutz`
										) 
								VALUES ('".$alldata->order->code."', '".$order_state."', '".$alldata->order->customer->id."', '".replace_bad($alldata->order->customer->first_name)."', '".replace_bad($alldata->order->customer->last_name)."', 
										'".replace_bad($alldata->order->customer->address->street_name)."','".$alldata->order->customer->address->street_number."', '".$alldata->order->customer->address->zip."','".replace_bad($alldata->order->customer->address->city)."', 
										'".replace_bad($alldata->order->customer->address->region)."','".replace_bad($alldata->order->customer->address->collection_point_address)."', 
										'".$alldata->order->invoice."', '".replace_bad($alldata->order->comments)."','".$alldata->order->courier."', '".$alldata->order->courier_voucher."', '".$alldata->order->courier_tracking_codes[0]."', 
										'".date('Y-m-d H:i:s', strtotime($alldata->order->created_at))."','".date('Y-m-d H:i:s', strtotime($alldata->order->expires_at))."','".date('Y-m-d H:i:s', strtotime($alldata->order->dispatch_until))."'
										,'".$event_type."'
										
										, '".replace_bad($alldata->order->invoice_details->company)."', '".replace_bad($alldata->order->invoice_details->profession)."', '".$alldata->order->invoice_details->vat_number."'
										, '".replace_bad($alldata->order->invoice_details->doy)."', '".replace_bad($alldata->order->invoice_details->address->street_name)."', '".$alldata->order->invoice_details->address->street_number."'
										, '".$alldata->order->invoice_details->address->zip."', '".replace_bad($alldata->order->invoice_details->address->city)."', '".replace_bad($alldata->order->invoice_details->address->region)."'
										, '".$alldata->order->invoice_details->vat_exclusion_requested."'
										
                                        ,'".$alldata->order->express."','".$alldata->order->gift_wrap."','".$alldata->order->fulfilled_by_skroutz."'
                                        )
		
		ON DUPLICATE KEY UPDATE `state`='".$order_state."',`courier`='".$alldata->order->courier."', `courier_voucher`='".$alldata->order->courier_voucher."', 
								`courier_tracking_codes`='".$alldata->order->courier_tracking_codes[0]."',`event_type`='".$event_type."'
		
		";
	
	
	//echo $query;           $alldata->order->state
	
	/////////////

	file_put_contents('smart_cart.log',$query."\n", FILE_APPEND | LOCK_EX);


	$data = mysqli_query($link,$query) or die(mysqli_error($link));;


	foreach ($alldata->order->line_items as $value) {
		
		
		
		$query="
		
		INSERT INTO `sbz_skroutz_lines` (`code`, `id`, `shop_uid`, `product_name`, `quantity`, `size_label`, `size_value` , `shop_value`, `unit_price` , `total_price` , `price_includes_vat` ) 
								VALUES ('".$alldata->order->code."', '".$value->id."', '".$value->shop_uid."', '".replace_bad($value->product_name)."', ".$value->quantity.", 
										'".$value->size->label."','".$value->size->value."','".$value->size->shop_value."',".$value->unit_price.",".$value->total_price.",".$value->price_includes_vat." 
										) 
		ON DUPLICATE KEY UPDATE `code`='".$alldata->order->code."'
		
		";
		
		file_put_contents('smart_cart.log',$query."\n", FILE_APPEND | LOCK_EX);
		/////////////
		$data = mysqli_query($link,$query) or die(mysqli_error($link));;
		
		

	}



}



mysqli_close($link);
//echo 'ok';


?>
