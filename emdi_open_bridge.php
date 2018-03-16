<?php
error_reporting(0);
/*------------------------------------------------------------------------
# EMDI - OPENCART BRIDGE by SBZ systems - Solon Zenetzis - version 1.6
# ------------------------------------------------------------------------
# author    SBZ systems - Solon Zenetzis
# copyright Copyright (C) 2013-2014 sbzsystems.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.sbzsystems.com
# Technical Support:  Forum - http://www.sbzsystems.com
-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');
//error_reporting(0);

require 'config.php';

$logfile = 'emdibridge.log';
$offset= '';
$host = DB_HOSTNAME;
$user = DB_USERNAME;
$password = DB_PASSWORD;
$db = DB_DATABASE;
$dbprefix = DB_PREFIX;
$product_code_prefix='';
$customer_code_prefix='IC';
$onetime_customer_code_prefix='AC';
$lang_code='gr';
$lang_id=2;
$store_id=0;
$tmp_path = DIR_SYSTEM.'tmp';
$timezone=$config->offset; 
$passkey='';
$relatedchar='^';
$addonid='PRO';

//////////////
$measurement='ΤΕΜΑΧΙΑ';
$measurementaddon='ΠΡΟΣΘΕΤΑ';

//$vat_field='ΑΦΜ';
//$tax_office_field='ΔΟΥ';
//$maintax=23;
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
mysql_set_charset('utf8',$link); 

$photourl=HTTP_IMAGE;	
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
$data = mysql_query("SELECT 
cust.customer_id as user_id,

max(email) as email,max(telephone) as b_phone,max(fax) as phone,
max(addr.firstname) as firstname,max(addr.lastname) as lastname,
max(cust.firstname),max(cust.lastname),

max(addr.company) as company,
max(addr.company_id) as doy,max(addr.tax_id) as afm,
max(addr.address_1) as b_address,


max(addr.address_2) as c_address,
max(addr.city) as b_city,max(addr.postcode) as b_zipcode,
max(contr.name) as b_country,max(zone.name) as b_state,
cust.date_added as dd


FROM ".$dbprefix."customer as cust


left join ".$dbprefix."address as addr
on addr.customer_id=cust.customer_id

left join ".$dbprefix."country as contr
on contr.country_id=addr.country_id

left join ".$dbprefix."zone as zone
on zone.zone_id=addr.zone_id

where cust.status=1
and cust.date_added>'".date('Y-m-d H:i:s', $lastdate)."'


group by cust.customer_id") or die(mysql_error());
/////////////


										
echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
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

/////// ONE TIME CUSTOMERS

/////////////
$data = mysql_query("SELECT 
cust.order_id as user_id,

max(email) as email,max(telephone) as b_phone,max(fax) as phone,
max(payment_firstname) as firstname,max(payment_lastname) as lastname,

max(payment_company) as company,
max(payment_company_id) as doy,max(payment_tax_id) as afm,max(payment_address_1) as b_address,
max(payment_address_2) as c_address,
max(payment_city) as b_city,max(payment_postcode) as b_zipcode,
max(contr.name) as b_country,max(zone.name) as b_state,
cust.date_added as dd


FROM ".$dbprefix."order as cust


left join ".$dbprefix."address as addr
on addr.customer_id=cust.customer_id

left join ".$dbprefix."country as contr
on contr.country_id=addr.country_id

left join ".$dbprefix."zone as zone
on zone.zone_id=addr.zone_id

where cust.customer_id=0
and cust.date_added>'".date('Y-m-d H:i:s', $lastdate)."'

group by cust.order_id
"

) or die(mysql_error());
/////////////




		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['user_id'];  	 	
  	 	$firstname= $alldata['firstname']; 
  	 	$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['b_address'];  	 	
		$tu=$alldata['c_address']; 
		$postcode=$alldata['date_added'];  	 	
		$country=$alldata['b_country'];  	 	
		$state=$alldata['b_state'];  	 	
		$city=$alldata['b_city'];  	 	
		$phonenumber=$alldata['b_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		$companyname=$alldata['company'];  	 	
		$afm=$alldata['afm'];  	 	
		$doy=$alldata['doy'];  	 	

		
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

/*
	(select GROUP_CONCAT(prov.o_sku) from
 ".$dbprefix."product_option_value prov
 where prov.product_id=descr.product_id) as barcodes


 ,
 */
//---------------------------
$data = mysql_query("
SELECT pro.model as product_code,
descr.name as product,
tra.rate as rate_value,
pro.price as price,
group_concat(cdes.name) as category
,pro.date_modified as dd
,pro.image
,pro.product_id
,


 
 
 (SELECT prosp.price FROM oc_product_special prosp
where prosp.product_id=descr.product_id
and prosp.customer_group_id=2
ORDER BY prosp.product_special_id DESC
limit 1)  as priced

FROM  ".$dbprefix."product_description as descr


left join ".$dbprefix."product as pro
on pro.product_id=descr.product_id

left join ".$dbprefix."language as langu
on langu.language_id=descr.language_id

left join ".$dbprefix."product_to_category as ptc
on ptc.product_id=descr.product_id

left join ".$dbprefix."category_description as cdes
on cdes.category_id=ptc.category_id

left join ".$dbprefix."tax_rule as tru
on tru.tax_class_id=pro.tax_class_id and priority=1

left join ".$dbprefix."tax_rate as tra
on tra.tax_rate_id=tru.tax_rate_id







where 
langu.code='".$lang_code."'
and cdes.language_id=descr.language_id 

and (pro.date_added>'".date('Y-m-d H:i:s', $lastdate)."' or pro.date_modified>'".date('Y-m-d H:i:s', $lastdate)."')

group by descr.product_id
") or die(mysql_error()); 
//---------------------------
//date('Y-m-d H:i:s', $lastdate)

echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['product_code'];  	 	
  	 	$name1= $alldata['product']; 
  	 	//$name2= $alldata['attribute']; 
  	 	$taxrate=$alldata['rate_value'];
		//$monada= $alldata['product_unit']; 

		//$paytype=substr($alldata['paytype'],0,1);
		//$type=substr($alldata['keym'],0,1);
		//$servertype=$alldata['servertype'];  	
	    $taxrate=number_format($taxrate, 2, ',', '');	
		$price=$alldata['price'];
	    $price=number_format($price, 2, ',', '');
		$category= $alldata['category']; 
		//$category_id= $alldata['category_id']; 
		$priced=$alldata['priced'];
				
		//$barcodes='SKU:'.$alldata['barcodes'].'\n'; 		
		//$id=$id.'|'.$alldata['barcodes']; 		
		
		if ($priced) { $price=$priced; }
		
				
		echo $product_code_prefix.$id.';'.$name1.';'.$barcodes.';'.$taxrate.';'.$price.";;;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";
		

		//additional products based on options
		$arr = explode(',', $alldata['barcodes']);
		if (count($arr)>0) {
		
			foreach ($arr as $value) {
				if (($value!=$id) && ($value)) {
					echo $product_code_prefix.$value.';'.$name1.';'.$barcodes.';'.$taxrate.';'.$price.";;;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";	
				}
			}
		}
}
////





}







































if ($action == 'orders2') {



$data = mysql_query("
SELECT 
ord.order_id as order_id,
ord.customer_id as user_id,
ord.date_modified as timestamp,
ord.comment,

(select ordt.text from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
and ordt.code='shipping' limit 0,1) as shipping

FROM ".$dbprefix."order as ord


group by ord.order_id
") or die(mysql_error()); //

//where not ord.order_status_id in (0,5,7)

echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['order_id'];  	 	
  	 	$userid= $alldata['user_id']; 
  	    //$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		$shipping=   str_replace('€','',       $alldata['shipping']); 
		$comment=$alldata['comment'] ;
		
		if ($userid==0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";0;0;".$hmera.";".$comment.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";0;0;".$hmera.";".$comment.";<br>\n";
		}
		
}
}















if ($action == 'orders') {



$data = mysql_query("
SELECT 
ord.order_id as order_id,
ord.customer_id as user_id,
ord.date_modified as timestamp,
ord.comment,

(select ordt.text from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
and ordt.code='shipping' limit 0,1) as shipping

FROM ".$dbprefix."order as ord
where
not ord.order_status_id in (0,5,7)
group by ord.order_id
") or die(mysql_error()); //


echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['order_id'];  	 	
  	 	$userid= $alldata['user_id']; 
  	    //$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		$shipping=   str_replace('€','',       $alldata['shipping']); 
		$comment=$alldata['comment'] ;
		
		
		if ($userid==0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";0;0;".$hmera.";".$comment.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";0;0;".$hmera.";".$comment.";<br>\n";
		}
		
}
}


























if ($action == 'order') {
////order


$data = mysql_query("
SELECT 
ord.order_id as order_id,
ord.name as product,
pro.model as product_code,
ord.total as price,
tax.rate as rate_value,
ord.quantity as amount,
ord.product_id as product_id
FROM ".$dbprefix."order_product as ord
left join ".$dbprefix."product as pro on pro.product_id=ord.product_id
left join ".$dbprefix."tax_rule as rul on rul.tax_class_id=pro.tax_class_id
left join ".$dbprefix."tax_rate as tax on tax.tax_rate_id=rul.tax_rate_id

where ord.order_id=".$orderid) or die(mysql_error()); 


echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
  	 	$description = $alldata['product']; 
  	 	$product_id = $alldata['product_code']; 
		$product_quantity = $alldata['amount']; 
		$amount=number_format($alldata['price']/$product_quantity, 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;		
		
		$taxrate=number_format($alldata['rate_value'], 2, ',', '');	
		
	 	$monada = $measurement; 
		$product_attribute = $alldata['extra']; 
		
		
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		////split prostheta   


$datap = mysql_query("
SELECT 
ord.order_id as order_id,
concat(ord.name,':',ord.value) as product,
'".$addonid."' as product_code,
0 as price,
0 as rate_value,
0 as amount
FROM ".$dbprefix."order_option as ord
left join ".$dbprefix."order_product as pord on pord.order_product_id=ord.order_product_id

where ord.order_id=".$orderid." and pord.product_id=".$alldata['product_id']

."
group by product_option_value_id
order by order_option_id asc
"






) or die(mysql_error()); 
		
		
		
		//echo $alldata['product_id'].'###';
		while($alldatap = mysql_fetch_array( $datap ))
		{
			$description = $alldatap['product']; 
			$product_id = $alldatap['product_code']; 
			$product_quantity = $alldatap['amount']; 
			$amount=number_format($alldatap['price']/$product_quantity, 2, ',', '');
			$discount=0;		
		
			$taxrate=number_format($alldatap['rate_value'], 2, ',', '');	
		
			$monada = $measurementaddon; 
			$product_attribute = $alldatap['extra']; 
		
		
		
			echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
















			
		
		
		}
		
		
		
		
		
		
		
		
		
		

		
		
		
}


}


















































 


if ($action == 'confirmorder') {

$data = mysql_query("update ".$dbprefix."order set order_status_id=5 where order_id in (".$orderid.")") or die(mysql_error());

echo $hmera;
}



if ($action == 'updatestock') {
//echo "update ".$dbprefix."product set quantity=".$stock."  where product_id='".substr($productid,strlen($product_code_prefix))."'"; 
$data = mysql_query("update ".$dbprefix."product set quantity=".$stock."  where model='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
		
echo $hmera;
}



if ($action == 'cancelorder') {

$data = mysql_query("update ".$dbprefix."order set order_status_id=7 where order_id in (".$orderid.")") or die(mysql_error());
				
echo $hmera;

}



//header("Location: $goto?expdate=$nextduedate");




















if ($action == 'redirect') {
	
//customer_code_prefix
	

	// EDIT PRODUCT
	if ($productid) {
		$data = mysql_query("
		SELECT * FROM ".$dbprefix."product WHERE model = '".$productid."'
		") or die(mysql_error());

		//echo mysql_num_rows($data);

		if (mysql_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysql_fetch_array( $data ))
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
$data = mysql_query("
select * from ".$dbprefix."tax_rule as tru
left join ".$dbprefix."tax_rate as tra on tru.tax_rate_id=tra.tax_rate_id
left join ".$dbprefix."tax_class as tcl on  tru.tax_class_id=tcl.tax_class_id

where title='EMDI $tax'

") or die(mysql_error());








if (mysql_num_rows($data)==0) {
	
	//ADD DEFAULT EMDI TAX CLASS IF DOESN'T EXIST
	$data = mysql_query("
	INSERT INTO ".$dbprefix."tax_class (tax_class_id, title, description, date_added, date_modified) 
	VALUES (NULL, 'EMDI $tax', 'EMDI $tax', now(), '0000-00-00 00:00:00');
	") or die(mysql_error());			
		
	
	//GET CLASS ID
	$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
	while($alldata = mysql_fetch_array( $data ))
	{
			$classid=$alldata['id'];  	 	
			break;		
	}	
		
	//ADD TAX	
	$data = mysql_query("
	INSERT INTO ".$dbprefix."tax_rate (tax_rate_id, geo_zone_id, name, rate, type, date_added, date_modified) 
	VALUES (NULL, '0', '$tax%', '$tax', 'P', now(), '0000-00-00 00:00:00');
	") or die(mysql_error());			
	
	
	//GET TAX ID
	$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
	while($alldata = mysql_fetch_array( $data ))
	{
			$taxid=$alldata['id'];  	 	
			break;		
	}	
	
	//ADD RULE
	$data = mysql_query("
	INSERT INTO ".$dbprefix."tax_rule (tax_rule_id, tax_class_id, tax_rate_id, based, priority) 
	VALUES (NULL, '$classid', '$taxid', 'payment', '1');
	") or die(mysql_error());			

	
	
	
	
} else {
	//GET TAX CLASS IF DOESN'T EXIST
	while($alldata = mysql_fetch_array( $data ))
	{
			$classid=$alldata['tax_class_id'];  	 	
			break;		
	}	
}
//




	
	
	
 
	
	
	
	
	
	
	
	
// CREATE CATEGORY IF DOES NOT EXIST
$data = mysql_query("
SELECT * FROM ".$dbprefix."category WHERE category_id=$cat
") or die(mysql_error());
if (mysql_num_rows($data)==0) {




	$data = mysql_query("
		INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
		VALUES 
		('$cat', NULL, '0', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
	") or die(mysql_error());			

	//ADD CATEGORY DESCRIPTION
	$data = mysql_query("
		INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
		VALUES ('$cat', '$lang_id', '$cattitle', '', '', '');	
	") or die(mysql_error());			

	//ADD CATEGORY STORE
	$data = mysql_query("
		INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
		VALUES ('$cat', '$store_id');
	") or die(mysql_error());			


	//ADD CATEGORY PATH
	$data = mysql_query("
		INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
		VALUES ('$cat', '$cat', '0')
	") or die(mysql_error());			





}
//






	if ($subcat) {

// CREATE SUBCATEGORY IF DOES NOT EXIST
$data = mysql_query("
SELECT * FROM ".$dbprefix."category WHERE category_id=$subcat
") or die(mysql_error());
if (mysql_num_rows($data)==0) {




	$data = mysql_query("
		INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
		VALUES 
		('$subcat', NULL, '$cat', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
	") or die(mysql_error());			

	//ADD SUBCATEGORY DESCRIPTION
	$data = mysql_query("
		INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
		VALUES ('$subcat', '$lang_id', '$subcattitle', '', '', '');	
	") or die(mysql_error());			

	//ADD SUBCATEGORY STORE
	$data = mysql_query("
		INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
		VALUES ('$subcat', '$store_id');
	") or die(mysql_error());			


	//ADD SUBCATEGORY CATEGORY PATH
	$data = mysql_query("
		INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
		VALUES ('$subcat', '$cat', '1')
	") or die(mysql_error());			

	//ADD SUBCATEGORY  PATH 
	$data = mysql_query("
		INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
		VALUES ('$subcat', '$subcat', '2')
	") or die(mysql_error());			




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
$data = mysql_query("
SELECT * FROM ".$dbprefix."product WHERE model = '".$productid."'
") or die(mysql_error());
if (mysql_num_rows($data)==0) {

	//IF PRODUCT DOES NOT EXIST			
	$data = mysql_query("				
	INSERT INTO ".$dbprefix."product (product_id, model, sku, upc, ean, jan, isbn, mpn, location, quantity, 
	stock_status_id, image, manufacturer_id, shipping, price, points, tax_class_id, date_available, weight, 
	weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, status, date_added, 
	date_modified, viewed) 
	VALUES (
	NULL, '$productid', '', '', '', '', '', '', '', '0', '0', 'data/".$_FILES["file"]["name"]."', '0', '1', '$price', '0', '$classid', '10-10-2014', 
	'0.00000000', 0, '0.00000000', '0.00000000', '0.00000000',
	0, '1', '1', 0, 1, now(), '0000-00-00 00:00:00',0);				
				
	") or die(mysql_error());				
	
	
	//GET PRODCUT ID
	$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
	while($alldata = mysql_fetch_array( $data ))
	{
			$id=$alldata['id'];  	 	
			break;		
	}	

	
	//ADD ADDITIONAL IMAGE		
	/*	
	$data = mysql_query("
	INSERT INTO ".$dbprefix."product_image (product_image_id, product_id, image, sort_order) 
	VALUES (NULL, '$id', 'data/".$_FILES["file"]["name"]."', '');
	") or die(mysql_error());					
	*/

		
	//ADD DESCRIPTION       
	$data = mysql_query("
	INSERT INTO ".$dbprefix."product_description (product_id, language_id, name, 
	description, meta_description, meta_keyword, tag) 
	VALUES ('$id', '$lang_id', '$title', '$descr', '', '', '');
	") or die(mysql_error());					
				
		
	//ADD CATEGORY
	$data = mysql_query("
	INSERT INTO ".$dbprefix."product_to_category (product_id, category_id) 
	VALUES ('$id', '$subcat');
	") or die(mysql_error());					


	//ADD STORE                 
	$data = mysql_query("
	INSERT INTO ".$dbprefix."product_to_store (product_id, store_id) 
	VALUES ('$id', '$store_id');
	") or die(mysql_error());					
				
				
				
} else {
	//IF PRODUCT EXISTS UPDATE FIELDS
//GET TAX CLASS IF DOESN'T EXIST
	while($alldata = mysql_fetch_array( $data ))
	{
			$id=$alldata['product_id'];  	 	
			break;		
	}	
	/*
//UPDATE PRODUCT NO PHOTO!!!
	$data = mysql_query("				
	update ".$dbprefix."product set price='$price', tax_class_id='$classid', date_modified=now()
	where product_id=$id
	") or die(mysql_error());				
	
	*/
	//UPDATE PRODUCT
	$data = mysql_query("				
	update ".$dbprefix."product set image='data/".$_FILES["file"]["name"]."', price='$price', tax_class_id='$classid', date_modified=now()
	where product_id=$id
	") or die(mysql_error());				
	
		
	//UPDATE DESCRIPTION       
	$data = mysql_query("
	update ".$dbprefix."product_description set name='$title', description='$descr'
	where product_id=$id
	") or die(mysql_error());					
				
		
	//ADD CATEGORY
	$data = mysql_query("
	update ".$dbprefix."product_to_category set category_id='$subcat'
	where product_id=$id
	") or die(mysql_error());					


	
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