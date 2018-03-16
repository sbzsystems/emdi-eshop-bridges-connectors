<?php
/*------------------------------------------------------------------------
# EMDI - WordPress eShop BRIDGE by SBZ systems - Solon Zenetzis - version 1.1
# ------------------------------------------------------------------------
# author    SBZ systems - Solon Zenetzis
# copyright Copyright (C) 2013 sbzsystems.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.sbzsystems.com
# Technical Support:  Forum - http://www.sbzsystems.com
-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');


require 'wp-config.php';

//$test=unserialize('a:8:{s:3:"sku";s:0:"";s:8:"products";a:3:{i:1;a:3:{s:6:"option";s:3:"ttt";s:5:"price";s:3:"111";s:9:"saleprice";s:0:"";}i:2;a:3:{s:6:"option";s:3:"eee";s:5:"price";s:3:"222";s:9:"saleprice";s:0:"";}i:3;a:3:{s:6:"option";s:0:"";s:5:"price";s:0:"";s:9:"saleprice";s:0:"";}}s:11:"description";s:0:"";s:8:"shiprate";s:1:"F";s:8:"featured";s:2:"no";s:4:"sale";s:2:"no";s:10:"cart_radio";s:1:"0";s:6:"optset";s:0:"";}');
//print_r($test);
 
//echo $test[products][1][price];

	
$offset= '';
$host = DB_HOST;
$user = DB_USER;
$password = DB_PASSWORD;
$db = DB_NAME;
$dbprefix = '';
$product_code_prefix='P';
$customer_code_prefix='IC';
$lang_code='el';
$tmp_path = ABSPATH . 'tmp';
$timezone=$config->offset; 
$passkey='';



//////////////
$measurement='ΤΕΜΑΧΙΟ';
$vat_field='ΑΦΜ';
$tax_office_field='ΔΟΥ';
$maintax=23;
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
mysql_set_charset('utf8',$link); 

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
$data = mysql_query("SELECT * from wp_eshop_orders where
 edited>'".date('Y-m-d H:i:s', $lastdate)."'
group by user_id") or die(mysql_error());
/////////////


										
echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['user_id'];  	 	
  	 	$firstname= $alldata['first_name']; 
  	 	$lastname=$alldata['last_name'];  	 	
		$address1=$alldata['address1'];  	 	
		$postcode=$alldata['zip'];  	 	
		$country=$alldata['country'];  	 	
		$state=$alldata['state'];  	 	
		$city=$alldata['city'];  	 	
		$phonenumber=$alldata['phone'];  	 	
		$mobile='';//$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		$companyname=$alldata['company'];  	 	
		$afm='';//$alldata['afm'];  	 	
		$doy='';//$alldata['doy'];  	 	

		
		echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,";<br>\n";
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

wp_posts.ID as product_code,
wp_posts.post_title as product,
wp_postmeta.meta_value as info,




(select wp_terms.name

from wp_term_taxonomy, wp_term_relationships, wp_terms

where wp_term_relationships.term_taxonomy_id=
wp_term_taxonomy.term_taxonomy_id
and 

wp_terms.term_id=wp_term_taxonomy.term_id

and wp_term_taxonomy.taxonomy='product_cat'
and wp_term_relationships.object_id=wp_posts.ID
group by wp_term_relationships.object_id) as category







FROM wp_posts

left join wp_postmeta on wp_posts.ID=wp_postmeta.post_id
and wp_postmeta.meta_key='_eshop_product'




where


 wp_posts.post_status='publish'
and wp_posts.post_type='product'






and (wp_posts.post_date>'".date('Y-m-d H:i:s', $lastdate)."' or wp_posts.post_modified>'".date('Y-m-d H:i:s', $lastdate)."')


") or die(mysql_error()); 
//---------------------------
//date('Y-m-d H:i:s', $lastdate)

echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['product_code'];  	 	
  	 	$name1= $alldata['product']; 
		$taxrate=$maintax;
		$taxrate=number_format($taxrate, 2, ',', '');	
		$category= $alldata['category']; 
		$info=unserialize($alldata['info']);  	

		$price=$info[products][1][price];
	    $price=number_format($price, 2, ',', '');
		

		
		echo $product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$measurement.";".$category.";<br>\n";			 
		
}
////





}


































if ($action == 'orders') {



$data = mysql_query("
select
*

from 
wp_eshop_orders
where status='Waiting'
group by id
") or die(mysql_error()); //


echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['id'];  	 	
  	 	$userid= $alldata['user_id']; 
  	    //$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['edited'] ;
		$shipping=   str_replace('€','',       $alldata['shipping']); 
		
		
		
		echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";0;0;".$hmera.";<br>\n";
		
}
}


























if ($action == 'order') {
////order


$data = mysql_query("

select *


from wp_eshop_order_items

left join 
wp_posts on wp_posts.ID=  wp_eshop_order_items.post_ID

where
wp_eshop_order_items.option_id=".$orderid) or die(mysql_error()); 


echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
  	 	$description = $alldata['post_title']; 
  	 	$product_id = $alldata['ID']; 
		$product_quantity = $alldata['item_qty']; 
		$amount=number_format($alldata['item_amt']/$product_quantity, 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;		
		
		$taxrate=number_format($maintax, 2, ',', '');	
		
	 	$monada = $measurement; 
		$product_attribute = $alldata['extra']; 
		
		
		
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

$data = mysql_query("UPDATE wp_eshop_orders SET status = 'Completed' WHERE wp_eshop_orders.id = '".$orderid."'") or die(mysql_error());

echo $hmera;
}



if ($action == 'updatestock') {
//echo "update ".$dbprefix."product set quantity=".$stock."  where product_id='".substr($productid,strlen($product_code_prefix))."'"; 


$data = mysql_query("UPDATE wp_postmeta SET meta_value ='".$stock."' WHERE wp_postmeta.meta_id ='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
		
echo $hmera;
}



if ($action == 'cancelorder') {

$data = mysql_query("UPDATE wp_eshop_orders SET status = 'Deleted' WHERE wp_eshop_orders.id = '".$orderid."'") or die(mysql_error());
				
echo $hmera;

}



//header("Location: $goto?expdate=$nextduedate");




?>