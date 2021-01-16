<?php
//PS
/*------------------------------------------------------------------------
		# EMDI - SKROUTZ BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2021 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');

require 'config.php';

$logfile = 'emdibridge.txt';
$offset= '';
$host = DB_HOSTNAME;
$user = DB_USERNAME;
$password = DB_PASSWORD;
$db = DB_DATABASE;
$dbprefix = DB_PREFIX;
$product_code_prefix='';
$customer_invoice_code_prefix='IC';
$customer_code_prefix='AC';
$lang_code='el-gr';
$tmp_path = DIR_SYSTEM.'tmp';
$timezone=$config->offset; 
$passkey='';
$relatedchar='^';
$addonid='PRO';
$skroutzcat='SKROUTZ';
$avail_id=7;
$notavail_id=6;
$apikey='';

//////////////
$measurement='ΤΕΜΑΧΙΑ';
$measurementaddon='ΠΡΟΣΘΕΤΑ';

//$vat_field='ΑΦΜ';
//$tax_office_field='ΔΟΥ';
$maintax=24;
// Connects to your Database
$link=mysqli_connect("$host", $user, $password) or die(mysqli_error($link));
mysqli_select_db($link,"$db") or die(mysqli_error($link));
mysqli_set_charset($link,'utf8'); 

$photourl=HTTP_SERVER.'image/';	
$produrl=HTTP_SERVER.'index.php?route=product/product&product_id=';	
$customerid=$_REQUEST['customerid'];
$productid=$_REQUEST['productid'];
$stock=$_REQUEST['stock'];
$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
//$key=$_REQUEST['key'];       // PRODUCT CODE

//if (!($key==$passkey)) { exit; }
$key='skroutz';
///////////////////////////////////

if (!is_dir($tmp_path)) {
	mkdir($tmp_path);
}


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
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 19); 
		fclose($handle); 
	}
	
	$query="
		
				
		SELECT * FROM `sbz_skroutz_docs`
		
		-- DATE_ADD(created_at, INTERVAL 1 HOUR)>
		where created_at>'". $lastdate."'		
		

		
		";
	
	/////////////
	$data = mysqli_query($link,$query) or die(mysqli_error($link));;
	/////////////
	
	
	
	echo "ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΟΝΟΜΑ;ΕΠΩΝΥΜΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;PO BOX;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['customer_id'];  	 	
		
		$firstname= $alldata['customer_first_name']; 
		$lastname=$alldata['customer_last_name'];  	 	
		$address1=$alldata['customer_address_street_name'].' '.$alldata['customer_address_street_number'];  	 				
		$postcode=$alldata['customer_address_street_zip'];  	 
		
		$state=$alldata['customer_address_street_region'];  	 	
		$city=$alldata['customer_address_street_city'];  	 	
		
		
		$rowtext=$customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";		
		
		$rowtext=str_ireplace("&amp;","&",$rowtext);
		$rowtext=str_ireplace("&quot;","'",$rowtext);
		$rowtext=str_ireplace("&#039;","'",$rowtext);
		$rowtext=str_ireplace("'","`",$rowtext);
		echo $rowtext;	
		
		
		//}
	}
	
	
	
	
	
	
	
	
	$query="
		
				
SELECT * FROM `sbz_skroutz_docs`
where invoice=1 
-- and state='open' 

		
		";
	
	/////////////
	$data = mysqli_query($link,$query) or die(mysqli_error($link));;
	/////////////
	
	

	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['customer_id'];  	 	
		$firstname= ''; 
		$lastname=''; 
		$companyname= $alldata['invoice_company']; 
		$epaggelma= $alldata['invoice_profession']; 
		$afm= $alldata['invoice_vat_number']; 
		$doy= $alldata['invoice_doy']; 
		$address1=$alldata['invoice_street_name'].' '.$alldata['invoice_street_number'];  	 				
		$postcode=$alldata['invoice_street_zip'];  	 
		$state=$alldata['invoice_region'];  	 	
		$city=$alldata['invoice_city'];  	 	
		
		
		$rowtext=$customer_invoice_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";		
		
		$rowtext=str_ireplace("&amp;","&",$rowtext);
		$rowtext=str_ireplace("&quot;","'",$rowtext);
		$rowtext=str_ireplace("&#039;","'",$rowtext);
		$rowtext=str_ireplace("'","`",$rowtext);
		echo $rowtext;	
		
		
		//}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	mysqli_close($link);
}








if ($action == 'products') {


	$file = $tmp_path."/products_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 19); 
		fclose($handle); 
	}

	////PRODUCTS


	//---------------------------
	$query="

	SELECT 
skrl.product_name,skrl.shop_uid,skrl.quantity,skrl.size_label,skrl.size_value,skrl.unit_price,skrl.total_price,
skrl.price_includes_vat,skrd.created_at,skrl.shop_value
FROM `sbz_skroutz_lines` skrl
left join `sbz_skroutz_docs` skrd on skrd.code=skrl.code

-- DATE_ADD(created_at, INTERVAL 1 HOUR)>
where created_at>'". $lastdate."'		
		
		";
	
	
	//echo $query;
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	//---------------------------
	//date('Y-m-d H:i:s', $lastdate)
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ ΠΩΛΗΣΗΣ;ΤΙΜΗ ΑΓΟΡΑΣ;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ;ΒΑΡΟΣ;ΕΝΕΡΓΟ;<br>\n";
	
	
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['shop_uid'];  	 	
		$name= $alldata['product_name'];
		
		$taxrate=$maintax;
		
		$quantity=$alldata['quantity'];
		$price=$alldata['unit_price'];
		
		$size_label=$alldata['size_label'];
		$size_value=$alldata['size_value'];
		$shop_value=$alldata['shop_value'];
		
		$price_includes_vat=$alldata['price_includes_vat'];
		
		if ($price_includes_vat=='1') {
			//$price	= ($price*100)/(100+$maintax);
		}
		$price=number_format($price, 2, ',', '');
		
		
		$variant='';
		if ($size_label.$size_value) {
			$variant=$size_label.':'.$size_value.'\n';
		}
		
		
		$id_variant='';
		//if ($shop_value) {$id_variant='.'.$shop_value;} 
		
		echo $product_code_prefix.get_model($id).$id_variant.';'.$name.';'.$variant.';'.$taxrate.';'.$price.';;'.$quantity.";".$measurement.";".$skroutzcat.";;;;;;<br>\n";
		
		
		
		
		
		
		
		
		
		
	}
	////
	
	
	
	
	
}




//ORDERS


if ($action == 'orders') {



	$data = mysqli_query($link,"
SELECT * FROM `sbz_skroutz_docs`
where state='open'
") or die(mysqli_error($link)); //


	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;ΧΡΗΣΤΗΣ;VOUCHER;ΚΑΤΑΣΤΑΣΗ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ ΑΠΟΣΤΟΛΗΣ;ΤΡΟΠΟΣ ΠΛΗΡΩΜΗΣ;ΤΡΟΠΟΣ ΑΠΟΣΤΟΛΗΣ;ΠΑΡΑΣΤΑΤΙΚΟ;<br>\n";

	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['code'];  	 	
		$userid= $alldata['customer_id']; 
		$hmera=$alldata['created_at'] ;
		$shipping=   str_replace('€','',       0);
		$comment=$alldata['comment'].''.$alldata['courier'];
		$voucher=$alldata['courier_tracking'];
		$invoice=$alldata['invoice'];
		
		

		//$customer_invoice_code_prefix='IC';
		//$customer_code_prefix='AC';

		if ($invoice=='1') {
			$deliverycust=$customer_code_prefix.$userid;
			$maincust=$customer_invoice_code_prefix.$userid;	 
		} else {
			$deliverycust='';
			$maincust=$customer_code_prefix.$userid;
		}
		$rowtext= $id.';'.$maincust.";0;0;0;".$hmera.";".$comment.";;".$voucher.";;".$deliverycust.";ΚΑΡΤΑ;COURIER;;";

		
		$rowtext = str_ireplace("&amp;", "&", $rowtext);
		$rowtext = str_ireplace("&quot;", "'", $rowtext);
		$rowtext = str_ireplace("&#039;", "'", $rowtext);
		$rowtext = str_ireplace("\n", "", $rowtext);
		echo $rowtext."<br>\n";
		
		
		

	}
}





if ($action == 'order') {
	////ORDER
	
	$data = mysqli_query($link,"
		
SELECT * FROM `sbz_skroutz_lines` where code='".$orderid."'") or die(mysqli_error($link)); 
	
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['product_name']; 
		$product_id = $alldata['shop_uid']; 
		$product_quantity = $alldata['quantity']; 
		$price_includes_vat= $alldata['price_includes_vat']; 
		//$optionsnametitle = $alldata['optionsnametitle']; 
		//$optionsdescr = $alldata['optionsdescr']; 
		$optionsnametitle = explode(',', $alldata['optionsnametitle']);
		$optionsdescr = explode(',', $alldata['optionsdescr']);
		
		
		$amount=$alldata['total_price']/$product_quantity;
		
		if ($price_includes_vat=='1') {
			//$amount	= ($amount*100)/(100+$maintax);
		}
		$amount=number_format($amount, 2, ',', '');
		
		$size_label=$alldata['size_label'];
		$size_value=$alldata['size_value'];
		$shop_value=$alldata['shop_value'];
		
		
		$variant='';
		if ($size_label.$size_value) {
			$variant=$size_label.':'.$size_value.'\n';
		}
		
		
		$discount=0;		
		$taxrate=$maintax;		
		$monada = $measurement; 

		$id_variant='';
		//if ($shop_value) {$id_variant='.'.$shop_value;} 
		
		echo $product_code_prefix.get_model($product_id).$id_variant.';'.$description.' '.$variant.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		
	}
	

}








if ($action == 'confirmorder') {

	$splits = explode(',', $orderid);

	foreach ( $splits as $key ){
		
		
		
		
		//echo "https://api.skroutz.gr/merchants/ecommerce/orders/$key/accept"."\n";
		//
		


		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.skroutz.gr/merchants/ecommerce/orders/'.$key.'/accept',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_HTTPHEADER => array(
		'Authorization: Bearer '.$apikey,
		'Accept: application/vnd.skroutz+json; version=3.0'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;

		//
		//
		
		
		
		
		
		
		
		
		
		
		
		
		
	}

	echo $hmera;
	
}







if ($action == 'cancelorder') {

	$splits = explode(',', $orderid);

	foreach ( $splits as $key ){
		//echo "https://api.skroutz.gr/merchants/ecommerce/orders/$key/reject"."\n";
		//
		


		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.skroutz.gr/merchants/ecommerce/orders/'.$key.'/reject',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_HTTPHEADER => array(
		'Authorization: Bearer '.$apikey,
		'Accept: application/vnd.skroutz+json; version=3.0'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;

		//
		//
		
		
	}	
	echo $hmera;

}









// GET MODEL FROM OPENCART
function get_model($id) {


	global $link;

	$queryin="
		
				
SELECT model,ean  FROM `oc_product` 
where product_id=$id
		
		";
	
	/////////////
	$datain = mysqli_query($link,$queryin) or die(mysqli_error($link));;
	/////////////
	
	
	while($alldatain = mysqli_fetch_array( $datain ))
	{
		$model=$alldatain['model'];  
		break;			
		
	}
	
	return 	$model;


}



?> 					
