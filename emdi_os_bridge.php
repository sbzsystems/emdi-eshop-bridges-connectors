<?php
/*------------------------------------------------------------------------
# EMDI - osCommerce BRIDGE by SBZ systems - Solon Zenetzis - version 1.1
# ------------------------------------------------------------------------
# author    SBZ systems - Solon Zenetzis
# copyright Copyright (C) 2014 sbzsystems.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.sbzsystems.com
# Technical Support:  Forum - http://www.sbzsystems.com
-------------------------------------------------------------------------*/
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');

require 'includes/config.php';

$admin_alias='9n5sibestqbsfz4u';
$offset= '';
$host = DB_SERVER;
$user = DB_SERVER_USERNAME;
$password = DB_SERVER_PASSWORD;
$db = DB_DATABASE;
//$dbprefix = DB_PREFIX;
$product_code_prefix='';
$customer_code_prefix='IC';
$onetime_customer_code_prefix='AC';
$lang_code='el';
$lang_id=1;
//$store_id=0;
$tmp_path = DIR_FS_CATALOG.'temp';
//$timezone=$config->offset; 
$passkey='';
$relatedchar='^';


//echo $password;
//exit;

//////////////
$measurement='ΤΕΜΑΧΙΑ';
//$vat_field='ΑΦΜ';
//$tax_office_field='ΔΟΥ';
//$maintax=23;
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
mysql_set_charset('utf8',$link); 

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
$data = mysql_query("

SELECT * FROM customers,address_book
left join countries on countries.countries_id=address_book.entry_country_id

where customers.customers_id=address_book.customers_id

and (customers_dob>'".date('Y-m-d H:i:s', $lastdate)."' or ".$lastdate."=0)


") or die(mysql_error());

/////////////


										
echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['customers_id'];  	 	
  	 	$firstname= $alldata['customers_firstname']; 
  	 	$lastname=$alldata['customers_lastname'];  	 	
		$address1=$alldata['entry_street_address'];  	 	
		$postcode=$alldata['entry_postcode'];  	 	
		$country=$alldata['countries_name'];  	 	
		$state=$alldata['entry_state'];  	 	
		$city=$alldata['entry_city'];  	 	
		$phonenumber=$alldata['customers_telephone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['customers_email_address'];  	 	
		$companyname=$alldata['entry_company'];  	 	
		$afm=$alldata['afm'];  	 	
		$doy=$alldata['doy'];  	 	
		//$occupation=$alldata['doy'];  	 	

		
		echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$occupation.';'.$language,";<br>\n";
}















////////////////CUSTOMERS FROM ORDERS
/////////////
$data = mysql_query("

SELECT * FROM orders
where not orders.customers_id in (select customers_id from customers )
	

and (last_modified>'".date('Y-m-d H:i:s', $lastdate)."' or ".$lastdate."=0)


") or die(mysql_error());

/////////////


										
//echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['customers_id'];  	 	
  	 	$firstname= $alldata['customers_name']; 
  	 	$lastname=$alldata['customers_name'];  	 	
		$address1=$alldata['customers_address'];  	 	
		$postcode=$alldata['customers_postcode'];  	 	
		$country=$alldata['customers_country'];  	 	
		$state=$alldata['customers_state'];  	 	
		$city=$alldata['customers_city'];  	 	
		$phonenumber=$alldata['customers_telephone'];  	 	
		//$mobile=$alldata['phone'];  	 	
		$email=$alldata['customers_email_address'];  	 	
		$companyname=$alldata['customers_company'];  	 	
		$afm=$alldata['afm'];  	 	
		$doy=$alldata['doy'];  	 	
		//$occupation=$alldata['doy'];  	 	

		
		echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$occupation.';'.$language,";<br>\n";
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
SELECT * FROM products

left join products_description on 
products.products_id=products_description.products_id

left join products_to_categories on 
products_to_categories.products_id=products_description.products_id

left join categories_description on categories_description.categories_id=products_to_categories.categories_id

left join tax_rates on  tax_rates.tax_class_id=products.products_tax_class_id

and (products_date_added>'".date('Y-m-d H:i:s', $lastdate)."' or products_last_modified>'".date('Y-m-d H:i:s', $lastdate)."')
and  products_description.language_id =$lang_id
group by products.products_id

") or die(mysql_error()); 
//---------------------------
//date('Y-m-d H:i:s', $lastdate)

echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['products_model'];  	 	
  	 	$name1= $alldata['products_name']; 
  	 	//$name2= $alldata['attribute']; 
  	 	$taxrate=$alldata['tax_rate'];
		//$monada= $alldata['product_unit']; 

		//$paytype=substr($alldata['paytype'],0,1);
		//$type=substr($alldata['keym'],0,1);
		//$servertype=$alldata['servertype'];  	
	    $taxrate=number_format($taxrate, 2, ',', '');	
		$price=$alldata['products_price'];
	    $price=number_format($price, 2, ',', '');
		$category= $alldata['categories_name']; 
		//$category_id= $alldata['category_id']; 
				
		echo $product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$measurement.";".$category.";<br>\n";			 
		
}
////





}


































if ($action == 'orders') {



$data = mysql_query("
SELECT *
FROM orders
where orders_status<3

") or die(mysql_error()); //


echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['orders_id'];  	 	
  	 	$userid= $alldata['customers_id']; 
  	    //$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['date_purchased'] ;
		$shipping=0;//   str_replace('€','',       $alldata['shipping']); 
		
		
		if ($userid==0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";0;0;".$hmera.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";0;0;".$hmera.";<br>\n";
		}
		
}
}


























if ($action == 'order') {
////order


$data = mysql_query("

SELECT * FROM orders_products
where orders_id=".$orderid) or die(mysql_error()); 


echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
  	 	$description = $alldata['products_name']; 
  	 	$product_id = $alldata['products_model']; 
		$product_quantity = $alldata['products_quantity']; 
		$amount=number_format($alldata['final_price']/$product_quantity, 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;		
		
		$taxrate=number_format($alldata['products_tax'], 2, ',', '');	
		
	 	$monada = $measurement; 
		//$product_attribute = $alldata['extra']; 
		
		
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		////split prostheta   
		/*
		$words = preg_split('/;/', $product_attribute);
			
		$sel=0; $prv=''; $cou=0;
		foreach ($words as $k => $word) {

			preg_match('/"([^"]+)"/', $word, $result);		
					
			if ($sel==1) {
				if ($cou>0) {
					echo 'PRO;'.$prv.':'.$result[1].";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;<br>\n";
					$prv='';
					$cou=0;					
				} else {
					//echo 'PRO;'.$prv.':'.$result[1].";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;<br>\n";
					$prv=$result[1];
					$cou++;
				}
				$sel=0;				
			}
	
			if ((stripos($word, "option_name") !== false) || (stripos($word, "variant_name") !== false)) { $sel=1; }
			
			
        } 
		
		*/
		
		
		
}


}


















































 


if ($action == 'confirmorder') {

$data = mysql_query("update orders set orders_status=3 where orders_id in (".$orderid.")") or die(mysql_error());

echo $hmera;
}



if ($action == 'updatestock') {
$data = mysql_query("update products set products_quantity=".$stock."  where products_model='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
		
echo $hmera;
}



if ($action == 'cancelorder') {

$data = mysql_query("update orders set orders_status=4 where orders_id in (".$orderid.")") or die(mysql_error());
				
echo $hmera;

}



//header("Location: $goto?expdate=$nextduedate");




















if ($action == 'redirect') {
	
//customer_code_prefix
	

	// EDIT PRODUCT
	//http://www.realserver.info/oscom/9n5sibestqbsfz4u/categories.php?cPath=3_15&pID=20&action=new_product
	if ($productid) {
		$data = mysql_query("
		
		SELECT * FROM products 
		left join products_description on 
		products.products_id=products_description.products_id		

		left join products_to_categories on 
		products_to_categories.products_id=products_description.products_id
			
		WHERE products_model = '".$productid."'
		") or die(mysql_error());

		//echo mysql_num_rows($data);

		if (mysql_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysql_fetch_array( $data ))
			{
				$id=$alldata['products_id'];  	 	
				$cid=$alldata['categories_id']; 
				break;		
			}	

			header('Location: '.$admin_alias."/categories.php?cPath=".$cid."&pID=".$id."&action=new_product");
		}
	}

	// EDIT CUSTOMER
	if ($customerid) {
		//customer_code_prefix
		$customerid=str_replace($customer_code_prefix,'', $customerid); 
		
		header('Location: '.$admin_alias."/customers.php?page=1&cID=".$customerid."&action=edit");
	
	}


	// EDIT ORDER
	if ($orderid) {
		$orderid=str_replace($relatedchar,'', $orderid); 
		header('Location: '.$admin_alias."/orders.php?page=".$orderid."&oID=2&action=edit");
	
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


$productid = substr(base64_decode($pieces[1]),0,$pieces[0]); 



//$productid = substr(base64_decode($productid),0,$len); 

$title=base64_decode($_REQUEST['title']);
$descr=base64_decode($_REQUEST['descr']);    
$price=$_REQUEST['price'];
$cat=$_REQUEST['cat']+100000;
$subcat=$_REQUEST['subcat'];
$tax=$_REQUEST['tax'];

$cattitle=trim(base64_decode($_REQUEST['cattitle']));      
$subcattitle=trim(base64_decode($_REQUEST['subcattitle']));      



//
//CHECK IF TAX EXISTS ELSE ADD
$data = mysql_query("
select * from oc_tax_rule as tru
left join oc_tax_rate as tra on tru.tax_rate_id=tra.tax_rate_id
left join oc_tax_class as tcl on  tru.tax_class_id=tcl.tax_class_id

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


?> 