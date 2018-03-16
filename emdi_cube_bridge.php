<?php
/*------------------------------------------------------------------------
# EMDI - CubeCart BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
# ------------------------------------------------------------------------
# author    SBZ systems - Solon Zenetzis
# copyright Copyright (C) 2015 sbzsystems.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.sbzsystems.com
# Technical Support:  Forum - http://www.sbzsystems.com
-------------------------------------------------------------------------*/

//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header('Content-Type: text/html; charset=UTF-8');

require 'includes/global.inc.php';

$logfile = 'emdibridge.log';
$offset= '';
$host = $glob['dbhost'];
$user = $glob['dbusername'];
$password = $glob['dbpassword'];
$db = $glob['dbdatabase'];
$dbprefix = $glob['dbprefix'];
$product_code_prefix='';
$customer_code_prefix='IC';
$onetime_customer_code_prefix='AC';
$lang_code='gr';
$lang_id=2;
$store_id=0;
$tmp_path =  $glob['rootDir'].'tmp';
$one_time_cust_id=14;
//$timezone=$config->offset; 
$passkey='abc123';
$relatedchar='^';
$addonid='PRO';
//echo $tmp_path;
//////////////
$measurement='телавиа';
$measurementaddon='пяосхета';

//$vat_field='ажл';
//$tax_office_field='доу';
//$maintax=23;
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
//mysql_set_charset('utf8',$link); 

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

cust.email, phone as b_phone,mobile as phone,
firstName as firstname,lastName as lastname,

add_1 as b_address,
add_2 as c_address,

town as b_city,
postcode as b_zipcode,
country as b_country,
county as b_state


FROM ".$dbprefix."CubeCart_customer as cust

where 
 modified>'".date('Y-m-d H:i:s', $lastdate)."' 


") or die(mysql_error());
/////////////
//where cust.status=1
//and cust.date_added>'".date('Y-m-d H:i:s', $lastdate)."'


										
echo "йыдийос;омола;епихето;диеухумсг;тй;выяа;покг/молос;пеяиовг;тгкежымо;йимгто;EMAIL;ажл;доу;епымулиа;епаццекла;цкысса;тх;<br>\n";
		
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
cust.cart_order_id as user_id,

cust.email, phone as b_phone,mobile as phone,
name_d as lastname,final_company_name as company,final_company_title as company2,

add_1_d as b_address,
add_2_d as c_address,

town_d as b_city,
postcode_d as b_zipcode,
country_d as b_country,
county_d as b_state


FROM ".$dbprefix."CubeCart_order_sum as cust

where customer_id=".$one_time_cust_id."

") or die(mysql_error());
/////////////
//where cust.status=1
//and cust.date_added>'".date('Y-m-d H:i:s', $lastdate)."'


										
echo "йыдийос;омола;епихето;диеухумсг;тй;выяа;покг/молос;пеяиовг;тгкежымо;йимгто;EMAIL;ажл;доу;епымулиа;епаццекла;цкысса;тх;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['user_id'];  	 	
  	 	//$firstname= $alldata['firstname']; 
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
		$companyname=$alldata['company'].' '.$alldata['company2'];  	 	
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


//---------------------------
$data = mysql_query("
SELECT 

productCode as product_code,
name as product,
pro.percent as rate_value,
price as price,
group_concat(cat.cat_name) as category


FROM  ".$dbprefix."CubeCart_inventory as descr


left join ".$dbprefix."CubeCart_taxes as pro
on pro.id=descr.taxType


left join ".$dbprefix."CubeCart_category as cat
on cat.cat_id=descr.cat_id

where 
 modified>'".date('Y-m-d H:i:s', $lastdate)."' 

group by product_code
") or die(mysql_error()); 
//---------------------------
//,pro.date_modified as dd

//where 
//langu.code='".$lang_code."'
//and cdes.language_id=descr.language_id 
//and (pro.date_added>'".date('Y-m-d H:i:s', $lastdate)."' or pro.date_modified>'".date('Y-m-d H:i:s', $lastdate)."')


//date('Y-m-d H:i:s', $lastdate)

echo "йыдийос;пеяицяажг1;пеяицяажг2;жпа;тилг1;тилг2;диахесилотгта;ломада;йатгцояиа<br>\n";
		
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
				
		echo $product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$measurement.";".$category.";<br>\n";			 
		
}
////





}




















if ($action == 'orders') {



$data = mysql_query("
SELECT 
ord.cart_order_id as order_id,
ord.customer_id as user_id,
ord.time as timestamp,
ord.customer_comments as comment,
ord.total_ship as shipping,
ord.shipMethod,
ord.gateway

FROM ".$dbprefix."CubeCart_order_sum as ord
where ord.status in (4)
group by order_id
") or die(mysql_error()); //

//

//(select ordt.text from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
//and ordt.code='shipping' limit 0,1) as shipping

echo "йыдийос паяаццекиас;йыдийос пекатг;йостос летажояийым;йостос амтийатабокгс;ейптысг;глеяолгмиа;свокио;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{

//date("Y-m-d H:i:s", 1388516401);

		$id=$alldata['order_id'];  	 	
  	 	$userid= $alldata['user_id']; 
  	    //$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		$shipping=   str_replace('─','',       $alldata['shipping']); 
		$shipping=   str_replace('.',',',       $shipping); 
		
		
		
		$comment=$alldata['comment'].' '.explode(' ',trim($alldata['shipMethod']))[0].' '.explode(' ',trim($alldata['gateway']))[0] ;
		
		
		if ($userid==$one_time_cust_id) {
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
ord.cart_order_id as order_id,
ord.name as product,
ord.productCode as product_code,
ord.price as price,
0 as rate_value,
ord.quantity as amount,
ord.productId as product_id
FROM ".$dbprefix."CubeCart_order_inv as ord


where ord.cart_order_id='".$orderid."'") or die(mysql_error()); 


echo "йыдийос;пеяицяажг1;пеяицяажг2;пеяицяажг3;посотгта;ломада;тилг;жпа;ейптысг;<br>\n";
		
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
/*

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
		
		
			
	*/
		
		
		
		
		
		
		

		
		
		
}


}














































if ($action == 'confirmorder') {

//$data = mysql_query("update ".$dbprefix."CubeCart_order_sum set status=3 where cart_order_id in (".$orderid.")") or die(mysql_error());

echo $hmera;
}



if ($action == 'updatestock') {

$data = mysql_query("update ".$dbprefix."CubeCart_inventory set stock_level=".$stock."  where productCode='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
		
echo $hmera;
}



if ($action == 'cancelorder') {

$data = mysql_query("update ".$dbprefix."CubeCart_order_sum set status_id=8 where cart_order_id in (".$orderid.")") or die(mysql_error());
				
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

















/*
if ($action == 'uploadproduct') {



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


*/


function base_enc($encoded) {
	$result='';
	for($i=0, $len=strlen($encoded); $i<$len; $i+=4){
		$result=$result.base64_decode( substr($encoded, $i, 4) );
	}
	return $result;
}


?> 