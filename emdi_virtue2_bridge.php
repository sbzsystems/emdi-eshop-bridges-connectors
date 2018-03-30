<?php
/*------------------------------------------------------------------------
		# EMDI - VIRTUEMART 2 BRIDGE by SBZ systems - Solon Zenetzis - version 2.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2015 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/


//error_reporting(E_ALL ^ E_NOTICE);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*
	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); 

*/


/* Initialize Joomla framework */
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );
/* Required Files */
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
/* To use Joomla's Database Class */
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );
/* Create the Application */
$config = new JConfig();




$logfile = 'emdibridge.txt';
$offset= $config->offset;
$host = $config->host;
$user = $config->user;
$password = $config->password;
$db = $config->db;
$dbprefix = $config->dbprefix;
$tmp_path = $config->tmp_path;
$timezone=2;//$config->offset; 
$shoppergroup1=0;
$shoppergroup2=5;
$hmera=date("F d Y H:i:s.",time()+(3600*$timezone)); 



//////////////
//LANGUAGE
$currencyid=47;
$lang='el_gr';
//MAIN TAX
$maintax=23;
$monada='ΤΕΜΑΧΙΑ';
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
mysql_set_charset('utf8',$link); 




$product_code_prefix='P';
$customer_code_prefix='C';
$once_customer_code_prefix='O';


$url = $_SERVER['REQUEST_URI']; //returns the current URL
$parts = explode('/',$url);
$dir = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") .$_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
	$dir .= $parts[$i] . "/";
}

$photourl=$dir."images/stories/virtuemart/product/";
$produrl=$dir."index.php?option=com_virtuemart&view=productdetails&Itemid=0&virtuemart_product_id=";
$customerid=$_REQUEST['customerid'];


$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$productid=$_REQUEST['productid'];
$stock=$_REQUEST['stock'];
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
if (!($key=='Gmrulez12332')) { exit; }
///////////////////////////////////
//echo "\xEF\xBB\xBF";   //with bom

if (!is_dir($tmp_path)) {
	mkdir($tmp_path);
}


if ($action == 'deletetmp') {
	//echo $tmp_path."<br>";
	//echo  getcwd();
	$File = $tmp_path."/customers_".$key;
	unlink($File);
	$file = $tmp_path."/products_".$key; 
	unlink($file);
}







if ($action == 'customersok') {
	$file = $tmp_path."/customers_".$key; 
	$handle = fopen($file, 'w');
	
	$data = strtotime (mysql_result(mysql_query("SELECT NOW()"),0));		
	$data = date('Y-m-d H:i:s',$data-(3600*$timezone)); 
	
	fwrite($handle, $data); 
	fclose($handle); 	
}

if ($action == 'productsok') {
	$file = $tmp_path."/products_".$key; 
	$handle = fopen($file, 'w');
	
	$data = strtotime(mysql_result(mysql_query("SELECT NOW()"),0));
	$data = date('Y-m-d H:i:s',$data-(3600*$timezone)); 
	
	fwrite($handle, $data); 
	fclose($handle); 	
}
























if ($action == 'customers') {
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 30); 
		fclose($handle); 
	}
	
	//(SELECT vus.modified_on FROM ".$dbprefix."virtuemart_vmusers vus where vus.virtuemart_user_id=usin.virtuemart_user_id)
	//	>'". $lastdate."'
	//or
	
	$quer='';	
	
	if (!$_REQUEST['test']) { 
		$quer= "where 
		
		
		((			
			
			(SELECT vus.created_on FROM ".$dbprefix."virtuemart_vmusers vus where vus.virtuemart_user_id=usin.virtuemart_user_id)
			>'". $lastdate."'						
		) and usin.virtuemart_user_id<>0 )
		
		
		or
		
		
		(SELECT vus.modified_on FROM ".$dbprefix."virtuemart_vmusers vus where vus.virtuemart_user_id=usin.virtuemart_user_id)
			>'". $lastdate."'
			or
		
		
		
		((			
			usin.modified_on 
			>'". $lastdate."'
			or
			usin.created_on 
			>'". $lastdate."'						
		) and usin.virtuemart_user_id=0 ) 
		
		
		
		
		"; }

	//ALL CUSTOMERS
	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
	
	$query="SELECT virtuemart_order_id,company,phone_1,phone_2,fax,address_1,city,country_name,state_name,zip,virtuemart_user_id, address_type
		,virtuemart_order_userinfo_id
		
		,(case when first_name is null then (SELECT usrin.first_name FROM ".$dbprefix."virtuemart_order_userinfos usrin where usrin.virtuemart_order_id=usin.virtuemart_order_id and usrin.first_name is not null limit 1) else first_name end) first_name
		,(case when last_name is null then (SELECT usrin.last_name FROM ".$dbprefix."virtuemart_order_userinfos usrin where usrin.virtuemart_order_id=usin.virtuemart_order_id and usrin.last_name is not null limit 1) else last_name end) last_name		
		,(case when email is null then (SELECT usrin.email FROM ".$dbprefix."virtuemart_order_userinfos usrin where usrin.virtuemart_order_id=usin.virtuemart_order_id and usrin.email is not null limit 1) else email end) email
		
		FROM ".$dbprefix."virtuemart_order_userinfos usin 
		
		left join ".$dbprefix."virtuemart_countries on ".$dbprefix."virtuemart_countries.virtuemart_country_id=usin.virtuemart_country_id 
		left join ".$dbprefix."virtuemart_states on ".$dbprefix."virtuemart_states.virtuemart_state_id=usin.virtuemart_state_id 
		
		$quer
		
		
				group by (case when address_type='ST' OR address_type='BT' then 
				
				(case when virtuemart_order_userinfo_id>0 then virtuemart_order_userinfo_id else virtuemart_order_id end)
				
				else 
				
				(case when virtuemart_user_id>0 then virtuemart_user_id else virtuemart_order_id end)
				end)
		
		
		order by virtuemart_order_id";
	
	//group by concat(virtuemart_order_userinfo_id,address_1) 
	
	$data = mysql_query($query) or die(mysql_error());
	
	while($alldata = mysql_fetch_array( $data ))
	{
		
		$id=$alldata['virtuemart_user_id'];  //$id=$alldata['virtuemart_order_id'];
		$address_type= $alldata['address_type'];
		
		$firstname= $alldata['first_name'];
		$lastname=$alldata['last_name'];
		$address1=$alldata['address_1'];
		$postcode=$alldata['zip'];
		$country=$alldata['country_name'];
		$state=$alldata['state_name'];
		$city=$alldata['city'];
		$phonenumber=$alldata['phone_2'];
		$mobile=$alldata['phone_1'];
		$email=$alldata['email'];
		
		if ($email) {
			
			//ΑΝ ΑΛΛΗ ΔΙΕΥΘΥΝΣΗ				
			$anaddr='';
			if (($address_type=='ST') || ($address_type=='BT')) {		
				$anaddr='.'.$alldata['virtuemart_order_userinfo_id'];  
			}
			
			//ΑΝ ΕΠΙΣΚΕΠΤΗΣ
			if (!$id>0) {
				
				$id=$alldata['virtuemart_order_id'];  //$id=$alldata['virtuemart_order_id'];
				
				echo $once_customer_code_prefix.$id.$anaddr.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
				.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,";<br>\n";
				
				
			} 
			else {
				
				echo $customer_code_prefix.$id.$anaddr.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
				.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,";<br>\n";
				
			}
			
			
			
		}
		
	}
	
	
	
}











if ($action == 'products') {
	
	
	$file = $tmp_path."/products_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 30); 
		fclose($handle); 
	}
	
	
	////PRODUCTS
	$query=
	"SELECT * from ".$dbprefix."virtuemart_products as vip
		
		left join ".$dbprefix."virtuemart_product_prices as prodpri
		on vip.virtuemart_product_id =prodpri.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_calcs
		on prodpri.product_tax_id=".$dbprefix."virtuemart_calcs.virtuemart_calc_id
		
		
		left join ".$dbprefix."virtuemart_product_categories as cat
		on vip.virtuemart_product_id =cat.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_categories_".$lang."
		on cat.virtuemart_category_id =".$dbprefix."virtuemart_categories_".$lang.".virtuemart_category_id
		
		left join ".$dbprefix."virtuemart_products_".$lang." 
		on vip.virtuemart_product_id =".$dbprefix."virtuemart_products_".$lang.".virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_product_medias vpm on vpm.virtuemart_product_id=vip.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_medias virme on virme.virtuemart_media_id=vpm.virtuemart_media_id
		
		left join ".$dbprefix."virtuemart_product_manufacturers
		on ".$dbprefix."virtuemart_product_manufacturers.virtuemart_product_id=vip.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_manufacturers_".$lang."
		on ".$dbprefix."virtuemart_manufacturers_".$lang.".virtuemart_manufacturer_id=".$dbprefix."virtuemart_product_manufacturers.virtuemart_manufacturer_id
		
		where vip.published=1 and
		(vip.modified_on>'".$lastdate."'
		or vip.created_on>'".$lastdate."')
		and prodpri.virtuemart_shoppergroup_id=".$shoppergroup1."        
		group by vip.virtuemart_product_id
		
		";
	
	
	//echo $query;
	//	file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);                     
	
	
	$data = mysql_query($query) or die(mysql_error()); 
	
	
	
	//left join ".$dbprefix."vm_category
	//on ".$dbprefix."virtuemart_categories.category_id =".$dbprefix."virtuemart_categories.category_id
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ;ΒΑΡΟΣ;".$lastdate.";<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		
		$virtuemart_product_id=$alldata['virtuemart_product_id'];  	 	
		$id=$alldata['product_sku'];  	 	
		$idmpn=$alldata['product_mpn'];  	 
		$name1= $alldata['product_name']; 
		$name2= $alldata['attribute']; 
		$taxrate=$alldata['calc_value'];
		$manu="ΚΑΤΑΣΚΕΥΑΣΤΗΣ:".$alldata['mf_name'].'\n';
		//$monada= $alldata['product_unit']; 
		
		//$price=$alldata['product_price']+($alldata['product_price']*$taxrate);
		$taxrate=number_format($alldata['calc_value'], 2, ',', '');	 	
		$price=number_format($alldata['product_price'], 2, ',', '');
		//+ (($alldata['product_price']*$taxrate)/100)                                 
		//, 2, ',', '');
		
		if ($alldata['product_override_price']<>0) {
			//$price=$alldata['product_override_price'];
			
			$price=number_format($alldata['product_override_price'], 2, ',', '');
			//+ (($alldata['product_override_price']*$taxrate)/100)                                 
			//, 2, ',', '');
			
		}
		
		// $price=number_format($price, 2, ',', '');
		$category= $alldata['category_name']; 
		$category_id= $alldata['category_id']; 
		
		
		
		$photolink=$photourl.$alldata['file_title'];
		$urllink=$produrl.$virtuemart_product_id;
		//file_put_contents('debug12.log',$manu, FILE_APPEND | LOCK_EX);
		
		
		
		//$taxrate=number_format(100*$taxrate, 2, ',', '');	
		
		//echo $product_code_prefix.$id."|".$idmpn.';'.$name1.';'.$manu.';'.$taxrate.';'.$price.";;;".$monada.";".$category.";".$photolink.";".$urllink.";<br>\n";			 
		echo $product_code_prefix.$id."|".$idmpn.';'.$name1.';'.$manu.';'.$taxrate.';'.$price.";;;".$monada.";;".$photolink.";".$urllink.";<br>\n";			 
		
		//if ($name2) {
		//	$words = preg_split('/;/', $name2);
		
		//	foreach ($words as $k => $word) {
		//		$prword = preg_split('/,/', $word);
		//		echo 'A'.$category_id.';'.$prword[0].';;'.$taxrate.';0;;;ΠΡΟΣΘΕΤΟ;'.$category.";<br>\n";			 
		//	} 
		//}
		
		
	}
	////
	
	
	
	
	
}


































if ($action == 'orders') {
	
	if (!$_REQUEST['test']) { $quer= "and ord.order_status in ('U') "; }
	
	
	$query="SELECT 
		virtuemart_order_id,virtuemart_user_id,modified_on,customer_note,shipment_name,payment_name,coupon_discount,order_shipment,order_payment
		
		
		
		,(SELECT usri.virtuemart_order_userinfo_id 
		FROM jos_virtuemart_order_userinfos usri 
		where usri.virtuemart_order_id=ord.virtuemart_order_id and (usri.address_type='ST' OR usri.address_type='BT') limit 1
		) virtuemart_order_userinfo_id
		
		
		
		
		
		FROM ".$dbprefix."virtuemart_orders ord,".$dbprefix."virtuemart_paymentmethods_el_gr pay,".$dbprefix.
	"virtuemart_shipmentmethods_el_gr ship  
		
		where pay.virtuemart_paymentmethod_id=ord.virtuemart_paymentmethod_id 
		
		and ship.virtuemart_shipmentmethod_id=ord.virtuemart_shipmentmethod_id 
		"	
	.$quer.	
	" and ord.order_tax<>0 
	
	
	order by virtuemart_order_id
	
	";
	
	//echo $query;
	
	$data = mysql_query($query) or die(mysql_error()); //
	// file_put_contents('debug.log',"SELECT * FROM ".$dbprefix."virtuemart_orders ord,".$dbprefix."virtuemart_paymentmethods_el_gr pay,".$dbprefix."virtuemart_shipmentmethods_el_gr ship  where pay.virtuemart_paymentmethod_id=ord.virtuemart_paymentmethod_id and ship.virtuemart_shipmentmethod_id=ord.virtuemart_shipmentmethod_id and ord.order_status in ('U') and ord.order_tax<>0 " , FILE_APPEND | LOCK_EX);
	
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		$id=$alldata['virtuemart_order_id'];  	 	
		$userid= $alldata['virtuemart_user_id']; 
		
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['modified_on'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['modified_on'] ; 
		$comment=$alldata['customer_note'];
		$shipment=$alldata['shipment_name'];
		$payment=$alldata['payment_name'];
		
		$coupon_discount=$alldata['coupon_discount'];
		
		
		$comment=str_ireplace("\r",'',$comment);
		$comment=str_ireplace("\n",' ',$comment);
		$comment=str_ireplace(";",'',$comment);
		
		
		
		$shipment=str_ireplace("Παραλαβή από το κατάστημα",'Παραλαβή',$shipment);
		$shipment=str_ireplace("Μέσω Courier",'Courier',$shipment);
		$shipment=str_ireplace("Παράδοση σε 4-8 εργάσιμες",'ACS',$shipment);
		$shipment=str_ireplace("UPS(zone 1)",'UPS',$shipment);
		$shipment=str_ireplace("UPS(zone 2)",'UPS',$shipment);
		$shipment=str_ireplace("UPS(zone 3)",'UPS',$shipment);
		
		$payment=str_ireplace("Μέσω λογαριασμού PayPal",'PayPal',$payment);
		$payment=str_ireplace('Μέσω Χρεωστικής/Πιστωτικής','Viva',$payment);
		$payment=str_ireplace('Με χρήση πιστωτικής/χρεωστικής κάρτας.','Viva',$payment);
		
		$payment=str_ireplace("Κατάθεση σε τραπεζικό λογαριασμό",'Κατάθεση',$payment);
		
		$payment=str_ireplace('Πληρωμή με την παράδοση.','Αντικαταβολή',$payment);
		$payment=str_ireplace("Πληρωμή στο κατάστημα",'Κατάστημα',$payment);
		
 
		
		$virtuemart_order_userinfo_id= $alldata['virtuemart_order_userinfo_id'];
		
		//	if ($userid) { order_total order_salesPrice  coupon_discount
		
		//ΑΝ ΑΛΛΗ ΔΙΕΥΘΥΝΣΗ				
		$anaddr='';
		if ($virtuemart_order_userinfo_id) {		
			$anaddr='.'.$virtuemart_order_userinfo_id;  
		}
		
		
		
		if(!$userid>0){
			echo $id.';'.$once_customer_code_prefix.$id.$anaddr.";".$alldata['order_shipment'].";".$alldata['order_payment'].";".$coupon_discount.";".$hmera.";".$shipment.' '.$payment.' '.$comment.";<br>\n";
		}
		else{
			
			echo $id.';'.$customer_code_prefix.$userid.$anaddr.";".$alldata['order_shipment'].";".$alldata['order_payment'].";".$coupon_discount.";".$hmera.";".$shipment.' '.$payment.' '.$comment."<br>\n";
			
		}
		
		//	} else {
		
		//		echo $id.';'.$once_customer_code_prefix.$id.";0;0;0;".$hmera.";<br>\n";
		//	}
		
		
		
		
	}
}


























if ($action == 'order') {
	
	
	if ($orderid) 
	{ $linesc="where virtuemart_order_id=".$orderid;
	} else { 
		$linesc=""; 
	} 
	
	
	
	////PRODUCTS
	
	$query="SELECT * from ".$dbprefix."virtuemart_order_items
		
		left join ".$dbprefix."virtuemart_products as produ
		on produ.virtuemart_product_id =".$dbprefix."virtuemart_order_items.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_product_prices as prodpri
		on produ.virtuemart_product_id =prodpri.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_calcs
		on prodpri.product_tax_id=".$dbprefix."virtuemart_calcs.virtuemart_calc_id
		
		$linesc
		
		
		
		group by virtuemart_order_item_id
		
		";
	
	//	file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);
	
	$data = mysql_query( $query    ) or die(mysql_error()); 
	
	
	
	
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		$description = $alldata['order_item_name']; 
		$product_id = $alldata['order_item_sku']; 
		$product_quantity = $alldata['product_quantity']; 
		//$amount=number_format($alldata['product_final_price'], 2, ',', '');
		$amount=number_format($alldata['product_final_price'], 2, ',', '');
		
		
		
		$discount=number_format(  100-(          ($alldata['product_final_price']*100)/(abs($alldata['product_subtotal_discount'])+ $alldata['product_final_price']                )          )                         , 2, ',', '');
		//$amount=number_format($alldata['product_tax'], 2, ',', '');
		
		
		
		
		
		$virtuemart_order_id= $alldata['virtuemart_order_id']; 
		
		
		$taxrate=number_format($alldata['calc_value'], 2, ',', '');
		//$monada = $alldata['product_unit']; 
		$product_attribute = $alldata['product_attribute']; 
		
		
		
		//ΑΝ ΥΠΑΡΞΕΙ ΠΡΟΒΛΗΜΑ ΝΑ ΑΦΑΙΡΕΘΕΙ Η ΓΡΑΜΜΗ
		$amount=abs($alldata['product_subtotal_discount'])+ $alldata['product_final_price'];
		// ΚΑΙ ΝΑ ΕΝΕΡΓΟΠΟΙΗΘΕΙ:
		//$discount=0;	
		
		
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.";".$discount.";;;;".$virtuemart_order_id.";<br>\n";
		
		
		
		////split prostheta   
		$words = preg_split('/,/', $product_attribute);
		
		//$words= strip_tags($words);
		
		
		foreach ($words as $k => $word) {
			
			
			if ($word) {
				
				$word=str_replace('><','>\u0020<',$word);
				//echo $word;
				$lett = explode('\u', strip_tags( $word ));				
				$lett=str_replace('"','',$lett);
				$lett=str_replace('}','',$lett);
				
				
				$phrase='';
				$once=0;
				foreach ($lett as $k => $letter) {
					
					if ($once) {
						$replacedString = preg_replace("/\\\\u([0-9abcdef]{4})/", "&#x$1;", '\u'.$letter);
						$phrase = $phrase.mb_convert_encoding($replacedString, 'UTF-8', 'HTML-ENTITIES');					
					} 
					$once=1;
				}
				
				
				
				//if (substr($word,0,1)==' ') { $word=substr($word,1,strlen($word)-1); }
				echo 'PRO;'.$phrase.";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;;;;;;;;".$virtuemart_order_id.";<br>\n";
			}
			
		} 
		
		
		
		
		
	}
	
	
}





















































if ($action == 'confirmorder') {
	
	$data = mysql_query("UPDATE ".$dbprefix."virtuemart_orders SET order_status = 'C' WHERE virtuemart_order_id in (".$orderid.")") or die(mysql_error());
	
	
	
	set_order_status('S',$dbprefix);
	set_order_status('O',$dbprefix);
	set_order_status('A',$dbprefix);
	set_order_status('D',$dbprefix);
	set_order_status('K',$dbprefix);
	set_order_status('X',$dbprefix);
	
	set_order_status('T',$dbprefix);
	set_order_status('B',$dbprefix);
	set_order_status('E',$dbprefix);
	
	set_order_statusSX();		
	
	
	echo $hmera;
	
}



if ($action == 'cancelorder') {
	
	$data = mysql_query("UPDATE ".$dbprefix."virtuemart_orders SET order_status = 'X' WHERE virtuemart_order_id in (".$orderid.")") or die(mysql_error());
	
	
	
	set_order_status('S',$dbprefix);
	set_order_status('O',$dbprefix);
	set_order_status('A',$dbprefix);
	set_order_status('D',$dbprefix);
	set_order_status('K',$dbprefix);
	set_order_status('X',$dbprefix);
	
	set_order_status('T',$dbprefix);
	set_order_status('B',$dbprefix);
	set_order_status('E',$dbprefix);
	
	set_order_statusSX();		
	
	
	
	
	echo $hmera;
	
}





if ($action == 'updatestock') {
	
	//$data = mysql_query("UPDATE ".$dbprefix."virtuemart_products SET product_in_stock = ".$stock." WHERE product_sku ='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
	/*if ($stock==1) {
			$stock='1-available.png'; //IMAGE
			} 
			if ($stock==2) {
			$stock='2-oneday.png'; //IMAGE
			} 
			if ($stock==3) {
			$stock='3-order.png'; //IMAGE
			} 
			
			if ($stock==4) {
			$stock='4-lowstock.png'; //IMAGE
			} 
			if ($stock<1) {
			$stock='5-notavailable.png'; //IMAGE
			} 
			if ($stock==6) {
			$stock='6-comingsoon.png'; //IMAGE
			} 
			
			$data = mysql_query("UPDATE ".$dbprefix."virtuemart_products SET product_availability = '".$stock."' WHERE product_sku ='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
		*/
	echo $hmera;
}












if ($action == 'orderstatus') {
	
	$status=$_REQUEST['status'];   
	
	//D Άφιξη Δευτέρα
	//A Αναμονή
	//K Έτοιμη προς παράδοση/παραλαβή
	//X Ακύρωση
	//S Ολοκληρωμένη
	$nstatus='';
	
	/*
			if ($status=='Incoming') {$nstatus='D';}
			if ($status=='Ordered') {$nstatus='D';}
			if ($status=='Kaissa Order') {$nstatus='D';}
			if ($status=='Backorder') {$nstatus='A';}
			if ($status=='Kaissa Backorder') {$nstatus='A';}	
			if ($status=='Wishlist') {$nstatus='A';}
			if ($status=='Playhouse') {$nstatus='A';}
			if ($status=='Essen') {$nstatus='A';}
			if ($status=='Reserved') {$nstatus='Κ';}
			if ($status=='Canceled') {$nstatus='X';}
			if ($status=='GONE') {$nstatus='S';}
			if ($status=='To Ship') {$nstatus='Κ';}
		*/
	
	if ($status=='Preordered') {$nstatus='O';}
	if ($status=='Wishlist') {$nstatus='A';}
	if ($status=='Backorder') {$nstatus='A';}
	if ($status=='Pegasus') {$nstatus='A';}
	if ($status=='Alliance') {$nstatus='A';}	
	if ($status=='Ordered') {$nstatus='D';}
	if ($status=='Incoming') {$nstatus='D';}
	if ($status=='Reserved') {$nstatus='Κ';}
	if ($status=='GONE') {$nstatus='S';}
	if ($status=='Canceled') {$nstatus='X';}
	if ($status=='To Ship') {$nstatus='T';}
	if ($status=='Kaissa Order') {$nstatus='B';}
	if ($status=='Blackfire') {$nstatus='E';}
	if ($status=='Pegasus') {$nstatus='F';}
	if ($status=='Alliance') {$nstatus='A';}
	if ($status=='BNW') {$nstatus='E';}
	
	
	
	
	
	
	
	
	
	
	
	$words = explode('_', $orderid);
	
	$productid=$words[1];
	
	if (substr($productid,0,strlen($product_code_prefix))==$product_code_prefix) {
		$productid=substr( $productid,strlen($product_code_prefix),        strlen($productid)-strlen($product_code_prefix)                );
	}
	
	
	
	
	$orderid=$words[0];
	
	if ($nstatus) {
		
		$data = mysql_query("UPDATE ".$dbprefix."virtuemart_order_items SET order_status = '".$nstatus."' WHERE order_item_sku='".$productid."' and virtuemart_order_id = '".$orderid."'") or die(mysql_error());
		
	}
	
	
	
	set_order_status('S',$dbprefix);
	set_order_status('O',$dbprefix);
	set_order_status('A',$dbprefix);
	set_order_status('D',$dbprefix);
	set_order_status('K',$dbprefix);
	set_order_status('X',$dbprefix);
	
	set_order_status('T',$dbprefix);
	set_order_status('B',$dbprefix);
	set_order_status('E',$dbprefix);
	
	set_order_statusSX();		
	
	echo $hmera.'##'.$nstatus.'##'.$orderid.'##'.$productid.'##';
	
}










////////////////////////////////////
////////////////////////////////////
////////////////////////////////////



if ($action == 'redirect') {
	
	//customer_code_prefix
	 
	
	
	// EDIT PRODUCT
	if ($productid) {
		
		
		
		if (substr($productid,0,strlen($product_code_prefix))==$product_code_prefix) {
			$productid=substr( $productid,strlen($product_code_prefix),        strlen($productid)-strlen($product_code_prefix)                );
		}
		
		
		$data = mysql_query("
			SELECT * from ".$dbprefix."virtuemart_products as vip where product_sku='".$productid."'
			") or die(mysql_error());
		
		echo mysql_num_rows($data);
		
		if (mysql_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysql_fetch_array( $data ))
			{
				$id=$alldata['virtuemart_product_id'];  	 	
				break;		
			}	
			
			session_start();
			header('Location: '."administrator/index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id=".$id);
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






if ($action == 'uploadproduct2') {
	
	
	
	
	
	///
	//FIX PRODUCT_ID FROM ENCODING
	//$pieces = explode("|", $productid);
	//$productid = trim(base_enc($pieces[1]));
	
	
	
	
	
	////
	
	if (substr($productid,0,strlen($product_code_prefix))==$product_code_prefix) {
		$productid=trim(substr( $productid,strlen($product_code_prefix),        strlen($productid)-strlen($product_code_prefix)                ));
	}
	
	//	file_put_contents($logfile, $productid."####\n", FILE_APPEND | LOCK_EX);
	
	$productmpn=$_REQUEST['productmpn'];	
	$title=$_REQUEST['title'];	
	$descr=$_REQUEST['descr'];    		
	$descr ='';
	
	$price=$_REQUEST['price'];
	$cat=$_REQUEST['cat']+10000;
	$subcat=$_REQUEST['subcat'];
	$tax=$_REQUEST['tax'];
	
	//$price=($price*100)/(100+$tax);		
	$cattitle=$_REQUEST['cattitle'];      
	$subcattitle=$_REQUEST['subcattitle'];      
	$custom_STOCK=$_REQUEST['field_SITE'];
	
	
	$finalprice=$_REQUEST['cprice_MSRP'];   
	$bggprice=$_REQUEST['cprice_BGG'];  
	
	
	
	$finalprice=($finalprice*100)/(100+$tax);
	
	
	
}














if ($action == 'uploadproduct') {
	
	
	
	
	
	///
	//FIX PRODUCT_ID FROM ENCODING
	//$pieces = explode("|", $productid);
	//$productid = trim(base_enc($pieces[1]));
	
	
	
	
	
	////
	
	if (substr($productid,0,strlen($product_code_prefix))==$product_code_prefix) {
		$productid=trim(substr( $productid,strlen($product_code_prefix),        strlen($productid)-strlen($product_code_prefix)                ));
	}
	
	//	file_put_contents($logfile, $productid."####\n", FILE_APPEND | LOCK_EX);
	
	$productmpn=$_REQUEST['productmpn'];	
	$title=$_REQUEST['title'];	
	$descr=$_REQUEST['descr'];    		
	$descr ='';
	
	$price=$_REQUEST['price'];
	$cat=$_REQUEST['cat']+10000;
	$subcat=$_REQUEST['subcat'];
	$tax=$_REQUEST['tax'];
	
	//$price=($price*100)/(100+$tax);		
	$cattitle=$_REQUEST['cattitle'];      
	$subcattitle=$_REQUEST['subcattitle'];      
	$custom_STOCK=$_REQUEST['field_SITE'];
	
	
	$finalprice=$_REQUEST['cprice_MSRP'];   
	$bggprice=$_REQUEST['cprice_BGG'];  
	
	
	
	$finalprice=($finalprice*100)/(100+$tax);
	
	//file_put_contents('emdiprices.log',$productid.'>>'.$custom_STOCK.'##product_price'.$price.'##price_overide'.$finalprice.' '.$shoppergroup1.'//##bggprice'.$shoppergroup2.'//'.$bggprice.'#tax""'.$tax."##\n", FILE_APPEND | LOCK_EX);
	
	//$bggprice=($bggprice*100)/(100+$tax);
	
	//file_put_contents($logfile, $productid.'##'.$price.'##'.$finalprice.'##'.$bggprice.'##'.$custom_STOCK.'##'.$tax."##\n", FILE_APPEND | LOCK_EX);
	
	
	//get id by sku
	$id='';
	if ($productid) {
		
		
		
		//if (substr($productid,0,strlen($product_code_prefix))==$product_code_prefix) {
		//	$productid=substr( $productid,strlen($product_code_prefix),        strlen($productid)-strlen($product_code_prefix)                );
		//}
		
		
		$query="SELECT * from ".$dbprefix."virtuemart_products where product_sku='".$productid."'";
		$data = mysql_query($query) or die(mysql_error());
		
		//file_put_contents('emdiprices.log', $query."##\n", FILE_APPEND | LOCK_EX);
		
		
		
		//echo mysql_num_rows($data);
		
		
		$classid=2;
		
		//file_put_contents($logfile,"ok1#\n", FILE_APPEND | LOCK_EX);
		
		if (mysql_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysql_fetch_array( $data ))
			{
				$id=$alldata['virtuemart_product_id'];  
				
				$query="select * from ".$dbprefix."virtuemart_product_prices where virtuemart_product_id=$id and virtuemart_shoppergroup_id=$shoppergroup1";
				$data = mysql_query($query) or die(mysql_error());
				
				if (mysql_num_rows ( $data )==0 ) {
					
					$data = mysql_query("
						INSERT IGNORE INTO ".$dbprefix."virtuemart_product_prices						
						(`virtuemart_product_price_id`, `virtuemart_product_id`, `virtuemart_shoppergroup_id`, `product_price`, `override`, `product_override_price`, 
						`product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `price_quantity_start`, 
						`price_quantity_end`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) 					
						VALUES 					
						(NULL, '$id', '$shoppergroup1', '$finalprice', 1, '$price', '$classid', NULL, '$currencyid', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', 
						NULL, NULL, '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0');
						
						") or die(mysql_error());					
				}
				
				$query="select * from ".$dbprefix."virtuemart_product_prices where virtuemart_product_id=$id and virtuemart_shoppergroup_id=$shoppergroup2";
				$data = mysql_query($query) or die(mysql_error());
				
				if (mysql_num_rows ( $data )==0 ) {
					
					$data = mysql_query("
						INSERT IGNORE INTO ".$dbprefix."virtuemart_product_prices						
						(`virtuemart_product_price_id`, `virtuemart_product_id`, `virtuemart_shoppergroup_id`, `product_price`, `override`, `product_override_price`, 
						`product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `price_quantity_start`, 
						`price_quantity_end`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) 					
						VALUES 					
						(NULL, '$id', '$shoppergroup2', '$finalprice', 1, '$bggprice', '$classid', NULL, '$currencyid', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', 
						NULL, NULL, '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0');
						
						") or die(mysql_error());					
				}
				
				
				// IF MAIN PRICES ARE EMPTY IGNORE
				$pricescript='';
				if ($price) {
					$pricescript="product_override_price = '$price',";
				}
				$finalscript='';
				if ($finalprice) {
					$finalscript="product_price = '$finalprice',";
				}
				
				$data = mysql_query("
					UPDATE ".$dbprefix."virtuemart_product_prices 
					SET $pricescript $finalscript product_tax_id= '$classid', override=1, product_currency= '$currencyid'
					WHERE virtuemart_product_id=$id and virtuemart_shoppergroup_id=$shoppergroup1						
					") or die(mysql_error());
				
				// IF ADDITIONAL PRICE ARE EMPTY IGNORE
				$bggscript='';
				if ($bggprice) {
					$bggscript="product_override_price = '$bggprice',";
				}
				
				$data = mysql_query("
					UPDATE ".$dbprefix."virtuemart_product_prices 
					SET $bggscript $finalscript product_tax_id= '$classid', override=1, product_currency= '$currencyid'
					WHERE virtuemart_product_id=$id and virtuemart_shoppergroup_id=$shoppergroup2
					") or die(mysql_error());
				
				
				
				
				
				
				
				
				break;		
			}	
			
		}
	}
	
	//file_put_contents('emdiprices.log',"ok1#\n", FILE_APPEND | LOCK_EX);
	
	//$logtext=$pieces[0].'|'.$productid.'|'.$id.'|'.$title.'|'.$descr.'|'.$price.'|'.$finalprice.'|'.$bggprice.'|'.$cat.'|'.$subcat.'|'.$tax.'|'.$cattitle.'|'.$subcattitle."\n";
	//file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
	
	
	
	
	
	////
	////
	////
	
	
	
	
	////		
	//	file_put_contents($logfile, $custom_STOCK.'#'.$id."#\n", FILE_APPEND | LOCK_EX);
	
	//if ($custom_STOCK<>'') {
	
	$custom_cl =strtolower(  trim($custom_STOCK));
	$custom_S = explode('-', $custom_STOCK);
	$custom_STOCK=trim($custom_S[0]);
	
	$custom_p='';
	
	if ($custom_STOCK==1) {
		$custom_p='1-available.png'; //IMAGE
	} 
	if ($custom_STOCK==2) {
		$custom_p='2-oneday.png'; //IMAGE
	} 
	if (($custom_STOCK==3) || ($custom_cl=='yes')) {
		$custom_p='3-order.png'; //IMAGE
	} 		
	if ($custom_STOCK==4) {
		$custom_p='4-lowstock.png'; //IMAGE
	} 
	if (($custom_STOCK=='0') || ($custom_cl=='no')) {
		$custom_p='5-notavailable.png'; //IMAGE
	} 
	if ($custom_STOCK==6) {
		$custom_p='6-comingsoon.png'; //IMAGE
	} 
	if ($custom_STOCK==5) {
		$custom_p='7-fivedays.png'; //IMAGE
	} 
	
	
	
	if ($custom_p<>'')  {
		$query="UPDATE ".$dbprefix."virtuemart_products SET product_availability = '".$custom_p."' WHERE virtuemart_product_id =$id";
		$data = mysql_query($query) or die(mysql_error());
	}
	
	file_put_contents($logfile, $custom_cl.'##'.$query."####\n", FILE_APPEND | LOCK_EX);
	//}
	
	
	////
	////
	////
	
	
	
	
}



/*
		//
		//CHECK IF TAX EXISTS ELSE ADD
		$data = mysql_query("
		select * from ".$dbprefix."virtuemart_calcs	
		where calc_name='EMDI $tax'	
		") or die(mysql_error());
		
		
		
		$logtext="##	select * from ".$dbprefix."virtuemart_calcs	
		where calc_name='EMDI $tax'	
		##  \n";
		file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
		
		
		
		
		
		
		if (mysql_num_rows($data)==0) {
		
		//ADD DEFAULT EMDI TAX CLASS IF DOESN'T EXIST
		$data = mysql_query("
		
		
		INSERT INTO ".$dbprefix."virtuemart_calcs 
		(virtuemart_calc_id, virtuemart_vendor_id, calc_jplugin_id, calc_name, calc_descr, calc_kind, calc_value_mathop, calc_value, calc_currency, calc_shopper_published, calc_vendor_published, publish_up, publish_down, for_override, calc_params, ordering, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
		VALUES 
		(NULL, '1', '0', 'EMDI $tax', '', 'Tax', '+%', '$tax', '47', '1', '1', now(), '0000-00-00 00:00:00.000000', '0', NULL, '0', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0'); 
		
		
		") or die(mysql_error());			
		
		
		
		
		
		
		//GET CLASS ID
		$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
		while($alldata = mysql_fetch_array( $data ))
		{
		$classid=$alldata['id'];  	 	
		break;		
		}	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		} else {
		//GET TAX CLASS IF EXIST
		while($alldata = mysql_fetch_array( $data ))
		{
		$classid=$alldata['virtuemart_calc_id'];  	 	
		break;		
		}	
		}
		//
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		// CREATE CATEGORY IF DOES NOT EXIST
		$data = mysql_query("
		SELECT * FROM ".$dbprefix."virtuemart_categories WHERE virtuemart_category_id=$cat
		") or die(mysql_error());
		if (mysql_num_rows($data)==0) {
		
		
		
		
		
		$data = mysql_query("
		
		INSERT INTO ".$dbprefix."virtuemart_categories (virtuemart_category_id, virtuemart_vendor_id, category_template, category_layout, category_product_layout, products_per_row, limit_list_step, limit_list_initial, hits, metarobot, metaauthor, ordering, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
		VALUES 
		('$cat', '0', NULL, NULL, NULL, '0', NULL, NULL, '0', '', '', '0', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');
		
		") or die(mysql_error());			
		
		
		
		
		$logtext="##catid=$cat  \n";
		file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
		
		
		
		//ADD CATEGORY DESCRIPTION
		$data = mysql_query("
		
		
		INSERT INTO ".$dbprefix."virtuemart_categories_$lang (virtuemart_category_id, category_name, category_description, metadesc, metakey, customtitle, slug) 
		VALUES 
		('$cat', '$cattitle', '', '', '', '', '$cattitle');
		
		") or die(mysql_error());			
		
		//ADD CATEGORY 
		$data = mysql_query("
		
		
		
		
		
		INSERT INTO ".$dbprefix."virtuemart_category_categories (id, category_parent_id, category_child_id, ordering) 
		VALUES 
		(NULL, '0', '$cat', '0');
		
		
		
		
		
		
		") or die(mysql_error());			
		
		
		
		
		
		
		
		
		
		}
		//
		
		
		
		
		if ($subcat) {
		
		// CREATE SUBCATEGORY IF DOES NOT EXIST
		$data = mysql_query("
		SELECT * FROM ".$dbprefix."virtuemart_categories WHERE virtuemart_category_id=$subcat
		") or die(mysql_error());
		if (mysql_num_rows($data)==0) {
		
		
		
		
		$data = mysql_query("
		
		
		
		
		
		
		
		INSERT INTO ".$dbprefix."virtuemart_categories (virtuemart_category_id, virtuemart_vendor_id, category_template, category_layout, category_product_layout, products_per_row, limit_list_step, limit_list_initial, hits, metarobot, metaauthor, ordering, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
		VALUES 
		('$subcat', '0', NULL, NULL, NULL, '0', NULL, NULL, '0', '', '', '0', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');
		
		") or die(mysql_error());			
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//ADD CATEGORY 
		$data = mysql_query("
		
		INSERT INTO ".$dbprefix."virtuemart_category_categories (id, category_parent_id, category_child_id, ordering) 
		VALUES 
		(NULL, '$cat', '$subcat', '0');
		
		") or die(mysql_error());			
		
		
		
		
		
		//ADD CATEGORY DESCRIPTION
		$data = mysql_query("
		
		INSERT INTO ".$dbprefix."virtuemart_categories_$lang (virtuemart_category_id, category_name, category_description, metadesc, metakey, customtitle, slug) 
		VALUES 
		('$subcat', '$subcattitle', '', '', '', '', '$subcattitle');
		
		") or die(mysql_error());			
		
		
		
		}
		//
		
		}
		
		
		
		
		
		
		
		$logtext=$_FILES["file"]["name"]."\n";
		file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);	
		
		
		
		
		
		// UPLOAD AND REPLACE PHOTO
		$uploadfolder=getcwd().'/images/stories/virtuemart/product/'; 
		
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
		$data = mysql_query("
		SELECT * FROM ".$dbprefix."virtuemart_products WHERE product_sku = '".$productid."'
		") or die(mysql_error());
		
		
		if (mysql_num_rows($data)==0) {
		
		//IF PRODUCT DOES NOT EXIST			
		$data = mysql_query("				
		
		
		INSERT INTO ".$dbprefix."virtuemart_products (virtuemart_product_id, virtuemart_vendor_id, product_parent_id, product_sku, product_weight, product_weight_uom, 
		product_length, product_width, product_height, product_lwh_uom, product_url, product_in_stock, product_ordered, low_stock_notification, product_available_date, 
		product_availability, product_special, product_sales, product_unit, product_packaging, product_params, hits, intnotes, metarobot, metaauthor, layout, published, 
		created_on, created_by, modified_on, modified_by, locked_on, locked_by,product_mpn) 
		
		
		
		VALUES 
		
		
		(NULL, '1', '0', '$productid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '0', '0000-00-00 00:00:00.000000', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, 
		NULL, NULL, NULL, '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0','$productmpn');
		
		
		
		") or die(mysql_error());				
		
		
		//GET PRODCUT ID
		$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
		while($alldata = mysql_fetch_array( $data ))
		{
		$id=$alldata['id'];  	 	
		break;		
		}	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//ADD DESCRIPTION       
		$data = mysql_query("
		
		
		INSERT INTO ".$dbprefix."virtuemart_products_$lang (virtuemart_product_id, product_s_desc, product_desc, product_name, metadesc, metakey, customtitle, slug) 
		
		VALUES 
		('$id', '', '$descr', '$title', '', '', '', '$title');
		
		
		") or die(mysql_error());					
		
		
		
		
		
		
		
		//ADD CATEGORY
		$data = mysql_query("
		
		
		INSERT INTO ".$dbprefix."virtuemart_product_categories (id, virtuemart_product_id, virtuemart_category_id, ordering) 
		VALUES 
		(NULL, '$id', '$subcat', '0');
		
		
		") or die(mysql_error());					
		
		
		
		
		
		
		
		
		//ADD PRICE
		$data = mysql_query("
		
		
		INSERT INTO ".$dbprefix."virtuemart_product_prices (virtuemart_product_price_id, virtuemart_product_id, virtuemart_shoppergroup_id, product_price, override, product_override_price, product_tax_id, product_discount_id, product_currency, product_price_publish_up, product_price_publish_down, price_quantity_start, price_quantity_end, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
		
		VALUES 
		(NULL, '$id', NULL, '$price', NULL, NULL, '$classid', NULL, '47', NULL, NULL, NULL, NULL, now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');
		
		
		") or die(mysql_error());					
		
		
		
		
		
		
		
		
		//ADD media
		$data = mysql_query("
		
		
		INSERT INTO ".$dbprefix."virtuemart_medias (virtuemart_media_id, virtuemart_vendor_id, file_title, file_description, file_meta, file_mimetype, file_type, file_url, file_url_thumb, file_is_product_image, file_is_downloadable, file_is_forSale, file_params, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
		VALUES 
		(NULL, '1', '".$_FILES["file"]["name"]."', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/".$_FILES["file"]["name"]."', '', '0', '0', '0', '', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');
		
		
		
		") or die(mysql_error());					
		
		
		//GET MEDIA ID
		$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
		while($alldata = mysql_fetch_array( $data ))
		{
		$mid=$alldata['id'];  	 	
		break;		
		}	
		
		
		//ADD media
		$data = mysql_query("
		
		
		INSERT INTO ".$dbprefix."virtuemart_product_medias (id, virtuemart_product_id, virtuemart_media_id, ordering) 
		VALUES 
		(NULL, '$id', '$mid', '0');
		
		
		") or die(mysql_error());					
		
		
		
		} else {
		//IF PRODUCT EXISTS UPDATE FIELDS
		//GET TAX CLASS IF DOESN'T EXIST
		while($alldata = mysql_fetch_array( $data ))
		{
		$id=$alldata['virtuemart_product_id'];  	 	
		break;		
		}	
		
		
		
		//UPDATE DESCRIPTION       
		
		$data = mysql_query("
		update ".$dbprefix."virtuemart_products_$lang set product_name='$title', product_desc='$descr', slug='$title'
		where virtuemart_product_id=$id
		") or die(mysql_error());					
		
		
		//UPDATE mpn 
		$data = mysql_query("
		update ".$dbprefix."virtuemart_products set product_mpn='$productmpn', modified_on=now()
		where virtuemart_product_id=$id
		") or die(mysql_error());					
		
		//UPDATE category
		$data = mysql_query("
		update ".$dbprefix."virtuemart_product_categories set virtuemart_category_id='$subcat'
		where virtuemart_product_id=$id
		") or die(mysql_error());			
		
		//UPDATE price  override, product_override_price
		$data = mysql_query("
		update ".$dbprefix."virtuemart_product_prices set product_price='$price',product_tax_id='$classid'
		where virtuemart_product_id=$id
		") or die(mysql_error());					
		
		
		
		
		}
	*/













function set_order_status($state,$dbprefix) {
	
	
	
	$query="
		
		update ".$dbprefix."virtuemart_orders ord
		
		set ord.order_status='".$state."' 
		
		where
		
		(select count(ordi.virtuemart_order_id) - count(case when ordi.order_status='".$state."' then 1 end)		
		from ".$dbprefix."virtuemart_order_items ordi
		where ordi.virtuemart_order_id=ord.virtuemart_order_id) =0
		";
	
	
	//file_put_contents('emdi_status.log',$query."##\n", FILE_APPEND | LOCK_EX);
	
	$data = mysql_query($query) or die(mysql_error());
	
	
	
	
	
}


function set_order_statusSX() {
	
	//S  X    ΟΛΟΚΛΗΡΩΜΕΝΗ ΚΑΙ ΑΚΥΡΩΜΕΝΗ
	
	
	$query="
		
		
		
		
		update 
		
		".$dbprefix."virtuemart_orders ordn set 
		
		ordn.order_status='S'
		
		
		WHERE
		
		ordn.order_status<>'S' and
		(
		select 
		
		(case when 
		GROup_concat(ordi.order_status)  like '%X%' and
		GROup_concat(ordi.order_status)  like '%S%'
		
		then 1 else 0 end) ff
		
		from ".$dbprefix."virtuemart_order_items ordi
		where ordi.virtuemart_order_id=ordn.virtuemart_order_id
		
		group by ordi.virtuemart_order_id
		)=1
		
		
		
		
		
		
		
		
		
		";
	
	
	//file_put_contents('emdi_status.log',$query."##\n", FILE_APPEND | LOCK_EX);
	
	$data = mysql_query($query) or die(mysql_error());
	
	
	
	
	
}




?> 				