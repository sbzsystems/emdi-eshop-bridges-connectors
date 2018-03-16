<?php
/*------------------------------------------------------------------------
		# EMDI - HikaShop BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		#            Copyright (C) 2013-2017 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');



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

error_reporting(E_ALL ^ E_NOTICE);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$logfile = 'emdibridge.txt';
$offset= $config->offset;
$host = $config->host;
$user = $config->user;
$password = $config->password;
$db = $config->db;
$dbprefix = $config->dbprefix;
$tmp_path = $config->tmp_path;
$timezone=3;//$config->offset; 
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
$manufacturer='ΚΑΤΑΣΚΕΥΑΣΤΗΣ';
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
mysql_set_charset('utf8',$link); 



$product_code_prefix='P';
$customer_code_prefix='IC';
$once_customer_code_prefix='AC';


$url = $_SERVER['REQUEST_URI']; //returns the current URL
$parts = explode('/',$url);
$dir = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") .$_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
	$dir .= $parts[$i] . "/";
}

$photourl=$dir."images/com_hikashop/upload/";
$produrl=$dir."index.php?option=com_hikashop&ctrl=product&task=show&product_id=";
$customerid=$_REQUEST['customerid'];


$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$productid=$_REQUEST['productid'];
$stock=$_REQUEST['stock'];
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
$passkey='';
if (!($key==$passkey)) { exit; }
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
	$File = $tmp_path."/customers_".$key; 
	$Handle = fopen($File, 'w');
	
	$data = mysql_result(mysql_query("SELECT NOW()"),0);
	
	//$data = date('Y-m-d H:i:s',time()); 
	fwrite($Handle, $data); 
	fclose($Handle); 	
}
if ($action == 'productsok') {
	$file = $tmp_path."/products_".$key; 
	$handle = fopen($file, 'w');
	
	$data = mysql_result(mysql_query("SELECT NOW()"),0);
	//$data = date('Y-m-d H:i:s',time()); 
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
	//	if (!$_REQUEST['test']) { $quer= "where ".$dbprefix."virtuemart_order_userinfos.modified_on>'". $lastdate."' "; }
	
	
	//ALL CUSTOMERS
	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
	
	$query=" 
		
		
		
		SELECT 
		
		addr.address_id muser_id,
		addr.address_firstname first_name,
		addr.address_lastname last_name,
		addr.address_company companyname,
		
		
		addr.address_street address_1,
		addr.address_post_code zip,
		addr.address_city city,
		addr.address_telephone phone_2,
		addr.address_telephone2 phone_1,
		
		
		(SELECT zone_name FROM ".$dbprefix."hikashop_zone zno where zno.zone_namekey=addr.address_state) state_name,
		(SELECT zone_name FROM ".$dbprefix."hikashop_zone zno where zno.zone_namekey=addr.address_country) country_name,
		
		
		
		addr.address_published,
		addr.address_vat vat,
		
		usr.user_created,	
		usr.user_email email
		
		
		
		
		
		
		FROM ".$dbprefix."hikashop_order ord
		left join ".$dbprefix."hikashop_address addr on ord.order_shipping_address_id=addr.address_id
		left join ".$dbprefix."hikashop_user usr on usr.user_id=addr.address_user_id  
		
		where FROM_UNIXTIME(usr.user_created)>'".$lastdate."'
		
		
		group by concat(addr.address_user_id,addr.address_lastname,addr.address_street)
		order by addr.address_user_id,addr.address_street
		
		";
	
	//echo $query;
	$data = mysql_query($query) or die(mysql_error());
	
	
	//and virtuemart_user_id=0 	
	// echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		
		
		$id=$alldata['muser_id'];  
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
		$companyname=$alldata['companyname'];
		$afm=$alldata['vat'];
		
		
		
		
		if ($email) {
			
			
			
			//ΑΝ ΕΠΙΣΚΕΠΤΗΣ
			if (!$id>0) {
				
				echo $once_customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
				.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,";<br>\n";
				
				
			} 
			else {
				
				echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
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
	
	//echo '##'.$lastdate.'##';
	
	
	
	////PRODUCTS
	$query="
		
		SELECT 
		
		pro.product_code as product_sku,
		product_id,
		product_name,
		product_weight,
		product_quantity,
		
		
		(SELECT ctg.category_name FROM ".$dbprefix."hikashop_category ctg where ctg.category_type='manufacturer' and category_id=pro.product_manufacturer_id) mf_name,
		
		
		(SELECT ftax.tax_rate*100 FROM ".$dbprefix."hikashop_category ctg, ".$dbprefix."hikashop_taxation taxa, ".$dbprefix."hikashop_tax ftax
		where ctg.category_id=pro.product_tax_id
		and  ctg.category_namekey=taxa.category_namekey
		and taxa.tax_namekey=ftax.tax_namekey) calc_value,
		
		
		(SELECT pric.price_value FROM ".$dbprefix."hikashop_price pric
		where pric.price_product_id=pro.product_id limit 1) product_price,
		
		
		(SELECT cgtg.category_name FROM ".$dbprefix."hikashop_product_category ctg, ".$dbprefix."hikashop_category cgtg
		where ctg.product_id=pro.product_id
		and cgtg.category_type='product'
		and cgtg.category_id=ctg.category_id
		order by ctg.ordering limit 1) category_name,
		
		
		(SELECT cgtg.category_id FROM ".$dbprefix."hikashop_product_category ctg, ".$dbprefix."hikashop_category cgtg
		where ctg.product_id=pro.product_id
		and cgtg.category_type='product'
		and cgtg.category_id=ctg.category_id
		order by ctg.ordering limit 1) category_id,		
		
		
		(SELECT fil.file_path FROM ".$dbprefix."hikashop_file fil
		where fil.file_ref_id=pro.product_id limit 1) file_title,
		
		'' characteristic
		
		FROM ".$dbprefix."hikashop_product pro
		
		where pro.product_published=1
		and FROM_UNIXTIME(pro.product_modified)>'".$lastdate."'	
		and pro.product_type='main'
		
		
		
		
		
		
		
		
		
		
		union all
		
		
		
		
		
		
		
		
		
		
		
		
		SELECT 
		
		
		pro.product_code as product_sku,
		product_id,
		(SELECT ppro.product_name FROM ".$dbprefix."hikashop_product ppro where ppro.product_id=pro.product_parent_id) product_name,
		(SELECT ppro.product_weight FROM ".$dbprefix."hikashop_product ppro where ppro.product_id=pro.product_parent_id) product_weight,
		(SELECT ppro.product_quantity FROM ".$dbprefix."hikashop_product ppro where ppro.product_id=pro.product_parent_id) product_quantity,
		
		
		(SELECT ctg.category_name FROM ".$dbprefix."hikashop_category ctg where ctg.category_type='manufacturer' and category_id=pro.product_manufacturer_id) mf_name,
		
		
		(SELECT ftax.tax_rate*100 FROM ".$dbprefix."hikashop_category ctg, ".$dbprefix."hikashop_taxation taxa, ".$dbprefix."hikashop_tax ftax
		where ctg.category_id=
		
		(SELECT ppro.product_tax_id FROM ".$dbprefix."hikashop_product ppro where ppro.product_id=pro.product_parent_id)
		
		and  ctg.category_namekey=taxa.category_namekey
		and taxa.tax_namekey=ftax.tax_namekey) calc_value,
		
		
		(SELECT pric.price_value FROM ".$dbprefix."hikashop_price pric
		where pric.price_product_id=pro.product_parent_id limit 1) product_price,
		
		
		(SELECT cgtg.category_name FROM ".$dbprefix."hikashop_product_category ctg, ".$dbprefix."hikashop_category cgtg
		where ctg.product_id=pro.product_parent_id
		and cgtg.category_type='product'
		and cgtg.category_id=ctg.category_id
		order by ctg.ordering limit 1) category_name,
		
		
		(SELECT cgtg.category_id FROM ".$dbprefix."hikashop_product_category ctg, ".$dbprefix."hikashop_category cgtg
		where ctg.product_id=pro.product_parent_id
		and cgtg.category_type='product'
		and cgtg.category_id=ctg.category_id
		order by ctg.ordering limit 1) category_id,		
		
		
		(SELECT fil.file_path FROM ".$dbprefix."hikashop_file fil
		where fil.file_ref_id=pro.product_parent_id limit 1) file_title,
		
		
		(SELECT chrt.characteristic_value FROM ".$dbprefix."hikashop_variant vrn,".$dbprefix."hikashop_characteristic chrt
		WHERE vrn.variant_product_id=pro.product_id
		and chrt.characteristic_id=vrn.variant_characteristic_id limit 1) characteristic
		
		FROM ".$dbprefix."hikashop_product pro
		
		where 
		
		(SELECT ppro.product_published FROM ".$dbprefix."hikashop_product ppro where ppro.product_id=pro.product_parent_id)=1
		
		and FROM_UNIXTIME(pro.product_modified)>'".$lastdate."'	
		and pro.product_type='variant'
		";
	
	
	//echo $query;
	//	file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);
	
	
	$data = mysql_query($query) or die(mysql_error()); 
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		
		$characteristic=$alldata['characteristic'];  
		$product_id=$alldata['product_id'];  	 	
		$id=$alldata['product_sku'];  	 	
		$idmpn=$alldata['product_mpn'];  	 
		$name1= str_ireplace("|",' - ',$alldata['product_name'].' '.$characteristic);        
		$name2= $alldata['attribute']; 
		$taxrate=$alldata['calc_value'];
		$manu="$manufacturer:".$alldata['mf_name'].'\n';
		$product_weight=$alldata['product_weight'];
		$product_quantity=$alldata['product_quantity'];
		
		
		
		
		
		//$monada= $alldata['product_unit']; 
		
		//$price=$alldata['product_price']+($alldata['product_price']*$taxrate);
		$taxrate=number_format($alldata['calc_value'], 2, ',', '');	 	
		$price=number_format($alldata['product_price']+ (($alldata['product_price']*$taxrate)/100), 2, ',', '');
		
		$product_quantity=number_format($alldata['product_quantity'], 2, ',', '');
		$product_weight=number_format($alldata['product_weight'], 2, ',', '');
		
		//+ (($alldata['product_price']*$taxrate)/100)                                 
		//, 2, ',', '');
		
		
		
		// $price=number_format($price, 2, ',', '');
		$category= $alldata['category_name']; 
		$category_id= $alldata['category_id']; 
		
		
		$photolink='';
		if ($alldata['file_title']) {
			$photolink=$photourl.$alldata['file_title'];
		} 
		$urllink=$produrl.$product_id;
		//file_put_contents('debug12.log',$manu, FILE_APPEND | LOCK_EX);
		
		
		
		//$taxrate=number_format(100*$taxrate, 2, ',', '');	
		
		echo $product_code_prefix.$id."|".$idmpn.';'.$name1.';'.$manu.';'.$taxrate.';'.$price.";;".$product_quantity.";".$monada.";".$category.";".$photolink.";".$urllink.";".$product_weight.";<br>\n";			 
		
		
		
	}
	////
	
	
	
	
	
}


































if ($action == 'orders') {
	

	
	$query="
	
	
	
	
	SELECT 
order_id,
order_shipping_address_id,
order_user_id,
order_full_price,
order_discount_price,
order_payment_price,
order_payment_method,
order_shipping_price,
order_shipping_method,
FROM_UNIXTIME(order_modified) modified,
comment,
order_number,
(SELECT payment_name FROM ".$dbprefix."hikashop_payment where payment_id=order_payment_id) payment_

,

(SELECT shipping_name FROM ".$dbprefix."hikashop_shipping where shipping_id=order_shipping_id) shipment_



FROM ".$dbprefix."hikashop_order 
where order_status<>'shipped' and  order_status<>'cancelled'
and order_type='sale'

ORDER BY order_id  asc	
	
	
	
	";
	
	//echo $query;
	
	$data = mysql_query($query) or die(mysql_error()); //
	// file_put_contents('debug.log',"SELECT * FROM ".$dbprefix."virtuemart_orders ord,".$dbprefix."virtuemart_paymentmethods_el_gr pay,".$dbprefix."virtuemart_shipmentmethods_el_gr ship  where pay.virtuemart_paymentmethod_id=ord.virtuemart_paymentmethod_id and ship.virtuemart_shipmentmethod_id=ord.virtuemart_shipmentmethod_id and ord.order_status in ('U') and ord.order_tax<>0 " , FILE_APPEND | LOCK_EX);
	
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		$id=$alldata['order_id'];  	 	
		$userid= $alldata['order_shipping_address_id']; 
		
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['modified_on'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['modified'] ; 
		$comment=$alldata['comment'];
		$shipment=$alldata['shipment_'];
		$payment=$alldata['payment_'];
		$order_number=$alldata['order_number'];
		
		$order_shipping_price=number_format($alldata['order_shipping_price'], 2, ',', '');
	    $order_payment_price=number_format($alldata['order_payment_price'], 2, ',', '');
		
		$coupon_discount=number_format(-$alldata['order_discount_price'], 2, ',', '');//
		
			
		$comment=str_ireplace("\r",'',$comment);
		$comment=str_ireplace("\n",' ',$comment);
		$comment=str_ireplace(";",'',$comment);
		
		$comment=$comment.' '.$coupon_discount;
		//	if ($userid) { order_total order_salesPrice  coupon_discount
		

		
		
		if(!$userid>0){
			echo $id.';'.$once_customer_code_prefix.$userid.";".$order_shipping_price.";".$order_payment_price.";0;".$hmera.";".$shipment.' '.$payment.' '.$comment.";<br>\n";
		}
		else{
			
			echo $id.';'.$customer_code_prefix.$userid.";".$order_shipping_price.";".$order_payment_price.";0;".$hmera.";".$order_number,' '.$shipment.' '.$payment.' '.$comment."<br>\n";
			
		}
		

		
		
		
	}
}


























if ($action == 'order') {
	
	
	
	
	
	$query="
	
	SELECT 
order_product_code,
order_product_id,
order_product_name,
order_product_price,
order_product_quantity,
order_product_tax_info,
order_product_tax,
product_id,
ord.order_id,



(SELECT orr.order_discount_price FROM ".$dbprefix."hikashop_order orr where orr.order_id=ord.order_id) disp,

(select sum((orr.order_product_price+orr.order_product_tax)*orr.order_product_quantity) 
FROM ".$dbprefix."hikashop_order_product orr
where orr.order_id=ord.order_id) fulp







FROM ".$dbprefix."hikashop_order_product ord

where ord.order_id=$orderid


		
		";
	
	//	file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);
	
	$data = mysql_query( $query    ) or die(mysql_error()); 
	
	
	
	
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{

$fulp=$alldata['fulp']; 
$disp=$alldata['disp']; 
		$discount=number_format(  (abs($disp)*100)/$fulp, 2, ',', '');





		$description =  str_ireplace("|",' - ',$alldata['order_product_name']);     
		$description =  str_ireplace(">:",'>',$description);     
		$description = strip_tags($description );
		
		
		$product_id = $alldata['order_product_code']; 
		$product_quantity = $alldata['order_product_quantity']; 
		//$amount=number_format($alldata['product_final_price'], 2, ',', '');
		$amount=number_format($alldata['order_product_price']+$alldata['order_product_tax'], 2, ',', '');
		//-
		

		
		$order_id = $alldata['order_id']; 
		
	//	$order_id=$fulp.' ' .$disp;
	
	$taxrate=unserialize($alldata['order_product_tax_info']);	
	
	
	
	$taxr=$taxrate[0]->tax_rate;
	$taxr=number_format($taxr*100, 2, ',', '');	
	
	
		
		//$taxrate=number_format($alldata['calc_value'], 2, ',', '');
		//$monada = $alldata['product_unit']; 
		
		
		
		
		//ΑΝ ΥΠΑΡΞΕΙ ΠΡΟΒΛΗΜΑ ΝΑ ΑΦΑΙΡΕΘΕΙ Η ΓΡΑΜΜΗ
		//$amount=abs($alldata['product_subtotal_discount'])+ $alldata['product_final_price'];
		// ΚΑΙ ΝΑ ΕΝΕΡΓΟΠΟΙΗΘΕΙ:
		//$discount=0;	
		
		
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxr.";".$discount.";;;;".$order_id.";<br>\n";
		
		
		

		
		
		
		
		
	}
	
	
}




















































//shipped
if ($action == 'confirmorder') {
	
	$data = mysql_query("update ".$dbprefix."hikashop_order  set order_status='shipped' where order_id in (".$orderid.")") or die(mysql_error());	
	echo $hmera;
	
}


////canceled
if ($action == 'cancelorder') {
	
	$data = mysql_query("update ".$dbprefix."hikashop_order  set order_status='canceled' where order_id in (".$orderid.")") or die(mysql_error());	
	echo $hmera;
	
}





if ($action == 'updatestock') {

echo  $stock.'#ok';
//file_put_contents($logfile,'ok', FILE_APPEND | LOCK_EX);
$query="update ".$dbprefix."hikashop_product set product_quantity='".$stock."' where product_code='".substr($productid,strlen($product_code_prefix))."'";
file_put_contents($logfile,$query."\n", FILE_APPEND | LOCK_EX);
		


$data = mysql_query($query) or die(mysql_error());


echo substr($productid,strlen($product_code_prefix))."--".$hmera;

}












if ($action == 'orderstatus') {
	
	$status=$_REQUEST['status'];   
	
	//D Άφιξη Δευτέρα
	//A Αναμονή
	//K Έτοιμη προς παράδοση/παραλαβή
	//X Ακύρωση
	//S Ολοκληρωμένη
	$nstatus='';
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
	
	
	
	$words = explode('_', $orderid);
	
	$productid=$words[1];
	
	if (substr($productid,0,strlen($product_code_prefix))==$product_code_prefix) {
		$productid=substr( $productid,strlen($product_code_prefix),        strlen($productid)-strlen($product_code_prefix)                );
	}
	
	
	
	
	$orderid=$words[0];
	
	if ($nstatus) {
		
		$data = mysql_query("UPDATE ".$dbprefix."virtuemart_order_items SET order_status = '".$nstatus."' WHERE order_item_sku='".$productid."' and virtuemart_order_id = '".$orderid."'") or die(mysql_error());
		
	}
	
	echo $hmera.'##'.$nstatus.'##'.$orderid.'##'.$productid.'##';
	
}










////////////////////////////////////
////////////////////////////////////
////////////////////////////////////



if ($action == 'redirect') {
	
	//customer_code_prefix
	
	//http://gamerules.gr/1114
	
	
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







?> 		