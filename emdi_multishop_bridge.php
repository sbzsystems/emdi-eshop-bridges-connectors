<?php
/*------------------------------------------------------------------------
# EMDI - MULTISHOP BRIDGE by SBZ systems - Solon Zenetzis - version 1
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


$randomvar='&rndval='.rand(10000000,90000000); //reduce cache issues

if ($action == 'deletetmp') {

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=deletetmp$randomvar");	
	echo file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=deletetmp$randomvar");	
	
}


if ($action == 'customersok') {

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=customersok$randomvar");	
	echo file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=customersok$randomvar");	

}


if ($action == 'productsok') {
	
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=productsok$randomvar");	
	echo file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=productsok$randomvar");	

}


if ($action == 'customers') {

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=customers$randomvar");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=customers$randomvar"));
	
}


if ($action == 'products') { 

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=products$randomvar");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=products$randomvar"));
	
}


if ($action == 'orders') {
	
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=orders$randomvar");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=orders$randomvar"));
	
}


if ($action == 'order') {

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=order&orderid=$orderid$randomvar");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=order&orderid=$orderid$randomvar"));	
	
}


if ($action == 'confirmorder') {

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=confirmorder&orderid=$orderid$randomvar");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=confirmorder&orderid=$orderid$randomvar"));

}


if ($action == 'updatestock') { 

	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=updatestock&productid=$productid&stock=$stock$randomvar");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=updatestock&productid=$productid&stock=$stock$randomvar"));
	
}


if ($action == 'cancelorder') {
	
	echo file_get_contents("http://eshop1.gr/emdi_wp_woo_bridge.php?action=cancelorder&orderid=$orderid$randomvar");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("https://eshop2.com/emdi_open2_bridge.php?action=cancelorder&orderid=$orderid$randomvar"));
	
} 





function base_enc($encoded) {
	$result='';
	for($i=0, $len=strlen($encoded); $i<$len; $i+=4){
		$result=$result.base64_decode( substr($encoded, $i, 4) );
	}
	return $result;
}
?>