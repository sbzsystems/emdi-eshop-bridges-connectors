<?php


error_reporting(0);
/*------------------------------------------------------------------------
		# EMDI - WordPress Woo BRIDGE by SBZ systems - Solon Zenetzis - version 1.2
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2018 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');
//error_reporting(0);

require 'wp-config.php';

//$test=unserialize('a:8:{s:3:"sku";s:0:"";s:8:"products";a:3:{i:1;a:3:{s:6:"option";s:3:"ttt";s:5:"price";s:3:"111";s:9:"saleprice";s:0:"";}i:2;a:3:{s:6:"option";s:3:"eee";s:5:"price";s:3:"222";s:9:"saleprice";s:0:"";}i:3;a:3:{s:6:"option";s:0:"";s:5:"price";s:0:"";s:9:"saleprice";s:0:"";}}s:11:"description";s:0:"";s:8:"shiprate";s:1:"F";s:8:"featured";s:2:"no";s:4:"sale";s:2:"no";s:10:"cart_radio";s:1:"0";s:6:"optset";s:0:"";}');
//print_r($test);

//echo $test[products][1][price];



$logfile = 'emdibridge.log';
$offset= '';
$host = DB_HOST;
$user = DB_USER;
$password = DB_PASSWORD;
$db = DB_NAME;
$dbprefix = $table_prefix;
$product_code_prefix='';
$customer_code_prefix='IC';
$lang_code='el';
$tmp_path = ABSPATH . 'tmp';
$timezone=$config->offset; 
$passkey='';

$onetime_customer_code_prefix='AC';
$lang_id=1;
$store_id=0;
$relatedchar='^';
$addonid='PRO';

//////////////
$measurement='ΤΕΜΑΧΙΑ';
$measurementaddon='ΠΡΟΣΘΕΤΑ';
$size_field='ΜΕΓΕΘΟΣ';
$color_field='ΧΡΩΜΑ';

//$vat_field='ΑΦΜ';
//$tax_office_field='ΔΟΥ';
$maintax=24;

// Connects to your Database
$link=mysqli_connect("$host", $user, $password) or die(mysqli_error($link));
mysqli_select_db($link,"$db") or die(mysqli_error($link));
mysqli_set_charset($link,'utf8'); 

//$photourl=HTTP_IMAGE;	
//$produrl=HTTP_SERVER.'index.php?route=product/product&product_id=';	
$customerid=$_REQUEST['customerid'];
$productid=$_REQUEST['productid'];

$productid=iconv("ISO-8859-7", "UTF-8",  $productid);


$stock=$_REQUEST['stock'];
$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
$test=$_REQUEST['test'];       

if (!($key==$passkey)) { exit; }
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
	$Data = time(); 
	fwrite($Handle, $Data); 
	fclose($Handle); 	
}
if ($action == 'productsok') {
	$file = $tmp_path."/products_".$key; 
	$handle = fopen($file, 'w');
	$data = time();
	fwrite($handle, $data); 
	fclose($handle); 	
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($action == 'customers') {
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 11); 
		fclose($handle); 
	}
	//echo date('Y-m-d H:i:s', $lastdate);
	
	/////////////
	
	//members
	$query="SELECT 
		
		pst1.post_id as user_id,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_address_1') as b_address,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_address_2') as c_address,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_city') as b_city,
		
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_country'),
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_email') as email,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_first_name') as firstname,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_last_name') as lastname,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_phone') as phone,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_postcode') as b_zipcode,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_state') as b_state,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='ΑΦΜ') as afm,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='ΔΟΥ') as doy,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='Επωνυμία Επιχείρησης') as company,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='Δραστηριότητα Επιχείρησης') as epaggelma,
		
		(SELECT pop.post_date FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) as dd
		
		FROM ".$dbprefix."postmeta pst1
		where
		pst1.meta_key='_customer_user'
		and
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_customer_user')=0
		and  
		(SELECT pop.post_date FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) >'".date('Y-m-d H:i:s', $lastdate)."'
		
		
		";
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	
	
	
	
	
	
	/////////////
	
	
	
	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['user_id'];  	 	
		$firstname= $alldata['firstname']; 
		$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['b_address'];  	 	
		$tu=$alldata['c_address']; 
		
		$postcode=$alldata['postcode'];  	 
		$country=$alldata['b_country'];  	 	
		$state=$alldata['b_state'];  	 	
		$city=$alldata['b_city'];  	 	
		$phonenumber=$alldata['b_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		$companyname=$alldata['company'];  	 	
		$afm=$alldata['afm'];  	 	
		$doy=$alldata['doy'];  	 	
		//		$postcode=$alldata['date_added'];  	 	
		
		echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";
	}
	//ONE TIME CUSTOMERS
	
	$query="SELECT 
		
		pst1.post_id as user_id,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_address_1') as b_address,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_address_2') as c_address,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_city') as b_city,
		
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_country'),
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_email') as email,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_first_name') as firstname,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_last_name') as lastname,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_phone') as phone,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_postcode') as b_zipcode,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_billing_state') as b_state,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='ΑΦΜ') as afm,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='ΔΟΥ') as doy,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='Επωνυμία Επιχείρησης') as company,
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='Δραστηριότητα Επιχείρησης') as epaggelma,
		
		(SELECT pop.post_date FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) as dd
		
		FROM ".$dbprefix."postmeta pst1
		where
		pst1.meta_key='_customer_user'
		and
		(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_customer_user')>0
		and  
		(SELECT pop.post_date FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) >'".date('Y-m-d H:i:s', $lastdate)."'
		
		
		";
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['user_id'];  	 	
		$firstname= $alldata['firstname']; 
		$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['b_address'];  	 	
		$tu=$alldata['c_address']; 
		
		$postcode=$alldata['postcode'];  	 
		$country=$alldata['b_country'];  	 	
		$state=$alldata['b_state'];  	 	
		$city=$alldata['b_city'];  	 	
		$phonenumber=$alldata['b_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		$companyname=$alldata['company'];  	 	
		$afm=$alldata['afm'];  	 	
		$doy=$alldata['doy'];  	 	
		//		$postcode=$alldata['date_added'];  	 	
		
		echo $onetime_customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";
	}
	
	
	
}




if ($action == 'products') {
	
	
	$file = $tmp_path."/products_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 11); 
		fclose($handle); 
	}
	
	////PRODUCTS
	
	
	$query= "
		
		SELECT 
		
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_sku' and pom.post_id=posts_.ID limit 1) as product_code,
		posts_.post_title as product,
		'".$maintax."' as rate_value,
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_regular_price' and pom.post_id=posts_.ID limit 1) as price,
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_sale_price' and pom.post_id=posts_.ID limit 1) as priced,
		
		/*product_cat: category */
		(SELECT 
		group_concat(trs.name)
		
		FROM ".$dbprefix."term_taxonomy tet,".$dbprefix."terms trs,".$dbprefix."term_relationships tre
		
		where tet.taxonomy='product_cat'
		and tre.term_taxonomy_id=tet.term_taxonomy_id
		and trs.term_id=tet.term_id
		and object_id=posts_.ID) as category,
		
		
		
		
		
		
		(SELECT ppst.guid FROM ".$dbprefix."posts ppst where ppst.post_type='attachment' and ppst.post_parent=posts_.ID and ppst.post_mime_type='image/jpeg' limit 1) as image,
		
		posts_.guid as product_url
		
		FROM ".$dbprefix."posts posts_
		
		where post_type like 'product'
		and post_status='publish'
		
		
		
		and (post_date>'".date('Y-m-d H:i:s', $lastdate)."' or post_modified>'".date('Y-m-d H:i:s', $lastdate)."')
		
		
		
		";
	
	
	
	
	//---------------------------
	//echo $query;
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	
	
	
	
	//---------------------------
	//date('Y-m-d H:i:s', $lastdate)
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['product_code'];  	 	
		$name1= $alldata['product']; 
		$taxrate=$alldata['rate_value'];
		$taxrate=number_format($taxrate, 2, ',', '');	
		$price=$alldata['price'];
		$price=number_format($price, 2, ',', '');
		$category= $alldata['category']; 
		$taxrate=$maintax;
		
		$priced=$alldata['priced'];
		$priced=number_format($priced, 2, ',', '');
		
		if ($priced) { $price=$priced; }
		
		if ($id) {
			echo $product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$measurement.";".$category.";".$alldata['image'].";".$alldata['product_url'].";<br>\n";		
		}
	}
	
	// CHILD
	$query= "
		
		SELECT 
		
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_sku' and pom.post_id=posts_.ID limit 1) as product_code,
		
		(select pom.post_title from ".$dbprefix."posts pom where pom.id=posts_.post_parent limit 1) as product,
		
		'".$maintax."' as rate_value,
		
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_regular_price' and pom.post_id=posts_.ID limit 1) as price,
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_sale_price' and pom.post_id=posts_.ID limit 1) as priced,
		
		/*product_cat: category */
		(SELECT 
		group_concat(trs.name)
		
		FROM ".$dbprefix."term_taxonomy tet,".$dbprefix."terms trs,".$dbprefix."term_relationships tre
		
		where tet.taxonomy='product_cat'
		and tre.term_taxonomy_id=tet.term_taxonomy_id
		and trs.term_id=tet.term_id
		and object_id=posts_.post_parent) as category,
		
		/*pa_χρώμα: color */
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='attribute_pa_%cf%87%cf%81%cf%8e%ce%bc%ce%b1' and pom.post_id=posts_.ID limit 1) as color,
		
		/*pa_νούμερο: size */
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='attribute_pa_%ce%bd%ce%bf%cf%8d%ce%bc%ce%b5%cf%81%ce%bf' and pom.post_id=posts_.ID limit 1) as size,
		
		
		(SELECT 
		ppst.guid FROM ".$dbprefix."posts ppst 
		where ppst.post_type='attachment' and 
		ppst.post_parent=posts_.post_parent and 
		ppst.post_mime_type='image/jpeg' limit 1) as image,
		
		
		(case when posts_.guid ='' then 
		
		(select pom.guid from ".$dbprefix."posts pom where pom.id=posts_.post_parent limit 1) 
		
		else post_parent end) as product_url
		
		
		
		FROM ".$dbprefix."posts posts_
		
		where post_type like 'product_variation'
		and post_status='publish'
		
		
		
		and (post_date>'".date('Y-m-d H:i:s', $lastdate)."' or post_modified>'".date('Y-m-d H:i:s', $lastdate)."')
		
		
		
		";
	//---------------------------
	//echo $query;
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	
	
	
	
	//---------------------------
	//date('Y-m-d H:i:s', $lastdate)
	
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['product_code'];  	 	
		$name1= $alldata['product']; 
		$taxrate=$alldata['rate_value'];
		$taxrate=number_format($taxrate, 2, ',', '');	
		$price=$alldata['price'];
		$price=number_format($price, 2, ',', '');
		$category= $alldata['category']; 
		$size= urldecode($alldata['size']); 
		$color= urldecode($alldata['color']); 
		$taxrate=$maintax;
		
		$priced=$alldata['priced'];
		$priced=number_format($priced, 2, ',', '');
		
		if ($priced) { $price=$priced; }
		
		$options='';
		if ($color) {
			$options=$color_field.':'.$color.'\n';
		}
		if ($size) {
			$options=$options.$size_field.':'.$size.'\n';
		}
		
		
		
		
		echo $product_code_prefix.$id.';'.$name1.';'.$options.';'.$taxrate.';'.$price.";;;".$measurement.";".$category.";".$alldata['image'].";".$alldata['product_url'].";<br>\n";		
	}
	
	////
	
	
	
	
	
}









































if ($action == 'orders') {
	
	
	if ($test) {
		
		$query="SELECT 
			
			pst1.post_id as user_id,
			pst1.post_id as order_id,
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_order_shipping') as shipping,
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_payment_method') as paymentcost,
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_cart_discount') as discount,
			(SELECT pop.post_excerpt FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) as comment,
			(SELECT pop.post_date FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) as timestamp,
			
			
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_customer_user') as onetime
			
			FROM ".$dbprefix."postmeta pst1
			where
			pst1.meta_key='_customer_user'
			
			and
			
			(SELECT pop.post_status FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) not in ('wc-cancelled','wc-failed','trash','wc-')
			
			order by order_id desc
			limit 100
			
			";
		
		
	} else {
		
		$query="SELECT 
			
			pst1.post_id as user_id,
			pst1.post_id as order_id,
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_order_shipping') as shipping,
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_payment_method') as paymentcost,
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_cart_discount') as discount,
			(SELECT pop.post_excerpt FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) as comment,
			(SELECT pop.post_date FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) as timestamp,
			
			
			(select pst2.meta_value from ".$dbprefix."postmeta pst2 where pst1.post_id=pst2.post_id and pst2.meta_key='_customer_user') as onetime
			
			FROM ".$dbprefix."postmeta pst1
			where
			pst1.meta_key='_customer_user'
			
			and
			
			(SELECT pop.post_status FROM ".$dbprefix."posts pop where pop.ID=pst1.post_id) not in ('wc-completed','wc-cancelled','wc-failed','trash','wc-')
			
			
			
			
			";
		
	}
	
	//echo $query;
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	//or pst1.post_id=11852
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['order_id'];  	 	
		$userid= $alldata['user_id']; 
		$onetime= $alldata['onetime']; 
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		$shipping=   str_replace('€','',       $alldata['shipping']); 
		//$shipping= round(   ($shipping*$maintax/100)+$shipping ,2);
		//$shipping= $shipping*100/123;
		$shipping=   str_replace('.',',',       $shipping); 
		
		if (!$shipping) {
			$shipping=0;
		}
		
		
		
		//$paymentcost=   str_replace('€','',       $alldata['paymentcost']); 
		//$paymentcost= round(   ($paymentcost*$maintax/100)+$paymentcost ,2);
		//$paymentcost= $paymentcost*100/123;
		//$paymentcost=   str_replace('.',',',       $paymentcost); 
		
		
		if (!$paymentcost) {
			$paymentcost=0;
		}
		
		
		
		$discount=   str_replace('€','',       -$alldata['discount']); 
		//$discount= round(   ($discount*$maintax/100)+$discount ,2);
		//$discount= $discount*100/(100+$maintax);
		$discount=   str_replace('.',',',       $discount); 
		
		if (!$discount) {
			$discount=0;
		}  
		
		$comment=$alldata['comment'] ;
		
		$comment=   str_replace("'",'`',$comment); 
		$comment=preg_replace( "/\r|\n/", "", $comment );
		
		
		if ($onetime<>0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";".$paymentcost.";".$discount.";".$hmera.";".$comment.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$paymentcost.";".$discount.";".$hmera.";".$comment.";<br>\n";
		}
		
	}
}


























if ($action == 'order') {
	////order
	
	
	
	$query="
		
		select 
		
		order_id,
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_line_total') 
		+
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_line_tax') as price,
		
		
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_qty') as amount,
		
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_fee_amount') as fee,		
		
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_sku' and pom.post_id=
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_product_id')) as product_code,
		
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_sku' and pom.post_id=
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_variation_id')) as variation_code,
		
		(select pom.post_title from ".$dbprefix."posts pom where pom.id=
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_product_id')) as product,
		
		(select pom.post_title from ".$dbprefix."posts pom where pom.id=
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_variation_id')) as variation,
		
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_product_id') as product_id,
		
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_variation_id') as variation_id,
		
		
		
		
		
		
		/*product_cat: category */
		(SELECT 
		group_concat(trs.name)
		
		FROM ".$dbprefix."term_taxonomy tet,".$dbprefix."terms trs,".$dbprefix."term_relationships tre
		
		where tet.taxonomy='product_cat'
		and tre.term_taxonomy_id=tet.term_taxonomy_id
		and trs.term_id=tet.term_id
		and tre.object_id=
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_product_id')
		) as category,
		
		
		
		
		
		
		
		'0' as discount,
		'".$maintax."' as rate_value
		
		
		,
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_tmcartepo_data') as analysis
		
		
		
		from ".$dbprefix."woocommerce_order_items ordi
		
		
		where ordi.order_item_type='line_item'
		and
		(select pom.meta_value from ".$dbprefix."postmeta pom where pom.meta_key='_sku' and pom.post_id=
		(select woim.meta_value from ".$dbprefix."woocommerce_order_itemmeta woim where woim.order_item_id=ordi.order_item_id and woim.meta_key='_product_id'))<>''
		
		
		
		and ordi.order_id=".$orderid;
	
	//echo $query;
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['product']; 
		$product_id = $alldata['variation_code']; 
		
		if (!$product_id) {
			$product_id = $alldata['product_code']; 
		}
		
		
		
		
		$product_quantity = $alldata['amount']; 
		$amount=number_format($alldata['price']/$product_quantity, 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;		
		
		$taxrate=number_format($alldata['rate_value'], 2, ',', '');	
		
		$monada = $measurement; 
		$product_attribute = $alldata['extra']; 
		
		$taxrate=$maintax;
		
		$category=$alldata['category']; 
		
		
		
		
		
		$analysis = unserialize($alldata['analysis']); 
		
		
		
		
		//$analysis= print_r($analysis);
		
		
		
		
		//echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		echo 'EID;'.$category.' '.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		
		
		
		foreach ($analysis as $type) {
			
			$proprice=number_format($type[price], 2, ',', '');
			//$proprice=0;
			//echo $product_code_prefix.$product_id.';'.$type[value].';;;'.$type[quantity].";ΠΡΟΣΘΕΤΟ;0;0;0;<br>\n";
			echo 'PRO;'.$type[value].';;;'.$type[quantity]*$product_quantity.";".$measurementaddon.";".$proprice.";".$taxrate.";0;<br>\n";
			
		}
		
		
		/*		
				$i=(count($analysis)>0);
				
				if  ($i>0) {
				
				
				for ($ii = 0; $ii < $i; $ii++) {  
				
				echo $product_code_prefix.$product_id.';'.$analysis[$ii][value].';;;'.$analysis[$ii][quantity].";ΠΡΟΣΘΕΤΑ'.';0;0;0;<br>\n";
				
				}
				
				
				
				}
				
				
			*/	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}
	
	
}





















































if ($action == 'confirmorder') {
	//('wc-completed','wc-cancelled
	$data = mysqli_query($link,"update ".$dbprefix."posts set post_status='wc-completed' where ID in (".$orderid.")") or die(mysqli_error($link));
	
	echo $hmera;
}



if ($action == 'updatestock') {
	//echo "update ".$dbprefix."product set quantity=".$stock."  where product_id='".substr($productid,strlen($product_code_prefix))."'";
	
	//find meta
	$query="select pom.post_id from ".$dbprefix."postmeta pom where pom.meta_key='_sku' and pom.meta_value='".substr($productid,strlen($product_code_prefix))."'";
	//file_put_contents($logfile, $query."\n", FILE_APPEND | LOCK_EX);
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	//file_put_contents($logfile, '#'.$post_id.'#'.$stock."#\n", FILE_APPEND | LOCK_EX);		
	while($alldata = mysqli_fetch_array( $data ))
	{
		$post_id = $alldata['post_id'];   	 
	}
	//file_put_contents($logfile, "#".$post_id."#\n", FILE_APPEND | LOCK_EX);
	
	
	
	
	//update stock
	$query="update ".$dbprefix."postmeta pos set pos.meta_value='".$stock."' where pos.meta_key='_stock' and pos.post_id=".$post_id;
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	if ($stock) {
		$query="update ".$dbprefix."postmeta pos set pos.meta_value='instock' where pos.meta_key='_stock_status' and pos.post_id=".$post_id;
	} else {
		$query="update ".$dbprefix."postmeta pos set pos.meta_value='outofstock' where pos.meta_key='_stock_status' and pos.post_id=".$post_id;
	}		
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
}



if ($action == 'cancelorder') {
	
	//('wc-completed','wc-cancelled
	$data = mysqli_query($link,"update ".$dbprefix."posts set post_status='wc-cancelled' where ID in (".$orderid.")") or die(mysqli_error($link));
	
	echo $hmera;
	
}



//header("Location: $goto?expdate=$nextduedate");




















if ($action == 'redirect') {
	
	//customer_code_prefix
	
	
	// EDIT PRODUCT
	if ($productid) {
		$data = mysqli_query($link,"
			SELECT * FROM ".$dbprefix."product WHERE model = '".$productid."'
			") or die(mysqli_error($link));
		
		//echo mysqli_num_rows($data);
		
		if (mysqli_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysqli_fetch_array( $data ))
			{
				$id=$alldata['product_id'];  	 	
				break;		
			}	
			
			session_start();
			header('Location: '."admin/index.php?route=catalog/product/update&token=".$_SESSION['token']."&product_id=".$id);
		}
	}
	
	// EDIT CUSTOMER
	if ($customerid) {
		//customer_code_prefix
		$customerid=str_replace($customer_code_prefix,'', $customerid); 
		session_start();
		header('Location: '."admin/index.php?route=sale/customer/update&token=".$_SESSION['token']."&customer_id=".$customerid);
		
	}
	
	
	// EDIT ORDER
	if ($orderid) {
		$orderid=str_replace($relatedchar,'', $orderid); 
		session_start();
		header('Location: '."admin/index.php?route=sale/order/info&token=".$_SESSION['token']."&order_id=".$orderid);
		
	}
	
	
	
	
	
	
}


















if ($action == 'uploadproduct') {
	
	
	
	
	///
	//FIX PRODUCT_ID FROM ENCODING
	/*
			for($i=0, $len=strlen($productid); $i<$len; $i+=4){
			$productidf=$productidf. base64_decode( substr($productid, $i, 4) );
			}
		$productid=$productidf;*/
	///
	
	//$len=$_REQUEST['len'];
	$pieces = explode("|", $productid);
	
	//$productid = substr(base_enc($pieces[1]),0,$pieces[0]); 
	//$productid2 = substr(base_enc($pieces[1]),0,$pieces[0]); 
	$productid = trim(base_enc($pieces[1]));
	//$productid = substr(base64_decode($productid),0,$len); 
	
	
	
	
	$title=base_enc($_REQUEST['title']);
	$descr=base_enc($_REQUEST['descr']);    
	
	
	$price=$_REQUEST['price'];
	$cat=$_REQUEST['cat']+100000;
	$subcat=$_REQUEST['subcat'];
	$tax=$_REQUEST['tax'];
	
	$cattitle=trim(base_enc($_REQUEST['cattitle']));      
	$subcattitle=trim(base_enc($_REQUEST['subcattitle']));      
	
	
	
	$logtext=$pieces[0].'|'.$productid.'|'.$title.'|'.$descr.'|'.$price.'|'.$cat.'|'.$subcat.'|'.$tax.'|'.$cattitle.'|'.$subcattitle."\n";
	file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
	
	//
	//CHECK IF TAX EXISTS ELSE ADD
	$data = mysqli_query($link,"
		select * from ".$dbprefix."tax_rule as tru
		left join ".$dbprefix."tax_rate as tra on tru.tax_rate_id=tra.tax_rate_id
		left join ".$dbprefix."tax_class as tcl on  tru.tax_class_id=tcl.tax_class_id
		
		where title='EMDI $tax'
		
		") or die(mysqli_error($link));
	
	
	
	
	
	
	
	
	if (mysqli_num_rows($data)==0) {
		
		//ADD DEFAULT EMDI TAX CLASS IF DOESN'T EXIST
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."tax_class (tax_class_id, title, description, date_added, date_modified) 
			VALUES (NULL, 'EMDI $tax', 'EMDI $tax', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error($link));			
		
		
		//GET CLASS ID
		$data = mysqli_query($link,"SELECT LAST_INSERT_ID() as id") or die(mysqli_error($link));					
		while($alldata = mysqli_fetch_array( $data ))
		{
			$classid=$alldata['id'];  	 	
			break;		
		}	
		
		//ADD TAX	
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."tax_rate (tax_rate_id, geo_zone_id, name, rate, type, date_added, date_modified) 
			VALUES (NULL, '0', '$tax%', '$tax', 'P', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error($link));			
		
		
		//GET TAX ID
		$data = mysqli_query($link,"SELECT LAST_INSERT_ID() as id") or die(mysqli_error($link));					
		while($alldata = mysqli_fetch_array( $data ))
		{
			$taxid=$alldata['id'];  	 	
			break;		
		}	
		
		//ADD RULE
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."tax_rule (tax_rule_id, tax_class_id, tax_rate_id, based, priority) 
			VALUES (NULL, '$classid', '$taxid', 'payment', '1');
			") or die(mysqli_error($link));			
		
		
		
		
		
	} else {
		//GET TAX CLASS IF DOESN'T EXIST
		while($alldata = mysqli_fetch_array( $data ))
		{
			$classid=$alldata['tax_class_id'];  	 	
			break;		
		}	
	}
	//
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// CREATE CATEGORY IF DOES NOT EXIST
	$data = mysqli_query($link,"
		SELECT * FROM ".$dbprefix."category WHERE category_id=$cat
		") or die(mysqli_error($link));
	if (mysqli_num_rows($data)==0) {
		
		
		
		
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
			VALUES 
			('$cat', NULL, '0', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error($link));			
		
		//ADD CATEGORY DESCRIPTION
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
			VALUES ('$cat', '$lang_id', '$cattitle', '', '', '');	
			") or die(mysqli_error($link));			
		
		//ADD CATEGORY STORE
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
			VALUES ('$cat', '$store_id');
			") or die(mysqli_error($link));			
		
		
		//ADD CATEGORY PATH
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
			VALUES ('$cat', '$cat', '0')
			") or die(mysqli_error($link));			
		
		
		
		
		
	}
	//
	
	
	
	
	
	
	if ($subcat) {
		
		// CREATE SUBCATEGORY IF DOES NOT EXIST
		$data = mysqli_query($link,"
			SELECT * FROM ".$dbprefix."category WHERE category_id=$subcat
			") or die(mysqli_error($link));
		if (mysqli_num_rows($data)==0) {
			
			
			
			
			$data = mysqli_query($link,"
				INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
				VALUES 
				('$subcat', NULL, '$cat', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
				") or die(mysqli_error($link));			
			
			//ADD SUBCATEGORY DESCRIPTION
			$data = mysqli_query($link,"
				INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
				VALUES ('$subcat', '$lang_id', '$subcattitle', '', '', '');	
				") or die(mysqli_error($link));			
			
			//ADD SUBCATEGORY STORE
			$data = mysqli_query($link,"
				INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
				VALUES ('$subcat', '$store_id');
				") or die(mysqli_error($link));			
			
			
			//ADD SUBCATEGORY CATEGORY PATH
			$data = mysqli_query($link,"
				INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
				VALUES ('$subcat', '$cat', '1')
				") or die(mysqli_error($link));			
			
			//ADD SUBCATEGORY  PATH 
			$data = mysqli_query($link,"
				INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
				VALUES ('$subcat', '$subcat', '2')
				") or die(mysqli_error($link));			
			
			
			
			
		}
		//
		
	}
	
	
	
	
	
	
	$logtext=$_FILES["file"]["name"]."\n";
	file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);	
	
	
	// UPLOAD AND REPLACE PHOTO
	$uploadfolder=getcwd().'/image/data/';
	
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	
	if ((($_FILES["file"]["type"] == "image/gif")
				|| ($_FILES["file"]["type"] == "image/jpeg")
				|| ($_FILES["file"]["type"] == "image/jpg")
				|| ($_FILES["file"]["type"] == "image/pjpeg")
				|| ($_FILES["file"]["type"] == "image/x-png")
				|| ($_FILES["file"]["type"] == "image/png"))
			//&& ($_FILES["file"]["size"] < 1000000)
			//&& in_array($extension, $allowedExts)
			) 
	{
		if ($_FILES["file"]["error"] > 0) {
			
			echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
			
		} else {
			
			move_uploaded_file($_FILES["file"]["tmp_name"],$uploadfolder.$_FILES["file"]["name"]);
			
		}
	} else {
		echo "Invalid file";
	}
	//
	
	
	
	
	
	
	
	
	
	// ADD PRODUCT 
	$data = mysqli_query($link,"
		SELECT * FROM ".$dbprefix."product WHERE model = '".$productid."'
		") or die(mysqli_error($link));
	if (mysqli_num_rows($data)==0) {
		
		//IF PRODUCT DOES NOT EXIST			
		$data = mysqli_query($link,"				
			INSERT INTO ".$dbprefix."product (product_id, model, sku, upc, ean, jan, isbn, mpn, location, quantity, 
			stock_status_id, image, manufacturer_id, shipping, price, points, tax_class_id, date_available, weight, 
			weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, status, date_added, 
			date_modified, viewed) 
			VALUES (
			NULL, '$productid', '', '', '', '', '', '', '', '0', '0', 'data/".$_FILES["file"]["name"]."', '0', '1', '$price', '0', '$classid', '10-10-2014', 
			'0.00000000', 0, '0.00000000', '0.00000000', '0.00000000',
			0, '1', '1', 0, 1, now(), '0000-00-00 00:00:00',0);				
			
			") or die(mysqli_error($link));				
		
		
		//GET PRODCUT ID
		$data = mysqli_query($link,"SELECT LAST_INSERT_ID() as id") or die(mysqli_error($link));					
		while($alldata = mysqli_fetch_array( $data ))
		{
			$id=$alldata['id'];  	 	
			break;		
		}	
		
		
		//ADD ADDITIONAL IMAGE		
		/*	
				$data = mysqli_query($link,"
				INSERT INTO ".$dbprefix."product_image (product_image_id, product_id, image, sort_order) 
				VALUES (NULL, '$id', 'data/".$_FILES["file"]["name"]."', '');
				") or die(mysqli_error($link));					
			*/
		
		
		//ADD DESCRIPTION       
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."product_description (product_id, language_id, name, 
			description, meta_description, meta_keyword, tag) 
			VALUES ('$id', '$lang_id', '$title', '$descr', '', '', '');
			") or die(mysqli_error($link));					
		
		
		//ADD CATEGORY
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."product_to_category (product_id, category_id) 
			VALUES ('$id', '$subcat');
			") or die(mysqli_error($link));					
		
		
		//ADD STORE                 
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."product_to_store (product_id, store_id) 
			VALUES ('$id', '$store_id');
			") or die(mysqli_error($link));					
		
		
		
	} else {
		//IF PRODUCT EXISTS UPDATE FIELDS
		//GET TAX CLASS IF DOESN'T EXIST
		while($alldata = mysqli_fetch_array( $data ))
		{
			$id=$alldata['product_id'];  	 	
			break;		
		}	
		/*
				//UPDATE PRODUCT NO PHOTO!!!
				$data = mysqli_query($link,"				
				update ".$dbprefix."product set price='$price', tax_class_id='$classid', date_modified=now()
				where product_id=$id
				") or die(mysqli_error($link));				
				
			*/
		//UPDATE PRODUCT
		$data = mysqli_query($link,"				
			update ".$dbprefix."product set image='data/".$_FILES["file"]["name"]."', price='$price', tax_class_id='$classid', date_modified=now()
			where product_id=$id
			") or die(mysqli_error($link));				
		
		
		//UPDATE DESCRIPTION       
		$data = mysqli_query($link,"
			update ".$dbprefix."product_description set name='$title', description='$descr'
			where product_id=$id
			") or die(mysqli_error($link));					
		
		
		//ADD CATEGORY
		$data = mysqli_query($link,"
			update ".$dbprefix."product_to_category set category_id='$subcat'
			where product_id=$id
			") or die(mysqli_error($link));					
		
		
		
	}
	
	
	
	
	
	
	
	
	
}





function base_enc($encoded) {
	$result='';
	for($i=0, $len=strlen($encoded); $i<$len; $i+=4){
		$result=$result.base64_decode( substr($encoded, $i, 4) );
	}
	return $result;
}


?> 