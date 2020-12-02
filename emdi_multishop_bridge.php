<?php
/*------------------------------------------------------------------------
# EMDI - multishop bridge by SBZ systems - Solon Zenetzis - version 1
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


$productid=$_REQUEST['productid'];
$productid=iconv("ISO-8859-7", "UTF-8",  $productid);
$stock=$_REQUEST['stock'];
$action=$_REQUEST['action'];       
$orderid=$_REQUEST['orderid'];     
$key=$_REQUEST['key'];       

$shipcomp=$_REQUEST['shipcomp'];
$voucherno=$_REQUEST['voucherno'];
$docid=$_REQUEST['docid'];

$randomvar='&rndval='.rand(10000000,90000000); //reduce cache issues
$eshopurl_1='http://eshop1.gr/emdi_wp_woo_bridge.php?company=ike&key=12245325'.$randomvar;
$eshopurl_2='https://eshop2.com/emdi_open2_bridge.php?key=235232354235'.$randomvar;
$order_id_prefix_eshop2='EM';


if (!($key==$passkey)) { exit; }




if ($action == 'deletetmp') {

	echo file_get_contents("$eshopurl_1&action=deletetmp");	
	echo file_get_contents("$eshopurl_2&action=deletetmp");	
	
}


if ($action == 'customersok') {

	echo file_get_contents("$eshopurl_1&action=customersok");	
	echo file_get_contents("$eshopurl_2&action=customersok");	

}


if ($action == 'productsok') {
	
	echo file_get_contents("$eshopurl_1&action=productsok");	
	echo file_get_contents("$eshopurl_2&action=productsok");	

}


if ($action == 'customers') {

	echo file_get_contents("$eshopurl_1&action=customers");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("$eshopurl_2&action=customers"));
	
}


if ($action == 'products') { 

	echo file_get_contents("$eshopurl_1&action=products");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("$eshopurl_2&action=products"));
	
}


if ($action == 'orders') {
	
	echo file_get_contents("$eshopurl_1&action=orders");	
	//Delete 1st row
	echo preg_replace('/^.+\n/', '', file_get_contents("$eshopurl_2&action=orders"));
	
}


if ($action == 'order') {
 
	if (mb_stripos($orderid, $order_id_prefix_eshop2) !== false) { 
		
		echo file_get_contents("$eshopurl_2&action=order&orderid=$orderid");	
		
	} else {
		
		echo file_get_contents("$eshopurl_1&action=order&orderid=$orderid");	
		
	}
		
}


if ($action == 'confirmorder') {
 
	if (mb_stripos($orderid, $order_id_prefix_eshop2) !== false) { 

		echo file_get_contents("$eshopurl_2&action=confirmorder&docid=$docid&shipcomp=$shipcomp&voucherno=$voucherno&orderid=$orderid");	
		
	} else {
		
		echo file_get_contents("$eshopurl_1&action=confirmorder&docid=$docid&shipcomp=$shipcomp&voucherno=$voucherno&orderid=$orderid");
		
	}

}


if ($action == 'updatestock') { 

	echo file_get_contents("$eshopurl_1&action=updatestock&productid=$productid&stock=$stock");	
	echo file_get_contents("$eshopurl_2&action=updatestock&productid=$productid&stock=$stock");
	
}


if ($action == 'cancelorder') {
	
	if (mb_stripos($orderid, $order_id_prefix_eshop2) !== false) { 
		
		echo file_get_contents("$eshopurl_2&action=cancelorder&orderid=$orderid");	
		
	} else {
		
		echo file_get_contents("$eshopurl_1&action=cancelorder&orderid=$orderid");
		
	}
	
} 





 


?>