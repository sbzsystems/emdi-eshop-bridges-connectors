<?php
	//
/*------------------------------------------------------------------------
		# EMDI - INTERNETI BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2019 sbzsystems.com. All Rights Reserved.
		# @license - https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: https://www.sbzsystems.com
		# Technical Support:  Forum - https://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');


function friendurl($title){
	$title = urlencode(str_replace(array(" / ", " - ", " ", "/", "&amp;", "---", "--", ","), array("-", "-", "-", "-", "-", "-", "-", ""), strip_all(mb_strtolower(strip_tags($title), 'utf-8'))));
	if(mb_substr($title, -1) == "-")
	{
		$title = substr($title, 0, -1);
	}
	return $title;
}

function strip_all($str)
{
//$str = strtr($str, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
$str = stripslashes($str);
$str = str_replace("\\", "", $str);
return $str;
} 

$logfile = 'emdibridge.txt';
$offset= '';
$host = '';
$user = '';
$password = '';
$db = '';
$dbprefix = '';
$product_code_prefix='';
$customer_code_prefix='IC';
$onetime_customer_code_prefix='AC';
$tmp_path ='temp/';
$timezone=$config->offset; 
$passkey='';
$relatedchar='^';
$addonid='PRO';
$manu_field='ΚΑΤΑΣΚΕΥΑΣΤΗΣ';
$size_field='ΜΕΓΕΘΟΣ';

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

$photourl='https://mysite.gr/photos/';	
$produrl='https://www.mysite.gr/gr/product/';	
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

	date_default_timezone_set('Europe/Athens'); 
	$data =  date("Y-m-d H:i:s"); 	
	
	fwrite($Handle,$data); 
	fclose($Handle); 	
}
if ($action == 'productsok') {
	$file = $tmp_path."/products_".$key; 
	$handle = fopen($file, 'w');

	date_default_timezone_set('Europe/Athens'); 
	$data =  date("Y-m-d H:i:s"); 	

	fwrite($handle, $data); 
	fclose($handle); 	
}


//CUSTOMERS
if ($action == 'customers') {

$file = $tmp_path."/customers_".$key; 
$lastdate='';
if (file_exists($file)) {
	$handle = fopen($file, 'r'); 
	$lastdate = fread($handle, 20); 
	fclose($handle); 
	
}
//echo date('Y-m-d H:i:s', $lastdate);

/////////////
$data = mysqli_query($link,"SELECT *
FROM ".$dbprefix."customers as cust


left join countries as contr
on contr.country_id=cust.country_id

left join areas as zone
on zone.area_id=cust.area_id

left join locations as poli
on poli.location_id=cust.city

where cust.registered>'".$lastdate."'


group by cust.customer_id") or die(mysqli_error($link));
/////////////


										
echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
		
while($alldata = mysqli_fetch_array( $data ))
{
	
$ct=$alldata['customer_type'];	
	
if ($ct==1) { 
		$id=$alldata['customer_id'];  	 	
  	 	$firstname= $alldata['first_name']; 
  	 	$lastname=$alldata['last_name'];  	 	
		$address1=$alldata['address'];  	 	
        $postcode=$alldata['postal_code'];  	 
		$country=$alldata['country_gr'];  	 	
		$state=$alldata['area_gr'];
        $state=str_ireplace(" ","",$state);  	 	
		$city=$alldata['location_gr'];  	 	
		$phonenumber=$alldata['telephone'];  	 	
		$mobile=$alldata['telephone2'];  	 	
		$email=$alldata['email'];  	 
        $companyname='';  	 	
		$afm=$alldata='';  	 	
		$doy=$alldata='';
        $epaggelma='';

} else { 

        $id=$alldata['customer_id'];  	 	
  	 	$firstname=''; 
  	 	$lastname='';  	 	
		$address1=$alldata['address'];  	 	
        $postcode=$alldata['postal_code'];  	 
		$country=$alldata['country_gr'];  	 	
		$state=$alldata['area_gr'];
        $state=str_ireplace(" ","",$state);  	 	
		$city=$alldata['location_gr'];  	 	
		$phonenumber=$alldata['telephone'];  	 	
		$mobile=$alldata['telephone2'];  	 	
		$email=$alldata['email'];  	 
        $companyname=$alldata['first_name'];  	 	
		$afm=$alldata['afm'];  	 	
		$doy=$alldata['doy'];
        $epaggelma=$alldata['last_name'];

}



		
		  	 	 	 	
		
		$rowtext=$customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";
		
		    $rowtext=str_ireplace("&amp;","&",$rowtext);
			$rowtext=str_ireplace("\'","&",$rowtext);
			$rowtext=str_ireplace("&quot;","'",$rowtext);
			$rowtext=str_ireplace("&#039;","'",$rowtext);
			$rowtext=str_ireplace("'","`",$rowtext);
			echo $rowtext;	
}
}
////


////PRODUCTS
if ($action == 'products') {
	
	$file = $tmp_path."/products_".$key; 
	$lastdate='';
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 20); 
		fclose($handle); 
	}
	
	
	//---------------------------
	$data = mysqli_query($link,"
	SELECT *
    FROM ".$dbprefix."products as pro
	
	left join categories as cat
    on cat.category_id=pro.category_id
	
	left join sub_categories as subcat
    on subcat.sub_category_id=pro.sub_category_id
	
		
where pro.modified>'".$lastdate."'


group by pro.product_id") or die(mysqli_error($link));
	//---------------------------
	//date('Y-m-d H:i:s', $lastdate)
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ<br>\n";
	
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['product_code'];  	 	
		$name= $alldata['product_gr']; 
		//$name = preg_replace("/&#?[a-z0-9]+;/i","",$name);
		$name = preg_replace('/[\x00-\x1F\x7F]/', '', $name);
		$price=$alldata['price'];
		$price= round(($price*24/100)+$price ,2);
		$price=number_format($price, 2, ',', '');
		$category= $alldata['category_gr'];
        
		
		
		$rowtext= $product_code_prefix.$id.';'.$name.';;'.$maintax.';'.$price.";;;".$measurement.";".$category.";".";".";".";<br>\n";			 
	        $rowtext=str_ireplace("&amp;","&",$rowtext);
			$rowtext=str_ireplace("\'","&",$rowtext);
			$rowtext=str_ireplace("&quot;","'",$rowtext);
			$rowtext=str_ireplace("&#039;","'",$rowtext);
			$rowtext=str_ireplace("'","`",$rowtext);
		
		echo $rowtext;			 
		
	}
	
	
	
	
	
	
}
////



////ORDERS
if ($action == 'orders') {



	$data = mysqli_query($link,"
	SELECT *
    FROM ".$dbprefix."payments as ords
	
	left join agores as ag
    on ag.customer_id=ords.customer_id

		
where ords.progress in (1,2,3)


group by ords.payment_id") or die(mysqli_error($link));


	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";

	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['payment_id'];  	 	
		$userid= $alldata['customer_id']; 
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['date_time'] ;
		$hmera= str_replace('(','',       $hmera);
		$hmera= str_replace(')','',       $hmera);
        $hmera= substr($hmera,6,4).'-'.substr($hmera,3,2).'-'.substr($hmera,0,2).' '.substr($hmera,11,8);
        $shipping=   $alldata['skroutz_shipping'];
		//$shipping=   str_replace('.',',',       $shipping);
		//$shipping=   ($shipping*100)/124;
		$shipping_title= $alldata['shipping_title'];	
		$comment=$alldata['comments'] ;
		$handling=   str_replace('€','',       $alldata['handling']);
		$handling=   str_replace('.',',',       $handling);
		$handling=   $alldata['total_price']-$alldata['skroutz_revenue'];
		//$handling=   round(   $handling*100/124 ,2);
		$handling_title= $alldata['handling_title'];

            $rowtext= $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$handling.";0;".$hmera.";".$shipping_title.";".$handling_title.";".$comment.";<br>\n";		 
	        $rowtext=str_ireplace("&amp;","&",$rowtext);
			$rowtext=str_ireplace("\'","&",$rowtext);
			$rowtext=str_ireplace("&quot;","'",$rowtext);
			$rowtext=str_ireplace("&#039;","'",$rowtext);
			$rowtext=str_ireplace("'","`",$rowtext);
			$rowtext=str_ireplace("rn","",$rowtext);
			
		
		echo $rowtext;	

	}
}
////




////ORDER
if ($action == 'order') {

	$query="
	SELECT *
    FROM ".$dbprefix."agores as ord
	
	left join products as pro
    on pro.product_id=ord.product_id
	
	left join payments as pay
    on pay.unique_var=ord.unique_var
	
    where pay.payment_id=".$orderid;


	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 


	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";

	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['product_gr']; 	
		$product_id = $alldata['product_code'];
        $product_id=str_ireplace(" ","",$product_id);		
		$product_quantity = $alldata['quantity']; 
		$amount=$alldata['current_price'];
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;					
		$taxrate=$maintax;			
		$monada = $measurement; 
		


		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";

		




	}


}




////CONFIRM ORDER
if ($action == 'confirmorder') {

	$data = mysqli_query($link,"update ".$dbprefix."payments set progress=4 where payment_id in (".$orderid.")") or die(mysqli_error($link));

	echo $hmera;
}


////CANCEL ORDER
if ($action == 'cancelorder') {

	$data = mysqli_query($link,"update ".$dbprefix."order set progress=4 where payment_id in (".$orderid.")") or die(mysqli_error($link));

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

		//echo mysql_num_rows($data);

		if (mysqli_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysqli_fetch_array($link,$data ))
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

	$pieces = explode("|", $productid);
	$pieces = explode("|", $productid);
#file_put_contents('emdi22.log', $pieces, FILE_APPEND | LOCK_EX);
	$productid = trim($pieces[0]);
	//file_put_contents('emdi22.log',"##".$productid, FILE_APPEND | LOCK_EX);




	$title=$_REQUEST['title'];
	$descr=$_REQUEST['descr'];    


	$price=$_REQUEST['price'];
	$cat=$_REQUEST['cat']+100000;
	$subcat=$_REQUEST['subcat'];
	$tax=$_REQUEST['tax'];

	$cattitle=trim($_REQUEST['cattitle']);      
	$subcattitle=trim($_REQUEST['subcattitle']);      



	$logtext=$pieces[0].'|'.$productid.'|'.$title.'|'.$descr.'|'.$price.'|'.$cat.'|'.$subcat.'|'.$tax.'|'.$cattitle.'|'.$subcattitle."\n";
	file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);

	//
	//CHECK IF TAX EXISTS ELSE ADD
	$data = mysqli_query("
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
		while($alldata = mysqli_fetch_array($link,$data ))
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
		while($alldata = mysqli_fetch_array($link,$data ))
		{
			$classid=$alldata['tax_class_id'];  	 	
			break;		
		}	
	}
	//
















	// CREATE CATEGORY IF DOES NOT EXIST
	/*$data = mysqli_query($link,"
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
*/







	// CREATE SUBCATEGORY IF DOES NOT EXIST
	/*
$data = mysqli_query($link,"
SELECT * FROM ".$dbprefix."category WHERE category_id=$subcat
") or die(mysqli_error($link));
if (mysql_num_rows($data)==0) {




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

*/






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
		while($alldata = mysqli_fetch_array($link,$data ))
		{
			$id=$alldata['product_id'];  	 	
			break;		
		}	
		/*
//UPDATE PRODUCT NO PHOTO!!!
$data = mysql_query("				
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







?> 					