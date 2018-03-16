<?php
/*------------------------------------------------------------------------
# EMDI - VIRTUEMART BRIDGE by SBZ systems - Solon Zenetzis - version 1.1
# ------------------------------------------------------------------------
# author    SBZ systems - Solon Zenetzis
# copyright Copyright (C) 2013 sbzsystems.com. All Rights Reserved.
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

$offset= $config->offset;
$host = $config->host;
$user = $config->user;
$password = $config->password;
$db = $config->db;
$dbprefix = $config->dbprefix;
$tmp_path = $config->tmp_path;

$timezone=$config->offset; 
	
//////////////
$maintax=23;
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
mysql_set_charset('utf8',$link); 

$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
if (!($key==$password)) { exit; }
///////////////////////////////////


 
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
 
 

 
if ($action == 'customers') {

$file = $tmp_path."/customers_".$key; 
$lastdate=0;
if (file_exists($file)) {
	$handle = fopen($file, 'r'); 
	$lastdate = fread($handle, 11); 
	fclose($handle); 
}



$data = mysql_query("SELECT * FROM ".$dbprefix."vm_user_info where mdate>".$lastdate) or die(mysql_error()); 
//echo "SELECT * FROM ".$dbprefix."vm_user_info where mdate>".$lastdate;

echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['user_id'];  	 	
  	 	$firstname= $alldata['first_name']; 
  	 	$lastname=$alldata['last_name'];  	 	
		$address1=$alldata['address_1'];  	 	
		$postcode=$alldata['zip'];  	 	
		$country=$alldata['country'];  	 	
		$state=$alldata['state'];  	 	
		$city=$alldata['city'];  	 	
		$phonenumber=$alldata['phone_1'];  	 	
		$mobile=$alldata['phone_2'];  	 	
		$email=$alldata['user_email'];  	 	
	
		echo 'C'.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
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
$data = mysql_query("SELECT * from ".$dbprefix."vm_product 


left join ".$dbprefix."vm_tax_rate 
on ".$dbprefix."vm_product.product_tax_id =".$dbprefix."vm_tax_rate.tax_rate_id

left join ".$dbprefix."vm_product_price 
on ".$dbprefix."vm_product.product_id =".$dbprefix."vm_product_price.product_id

left join ".$dbprefix."vm_product_category_xref 
on ".$dbprefix."vm_product.product_id =".$dbprefix."vm_product_category_xref.product_id

left join ".$dbprefix."vm_category
on ".$dbprefix."vm_product_category_xref.category_id =".$dbprefix."vm_category.category_id


where 	product_publish='Y'
and ".$dbprefix."vm_product.mdate>'".$lastdate."'

") or die(mysql_error()); 
echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['product_id'];  	 	
  	 	$name1= $alldata['product_name']; 
  	 	$name2= $alldata['attribute']; 
  	 	$taxrate=$alldata['tax_rate'];
		$monada= $alldata['product_unit']; 

		$paytype=substr($alldata['paytype'],0,1);
		$type=substr($alldata['keym'],0,1);
		$servertype=$alldata['servertype'];  	
	    $price=$alldata['product_price']+($alldata['product_price']*$taxrate);
	    $price=number_format($price, 2, ',', '');
		$category= $alldata['category_name']; 
		$category_id= $alldata['category_id']; 
		
		$taxrate=number_format(100*$taxrate, 2, ',', '');	
				
		echo 'P'.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$monada.";".$category.";<br>\n";			 
		
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
$data = mysql_query("SELECT * FROM ".$dbprefix."vm_orders where order_status='P' and order_tax<>0 order by order_id,user_id desc") or die(mysql_error()); //


echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['order_id'];  	 	
  	 	$userid= $alldata['user_id']; 
  	 

		
		$hmera=gmdate("d/m/Y H:i:s", $alldata['cdate'] + 3600*($timezone+date("I"))); 

		
		
		
		echo $id.';C'.$userid.";0;0;0;".$hmera.";<br>\n";
		
}
}


























if ($action == 'order') {

////PRODUCTS
$data = mysql_query("SELECT * from ".$dbprefix."vm_order_item

left join ".$dbprefix."vm_product
on ".$dbprefix."vm_product.product_id =".$dbprefix."vm_order_item.product_id


left join ".$dbprefix."vm_tax_rate 
on ".$dbprefix."vm_product.product_tax_id =".$dbprefix."vm_tax_rate.tax_rate_id


where order_id=".$orderid) or die(mysql_error()); 

echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
  	 	$description = $alldata['order_item_name']; 
  	 	$product_id = $alldata['product_id']; 
		$product_quantity = $alldata['product_quantity']; 
		$amount=number_format($alldata['product_final_price'], 2, ',', '');

		
		$taxrate=number_format(100*$alldata['tax_rate'], 2, ',', '');	
	 	$monada = $alldata['product_unit']; 
		$product_attribute = $alldata['product_attribute']; 
		
		echo 'P'.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.";0;<br>\n";
		////split prostheta   
		$words = preg_split('/<br\/>/', $product_attribute);
   
		foreach ($words as $k => $word) {
			//echo $word;
			
			
			//$word=substr($word,3,strlen($word));
			if ($word) {
				if (substr($word,0,1)==' ') { $word=substr($word,1,strlen($word)-1); }
				echo 'PRO;'.$word.";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;<br>\n";
			}
        
        } 
		
		
		
		
		
}


}





















































if ($action == 'confirmorder') {

//echo"UPDATE ".$dbprefix."vm_orders SET order_status = 'S' WHERE order_id in (".$orderid.")";
//
//$data = mysql_query("UPDATE admin_whmcs.tblinvoices SET notes = 'ΚΟΠΗΚΕ ΤΙΜΟΛΟΓΙΟ ".$hmera."' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysql_error()); 
$data = mysql_query("UPDATE ".$dbprefix."vm_orders SET order_status = 'S' WHERE order_id in (".$orderid.")") or die(mysql_error());
		
echo $hmera;

}




//header("Location: $goto?expdate=$nextduedate");




?> 