<?php
//PS
/*------------------------------------------------------------------------
		# EMDI - OPENCART 2 BRIDGE by SBZ systems - Solon Zenetzis - version 2.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2013-2017 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');

require 'config.php';

//UP TO 2 SIMULTANEOUSLY OPTIONS PER ITEM
$option_id1=13;
$option_id2=14;
$option_id3=15;


$logfile = 'emdibridge.txt';
$offset= '';
$host = DB_HOSTNAME;
$user = DB_USERNAME;
$password = DB_PASSWORD;
$db = DB_DATABASE;
$dbprefix = DB_PREFIX;
$product_code_prefix='';
$customer_code_prefix='IC';
$onetime_customer_code_prefix='AC';
$lang_code='el-gr';
$lang_id=2;
$store_id=0;
$tmp_path = DIR_SYSTEM.'tmp';
//$timezone=$config->offset; 
$timezone=.85;
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

$photourl=HTTP_SERVER.'image/';	
$produrl=HTTP_SERVER.'index.php?route=product/product&product_id=';	
$test=$_REQUEST['test'];
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
	$Data =  time()-(3600*$timezone); 	//time();
	fwrite($Handle, date('Y-m-d H:i:s', $Data)); 
	fclose($Handle); 	
}
if ($action == 'productsok') {
	$file = $tmp_path."/products_".$key; 
	$handle = fopen($file, 'w');
	$Data = time()-(3600*$timezone); 	//time();
	fwrite($handle, date('Y-m-d H:i:s', $Data)); 
	fclose($handle); 	
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////












//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//CUSTOMERS BY ORDERS
if ($action == 'customers') {
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 20); 
		fclose($handle); 
	}
	
	
	
	
	$query="
		
		SELECT 
		
		
		
		
		(case when customer_id=0 then concat('$onetime_customer_code_prefix',order_id) else concat('$customer_code_prefix',customer_id) end) as user_id,
		
		email,
		telephone as b_phone,
		fax as phone,
		
		firstname,
		lastname,
		payment_company as company,
		payment_address_1 as b_address,				
		payment_address_2 as c_address,
		payment_city as b_city,
		payment_postcode as b_zipcode,
		payment_country  as b_country,
		payment_zone as b_state,
		payment_custom_field,
		shipping_custom_field,
		custom_field,		
		shipping_firstname as sfirstname,
		shipping_lastname as slastname,
		shipping_company as scompany,
		shipping_address_1 as sb_address,				
		shipping_address_2 as sc_address,
		shipping_city as sb_city,
		shipping_postcode as sb_zipcode,
		shipping_country  as sb_country,
		shipping_zone as sb_state,
		
		
		date_added as dd,
		
		
		date_added
		
		FROM ".$dbprefix."order
		where date_added>'". $lastdate."'
		and email<>''
		
		group by 	
		(case when customer_id=0 then concat('$onetime_customer_code_prefix',order_id) else concat('$customer_code_prefix',customer_id) end) 
		
		
		";
	//-- and order_id>97000
	
	
	
	/////////////
	$data = mysqli_query($link,$query) or die(mysqli_error($link));;
	/////////////
	
	
	
	echo "CUSTOMER ID;FIRST NAME;LAST NAME;ADDRESS;ZIP;COUNTRY;CITY/STATE;AREA;PHONE;MOBILE;EMAIL;VAT;TAX OFFICE;COMPANY;OCCUPATION;LANGUAGE;PO BOX;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['user_id'];  	 	
		
		
		if ($alldata['sb_address']) {
			
			$firstname= $alldata['sfirstname']; 
			$lastname=$alldata['slastname'];  	 	
			$address1=$alldata['sb_address'];  	 	
			$tu=$alldata['sc_address']; 		
			$postcode=$alldata['sb_zipcode'];  	 
			$country=$alldata['sb_country'];  	 	
			$state=$alldata['sb_state'];  	 	
			$city=$alldata['sb_city'];  	 	
			//$companyname=$alldata['scompany'];
			$custom_field=$alldata['shipping_custom_field'];
			//$cfld=unserialize($custom_field);
			$cfld=json_decode($custom_field,true);
			//var_dump(json_decode($cfld, true));
			$afm=$cfld['3'];  	 	
			$doy=$cfld['4'];  	 	
			$epaggelma=$cfld['5'];
			$companyname=$firstname.' '.$lastname;//$cfld['2'];				
			
		} else {
			$firstname= $alldata['firstname']; 
			$lastname=$alldata['lastname'];  	 	
			$address1=$alldata['b_address'];  	 	
			$tu=$alldata['c_address']; 		
			$postcode=$alldata['b_zipcode'];  	 
			$country=$alldata['b_country'];  	 	
			$state=$alldata['b_state'];  	 	
			$city=$alldata['b_city'];  	 	
			//$companyname=$alldata['company'];
			$custom_field=$alldata['payment_custom_field'];
			//$cfld=unserialize($custom_field);
			$cfld=json_decode($custom_field,true);
			//var_dump(json_decode($cfld, true));
			$afm=$cfld['3'];  	 	
			$doy=$cfld['4'];  	 	
			$epaggelma=$cfld['5'];
			$companyname=$cfld['2'];
		}
		
		

		//$tu=var_dump($custom_field);
		//ΕΥΡΕΣΗ ΔΟΥ
		if ($doy) {
		
		$queryd="SELECT name FROM ".$dbprefix."custom_field_value_description where custom_field_id=4 and custom_field_value_id=$doy and language_id=2";
		
		//echo '##'.$queryd.'##';
		

	$datad = mysqli_query($link,$queryd) or die(mysqli_error($link));;
	
		
	while($alldatad = mysqli_fetch_array( $datad ))
	{
		$doy=$alldatad['name'];  	 
		break;				
	}
		
		
		
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
			
		
		
		$phonenumber=$alldata['b_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		$date_added=$alldata['date_added'];  	 	
		
		
		

		$rowtext=$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";		
		
		$rowtext=str_ireplace("&amp;","&",$rowtext);
		$rowtext=str_ireplace("&quot;","'",$rowtext);
		$rowtext=str_ireplace("&#039;","'",$rowtext);
		$rowtext=str_ireplace("'","`",$rowtext);
		echo $rowtext;	
		
		
		
		
		// NEW CUSTOMER FOR PAYMENT
		//if ($_REQUEST['test']=='1') {
		
		// NEW CUSTOMER FOR PAYMENT
		if (($alldata['sb_address']) 
				&& (
					($lastname<>$alldata['lastname'])
					|| ($address1<>$alldata['b_address'])
					|| ($tu<>$alldata['c_address'])
					|| ($postcode<>$alldata['b_zipcode'])
					|| ($country<>$alldata['b_country'])
					|| ($state<>$alldata['b_state'])
					|| ($city<>$alldata['b_city'])
					//|| ($companyname<>$alldata['company'])				
					))
		{
			
			$firstname= $alldata['firstname']; 
			$lastname=$alldata['lastname'];  	 	
			$address1=$alldata['b_address'];  	 	
			$tu=$alldata['c_address']; 		
			$postcode=$alldata['b_zipcode'];  	 
			$country=$alldata['b_country'];  	 	
			$state=$alldata['b_state'];  	 	
			$city=$alldata['b_city'];  	 	
			//$companyname=$alldata['company'];  	
			$custom_field=$alldata['payment_custom_field'];
			
			$cfld=unserialize($custom_field);
			//$cfld=json_decode($custom_field,true);
			//var_dump(json_decode($cfld, true));
			$afm=$cfld['3'];  	 	
			$doy=$cfld['4'];  	 	
			$epaggelma=$cfld['5'];
			$companyname=$cfld['2'];			
			
			$rowtext=$id.'.P;'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
			.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";		
			
			$rowtext=str_ireplace("&amp;","&",$rowtext);
			$rowtext=str_ireplace("&quot;","'",$rowtext);
			$rowtext=str_ireplace("&#039;","'",$rowtext);
			$rowtext=str_ireplace("'","`",$rowtext);
			echo $rowtext;	
		}
		
		//}
		
		
		//}
	}
	
	
	mysqli_close($link);
}


if ($action == 'products') {
	
	
	$file = $tmp_path."/products_".$key; 
	$lastdate='';
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 20); 
		fclose($handle); 
	}
	
	////PRODUCTS
	
	
	//---------------------------
	$query="
		SELECT 
		
		pro.model as product_code,
		descr.name as product,
		tra.rate as rate_value,
		pro.price as price,
		group_concat(cdes.name) as category
		,pro.date_modified as dd
		,pro.image
		,pro.product_id
		,pro.quantity
		
		
		, (SELECT pros.price FROM ".$dbprefix."product_special as pros where pros.product_id=pro.product_id limit 1) as price2
		
		,manu.name manname
		
		
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
		on tru.tax_class_id=pro.tax_class_id
		
		left join ".$dbprefix."tax_rate as tra
		on tra.tax_rate_id=tru.tax_rate_id
		
		left join ".$dbprefix."manufacturer as manu
		on manu.manufacturer_id=pro.manufacturer_id
		
		
		where 
		langu.code='".$lang_code."'
		and cdes.language_id=descr.language_id 
		
		and pro.status=1
		
		and (pro.date_added>'".$lastdate."' or pro.date_modified>'".$lastdate."')
		
		group by descr.product_id
		";
	
	
	
	
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	
	
	
	
	
	
	
	
	
	
	//---------------------------
	//date('Y-m-d H:i:s', $lastdate)
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['product_code'];  	 	
		$name1= $alldata['product']; 
		//$name2= $alldata['attribute']; 
		$taxrate= $alldata['rate_value'];
		//$monada= $alldata['product_unit']; 
		
		//$paytype=substr($alldata['paytype'],0,1);
		//$type=substr($alldata['keym'],0,1);
		//$servertype=$alldata['servertype'];  	
		$taxrate=number_format($maintax, 2, ',', '');	
		
		$price=$alldata['price'];
		//$price=number_format($price, 2, ',', '');
		
		$manufacturer=$manu_field.':'.$alldata['manname'].'\n';
		
		$price2=$alldata['price2'];
		$quantity=$alldata['quantity'];
		//$price2=number_format($price2, 2, ',', '');
		
		
		if ($price2) { $price=$price2; }
		
		
		
		$category= $alldata['category']; 
		//$category_id= $alldata['category_id']; 
		
		
		
		
		
		
		//---------------------------
		//LOAD OPTIONS 
		$data_L1 = mysqli_query($link,"
			SELECT opva.option_value_id,vdes.name,price,quantity
			FROM ".$dbprefix."product_option_value as opva
			left join ".$dbprefix."option_value_description as vdes on vdes.option_value_id=opva.option_value_id
			left join ".$dbprefix."language as langu on langu.language_id=vdes.language_id
			
			where opva.product_id=".$alldata['product_id']."                 
			and langu.code='".$lang_code."'
			and opva.option_id=".$option_id1."
			") or die(mysqli_error($link)); 
		$num_rows_L1 = mysqli_num_rows($data_L1);
		
		$data_L2 = mysqli_query($link,"
			SELECT opva.option_value_id,vdes.name,price,quantity
			FROM ".$dbprefix."product_option_value as opva
			left join ".$dbprefix."option_value_description as vdes on vdes.option_value_id=opva.option_value_id
			left join ".$dbprefix."language as langu on langu.language_id=vdes.language_id
			
			where opva.product_id=".$alldata['product_id']."                 
			and langu.code='".$lang_code."'
			and opva.option_id=".$option_id2."
			") or die(mysqli_error($link)); 
		$num_rows_L2 = mysqli_num_rows($data_L2);
		
		$data_L3 = mysqli_query($link,"
			SELECT opva.option_value_id,vdes.name,price,quantity
			FROM ".$dbprefix."product_option_value as opva
			left join ".$dbprefix."option_value_description as vdes on vdes.option_value_id=opva.option_value_id
			left join ".$dbprefix."language as langu on langu.language_id=vdes.language_id
			
			where opva.product_id=".$alldata['product_id']."                 
			and langu.code='".$lang_code."'
			and opva.option_id=".$option_id3."
			") or die(mysqli_error($link)); 
		$num_rows_L3 = mysqli_num_rows($data_L3);
		
		
		
		///////////////////////////////////////////////
		///////////////////////////////////////////////
		
		
		$db1='';
		$db2='';
		if (($num_rows_L1>0) && ($num_rows_L2>0)) {
			$db1=$data_L1;
			$db2=$data_L2;
		}
		if (($num_rows_L2>0) && ($num_rows_L3>0)) {
			$db1=$data_L2;
			$db2=$data_L3;
		}
		if (($num_rows_L1>0) && ($num_rows_L3>0)) {
			$db1=$data_L1;
			$db2=$data_L3;
		}
		
		//IF 2 OPTIONS PER ITEM
		if (($db1) && ($db2)) {
			
			
			while($alldata1 = mysqli_fetch_array($db1))
			{
				mysqli_data_seek($link,$db2, 0);        // first row number is 0
				while($alldata2 = mysqli_fetch_array($db2))
				{
					$name_= $alldata1['name'].' '.$alldata2['name']; 
					$code_= '.'.$alldata1['name'].'.'.$alldata2['name'];
					$price_add_= $alldata1['price']+$alldata2['price']; 
					$quantity=$alldata1['quantity'];
					
					echo $product_code_prefix.$id.$code_.';'.$name1.';'.$name_.';'.$taxrate.';'. str_replace('.',',',       $price+$price_add).";".";$quantity;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
					
				}
			}
			
			
			
			
		} 
		else {
			//IF 1 OPTION PER ITEM
			if (($num_rows_L1>0) || ($num_rows_L2>0) || ($num_rows_L3>0)) {
				if ($num_rows_L1>0) {
					$db1=$data_L1;
				}
				if ($num_rows_L2>0) {
					$db1=$data_L2;
				}
				if ($num_rows_L3>0) {
					$db1=$data_L3;
				}
				
				while($alldata1 = mysqli_fetch_array($db1))
				{
					$name_= $size_field.':'.$alldata1['name'].'\n'.$manufacturer; 
					$code_= '.'.$alldata1['name'];
					$price_add_= $alldata1['price']; 
					$quantity=$alldata1['quantity'];
					
					echo $product_code_prefix.$id.$code_.';'.$name1.';'.$name_.';'.$taxrate.';'.str_replace('.',',', $price+$price_add_ ).";".";$quantity;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
					
				}
			} else {
				//IF NO OPTION PER ITEM
				echo $product_code_prefix.$id.';'.$name1.';'.$manufacturer.';'.$taxrate.';'.str_replace('.',',',      $price).";;$quantity;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
			}
			
			
		}
		
	}
	////
	
	
	
	mysqli_close($link);
	
}








if ($action == 'orders') {



	$data = mysqli_query($link,"
SELECT 
ord.order_id as order_id,
ord.customer_id as user_id,
ord.date_modified as timestamp,
ord.comment,
ord.payment_method,

(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='shipping' limit 0,1) as shipping,
		
		(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='shipping' limit 0,1) as shipping_title,
		
		(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='xfee' limit 0,1) as handling,
		
		(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='xfee' limit 0,1) as handling_title


FROM ".$dbprefix."order as ord
where 
ord.order_status_id in (1)

group by ord.order_id
order by ord.order_id desc
") or die(mysqli_error($link)); //

//
//ord.order_status_id in (1,5) and 
//ord.order_id<14016





	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";

	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['order_id'];  	 	
		$userid= $alldata['user_id']; 
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		$shipping=   str_replace('€','',       $alldata['shipping']);
		$shipping=   str_replace('.',',',       $shipping);
		$shipping_title= $alldata['shipping_title'];	
		$comment=$alldata['comment'] ;
		//$handling=   str_replace('€','',       $alldata['handling']);
		//$handling=   str_replace('.',',',       $handling);
		//$handling_title= $alldata['handling_title'].' ΑΠΟΔΕΙΞΗ';
		
		$payment_method=$alldata['payment_method'];

		//$comment=$comment.' '.$shipping_title.' '.$handling_title;
		$comment=$comment.' '.$shipping_title.' '.$payment_method;	
		$comment = str_ireplace("\n", "<br>", $comment);

		if ($userid==0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";".$handling.";0;".$hmera.";".$comment.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$handling.";0;".$hmera.";".$comment.";<br>\n";
		}

	}
}











if ($action == 'orders_old_1') {
	
	
	if ($_REQUEST['test']<>'1') {
		
		$data = mysqli_query($link,"
			SELECT 
			ord.order_id as order_id,
			ord.customer_id as user_id,
			ord.date_modified as timestamp,
			ord.comment,
			
			(SELECT cgr.name FROM ".$dbprefix."customer_group_description cgr where cgr.customer_group_id=ord.customer_group_id and cgr.language_id=$lang_id) as custgroup,
			
			lastname,
			payment_address_1 as b_address,				
			payment_address_2 as c_address,
			payment_city as b_city,
			payment_postcode as b_zipcode,
			payment_country  as b_country,
			payment_zone as b_state,
			
			shipping_lastname as slastname,
			shipping_address_1 as sb_address,				
			shipping_address_2 as sc_address,
			shipping_city as sb_city,
			shipping_postcode as sb_zipcode,
			shipping_country  as sb_country,
			shipping_zone as sb_state,
			
			
			
			(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='shipping' limit 0,1) as shipping,
			
			(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='shipping' limit 0,1) as shipping_title,
			
			(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='payment_based_fee' limit 0,1) as handling,
			
			(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='payment_based_fee' limit 0,1) as handling_title
			
			
			FROM ".$dbprefix."order as ord
			where
			ord.order_status_id=1
			and order_id>120000
			group by ord.order_id
			
			") or die(mysqli_error($link)); //
		
	} else {
		
		
		$data = mysqli_query($link,"
			SELECT 
			ord.order_id as order_id,
			ord.customer_id as user_id,
			ord.date_modified as timestamp,
			ord.comment,
			
			(SELECT cgr.name FROM ".$dbprefix."customer_group_description cgr where cgr.customer_group_id=ord.customer_group_id and cgr.language_id=$lang_id) as custgroup,
			
			lastname,
			payment_address_1 as b_address,				
			payment_address_2 as c_address,
			payment_city as b_city,
			payment_postcode as b_zipcode,
			payment_country  as b_country,
			payment_zone as b_state,
			
			shipping_lastname as slastname,
			shipping_address_1 as sb_address,				
			shipping_address_2 as sc_address,
			shipping_city as sb_city,
			shipping_postcode as sb_zipcode,
			shipping_country  as sb_country,
			shipping_zone as sb_state,
			
			
			
			(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='shipping' limit 0,1) as shipping,
			
			(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='shipping' limit 0,1) as shipping_title,
			
			(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='payment_based_fee' limit 0,1) as handling,
			
			(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='payment_based_fee' limit 0,1) as handling_title
			
			
			FROM ".$dbprefix."order as ord
			where
			ord.order_status_id=1
			and order_id>97900
			group by ord.order_id
			
			") or die(mysqli_error($link)); //
		
		
	}
	
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;ΧΡΗΣΤΗΣ;VOUCHER;ΚΑΤΑΣΤΑΣΗ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ ΑΠΟΣΤΟΛΗΣ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['order_id'];  	 	
		$userid= $alldata['user_id']; 
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		$shipping=   str_replace('€','',       $alldata['shipping']);
		$shipping=   str_replace('.',',',       $shipping);
		$shipping_title= $alldata['shipping_title'];	 		
		$comment=$alldata['comment'] ;		 								
		$handling=   str_replace('€','',       $alldata['handling']);
		$handling=   str_replace('.',',',       $handling);
		$handling_title= $alldata['handling_title']; 
		
		$comment2=$shipping_title." ".$handling_title." ".$comment;		
		$comment2=str_ireplace("&amp;","&",$comment2);		
		$comment2=str_ireplace("&quot;","'",$comment2);		
		$comment2=str_ireplace("&#039;","'",$comment2);		
		$comment2=str_ireplace("'","`",$comment2); 						
		$comment2=str_ireplace("\n"," ",$comment2);			
		$comment2=str_ireplace("<br>"," ",$comment2);  
		
		
		
		
		$idp='';
		// NEW CUSTOMER FOR PAYMENT
		if ($_REQUEST['test']<>'1') {
			
			// NEW CUSTOMER FOR PAYMENT
			if (($alldata['sb_address']) 
					&& (
						($alldata['lastname']<>$alldata['lastname'])
						|| ($alldata['sb_address']<>$alldata['b_address'])
						|| ($alldata['sc_address']<>$alldata['c_address'])
						|| ($alldata['sb_zipcode']<>$alldata['b_zipcode'])
						|| ($alldata['sb_country']<>$alldata['b_country'])
						|| ($alldata['sb_state']<>$alldata['b_state'])
						|| ($alldata['sb_city']<>$alldata['b_city'])
						//|| ($companyname<>$alldata['company'])				
						))
			{
				
				if ($userid==0) {
					$idp=$onetime_customer_code_prefix.$id.'.P';
					echo $id.';'.$idp.";".$shipping.";".$handling.";0;".$hmera.";".$comment2.";;;;".$onetime_customer_code_prefix.$id.";<br>\n";
				} else {
					$idp=$customer_code_prefix.$userid.'.P';
					echo $id.';'.$idp.";".$shipping.";".$handling.";0;".$hmera.";".$comment2.";;;;".$customer_code_prefix.$userid.";<br>\n";
				}
				
				
			} else {
				if ($userid==0) {
					echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";".$handling.";0;".$hmera.";".$comment2.";;;;;<br>\n";
				} else {					
					echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$handling.";0;".$hmera.";".$comment2.";;;;;<br>\n";
				}
				
				
			}
		} else {
			
			
			
			
			if ($userid==0) {
				echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";".$handling.";0;".$hmera.";".$comment2.";;;;;<br>\n";
			} else {					
				echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$handling.";0;".$hmera.";".$comment2.";;;;;<br>\n";
			}
			
			
		}
		
		
		
	}
	
	
	mysqli_close($link);
}









if ($action == 'orders_old_2') {
	
	
	
	$query="
		SELECT 
		ord.order_id as order_id,
		ord.customer_id as user_id,
		ord.date_modified as timestamp,
		ord.comment,
		
		(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='shipping' limit 0,1) as shipping,
		
		(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='shipping' limit 0,1) as shipping_title,
		
		(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='payment_based_fee' limit 0,1) as handling,
		
		(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='payment_based_fee' limit 0,1) as handling_title
		
		
		FROM ".$dbprefix."order as ord
		where
		ord.order_status_id=1
		and order_id>97000
		group by ord.order_id
		";
	
	
	if ($test) {
		
		
		$query="
			SELECT 
			ord.order_id as order_id,
			ord.customer_id as user_id,
			ord.date_modified as timestamp,
			ord.comment,
			
			(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='shipping' limit 0,1) as shipping,
			
			(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='shipping' limit 0,1) as shipping_title,
			
			(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='payment_based_fee' limit 0,1) as handling,
			
			(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='payment_based_fee' limit 0,1) as handling_title
			
			
			FROM ".$dbprefix."order as ord
			
			group by ord.order_id
			";
		
	}
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); //
	
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['order_id'];  	 	
		$userid= $alldata['user_id']; 
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		$shipping=   str_replace('€','',       $alldata['shipping']);
		$shipping=   str_replace('.',',',       $shipping);
		$shipping_title= $alldata['shipping_title'];	
		$comment=$alldata['comment'] ;
		$handling=   str_replace('€','',       $alldata['handling']);
		$handling=   str_replace('.',',',       $handling);
		$handling_title= $alldata['handling_title'];
		
		
		
		$comment= str_replace("\n",' ',       $comment);
		
		if ($userid==0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";".$handling.";0;".$hmera.";".$shipping_title." ".$handling_title." ".$comment.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$handling.";0;".$hmera.";".$shipping_title." ".$handling_title." ".$comment.";<br>\n";
		}
		
	}
	
	
	mysqli_close($link);
}


























if ($action == 'order') {
	////order
	
	$query="
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
		
		left join ".$dbprefix."order_option as ordop on ord.order_product_id=ordop.order_product_id
		
		where ord.order_id=".$orderid;
	
	//echo $query;
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['product']; 	
		$product_id = $alldata['product_code']; 			
		$product_quantity = $alldata['amount']; 
		$amount=number_format($alldata['price']/$product_quantity, 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;					
		$taxrate=$maintax;//number_format($alldata['rate_value'], 2, ',', '');				
		$monada = $measurement; 
		$product_attribute = $alldata['extra']; 
		
		
		//echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		
		////split prostheta   
		$query="
			SELECT 
			ord.order_id as order_id,
			concat(ord.name,':',ord.value) as product, 
			ord.value as valuep,
			'".$addonid."' as product_code,
			0 as price,
			0 as rate_value,
			0 as amount
			FROM ".$dbprefix."order_option as ord
			left join ".$dbprefix."order_product as pord on pord.order_product_id=ord.order_product_id
			
			where ord.order_id=".$orderid." and pord.product_id=".$alldata['product_id']."
			group by product_option_value_id
			order by order_option_id asc
			";
		
		
		$datap = mysqli_query($link,$query) or die(mysqli_error($link)); 
		//echo $query;
		//echo $alldata['product_id'].'#a#';
		
		
		$addonstext='';
		while($alldatap = mysqli_fetch_array( $datap ))
		{
			
			
			//$product_quantityp = $alldatap['amount'];
			$product_idp=$alldatap['product_code'];
			$productp=$alldatap['product'];
			$valuep=$alldatap['valuep'];
			//$amount=0;
			//if ($product_quantityp>0) {
			//	$amount=number_format($alldatap['price']/$product_quantityp, 2, ',', '');
			//}
			$discountp=0;						
			$taxratep=number_format($alldatap['rate_value'], 2, ',', '');	
			$product_attribute = $alldatap['extra']; 
			
			
			$addonstext=$addonstext.	
			$product_code_prefix.$product_idp.';'.$productp.';;;'.$product_quantityp.';'.$measurementaddon.';'.$amount.';'.$taxratep.';'.$discountp.";<br>\n";
			
			
			
		}
		
		//
		//
		
		
		//echo '#'.$product_idp.'#' .$valuep.'#';
		if ((mysqli_num_rows ( $datap)==1) && ($valuep)  ) {
			$addonstext='';
			echo $product_code_prefix.$product_id.'.'.$valuep.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
			
		} else {
			
			echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
			
			
		}
		
		//	echo $addonstext;
		
		
		
		
		
		
		
	}
	mysqli_close($link);
	
}





















































if ($action == 'confirmorder') {
	
	$data = mysqli_query($link,"update ".$dbprefix."order set order_status_id=27 where order_id in (".$orderid.")") or die(mysqli_error($link));
	
	
	$data = mysqli_query($link,
	
	
	"INSERT INTO ".$dbprefix."order_history (`order_id`, `order_status_id`, `notify`, `comment`, `date_added`) VALUES (".$orderid.", '27', '0', 'Ενημέρωση παραγγελίας από EMDI', DATE_ADD(NOW(), INTERVAL 1 HOUR))"
	
	
	
	) or die(mysqli_error($link));

	
	
	echo $hmera;
	
	mysqli_close($link);
}















if ($action == 'updatestock_old') {		
	
	//GET PRODUCT_ID AND PRODUCT OPTION VALUE ID BASED ON MODEL
	$data = mysqli_query($link,"
		
		SELECT pov.product_option_value_id, pov.product_id
		FROM ".$dbprefix."option_value_description ovd,".$dbprefix."product_option_value pov,".$dbprefix."product pro
		where ovd.option_value_id=pov.option_value_id  
		and pro.product_id=pov.product_id
		and language_id=".$lang_id."  
		and concat(model,'.',name)='".substr($productid,strlen($product_code_prefix))."'
		
		") or die(mysqli_error($link));
	
	
	
	$prodopvid ='';
	$productmain_id='';
	while($alldata = mysqli_fetch_array( $data ))
	{
		$prodopvid = $alldata['product_option_value_id']; 	
		$productmain_id = $alldata['product_id']; 	
	}
	
	
	if ($productmain_id)  {
		
		// SET QUANTITY BASED ON OPTION
		if ($prodopvid) {
			$data = mysqli_query($link,"
				
				update ".$dbprefix."product_option_value poov
				set poov.quantity=".$stock." where poov.product_option_value_id=".$prodopvid 
			
			) or die(mysqli_error($link));					
		}
		
		
		//GET TOTAL QUANTITY OF OPTIONS
		$query="SELECT sum(pov.quantity) qua
			FROM ".$dbprefix."option_value_description ovd,".$dbprefix."product_option_value pov,".$dbprefix."product pro
			where ovd.option_value_id=pov.option_value_id  
			and pro.product_id=pov.product_id
			and language_id=".$lang_id."
			and pro.product_id=".$productmain_id;
		
		echo $query;
		$data = mysqli_query($link,$query  ) or die(mysqli_error($link));
		$totalqua ='';
		while($alldata = mysqli_fetch_array( $data ))
		{
			$totalqua = $alldata['qua']; 	
		}
		
		
		
		$query="update ".$dbprefix."product set quantity=".$totalqua."  where product_id=".$productmain_id;
		
	} else {
		
		$query="update ".$dbprefix."product set quantity=".$stock."  where model='".substr($productid,strlen($product_code_prefix))."'";	
		
	}
	
	
	
	
	//file_put_contents($logfile, substr($productid,strlen($product_code_prefix)).'##'.$totalqua.'#'. $prodopvid .'#'.$productmain_id."\n", FILE_APPEND | LOCK_EX);	
	
	//SET TOTAL QUANTITY
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	//echo $query;
	
	echo 'ok';//$hmera;
	
	
	mysqli_close($link);
}


if ($action == 'updatestock') {		
	
	//GET PRODUCT_ID AND PRODUCT OPTION VALUE ID BASED ON MODEL
	$data = mysqli_query($link,"
		
		SELECT pov.product_option_value_id, pov.product_id
		FROM ".$dbprefix."option_value_description ovd,".$dbprefix."product_option_value pov,".$dbprefix."product pro
		where ovd.option_value_id=pov.option_value_id  
		and pro.product_id=pov.product_id
		and language_id=".$lang_id."  
		and concat(model,'.',name)='".substr($productid,strlen($product_code_prefix))."'
		
		") or die(mysqli_error($link));
	
	
	
	$prodopvid ='';
	$productmain_id='';
	while($alldata = mysqli_fetch_array( $data ))
	{
		$prodopvid = $alldata['product_option_value_id']; 	
		$productmain_id = $alldata['product_id']; 	
	}
	
	
	if ($productmain_id)  {
		
		//ενημέρωση μόνο ΕΝΔΥΣΗ & ΥΠΟΔΗΣΗ
		$data_category = mysqli_query($link,"
	
			SELECT category_id FROM ".$dbprefix."product_to_category WHERE product_id='".$productmain_id."'"
		
		) or die(mysqli_error($link));
		$x=0;
		while($alldata_category = mysqli_fetch_array( $data_category ))
		{
			$c[$x] = $alldata_category['category_id'];
			//echo $c[$x];
			if ($c[$x]==100255){
				$update=1;
			}
			$x+=1;
		}
		
		if ($update==1){
		
		// SET QUANTITY BASED ON OPTION
		if ($prodopvid) {
			$data = mysqli_query($link,"
				
				update ".$dbprefix."product_option_value poov
				set poov.quantity=".$stock." where poov.product_option_value_id=".$prodopvid 
			
			) or die(mysqli_error($link));					
		}
		
		
		//GET TOTAL QUANTITY OF OPTIONS
		$query="SELECT sum(pov.quantity) qua
			FROM ".$dbprefix."option_value_description ovd,".$dbprefix."product_option_value pov,".$dbprefix."product pro
			where ovd.option_value_id=pov.option_value_id  
			and pro.product_id=pov.product_id
			and language_id=".$lang_id."
			and pro.product_id=".$productmain_id;
		
		
		$data = mysqli_query($link,$query  ) or die(mysqli_error($link));
		$totalqua ='';
		while($alldata = mysqli_fetch_array( $data ))
		{
			$totalqua = $alldata['qua']; 	
		}
		
		
		
		$query="update ".$dbprefix."product set quantity=".$totalqua."  where product_id=".$productmain_id;
		
		}
		
	} else {
		
		//ενημέρωση μόνο ΕΝΔΥΣΗ & ΥΠΟΔΗΣΗ
		$data_category = mysqli_query($link,"
	
			SELECT ptc.category_id
			FROM ".$dbprefix."product_to_category ptc,".$dbprefix."product pro
			WHERE pro.model='".substr($productid,strlen($product_code_prefix))."' and pro.product_id=ptc.product_id
		
		") or die(mysqli_error($link));
		$x=0;
		while($alldata_category = mysqli_fetch_array( $data_category ))
		{
			$c[$x] = $alldata_category['category_id'];
			//echo $c[$x];
			if ($c[$x]==100255){
				$update=1;
			}
			$x+=1;
		}
		
		if ($update==1){
		
			$query="update ".$dbprefix."product set quantity=".$stock."  where model='".substr($productid,strlen($product_code_prefix))."'";

		}
		else{
			
		}
		
	}
	
	
	
	
	//file_put_contents($logfile, substr($productid,strlen($product_code_prefix)).'##'.$totalqua.'#'. $prodopvid .'#'.$productmain_id."\n", FILE_APPEND | LOCK_EX);	
	
	//SET TOTAL QUANTITY
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	//echo $query;
	
	echo 'ok';//$hmera;
	
	
	mysqli_close($link);
	
}


if ($action == 'cancelorder') {
	
	$data = mysqli_query($link,"update ".$dbprefix."order set order_status_id=7 where order_id in (".$orderid.")") or die(mysqli_error($link));
	
	echo $hmera;
	
	
	mysqli_close($link);
	
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
	
	
	
	
	mysqli_close($link);
	
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
	
	
	
	
	
	mysqli_close($link);
	
	
	
}







?> 					