<?php

/*------------------------------------------------------------------------
		# EMDI - VIRTUEMART 3 BRIDGE by SBZ systems - Solon Zenetzis - version 2.1
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2015 - 2020 sbzsystems.com. All Rights Reserved.
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
use Joomla\CMS\Factory;
//require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );
/* Create the Application */
$config = new JConfig();

//	error_reporting(E_ALL ^ E_NOTICE);


$logfile = 'emdibridge.txt';
$offset= $config->offset;
$host = $config->host;
$user = $config->user;
$password = $config->password;
$db = $config->db;
$dbprefix = $config->dbprefix;
$tmp_path = $config->tmp_path;
$timezone=$config->offset; 
$shoppergroup1=0;
$shoppergroup2=2;

//echo $host.'#'. $db.'#'.$user.'#'.$password;


//////////////
//LANGUAGE
$currencyid=47;
$lang='el_gr';
//MAIN TAX
$maintax=24;
$monada='ΤΕΜΑΧΙΑ';
// Connects to your Database
$link=mysqli_connect("$host", $user, $password,$db) or die(mysqli_error());	
mysqli_set_charset($link,'utf8');



/*
		$data = mysqli_query($link,"
		
		
		INSERT INTO `".$dbprefix."extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
		
		(10097, 'com_xmlvmfeed', 'component', 'com_xmlvmfeed', '', 1, 1, 0, 0, '{\"legacy\":false,\"name\":\"com_xmlvmfeed\",\"type\":\"component\",\"creationDate\":\"2013-08-31\",\"author\":\"George Terzis\",\"copyright\":\"Copyright (C) 2013. All rights reserved.\",\"authorEmail\":\"webgeo10@gmail.com\",\"authorUrl\":\"http:\\/\\/www.modulesoft.eu\",\"version\":\"1.0.0\",\"description\":\"virtuemart datafeed export for greek prices comparison sites\",\"group\":\"\"}', '{\"vmlang\":\"el_gr\",\"ProdMultiple\":\"0\",\"Availability_Stock\":\"2\",\"AvailabilityWS\":\"\\u0386\\u03bc\\u03b5\\u03c3\\u03b1 \\u03b4\\u03b9\\u03b1\\u03b8\\u03ad\\u03c3\\u03b9\\u03bc\\u03bf\",\"Availability_Without_Stock\":\"2\",\"AvailabilityWOS\":\"\\u0386\\u03bc\\u03b5\\u03c3\\u03b1 \\u03b4\\u03b9\\u03b1\\u03b8\\u03ad\\u03c3\\u03b9\\u03bc\\u03bf\",\"Weight\":\"0\",\"DateAdded\":\"0\",\"ShippingCost\":\"0\",\"ShippingCostAmount\":\"0\",\"ItemID\":\"0\",\"ProductChildsS\":\"0\",\"SkroutzDesc\":\"0\",\"PriceFromS\":\"10\",\"StockS\":\"1\",\"Name_ManufS\":\"0\",\"Sku_MpnS\":\"1\",\"ManufacturerS\":\"1\",\"ProductChildsB\":\"0\",\"BestPriceDesc\":\"2\",\"PriceStart\":\"0\",\"PriceFromB\":\"10\",\"StockB\":\"1\",\"Name_ManufB\":\"0\",\"Sku_MpnB\":\"1\",\"ManufacturerB\":\"1\",\"ProductChildsU\":\"0\",\"UMarketDesc\":\"0\",\"PriceFromU\":\"10\",\"StockU\":\"0\",\"Name_ManufU\":\"0\",\"Sku_MpnU\":\"0\",\"ManufacturerU\":\"0\",\"ProductChildsN\":\"0\",\"NtynomaiDesc\":\"2\",\"PriceFromN\":\"10\",\"StockN\":\"0\",\"Name_ManufN\":\"0\",\"ManufacturerN\":\"0\",\"exportcatid\":[\"11\",\"31\",\"35\",\"36\",\"37\",\"32\",\"64\",\"34\",\"62\",\"63\",\"57\",\"60\",\"33\",\"30\",\"38\",\"66\",\"58\",\"61\",\"59\",\"39\",\"54\",\"55\",\"9\",\"21\",\"65\",\"20\",\"41\",\"24\",\"47\",\"48\",\"23\",\"22\",\"40\",\"8\",\"18\",\"44\",\"19\",\"17\",\"43\",\"42\",\"45\",\"15\",\"14\",\"16\",\"53\",\"10\",\"29\",\"51\",\"50\",\"27\",\"49\",\"28\",\"25\",\"26\"]}', '', '', 0, '0000-00-00 00:00:00', 0, 0);
		
		INSERT INTO `".$dbprefix."menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES
		
		(655, 'main', 'VM XMLDataFeed', 'vm-xmldatafeed', '', 'vm-xmldatafeed', 'index.php?option=com_xmlvmfeed', 'component', 0, 1, 1, 10097, 0, 0, '0000-00-00 00:00:00', 0, 1, 'components/com_xmlvmfeed/assets/images/modulesoft16.png', 0, '', 507, 508, 0, '', 1);
		
		
		
		INSERT INTO `".$dbprefix."redirect_links` (`id`, `old_url`, `new_url`, `referer`, `comment`, `hits`, `published`, `created_date`, `modified_date`) VALUES
		
		(1188, 'http://www.gamerules.gr/index.php?option=com_xmlvmfeed&task=createfeed&&format=bestprice', '', 'http://www..gamerules.gr/administrator/index.php?option=com_xmlvmfeed', '', 1, 0, '2015-01-19 10:21:34', '0000-00-00 00:00:00');
		
		INSERT INTO `".$dbprefix."assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES
		
		(198, 1, 464, 465, 1, 'com_xmlvmfeed', 'com_xmlvmfeed', '{}');
		
		
		
		
		
		"		) or die(mysqli_error());
		
		echo $dbprefix."virtuemart_userinfos ";
	exit;*/


$product_code_prefix='';
$customer_code_prefix='C';
$once_customer_code_prefix='O';


$url = $_SERVER['REQUEST_URI']; //returns the current URL
$parts = explode('/',$url);
$dir = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") .$_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
	$dir .= $parts[$i] . "/";
}

$photourl=$dir."images/stories/virtuemart/product/";
$produrl=$dir."index.php?option=com_virtuemart&view=productdetails&Itemid=0&virtuemart_product_id=";
$customerid=$_REQUEST['customerid'];



$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$productid=$_REQUEST['productid'];
$stock=$_REQUEST['stock'];
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
//if (!($key=='')) { exit; }
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
	$File   = $tmp_path . "/customers_" . $key;
	$Handle = fopen($File, 'w');
	
	//date_default_timezone_set('Europe/Athens');
	$data = date("Y-m-d H:i:s");
	//$data =  date("Y-m-d H:i:s",strtotime('-2 hours', time()));
	fwrite($Handle, $data);
	fclose($Handle);
}
if ($action == 'productsok') {
	$file   = $tmp_path . "/products_" . $key;
	$handle = fopen($file, 'w');
	
	//date_default_timezone_set('Europe/Athens');
	$data = date("Y-m-d H:i:s");
	//$data =  date("Y-m-d H:i:s",strtotime('-2 hour', time()));
	fwrite($handle, $data);
	fclose($handle);
}



if ($action == 'customers') {
	
	
	
	$file     = $tmp_path . "/customers_" . $key;
	$lastdate = 0;
	if (file_exists($file)) {
		$handle   = fopen($file, 'r');
		$lastdate = fread($handle, 20);
		fclose($handle);
	}
	
	
	
	/*
			$data = mysqli_query($link,"SELECT *".
			" FROM ".$dbprefix."virtuemart_userinfos ".
			"left join ".$dbprefix."users on id=virtuemart_user_id ".
			"left join ".$dbprefix."virtuemart_countries on ".$dbprefix."virtuemart_countries.virtuemart_country_id=".$dbprefix."virtuemart_userinfos.virtuemart_country_id ".
			"left join ".$dbprefix."virtuemart_states on ".$dbprefix."virtuemart_states.virtuemart_state_id=".$dbprefix."virtuemart_userinfos.virtuemart_state_id "
			."where ".$dbprefix."virtuemart_userinfos.modified_on>'". $lastdate ."'
			group by virtuemart_user_id
			" 
			) or die(mysqli_error()); 
			
		*/
	//echo tableme($data);
	
	
	
	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
	/*
			while($alldata = mysql_fetch_array( $data ))
			{
			$id=$alldata['virtuemart_user_id'];  	 	
			$firstname= $alldata['first_name']; 
			$lastname=$alldata['last_name'];  
			
			if ((!$firstname) && (!$lastname)) {
			$pieces = explode(" ", $alldata['name']);
			$firstname=  $pieces[1]; 
			$lastname=$pieces[0]; 
			}
			
			
			
			$address1=$alldata['address_1'];  	 	
			$postcode=$alldata['zip'];  	 	
			$country=$alldata['country_name'];  	 	
			$state=$alldata['state_name'];  	 	
			$city=$alldata['city'];  	 	
			$phonenumber=$alldata['phone_1'];  	 	
			$mobile=$alldata['phone_2'];  	 	
			$email=$alldata['email'];  	 	
			$tu=$alldata['address_2'];  	 	
			
			echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
			.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,';'.$tu.";<br>\n";
			}
		*/
	//UNREGISTERED CUSTOMERS
	$data = mysqli_query($link,"SELECT  virtuemart_order_id,company,last_name,first_name,phone_1,phone_2,fax,address_1,city,country_name,state_name,email,zip,virtuemart_user_id".
	
	" FROM ".$dbprefix."virtuemart_order_userinfos ".
	"left join ".$dbprefix."virtuemart_countries on ".$dbprefix."virtuemart_countries.virtuemart_country_id=".$dbprefix."virtuemart_order_userinfos.virtuemart_country_id ".
	"left join ".$dbprefix."virtuemart_states on ".$dbprefix."virtuemart_states.virtuemart_state_id=".$dbprefix."virtuemart_order_userinfos.virtuemart_state_id "
	."where ".$dbprefix."virtuemart_order_userinfos.modified_on>'". $lastdate ."' "
	//."group by phone_2 "
	."group by virtuemart_order_id "
	."order by virtuemart_order_id"
	) or die(mysqli_error());
	
	// $data1 = mysqli_query($link,"SELECT  usri.virtuemart_order_id,usri.company,usri.last_name,usri.first_name,usri.phone_1,usri.phone_2,usri.fax,usri.address_1,usri.city,stavi.state_name,usri.email,usri.zip,usri.virtuemart_user_id".
	
	// " FROM ".$dbprefix."virtuemart_order_userinfos as usri ".
	// //"left join ".$dbprefix."virtuemart_countries as couvi on couvi.virtuemart_country_id=usri.virtuemart_country_id ".
	// "left join ".$dbprefix."virtuemart_states as stavi on stavi.virtuemart_state_id=usri.virtuemart_state_id "
	// ."where usri.modified_on>'".date('Y-m-d H:i:s', $lastdate)."' "
	// ."group by email "
	// ."order by virtuemart_order_id"
	// ) or die(mysqli_error());
	
	//and virtuemart_user_id=0 
	
	
	// echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		
		
		$id=$alldata['virtuemart_user_id'];  //$id=$alldata['virtuemart_order_id'];
		
		$firstname= $alldata['first_name'];
		$lastname=$alldata['last_name'];
		$address1=$alldata['address_1'];
		$postcode=$alldata['zip'];
		$country=$alldata['country_name'];
		$state="ΣΕΡΡΕΣ";
		$city=$alldata['city'];
		$phonenumber=$alldata['phone_1'];
		$mobile=$alldata['phone_2'];
		$email=$alldata['email'];
		if ($mobile) {
			//			echo $once_customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
			//		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,";<br>\n";
			if(!$id>0){
				$id=$alldata['virtuemart_order_id'];
				echo $once_customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
				.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.";<br>\n";
			}
			else{
				echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
				.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.";<br>\n";
			}
		}
		
	}
	
	
	
}
















if ($action == 'products') {
	
	
	$file     = $tmp_path . "/products_" . $key;
	$lastdate = 0;
	if (file_exists($file)) {
		$handle   = fopen($file, 'r');
		$lastdate = fread($handle, 20);
		fclose($handle);
	}
	
	
	
	
	////PRODUCTS
	$query=
	"SELECT * from ".$dbprefix."virtuemart_products as vip
		
		
		
		left join ".$dbprefix."virtuemart_product_prices as prodpri
		on vip.virtuemart_product_id =prodpri.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_calcs
		on prodpri.product_tax_id=".$dbprefix."virtuemart_calcs.virtuemart_calc_id
		
		
		left join ".$dbprefix."virtuemart_product_categories as cat
		on vip.virtuemart_product_id =cat.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_categories_".$lang."
		on cat.virtuemart_category_id =".$dbprefix."virtuemart_categories_".$lang.".virtuemart_category_id
		
		left join ".$dbprefix."virtuemart_products_".$lang." 
		on vip.virtuemart_product_id =".$dbprefix."virtuemart_products_".$lang.".virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_product_medias vpm on vpm.virtuemart_product_id=vip.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_medias virme on virme.virtuemart_media_id=vpm.virtuemart_media_id

		
		where 	vip.published=1
		and vip.modified_on>'". $lastdate ."'
		
		group by vip.virtuemart_product_id
		 
		
		";
	
	
	//	file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);
	//echo $query;		
	
	$data = mysqli_query($link,$query) or die(mysqli_error());


	
	
	
	//left join ".$dbprefix."vm_category
	//on ".$dbprefix."virtuemart_categories.category_id =".$dbprefix."virtuemart_categories.category_id
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		
		$virtuemart_product_id=$alldata['virtuemart_product_id'];  	 	
		$id=$alldata['product_sku'];  	 	
		$shelf=$alldata['product_gtin'];
		$idmpn=$alldata['product_mpn'];  	 
		$name1= $alldata['product_name']; 
		$name2= $alldata['attribute']; 
		//$taxrate=$alldata['calc_value'];
		//$monada= $alldata['product_unit']; 
		
		//$price=$alldata['product_price']+($alldata['product_price']*$taxrate);
		//$taxrate=number_format($alldata['calc_value'], 2, ',', '');	 	
		$price=$alldata['product_price'];
		//+ (($alldata['product_price']*$taxrate)/100)                                 
		//, 2, ',', '');
		
		if ($alldata['product_override_price']<>0) {
			//$price=$alldata['product_override_price'];
			
			$price=number_format($alldata['product_override_price'], 2, ',', '');
			//+ (($alldata['product_override_price']*$taxrate)/100)                                 
			//, 2, ',', '');
			
		}
		
		// $price=number_format($price, 2, ',', '');
		$category= $alldata['category_name']; 
		$category_id= $alldata['category_id']; 
		//$price=number_format($price, 2, '.', '');
		//$taxrate=number_format($taxrate, 2, '.', '');
		$price=$price +($price*$taxrate/100);
		$taxrate=number_format($alldata['calc_value'], 2, ',', '');	 
		$price=number_format($price, 2, ',', '');
		//$price=number_format($price, 2, '.', '');
		
		//$price=str_replace(".",",",$price);
		
		
		
		$photolink=$photourl.$alldata['file_title'];
		$urllink=$produrl.$virtuemart_product_id;
		
		//"|".$idmpn
		//$rowtext=$product_code_prefix.$id.';'.$name1.';ΡΑΦΙ:'.$shelf.'\n'.';'.$taxrate.';'.$price.";;;".$monada.";".$category.";".$photolink.";".$urllink.";<br>\n";
		$rowtext=$product_code_prefix.$id.";".$name1.";ΡΑΦΙ:".$shelf.'\n'.";;;;;;".$category.";".$photolink.";".$urllink.";<br>\n";
		
		$rowtext=str_ireplace("&amp;","&",$rowtext);
		$rowtext=str_ireplace("&quot;","'",$rowtext);
		$rowtext=str_ireplace("&#039;","'",$rowtext);	
		$rowtext=str_ireplace("&#38;","&",$rowtext);
		$rowtext=str_ireplace("&#038;","&",$rowtext);
		$rowtext=str_ireplace("&#39;","'",$rowtext);	
		$rowtext=str_ireplace("&#62;",">",$rowtext);	
		$rowtext=str_ireplace("&gt;",">",$rowtext);	
		
		
		
		
		
		
		//$taxrate=number_format(100*$taxrate, 2, ',', '');	
		
		echo $rowtext;			 
		file_put_contents('products.log', $rowtext."####\n", FILE_APPEND | LOCK_EX);
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
	
	if (!$_REQUEST['test']) { $quer= "and ord.order_status in ('P','C') "; }
	
	$query="SELECT 
		virtuemart_order_id,virtuemart_user_id,modified_on,shipment_name,payment_name,coupon_discount,order_shipment,order_payment
		
		,(SELECT usri.virtuemart_order_userinfo_id FROM ".$dbprefix."virtuemart_order_userinfos usri where usri.virtuemart_order_id=ord.virtuemart_order_id and usri.address_type='ST' limit 1) virtuemart_order_userinfo_id
		,(SELECT usri.customer_note FROM ".$dbprefix."virtuemart_order_userinfos usri where usri.virtuemart_order_id=ord.virtuemart_order_id and usri.address_type='ST' limit 1) customer_note
				
		
		FROM ".$dbprefix."virtuemart_orders ord,".$dbprefix."virtuemart_paymentmethods_el_gr pay,".$dbprefix.
	"virtuemart_shipmentmethods_el_gr ship  
		
		where pay.virtuemart_paymentmethod_id=ord.virtuemart_paymentmethod_id 		
		and ship.virtuemart_shipmentmethod_id=ord.virtuemart_shipmentmethod_id 
		".$quer."
		and ord.order_tax<>0 ";		
	//echo $query;
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	// file_put_contents('debug.log',"SELECT * FROM ".$dbprefix."virtuemart_orders ord,".$dbprefix."virtuemart_paymentmethods_el_gr pay,".$dbprefix."virtuemart_shipmentmethods_el_gr ship  where pay.virtuemart_paymentmethod_id=ord.virtuemart_paymentmethod_id and ship.virtuemart_shipmentmethod_id=ord.virtuemart_shipmentmethod_id and ord.order_status in ('U') and ord.order_tax<>0 " , FILE_APPEND | LOCK_EX);
	
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['virtuemart_order_id'];  	 	
		$userid= $alldata['virtuemart_user_id']; 
		
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['modified_on'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['modified_on'] ; 
		$comment=$alldata['customer_note'];
		$shipment=$alldata['shipment_name'];
		$payment=$alldata['payment_name'];
		
		$coupon_discount=$alldata['coupon_discount'];
		
		
		$comment=str_ireplace("\r",'',$comment);
		$comment=str_ireplace("\n",' ',$comment);
		$comment=str_ireplace(";",'',$comment);
		
		$virtuemart_order_userinfo_id= $alldata['virtuemart_order_userinfo_id'];
		
		//	if ($userid) { order_total order_salesPrice  coupon_discount
		
		//ΑΝ ΑΛΛΗ ΔΙΕΥΘΥΝΣΗ				
		$anaddr='';
		if ($virtuemart_order_userinfo_id) {		
			$anaddr='.'.$virtuemart_order_userinfo_id;  
		}
		
		
		if(!$userid>0){
			echo $id.';'.$once_customer_code_prefix.$id.$anaddr.";".$alldata['order_shipment'].";".$alldata['order_payment'].";".$coupon_discount.";".$hmera.";".$shipment.$payment.$comment.";<br>\n";
		}
		else{				
			echo $id.';'.$customer_code_prefix.$userid.$anaddr.";".$alldata['order_shipment'].";".$alldata['order_payment'].";".$coupon_discount.";".$hmera.";".$shipment.$payment.$comment."<br>\n";				
		}
		
		
		
		
	}
}











































if ($action == 'order') {
	
	
	if ($orderid) 
	{ $linesc="where virtuemart_order_id=".$orderid;
	} else { 
		$linesc=""; 
	} 
	
	
	
	////PRODUCTS
	
	$query="SELECT * from ".$dbprefix."virtuemart_order_items
		
		left join ".$dbprefix."virtuemart_products as produ
		on produ.virtuemart_product_id =".$dbprefix."virtuemart_order_items.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_product_prices as prodpri
		on produ.virtuemart_product_id =prodpri.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_calcs
		on prodpri.product_tax_id=".$dbprefix."virtuemart_calcs.virtuemart_calc_id
		
		$linesc
		
		
		
		group by virtuemart_order_item_id
		
		";
	
	file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);
	
	$data = mysqli_query($link, $query    ) or die(mysqli_error()); 
	
	
	
	
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['order_item_name']; 
		$product_id = $alldata['order_item_sku']; 
		$product_quantity = $alldata['product_quantity']; 
		//$amount=number_format($alldata['product_final_price'], 2, ',', '');
		$amount=number_format($alldata['product_final_price'], 2, ',', '');
		$virtuemart_order_id= $alldata['virtuemart_order_id']; 
		
		
		$taxrate=number_format($alldata['calc_value'], 2, ',', '');
		//$monada = $alldata['product_unit']; 
		$product_attribute = $alldata['product_attribute']; 
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.";0;;;;".$virtuemart_order_id.";<br>\n";
		
		
		////split prostheta   
		$words = preg_split('/,/', $product_attribute);
		
		//$words= strip_tags($words);
		
		
		foreach ($words as $k => $word) {
			
			
			if ($word) {
				
				$word=str_replace('><','>\u0020<',$word);
				//echo $word;
				$lett = explode('\u', strip_tags( $word ));				
				$lett=str_replace('"','',$lett);
				$lett=str_replace('}','',$lett);
				
				
				$phrase='';
				$once=0;
				foreach ($lett as $k => $letter) {
					
					if ($once) {
						$replacedString = preg_replace("/\\\\u([0-9abcdef]{4})/", "&#x$1;", '\u'.$letter);
						$phrase = $phrase.mb_convert_encoding($replacedString, 'UTF-8', 'HTML-ENTITIES');					
					} 
					$once=1;
				}
				
				
				
				//if (substr($word,0,1)==' ') { $word=substr($word,1,strlen($word)-1); }
				echo 'PRO;'.$phrase.";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;;;;;;;;".$virtuemart_order_id.";<br>\n";
			}
			
		} 
		
		
		
		
		
	}
	
	
}





















































if ($action == 'confirmorder') {
	
	$data =mysqli_query($link,"UPDATE ".$dbprefix."virtuemart_orders SET order_status = 'C' WHERE virtuemart_order_id in (".$orderid.")") or die(mysqli_error());
	
	echo $hmera;
	
}



if ($action == 'cancelorder') {
	
	$data = mysqli_query($link,"UPDATE ".$dbprefix."virtuemart_orders SET order_status = 'X' WHERE virtuemart_order_id in (".$orderid.")") or die(mysqli_error());
	
	echo $hmera;
	
}





if ($action == 'updatestock') {
	
	$data = mysqli_query($link,"UPDATE ".$dbprefix."virtuemart_products SET product_in_stock = ".$stock." WHERE product_sku ='".substr($productid,strlen($product_code_prefix))."'") or die(mysqli_error());
	/*if ($stock==1) {
			$stock='1-available.png'; //IMAGE
			} 
			if ($stock==2) {
			$stock='2-oneday.png'; //IMAGE
			} 
			if ($stock==3) {
			$stock='3-order.png'; //IMAGE
			} 
			
			if ($stock==4) {
			$stock='4-lowstock.png'; //IMAGE
			} 
			if ($stock<1) {
			$stock='5-notavailable.png'; //IMAGE
			} 
			if ($stock==6) {
			$stock='6-comingsoon.png'; //IMAGE
			} 
			
			$data = mysqli_query($link,"UPDATE ".$dbprefix."virtuemart_products SET product_availability = '".$stock."' WHERE product_sku ='".substr($productid,strlen($product_code_prefix))."'") or die(mysqli_error());
		*/
	echo $hmera;
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
		
		
		$data = mysqli_query($link,"
			SELECT * from ".$dbprefix."virtuemart_products as vip where product_sku='".$productid."'
			") or die(mysqli_error());
		
		echo mysqli_num_rows($data);
		
		if (mysqli_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysqli_fetch_array( $data ))
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
	
	$productmpn=base_enc('productmpn');	
	$title=base_enc('title');	
	$descr=base_enc('base_enc');    
	
	$productmpn=base_enc('productmpn');	
	$title=base_enc('title');	
	$descr=base_enc('descr');    
	
	
	$descr ='';	
	$price=$_REQUEST['price'];
	$cat=$_REQUEST['cat']+10000;
	$subcat=$_REQUEST['subcat'];
	$tax=$_REQUEST['tax'];
	//$price=$price*100/(100+$tax);
	file_put_contents('emdi_prices.log','##'.$price.'--', FILE_APPEND | LOCK_EX);
	file_put_contents('emdi_prices.log','##'.$tax.'--', FILE_APPEND | LOCK_EX);
	//$tax=string($tax);
	//$tax=str_replace(",",".",$tax);
	
	//$price=($price*100)/(100+$tax);		
	$cattitle=base_enc('cattitle');      
	$subcattitle=base_enc('subcattitle');      
	$custom_STOCK=base_enc('field_SITE');
	
	
	$finalprice=$_REQUEST['cprice_OFFER'];   
	$bggprice=$_REQUEST['cprice_CLUB'];  
	
	file_put_contents($logfile,'##'.$price.'##'.$finalprice.'##'.$bggprice.'#tax""'.$tax."##".$productid."##\n", FILE_APPEND | LOCK_EX);
	
	//$finalprice=($finalprice*100)/(100+$tax);
	//$bggprice=($bggprice*100)/(100+$tax);
	
	//file_put_contents($logfile, $productid.'##'.$price.'##'.$finalprice.'##'.$bggprice.'##'.$custom_STOCK.'##'.$tax."##\n", FILE_APPEND | LOCK_EX);
	
	if ($finalprice>0) { 
		
		$override_price=1; 
		$proxprice=$price;
		
		$price=$finalprice;			
		$finalprice=$proxprice;
		
	}
	
	//get id by sku
	$id='';
	if ($productid) {
		
		//if (substr($productid,0,strlen($product_code_prefix))==$product_code_prefix) {
		//	$productid=substr( $productid,strlen($product_code_prefix),        strlen($productid)-strlen($product_code_prefix)                );
		//}
		
		
		
		
		
		
		
		//echo mysql_num_rows($data);
		//getting the class id
		$query="SELECT * from ".$dbprefix."virtuemart_calcs where calc_value=".$tax;
		file_put_contents('emdi.log',$query, FILE_APPEND | LOCK_EX);
		
		$data = mysqli_query($link,$query) or die(mysqli_error());
		file_put_contents('emdi.log',mysqli_num_rows($data), FILE_APPEND | LOCK_EX);
		if (mysqli_num_rows($data)<>0)
		{
			file_put_contents('emdi2.log','checking for calc' , FILE_APPEND | LOCK_EX);
			
			while ($alldata=mysqli_fetch_array($data))
			{
				$classid=$alldata['virtuemart_calc_id'];
			}
		}
		file_put_contents('emdi1.log',$classid , FILE_APPEND | LOCK_EX);
		//$classid=2;
		
		$query="SELECT * from ".$dbprefix."virtuemart_products where product_sku='".$productid."'";
		$data = mysqli_query($link,$query) or die(mysqli_error());
		
		file_put_contents($logfile, $query."##".mysqli_num_rows($data)."##\n", FILE_APPEND | LOCK_EX);
		
		
		if (mysqli_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysqli_fetch_array( $data ))
			{
				$id=$alldata['virtuemart_product_id'];  
				
				$query="select * from ".$dbprefix."virtuemart_product_prices where virtuemart_product_id=$id and virtuemart_shoppergroup_id=$shoppergroup1";
				$data = mysqli_query($link,$query) or die(mysqli_error());
				
				if (mysqli_num_rows ( $data )==0 ) {
					
					$data =mysqli_query($link,"
						INSERT IGNORE INTO ".$dbprefix."virtuemart_product_prices						
						(`virtuemart_product_price_id`, `virtuemart_product_id`, `virtuemart_shoppergroup_id`, `product_price`, `override`, `product_override_price`, 
						`product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `price_quantity_start`, 
						`price_quantity_end`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) 					
						VALUES 					
						(NULL, '$id', '$shoppergroup1', '$finalprice', '$override_price', '$price', '$classid', NULL, '$currencyid', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', 
						NULL, NULL, '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0');
						
						") or die(mysqli_error());					
				}
				/*
						$query="select * from ".$dbprefix."virtuemart_product_prices where virtuemart_product_id=$id and virtuemart_shoppergroup_id=$shoppergroup2";
						$data =mysqli_query($link,$query) or die(mysqli_error());
						
						if (mysqli_num_rows ( $data )==0 ) {
						
						$data = mysqli_query($link,"
						INSERT IGNORE INTO ".$dbprefix."virtuemart_product_prices						
						(`virtuemart_product_price_id`, `virtuemart_product_id`, `virtuemart_shoppergroup_id`, `product_price`, `override`, `product_override_price`, 
						`product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `price_quantity_start`, 
						`price_quantity_end`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) 					
						VALUES 					
						(NULL, '$id', '$shoppergroup2', '$finalprice', '$override_price', '$bggprice', '$classid', NULL, '$currencyid', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', 
						NULL, NULL, '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0');
						
						") or die(mysqli_error());					
						}
					*/
				file_put_contents('emdiprices.log',"---".$finalprice."###".$tax."textax" , FILE_APPEND | LOCK_EX);
				if (!$finalprice) 
				{ 
					$finalprice=$price; 
					$finalprice=($finalprice*100)/(100+$tax);
					file_put_contents('emdiprices.log',"****".$finalprice."*****".$classid."&&&" , FILE_APPEND | LOCK_EX);
					$price=0;
				}
				file_put_contents('emdiprices.log',"///".$finalprice."!!!!".$price."###".$override_price."--".$bggprice."++++" , FILE_APPEND | LOCK_EX);
				
				$query1="
					UPDATE ".$dbprefix."virtuemart_product_prices 
					SET product_override_price = '$price', product_price = '$finalprice', product_tax_id= '$classid', override='$override_price', product_currency= '$currencyid'
					WHERE virtuemart_product_id='$id' and virtuemart_shoppergroup_id='$shoppergroup1'						
					" ;
				file_put_contents('emdi1.log',$query1."###" , FILE_APPEND | LOCK_EX);
				$data = mysqli_query($link,$query1) or die(mysqli_error());
				/*
						$query2="
						UPDATE ".$dbprefix."virtuemart_product_prices 
						SET product_override_price = '$bggprice', product_price = '$finalprice', product_tax_id= '$classid', override='$override_price', product_currency= '$currencyid'
						WHERE virtuemart_product_id='$id' and virtuemart_shoppergroup_id='$shoppergroup2'
						";
						file_put_contents('emdi4.log',$query2."---" , FILE_APPEND | LOCK_EX);
					$data = mysqli_query($link,$query2) or die(mysqli_error());*/
				
				
				
				
				
				
				
				
				break;		
			}	
			
		}
	}
	
	
	
	
	
	//file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);
	//}
	
	
	////
	////
	////
	
	
	
	
}














function base_enc($encoded) {
	$result='';
	$encoded=$_REQUEST[$encoded];
	
	for($i=0, $len=strlen($encoded); $i<$len; $i+=4){
		$result=$result.base64_decode( substr($encoded, $i, 4) );
	}
	
	
	$result=explode("|", $result);
	$result=trim($result[0]);
	return $result;
	
	
}









function tableme($result){
	$header='';
	$rows='';
	while ($row = mysqli_fetch_array($result)) { 
		if($header==''){
			$header.='<tr>'; 
			$rows.='<tr>'; 
			foreach($row as $key => $value){ 
				// $header.='<th>'.$key.'</th>'; 
				$rows.='<td>'.$key.'</td>'; 
			} 
			$header.='</tr>'; 
			$rows.='</tr>'; 
		}else{
			$rows.='<tr>'; 
			foreach($row as $value){ 
				$rows .= "<td>".$value."</td>"; 
			} 
			$rows.='</tr>'; 
		}
	} 
	return '<table>'.$header.$rows.'</table>';
}

?> 			
