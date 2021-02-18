<?php
//PS
/*------------------------------------------------------------------------
		# EMDI - STRIPE BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2021 sbzsystems.com. All Rights Reserved.
		# @license - https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: https://www.sbzsystems.com
		# Technical Support:  Forum - https://www.sbzsystems.com
	-------------------------------------------------------------------------*/


header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');

$logfile = 'emdibridge.txt';
$offset= '';

$product_code_prefix='';
$customer_code_prefix='IC';
$onetime_customer_code_prefix='AC';
$lang_code='el-gr';
$lang_id=2;
$store_id=0;
$tmp_path ='/home/sbz/domains/sbzsystems.com/public_html/apps';
$timezone=$config->offset; 
$passkey='';
$relatedchar='^';
$addonid='PRO';
$manu_field='Brand';
$size_field='Μέγεθος';
$avail_id=7;
$notavail_id=6;
$apikey='sk_live_XXXXXXXXXXXXXXXXXXXXXX';

//////////////
$measurement='ΤΕΜΑΧΙΑ';
$measurementaddon='ΠΡΟΣΘΕΤΑ';

//$vat_field='ΑΦΜ';
//$tax_office_field='ΔΟΥ';
$maintax=24;
// Connects to your Database


$photourl=HTTP_SERVER.'image/';	
$produrl=HTTP_SERVER.'index.php?route=product/product&product_id=';	
$customerid=$_REQUEST['customerid'];
$productid=$_REQUEST['productid'];
$stock=$_REQUEST['stock'];
$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE

if (!($key==$passkey)) { exit; }
///////////////////////////////////

//if (!is_dir($tmp_path)) {
//	mkdir($tmp_path);
//}


if ($action == 'deletetmp') {
	$File = $tmp_path."/customers_".$key;
	unlink($File);
	$file = $tmp_path."/products_".$key; 
	unlink($file);    
}

if ($action == 'customersok') {
	$File = $tmp_path."/customers_".$key; 
	$Handle = fopen($File, 'w');
	$Data =  time()-(3600*$timezone); 	//time();
	fwrite($Handle, date('Y-m-d H:i:s', $Data)); 
	fclose($Handle); 	
}

if ($action == 'productsok') {
	$File = $tmp_path."/products_".$key; 
	$Handle = fopen($File, 'w');
	$Data =  time()-(3600*$timezone); 	//time();
	fwrite($Handle, date('Y-m-d H:i:s', $Data)); 
	fclose($Handle); 	 
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//CUSTOMERS BY ORDERS
if ($action == 'customers') {
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.stripe.com/v1/charges',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$apikey
	),
	));

	$response = curl_exec($curl); 



	curl_close($curl);


	$alldata=json_decode($response);
	//print_r($alldata);


	echo "ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΟΝΟΜΑ;ΕΠΩΝΥΜΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;PO BOX;<br>\n";
	


	$orderscount=count($alldata->data)-1;

	//echo '##'.$orderscount.'##';

	for ($x = 0; $x <= $orderscount; $x+=1) {


		
		//
		//
		//
		//
		//



		$customerid=$alldata->data[$x]->customer;





		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.stripe.com/v1/customers/'.$customerid,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
		'Authorization: Bearer '.$apikey
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		//echo $response;


		$alldata2=json_decode($response);
		//print_r($alldata2);





		$created=gmdate('d/m/Y H:i:s', $alldata2->created);
		
		$firstname=explode(' ', trim($alldata2->name))[0];
		$lastname=explode(' ', trim($alldata2->name))[1];
		$address1=$alldata2->address->line1.' '.$alldata2->address->line2;
		$postcode=$alldata2->address->postal_code;
		$state=$alldata2->address->state;
		$city=$alldata2->address->city;
		$phonenumber=$alldata2->metadata->phone_number;
		$memberid=$alldata2->metadata->kjb_member_id;
		$email=$alldata2->email;
		
		if ((!$firstname) && (!$lastname)) {
			$firstname=explode('@', trim($email))[0];
			$lastname=explode('@', trim($email))[0];
		}
		

		$rowtext=$alldata2->id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";		
		
		$rowtext=str_ireplace("&amp;","&",$rowtext);
		$rowtext=str_ireplace("&quot;","'",$rowtext);
		$rowtext=str_ireplace("&#039;","'",$rowtext);
		$rowtext=str_ireplace("'","`",$rowtext);
		echo $rowtext;	


		
















		
		//
		//
		//
		//
		//













	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	




	
	

	


}








if ($action == 'products') {


	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ ΠΩΛΗΣΗΣ;ΤΙΜΗ ΑΓΟΡΑΣ;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ;ΒΑΡΟΣ;ΕΝΕΡΓΟ;<br>\n";
	
	
	
	
	echo $product_code_prefix. $arr_sku[$x].';'.$name1.' '.$addons_des.';Brand:'.$manuname.';'.$taxrate.';'.$price."|8:".$pricen.";;".$arr_quantity[$x].";".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";;".$weight.";1;<br>\n";	
	
	
	
	
	
}




//ORDERS


if ($action == 'orders') {

	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.stripe.com/v1/charges',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$apikey
	),
	));

	$response = curl_exec($curl); 



	curl_close($curl);


	$alldata=json_decode($response);
	//print_r($alldata);


	echo "ORDER ID;CUSTOMER ID;SHIPPING COST;PAYMENT COST;DISCOUNT;DATE;NOTE;USER;VOUCHER;STATUS;SHIPPING CUSTOMER ID;PAYMENT METHOD;SHIPPING METHOD;DOCUMENT;<br>\n";




	$orderscount=count($alldata->data)-1;

	//echo '##'.$orderscount.'##';

	for ($x = 0; $x <= $orderscount; $x+=1) {


		$notes=
		//'Παράδοση μέχρι'.date('d/m/Y H:i:s', strtotime($alldata->data[$x]->shipping_deadline)) .' '.
		$alldata->data[$x]->shipping_type_label.' '. 
		$alldata->data[$x]->currency.' '. 
		$alldata->data[$x]->status.' '. 
		$alldata->data[$x]->metadata->order_id
		//$alldata->data[$x]->shipping_zone_label.' Προμήθεια:'.$alldata->data[$x]->total_commission.' '.
		//$alldata->data[$x]->order_additional_fields[0]->value
		//.' '.$alldata->data[$x]->shipping_tracking_url
		;


		$shipping_cost='';//$alldata->data[$x]->shipping_price+($alldata->data[$x]->shipping_price*24/100);


		echo $alldata->data[$x]->taxes[0]->amount;

		$rowtext=$alldata->data[$x]->id.';'.
		$alldata->data[$x]->customer.';'.
		$shipping_cost.';0;0;'.
		gmdate('d/m/Y H:i:s', $alldata->data[$x]->created).';'.$notes.';;'.
		$alldata->data[$x]->shipping.';;;'.
		$alldata->data[$x]->payment_method_details->card->brand.';'.
		';'.';'  
		;






		if (($alldata->data[$x]->status=='succeeded') && (!$alldata->data[$x]->metadata->order_id=='invoiced') )  {
			echo $rowtext." <br>\n";
		}



	}

	
	
	
}





if ($action == 'order') {
	

	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.stripe.com/v1/charges/'.$orderid,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'GET',
	CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$apikey
	),
	));

	$response = curl_exec($curl); 



	curl_close($curl);


	$alldata=json_decode($response);
	//print_r($alldata);


	echo "PRODUCT ID;DESCRIPTION 1;DESCRIPTION 2;DESCRIPTION 3;QUANTITY;MEASUREMENT UNIT;PRICE;TAX;DISCOUNT;START DATE;END DATE;POSITION;ORDER ID;<br>\n";
	


	$rowtext=
	'PRO'.';'.
	$alldata->calculated_statement_descriptor.';'.
	';'.
	';'.
	'1;'.
	'ΤΕΜΑΧΙΑ;'.		
	($alldata->amount/100).';'.
	'24;'.
	'0;';



	echo $rowtext." <br>\n";




	

}





 


if ($action == 'confirmorder') {


	//$data = mysqli_query($link,"update ".$dbprefix."order set order_status_id=18 where order_id in (".$orderid.")") or die(mysqli_error($link));
	//echo '23';
	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.stripe.com/v1/charges/'.$orderid.'?metadata%5Border_id%5D=invoiced',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$apikey
	),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	//echo $response;



	echo $hmera;
}






if ($action == 'updatestock')  {
}



if ($action == 'cancelorder') {

	//$data = mysqli_query($link,"update ".$dbprefix."order set order_status_id=18 where order_id in (".$orderid.")") or die(mysqli_error($link));
	//echo '23';
	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.stripe.com/v1/charges/'.$orderid.'?metadata%5Border_id%5D=submitted',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_HTTPHEADER => array(
	'Authorization: Bearer '.$apikey
	),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	//echo $response;



	echo $hmera;
}



//header("Location: $goto?expdate=$nextduedate");








?> 					