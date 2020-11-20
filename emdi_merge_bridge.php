<?php
/*------------------------------------------------------------------------
		# EMDI - MERGE by SBZ systems - Solon Zenetzis - version 1
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2020 sbzsystems.com. All Rights Reserved.
		# @license - https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: https://www.sbzsystems.com
		# Technical Support:  Forum - https://www.sbzsystems.com
	-------------------------------------------------------------------------*/
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');
error_reporting(0);
 
//$customerid=$_REQUEST['customerid'];
$productid=$_REQUEST['productid'];
$productid=iconv("ISO-8859-7", "UTF-8",  $productid);
$stock=$_REQUEST['stock'];
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
if (!($key==$passkey)) { exit; }
 

if ($action == 'deletetmp') {
 
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=deletetmp");	
	echo file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=deletetmp");	
	
}


if ($action == 'customersok') {

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=customersok");	
	echo file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=customersok");	
 
}


if ($action == 'productsok') {
  	
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=productsok");	
	echo file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=productsok");	

}


if ($action == 'customers') {

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=customers");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=customers"));
		
}


if ($action == 'products') { 

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=products");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=products"));
	
}


if ($action == 'orders') {
		
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=orders");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=orders"));
		 
}


if ($action == 'order') {
 
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=order&orderid=$orderid");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=order&orderid=$orderid"));	
	
}


if ($action == 'confirmorder') {
 
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=confirmorder&orderid=$orderid");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=confirmorder&orderid=$orderid"));

}


if ($action == 'updatestock') { 
 
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=updatestock&productid=$productid&stock=$stock");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=updatestock&productid=$productid&stock=$stock"));
	
}


if ($action == 'cancelorder') {
	
 	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=cancelorder&orderid=$orderid");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=cancelorder&orderid=$orderid"));
	
} 



 

function base_enc($encoded) {
	$result='';
	for($i=0, $len=strlen($encoded); $i<$len; $i+=4){
		$result=$result.base64_decode( substr($encoded, $i, 4) );
	}
	return $result;
}
?>