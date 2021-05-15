<?php
error_reporting(0);
/*------------------------------------------------------------------------
		# EMDI - OPENCART 2 BRIDGE by SBZ systems - Solon Zenetzis - version 2.1
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2013-2021 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support: http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');
//error_reporting(0);

require 'config.php';

$logfile = 'emdibridge.log';
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
$lang_id=4;
$store_id=0;
$tmp_path = DIR_SYSTEM.'tmp';
$timezone=$config->offset; 
$passkey='';
$relatedchar='^';
$addonid='PRO';
$avail_id=7;   // FROM TABLE stock_status AVAILABLE
$notavail_id=5; // FROM TABLE stock_status NOT AVAILABLE

//////////////
$measurement='ΤΕΜΑΧΙΑ';
$measurementaddon='ΠΡΟΣΘΕΤΑ';

//$vat_field='ΑΦΜ';
//$tax_office_field='ΔΟΥ';
//$maintax=24;
// Connects to your Database
$link=mysqli_connect("$host", $user, $password) or die(mysqli_error($link));
mysqli_select_db($link,"$db") or die(mysqli_error($link));
mysqli_set_charset($link,'utf8'); 

$photourl=HTTP_SERVER.'image/';		
$produrl=HTTP_SERVER.'index.php?route=product/product&product_id=';	
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
			$companyname=$alldata['scompany'];  	 	
			
		} else {
			$firstname= $alldata['firstname']; 
			$lastname=$alldata['lastname'];  	 	
			$address1=$alldata['b_address'];  	 	
			$tu=$alldata['c_address']; 		
			$postcode=$alldata['b_zipcode'];  	 
			$country=$alldata['b_country'];  	 	
			$state=$alldata['b_state'];  	 	
			$city=$alldata['b_city'];  	 	
			$companyname=$alldata['company'];  	 			$custom_field=$alldata['custom_field'];
			
			//$cfld=unserialize($custom_field);
			$cfld=json_decode($custom_field,true);
			//var_dump(json_decode($cfld, true));
			$companyname= $cfld['4'];
			$afm=$cfld['2'];
			$doy=$cfld['3'];
			$epaggelma=$cfld['0']; 
			
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
			$companyname=$alldata['company'];  	
			$custom_field=$alldata['custom_field'];
			
			//$cfld=unserialize($custom_field);
			$cfld=json_decode($custom_field,true);
			//var_dump(json_decode($cfld, true));
			$companyname= $cfld['4'];
			$afm=$cfld['2'];
			$doy=$cfld['3'];
			$epaggelma=$cfld['0']; 
			
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
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 11); 
		fclose($handle); 
	}
	
	////PRODUCTS
	
	/*
			(select GROUP_CONCAT(prov.o_sku) from
			".$dbprefix."product_option_value prov
			where prov.product_id=descr.product_id) as barcodes
			
			
			,
		*/
	//---------------------------
	$normal=" and (pro.date_added>'".date('Y-m-d H:i:s', $lastdate)."' or pro.date_modified>'".date('Y-m-d H:i:s', $lastdate)."') ";
	if ($_REQUEST['test']) {
		$normal='';
	}
	
	
	
	$query="
		SELECT pro.model as product_code,
		descr.name as product,
		tra.rate as rate_value,
		pro.price as price,
		group_concat(cdes.name) as category
		,pro.date_modified as dd
		,pro.image
		,pro.product_id
		,pro.quantity mainquantity
		,
		
		
		
		
		(SELECT GROUP_CONCAT(prov.model) 
		FROM ".$dbprefix."relatedoptions prov
		WHERE prov.product_id=descr.product_id ) as optionssku
		
		
		,
		
		(SELECT GROUP_CONCAT(prov.quantity) 
		FROM ".$dbprefix."relatedoptions prov
		WHERE prov.product_id=descr.product_id ) as optionsquantity
		
		
		,
		
	
		(SELECT GROUP_CONCAT(
		(select opvde.name from ".$dbprefix."option_value_description opvde where opvde.option_value_id=rltop.option_value_id and opvde.language_id=descr.language_id)                
		) 
		FROM ".$dbprefix."relatedoptions prov
		left join ".$dbprefix."relatedoptions_option rltop on rltop.relatedoptions_id=prov.relatedoptions_id		
		WHERE prov.product_id=descr.product_id ) as optionsdescr
		
			
		,
		
		
		
		
		
		
		(SELECT GROUP_CONCAT(
		(select opvde.name from ".$dbprefix."option_description opvde where opvde.option_id=rltop.option_id and opvde.language_id=descr.language_id)                
		) 
		FROM ".$dbprefix."relatedoptions prov
		left join ".$dbprefix."relatedoptions_option rltop on rltop.relatedoptions_id=prov.relatedoptions_id		
		WHERE prov.product_id=descr.product_id ) as optionsnametitle
		
			
		,
		
		
	
		
		
		(select GROUP_CONCAT(  concat(prov.price_prefix  ,prov.price)   )
		FROM ".$dbprefix."relatedoptions prov
		WHERE prov.product_id=descr.product_id ) as optionsprice
		
		
		,
		
		
		
		
		
		
		
		
		
		
		
		(SELECT prosp.price 
		FROM ".$dbprefix."product_special prosp
		where prosp.product_id=descr.product_id
		and prosp.customer_group_id=2
		ORDER BY prosp.product_special_id DESC
		limit 1)  as priced
		
		
		
		
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
		on tru.tax_class_id=pro.tax_class_id and priority=0
		
		left join ".$dbprefix."tax_rate as tra
		on tra.tax_rate_id=tru.tax_rate_id
		
		
		
		
		
		
		
		where 
		langu.code='".$lang_code."'
		and cdes.language_id=descr.language_id 
		
		$normal
		
		group by descr.product_id
		";
	
	
	
	
	
	
	
	
	
	//echo $query;
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	//---------------------------
	//date('Y-m-d H:i:s', $lastdate)
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ ΠΩΛΗΣΗΣ;ΤΙΜΗ ΑΓΟΡΑΣ;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ;ΒΑΡΟΣ;ΕΝΕΡΓΟ;<br>\n";
	
	
	
	while($alldata = mysqli_fetch_array( $data ))
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
		$category= $alldata['category']; 
		//$category_id= $alldata['category_id']; 
		
		$taxrate=24;
		
		$quantity=$alldata['mainquantity'];
		
		//additional products based on options
		$arr_sku = explode(',', $alldata['optionssku']);
		$arr_descr = explode(',', $alldata['optionsdescr']);
		$arr_price = explode(',', $alldata['optionsprice']);
		$arr_nametitle = explode(',', $alldata['optionsnametitle']);
		$arr_quantity= explode(',', $alldata['optionsquantity']); 
		$mc=count($arr_sku);
		$hidebasic=false;
		
		$price=$alldata['price'];
		$priced=$alldata['priced'];
		$manuname= $alldata['manuname']; 
		
		if ($mc) {		
			for ($x = 0; $x <= $mc; $x++) {
				
				if ($arr_sku[$x]) {
					
					if ($arr_sku[$x]==$id) { $hidebasic=true; }
					
					$pricen=$price+$arr_price[$x];
					$pricen=number_format($pricen, 2, ',', '');
					
					//$pricedn=number_format($pricedn, 2, ',', '');					
					//if ($pricedn!=0) { $pricen=$pricedn; }
					
					if ($priced!=0) { 
						
						$pricedn=number_format($priced+$arr_price[$x], 2, ',', '');											
						$pricen=$pricedn; 
						
						
					}
					
					$arr_sku_name = preg_replace('/[0-9]+/', '', $arr_sku[$x]);
					$arr_descr_name = preg_replace('/[0-9]+/', '', $arr_descr[$x]);
					
					echo $product_code_prefix. $arr_sku[$x].';'.$name1.' '.$arr_nametitle[$x].' '.$arr_descr[$x].';ΚΑΤΑΣΚΕΥΑΣΤΗΣ:'.$manuname.'\n;'.$taxrate.';'.$pricen.";;".$arr_quantity[$x].";".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";;;1;<br>\n";	
				}
			}
		}
		
		
		if ($hidebasic==false) {
			
			$price=number_format($price, 2, ',', '');
			$priced=number_format($priced, 2, ',', '');					
			if ($priced!=0) { $price=$priced; }
			
			
			echo $product_code_prefix.$id.';'.$name1.';ΚΑΤΑΣΚΕΥΑΣΤΗΣ:'.$manuname.'\n;'.$taxrate.';'.$price.";".$quantity.";;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";;;1;<br>\n";
		}
		
		
		
		
	}
	////
	
	
	
	
	
}







if ($action == 'orders') {
	
	
	

	
	
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
			and ordt.code='codfee_payment' limit 0,1) as handling,
			
			(select ordt.title from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
			and ordt.code='codfee_payment' limit 0,1) as handling_title
			
			
			FROM ".$dbprefix."order as ord
			WHERE ord.order_status_id in (1,15)
			group by ord.order_id
			
			") or die(mysqli_error($link)); //
	
	
	
	
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
		
		$comment2=$shipping_title." ".$handling_title." "/*.$alldata['custgroup']*/." ".$comment;		
		$comment2=str_ireplace("&amp;","&",$comment2);		
		$comment2=str_ireplace("&quot;","'",$comment2);		
		$comment2=str_ireplace("&#039;","'",$comment2);		
		$comment2=str_ireplace("'","`",$comment2); 						
		$comment2=str_ireplace("\n"," ",$comment2);			
		$comment2=str_ireplace("<br>"," ",$comment2);  
		
		
		
		
		$idp='';
		// NEW CUSTOMER FOR PAYMENT
		
		
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
		
		
		
		
	}
}






if ($action == 'order') {
	////order
	
	$data = mysqli_query($link,"
		SELECT
		ord.order_id as order_id,
		ord.name as product,
		ord.model as product_code,
		ord.total as price,
		ord.quantity as amount,
		ord.product_id as product_id,
		round ((ord.tax*100)/ord.total) as rate_value,
		
		
		
		(SELECT GROUP_CONCAT(
		(select opvde.name from ".$dbprefix."option_value_description opvde where opvde.option_value_id=rltop.option_value_id and opvde.language_id=$lang_id)                
		) 
		FROM ".$dbprefix."relatedoptions prov
		left join ".$dbprefix."relatedoptions_option rltop on rltop.relatedoptions_id=prov.relatedoptions_id		
		WHERE prov.model=ord.model ) as optionsdescr
		
			
		,
		
		
			
		
		
		(SELECT GROUP_CONCAT(
		(select opvde.name from ".$dbprefix."option_description opvde where opvde.option_id=rltop.option_id and opvde.language_id=$lang_id)                
		) 
		FROM ".$dbprefix."relatedoptions prov
		left join ".$dbprefix."relatedoptions_option rltop on rltop.relatedoptions_id=prov.relatedoptions_id		
		WHERE prov.model=ord.model ) as optionsnametitle
		
		
		
		
		
		
		FROM ".$dbprefix."order_product as ord
		left join ".$dbprefix."product as pro on pro.product_id=ord.product_id
		
		where ord.order_id=".$orderid) or die(mysql_error()); 
	
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['product']; 
		$product_id = $alldata['product_code']; 
		$product_quantity = $alldata['amount']; 
		
		$optionsnametitle = $alldata['optionsnametitle']; 
		$optionsdescr = $alldata['optionsdescr']; 
		
		
		$amount=number_format($alldata['price']/$product_quantity, 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;		
		
		//$taxrate=number_format($alldata['rate_value'], 2, ',', '');	
		$taxrate=24;
		
		$monada = $measurement; 
		$product_attribute = $alldata['extra']; 
		
		
		
		echo $product_code_prefix.$product_id.';'.$description.' '.$optionsnametitle.' '.$optionsdescr.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		
		
		
		////split prostheta   
		
		
		/*$datap = mysqli_query($link,"
			SELECT 
			ord.order_id as order_id,
			concat(ord.name,':',ord.value) as product,
			'".$addonid."' as product_code,
			0 as price,
			0 as rate_value,
			1 as amount,
			1000 as monada
			FROM ".$dbprefix."order_option as ord
			left join ".$dbprefix."order_product as pord on pord.order_product_id=ord.order_product_id
			
			where ord.order_id=".$orderid." and pord.product_id=".$alldata['product_id']
		
		."
			group by product_option_value_id
			order by order_option_id asc
			"
		
		
		
		
		
		
		) or die(mysqli_error($link)); 
		
		
		
		//echo $alldata['product_id'].'###';
		while($alldatap = mysqli_fetch_array( $datap ))
		{
			$description = $alldatap['product']; 
			$product_id = $alldatap['product_code']; 
			$product_quantity = $alldatap['amount']; 
			$amount=number_format($alldatap['price']/$product_quantity, 2, ',', '');
			$discount=0;		
			
			//$taxrate=number_format($alldatap['rate_value'], 2, ',', '');	
			$taxrate=24;
			
			$monada = $measurementaddon; 
			$product_attribute = $alldatap['extra']; 
			
			
			
			echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		}
		
		
		
		
		
		*/
		
		
		
		
		
		
		
		
	}
	

}







if ($action == 'confirmorder') {
	
	$data = mysqli_query($link,"update ".$dbprefix."order set order_status_id=5 where order_id in (".$orderid.")") or die(mysqli_error($link));
	
	echo $hmera;
}





















if ($action == 'updatestock') {	
	$optvalid='';
	//where concat(pro.model,rel.model)='".substr($productid,strlen($product_code_prefix))."'
	$data = mysqli_query($link,"
		
		select rel.relatedoptions_id, rel.product_id
		
		,(SELECT rlop.option_value_id FROM `ocjs_relatedoptions_option` rlop where relatedoptions_id=rel.relatedoptions_id) optvalid
		
		
		from ".$dbprefix."relatedoptions rel
		left join ".$dbprefix."product pro on pro.product_id=rel.product_id
		where rel.model='".substr($productid,strlen($product_code_prefix))."'
		
		
		
		") or die(mysqli_error($link));
	
	$id='';
	$prodid='';
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['relatedoptions_id'];  	 			
		$prodid=$alldata['product_id'];  	 			
		$optvalid=$alldata['optvalid'];  	 			
	}
	
	
	
	
	if ($id) {
		
		$data = mysqli_query($link,"
			
			update ".$dbprefix."relatedoptions set quantity=".$stock." where relatedoptions_id=".$id."
			
			") or die(mysqli_error($link));
		
		
		
		
		$query="
			update ".$dbprefix."product set quantity=
			
			
			(select sum(rel.quantity)
			from ".$dbprefix."relatedoptions rel
			where rel.product_id=".$prodid.")
			
			,stock_status_id=
			
			(case when (select sum(rel.quantity)
			from ".$dbprefix."relatedoptions rel
			where rel.product_id=".$prodid.")>0 then $avail_id else $notavail_id end)
			
			
			
			where product_id='".$prodid."'
			
			";
		//echo $query;
		$data = mysqli_query($link,$query) or die(mysqli_error($link));
		
	}
	
	
	
	
	
	
	
	
	/////////////////////////////////////////update standard options stock
	/////////////////////////////////////////
	
	
	
	
	
	if (($optvalid) && ($prodid)) {
		$data = mysqli_query($link,"
			
			update ".$dbprefix."product_option_value poov
			set poov.quantity=".$stock." where poov.option_value_id=".$optvalid." and poov.product_id=".$prodid  
		
		) or die(mysqli_error($link));
		
		
	}
	
	
	//$data = mysql_query("update ".$dbprefix."product set quantity=".$stock."  where model='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
	
	
	
	
	
	echo $hmera;
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	echo 'ok';
	echo $hmera;
}
























if ($action == 'cancelorder') {
	
	$data = mysqli_query($link,"update ".$dbprefix."order set order_status_id=7 where order_id in (".$orderid.")") or die(mysqli_error($link));
	
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
		
		if (mysql_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysqli_fetch_array( $data ))
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
	
	
	
	//file_put_contents($logfile, 'ok', FILE_APPEND | LOCK_EX);
	
	$title=$_REQUEST['title'];
	$descr=$_REQUEST['descr'];    	
	$price=$_REQUEST['price'];
	$cat=$_REQUEST['cat']+100000;
	$subcat=$_REQUEST['subcat'];
	$tax=$_REQUEST['tax'];
	//$price=($price *100)/(100+$tax);
	
	$price=round($price, 4);	
	
	
	
	$cattitle=trim($_REQUEST['cattitle']);      
	$subcattitle=trim($_REQUEST['subcattitle']);      
	
	
	$descr=explode('|',$descr)[0];
	
	
	$logtext='|'.$productid.'|'.$title.'|#'.$descr.'#|'.$price.'|'.$cat.'|'.$subcat.'|'.$tax.'|'.$cattitle.'|'.$subcattitle."\n";
	file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
	
	
	
	
	
	


	
	
	
	
	
	//
	//CHECK IF TAX EXISTS ELSE ADD
	$query="
		select * from ".$dbprefix."tax_rule as tru
		left join ".$dbprefix."tax_rate as tra on tru.tax_rate_id=tra.tax_rate_id
		left join ".$dbprefix."tax_class as tcl on  tru.tax_class_id=tcl.tax_class_id
		
		where rate=$tax
		
		";
	$data = mysqli_query($link,$query) or die(mysqli_error());
	
	

	//$logtext="before_update";
	//file_put_contents($logfile, $query, FILE_APPEND | LOCK_EX);
	
	
	if (mysqli_num_rows($data)==0) {
		
		//ADD DEFAULT EMDI TAX CLASS IF DOESN'T EXIST
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."tax_class (tax_class_id, title, description, date_added, date_modified) 
			VALUES (NULL, 'EMDI $tax', 'EMDI $tax', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error());			
		
		
		//GET CLASS ID
		$data = mysqli_query($link,"SELECT LAST_INSERT_ID() as id") or die(mysqli_error());
		
		while($alldata = mysqli_fetch_array( $data ))
		{
			$classid=$alldata['id'];  	 	
			break;		
		}	
		
		//ADD TAX	
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."tax_rate (tax_rate_id, geo_zone_id, name, rate, type, date_added, date_modified) 
			VALUES (NULL, '0', '$tax%', '$tax', 'P', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error());			
		
		
		//GET TAX ID
		$data = mysqli_query($link,"SELECT LAST_INSERT_ID() as id") or die(mysqli_error());					
		while($alldata = mysqli_fetch_array( $data ))
		{
			$taxid=$alldata['id'];  	 	
			break;		
		}	
		
		//ADD RULE
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."tax_rule (tax_rule_id, tax_class_id, tax_rate_id, based, priority) 
			VALUES (NULL, '$classid', '$taxid', 'payment', '1');
			") or die(mysqli_error());			
		
		
	} else {
		//file_put_contents($logfile, '$$$$'.$classid.'$$$$', FILE_APPEND | LOCK_EX);			
		//GET TAX CLASS IF DOESN'T EXIST
		
		while($alldata = mysqli_fetch_array( $data ))
		{
			$classid=$alldata['tax_class_id'];  	
			break;		
		}	
	}
	//
	
	
	
	
	// file_put_contents($logfile, '#qq#'.$query.'&&'.$classid.'##', FILE_APPEND | LOCK_EX);
	
	
	
	
	
	
	
	
	
	
	
	
	// CREATE CATEGORY IF DOES NOT EXIST
	$data = mysqli_query($link,"
		SELECT * FROM ".$dbprefix."category WHERE category_id=$cat
		") or die(mysqli_error());
	if (mysqli_num_rows($data)==0) {
		
		
		
		
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
			VALUES 
			('$cat', NULL, '0', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error());			
		
		//ADD CATEGORY DESCRIPTION
		
		//FOR ALL LANGUAGES    
		for ($lang_id = 1; $lang_id <= 3; $lang_id+=2) {
			
			$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
			VALUES ('$cat', '$lang_id', '$cattitle', '', '', '');	
			") or die(mysqli_error());			
			
		}
		
		
		
		//ADD CATEGORY STORE
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
			VALUES ('$cat', '$store_id');
			") or die(mysqli_error());			
		
		
		//ADD CATEGORY PATH
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
			VALUES ('$cat', '$cat', '0')
			") or die(mysqli_error());			
		
		
		
		
		
	}
	//
	
	
	
	
	
	
	
	
	// CREATE SUBCATEGORY IF DOES NOT EXIST
	$data = mysqli_query($link,"
		SELECT * FROM ".$dbprefix."category WHERE category_id=$subcat
		") or die(mysqli_error());
	if (mysqli_num_rows($data)==0) {
		
		
		
		
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
			VALUES 
			('$subcat', NULL, '$cat', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error());			
		
		
		//ADD SUBCATEGORY DESCRIPTION
		
		//FOR ALL LANGUAGES
		for ($lang_id = 1; $lang_id <= 3; $lang_id++) {
			
			
			$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
			VALUES ('$subcat', '$lang_id', '$subcattitle', '', '', '');	
			") or die(mysqli_error());			
			
		}
		
		//ADD SUBCATEGORY STORE
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
			VALUES ('$subcat', '$store_id');
			") or die(mysqli_error());			
		
		
		//ADD SUBCATEGORY CATEGORY PATH
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
			VALUES ('$subcat', '$cat', '1')
			") or die(mysqli_error());			
		
		//ADD SUBCATEGORY  PATH 
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
			VALUES ('$subcat', '$subcat', '2')
			") or die(mysqli_error());			
		
		
		
		
	}
	//
	
	
	
	
	
	
	
	
	$logtext=$_FILES["file"]["name"]."\n";
	
	//file_put_contents($logfile,'>>'. $logtext, FILE_APPEND | LOCK_EX);	
	
	
	// UPLOAD AND REPLACE PHOTO
	$uploadfolder=getcwd().'/image/data/';
	$photo_filename1='';
	
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
			$photo_filename1='data/'.$_FILES["file"]["name"];
			
		}
	} else {
		echo "Invalid file";
	}
	//
	
	// UPLOAD AND REPLACE PHOTO#2
	$uploadfolder=getcwd().'/image/data/';
	$photo_filename2='';
	
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES["file2"]["name"]);
	$extension = end($temp);
	
	if ((($_FILES["file2"]["type"] == "image/gif")
				|| ($_FILES["file2"]["type"] == "image/jpeg")
				|| ($_FILES["file2"]["type"] == "image/jpg")
				|| ($_FILES["file2"]["type"] == "image/pjpeg")
				|| ($_FILES["file2"]["type"] == "image/x-png")
				|| ($_FILES["file2"]["type"] == "image/png"))
			//&& ($_FILES["file2"]["size"] < 1000000)
			//&& in_array($extension, $allowedExts)
			) 
	{
		if ($_FILES["file2"]["error"] > 0) {
			
			echo "Return Code: " . $_FILES["file2"]["error"] . "<br>";
			
		} else {
			
			move_uploaded_file($_FILES["file2"]["tmp_name"],$uploadfolder.$_FILES["file2"]["name"]);
			$photo_filename2='data/'.$_FILES["file2"]["name"];
			
		}
	} else {
		echo "Invalid file2";
	}
	//
	
	//file_put_contents($logfile,'add@'. $productid."\n", FILE_APPEND | LOCK_EX);
	
	
	
	
	
	// ADD PRODUCT 
	$data = mysqli_query($link,"
		SELECT product_id FROM ".$dbprefix."product WHERE model = '".$productid."'
		") or die(mysqli_error());
	
	
	
	
	if (mysqli_num_rows($data)==0) {
		
		
		$query="				
			INSERT INTO ".$dbprefix."product ( model, sku, upc, ean, jan, isbn, mpn, location, quantity, 
			stock_status_id, image, manufacturer_id, shipping, price, points, tax_class_id, date_available, weight, 
			weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, status, date_added, 
			date_modified, viewed) 
			VALUES (
			'$productid', '', '', '', '', '', '', '', '0', '0', '$photo_filename1', '0', '1', '$price', '0', '$classid', '2014-01-01', 
			'0.00000000', 0, '0.00000000', '0.00000000', '0.00000000',
			0, '1', '1', 0, 1, now(), '0000-00-00 00:00:00',0);				
			
			";
		
		
		
		//IF PRODUCT DOES NOT EXIST			
		$data = mysqli_query($link,$query) or die(mysqli_error());		
		
		//GET PRODCUT ID
		$data = mysqli_query($link,"SELECT LAST_INSERT_ID() as id") or die(mysqli_error());					
		while($alldata = mysqli_fetch_array( $data ))
		{
			$id=$alldata['id'];  	 	
			break;		
		}	
		file_put_contents($logfile, $id."\n", FILE_APPEND | LOCK_EX);
		
		
		//ADD ADDITIONAL IMAGE	
		if ($photo_filename2) {
			$data =mysqli_query($link,"
				INSERT INTO ".$dbprefix."product_image (product_id, image) 
				VALUES ('$id', '$photo_filename2');
				") or die(mysqli_error());							
		}
		//file_put_contents($logfile, $query."#\n", FILE_APPEND | LOCK_EX);
		
		
		//ADD DESCRIPTION       
		
		//FOR ALL LANGUAGES
		for ($lang_id = 1; $lang_id <= 3; $lang_id++) {
			
			$query="
			INSERT INTO ".$dbprefix."product_description (`product_id`, `language_id`, `name`, 
			`description`, `meta_description`, `meta_keyword`, `meta_title`,`tag`) 
			VALUES ('$id', '$lang_id', '$title', '$descr', '$descr', '$title', '$title', '$title');
			";
			file_put_contents($logfile,'#@@'. $query."#\n", FILE_APPEND | LOCK_EX);
			$data = mysqli_query($link,$query) or die(mysqli_error());					
			
		}
		
		//ADD CATEGORY
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."product_to_category (product_id, category_id) 
			VALUES ('$id', '$subcat');
			") or die(mysqli_error());					
		
		
		//ADD STORE                 
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."product_to_store (product_id, store_id) 
			VALUES ('$id', '$store_id');
			") or die(mysqli_error());					
		
		
		
	} else {
		//IF PRODUCT EXISTS UPDATE FIELDS
		//GET TAX CLASS IF DOESN'T EXIST
		while($alldata = mysqli_fetch_array( $data ))
		{
			$id=$alldata['product_id'];  	 	
			break;		
		}	
		/*
				//UPDATE PRODUCT NO PHOTO!!!
				$data = mysql_query("				
				update ".$dbprefix."product set price='$price', tax_class_id='$classid', date_modified=now()
				where product_id=$id
				") or die(mysql_error());				
				
			*/
		//UPDATE PRODUCT
		$data = mysqli_query($link,"				
			update ".$dbprefix."product set image='$photo_filename1', price='$price', tax_class_id='$classid', date_modified=now()
			where product_id=$id
			") or die(mysqli_error());			




		//DELETE ADDITIONAL IMAGE	
		$data =mysqli_query($link,"delete from ".$dbprefix."product_image 
		WHERE product_id='$id' and image='$photo_filename2'") or die(mysqli_error());							


		//ADD ADDITIONAL IMAGE	
		$data =mysqli_query($link,"INSERT INTO ".$dbprefix."product_image (product_id, image) 
		VALUES ('$id', '$photo_filename2')") or die(mysqli_error());							


		
		
		//FOR ALL LANGUAGES
		for ($lang_id = 1; $lang_id <= 3; $lang_id++) {
			
			//UPDATE DESCRIPTION       
			$data = mysqli_query($link,"
			update ".$dbprefix."product_description set `name`='$title', `description`='$descr',
			`meta_description`='$descr', `meta_keyword`='$title', `meta_title`='$title'
			where product_id=$id and language_id=$lang_id			
			") or die(mysqli_error());				

		}			
		
		
		//ADD CATEGORY
		$data = mysqli_query($link,"
			update ".$dbprefix."product_to_category set category_id='$subcat'
			where product_id=$id
			") or die(mysqli_error());					
		
		
		
	}
	
	
	
	

	
	
	
	
}









?> 
