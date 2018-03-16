<?php
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
$option_id1=15;
$option_id2=1001;
$option_id3=1002;


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
$lang_code='el';
$lang_id=2;
$store_id=0;
$tmp_path = DIR_SYSTEM.'tmp';
$timezone=$config->offset; 
$passkey='';
$relatedchar='^';
$addonid='PRO';              //EMDI addon product ID
$manu_field='ΚΑΤΑΣΚΕΥΑΣΤΗΣ'; //EMDI manufacturer custom field
$size_field='ΜΕΓΕΘΟΣ';       //EMDi size custom field
$barcode='barcode';          //EMDi barcode custom field
$discount_title='ΚΟΥΠΟΝΙ ΑΝΤΙΚΑΤΑΒΟΛΗΣ';
$discount_product_id='ΚΠΑΝ';

//////////////
$measurement='ΤΕΜΑΧΙΑ';      //EMDI measurement unit title for all
$measurementaddon='ΠΡΟΣΘΕΤΑ';//EMDI measurement unit title for options

//$vat_field='ΑΦΜ';
//$tax_office_field='ΔΟΥ';
$maintax=24;
$paymenttax=24;
// Connects to your Database
$link =mysqli_connect("$host", $user, $password) or die(mysqli_error($link));
mysqli_select_db($link,"$db") or die(mysql_error());
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




//GET SQL TIME
$data = mysqli_query($link,"SELECT NOW() dtime") or die(mysql_error());
while($alldata = mysqli_fetch_array( $data ))
{
	$dtime=$alldata['dtime'];  	 
	break;
}
//




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
	
	//GET SQL TIME
	$data = mysqli_query($link,"SELECT NOW() dtime") or die(mysqli_error($link));
	while($alldata = mysqli_fetch_array($data ))
	{
		$dtime=$alldata['dtime'];  	 
		break;
	}
	//
	fwrite($Handle, $dtime); 
	fclose($Handle); 			
}

if ($action == 'productsok') {
	$file = $tmp_path."/products_".$key; 
	$handle = fopen($file, 'w');
	
	//GET SQL TIME
	$data = mysqli_query($link,"SELECT NOW() dtime") or die(mysqli_error($link));
	while($alldata = mysqli_fetch_array($data ))
	{
		$dtime=$alldata['dtime'];  	 
		break;
	}
	//
	fwrite($handle, $dtime); 
	fclose($handle); 	
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($action == 'customers') {
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 20); 
		fclose($handle); 
	}
	//echo date('Y-m-d H:i:s', $lastdate);
	
	
	
	$query="
			
		SELECT 
		
		
		
		
		(case when customer_id=0 then concat('$onetime_customer_code_prefix',order_id) else concat('$customer_code_prefix',customer_id) end) as user_id,
		
		email,
		telephone as b_phone,
		fax as phone,
		payment_firstname as firstname,payment_lastname as lastname,
		payment_company as company,
		payment_address_1 as b_address,				
		payment_address_2 as c_address,
		payment_city as b_city,
		payment_postcode as b_zipcode,
		payment_country  as b_country,
		payment_zone as b_state,
		date_added as dd,
		
		custom_field,
		date_added
		
		
		FROM ".$dbprefix."order
		where date_added>'". $lastdate."'
        and email<>''
      
		group by REPLACE(REPLACE(	concat(customer_id,email,payment_company,payment_lastname,payment_postcode),' ',''),'.','')
		
		";
		
		
		
	/////////////
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	/////////////
	
	
	
	echo "CUSTOMER ID;FIRST NAME;LAST NAME;ADDRESS;ZIP;COUNTRY;CITY/STATE;AREA;PHONE;MOBILE;EMAIL;VAT;TAX OFFICE;COMPANY;OCCUPATION;LANGUAGE;PO BOX;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['user_id'];  	 	
		$firstname= $alldata['firstname']; 
		$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['b_address'];  	 	
		$tu=$alldata['c_address']; 
		
		$postcode=$alldata['b_zipcode'];  	 
		$country=$alldata['b_country'];  	 	
		$state=$alldata['b_state'];  	 	
		$city=$alldata['b_city'];  	 	
		$phonenumber=$alldata['b_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		$date_added=$alldata['date_added'];  	 	
		
		
		$custom_field=$alldata['custom_field'];  	 
		//$cfld=unserialize($custom_field);
		$cfld=json_decode($custom_field,true);
		//var_dump(json_decode($cfld, true));
		
		$companyname= $cfld['5'];  	 	
		$afm=$cfld['1'];  	 	
		$doy=$cfld['2'];  	 	
		$epaggelma=$cfld['3'];  
		
		//$today = date("D M j G:i:s T Y");
		//file_put_contents('customers1.log',var_export($cfld), FILE_APPEND | LOCK_EX);	 	
		
		//print_r($test);
		
		//echo $test[products][1][price];
		
		#echo $afm."<br>";
		
		//		$postcode=$alldata['date_added'];  	 	
		//if($email){
		echo $id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";
		//}
	}
	
	
	
}




































if ($action == 'products') {
	
	
	$file = $tmp_path."/products_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 20); 
		fclose($handle); 
	}
	
	//---------------------------
	
	$query="
		SELECT 
		
		pro.model as product_code,
		descr.name as product,
		tra.rate as rate_value,
		pro.price as price,pro.quantity,
		
		/*CATEGORIES*/
		(SELECT group_concat(cdes.name) FROM ".$dbprefix."category ctr,".$dbprefix."category_description cdes,".$dbprefix."product_to_category as ptc
		where ctr.category_id=cdes.category_id 
		and cdes.language_id=$lang_id
		and ctr.category_id=ptc.category_id
		and ptc.product_id=pro.product_id
		order by ctr.sort_order) as category
		
		,pro.date_modified as dd
		,pro.image
		,pro.product_id
		,pro.isbn as barcodd
		
		/*SPECIAL PRICE*/
		, (SELECT pros.price FROM ".$dbprefix."product_special as pros where pros.product_id=pro.product_id limit 1) as price2
		
		,manu.name manname
		
		
		FROM  ".$dbprefix."product_description as descr
		
		left join ".$dbprefix."product as pro
		on pro.product_id=descr.product_id
		
		left join ".$dbprefix."tax_rule as tru
		on tru.tax_class_id=pro.tax_class_id
		
		left join ".$dbprefix."tax_rate as tra
		on tra.tax_rate_id=tru.tax_rate_id
		
		left join ".$dbprefix."manufacturer as manu
		on manu.manufacturer_id=pro.manufacturer_id
		
		where 
		descr.language_id=$lang_id
		
		and (pro.date_added>'". $lastdate."' or pro.date_modified>'".$lastdate."')
		
		group by descr.product_id
		";
	
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	
	// echo $query;
	//---------------------------
	//date('Y-m-d H:i:s', $lastdate)
	
	echo "PRODUCT ID;DESCRIPTION1;DESCRIPTION2;TAX;PRICE;PURCHASE PRICE;AVAILABILITY;MEASUREMENT UNIT;CATEGORY;PHOTO;URL;CATEGORY ORDER;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['product_code'];  	 	
		$name1= $alldata['product']; 
		$taxrate= $alldata['rate_value'];
		
		if ($taxrate=='') {
			$taxrate=number_format($maintax, 2, ',', '');	
		} else {
			$taxrate=number_format($taxrate, 2, ',', '');	
		}
		
		$price=$alldata['price'];
		//$price=number_format($price, 2, ',', '');
		
		$manufacturer=$manu_field.':'.$alldata['manname'].'\n';
		
		
		
		
		$purchaseprice=$alldata['barcodd'];
		
		
		
		$barc='';
		//$barc=$barcode.':'.$alldata['barcodd'].'\n';
		$price2=$alldata['price2'];
		$quantity=$alldata['quantity'];
		//$price2=number_format($price2, 2, ',', '');
		
		
		//if ($price2) { $price=$price2; }
		
		
		
		$category= $alldata['category']; 
		//$category_id= $alldata['category_id']; 
		
		
		
		$rowtext=$product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'. str_replace('.',',',       $price+$price_add).";$purchaseprice;$quantity;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
		$rowtext=str_ireplace("&amp;","&",$rowtext);
		$rowtext=str_ireplace("&quot;","'",$rowtext);
		$rowtext=str_ireplace("&#039;","'",$rowtext);
		echo $rowtext;	
		
		
		
		
		
		
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
					$code_= '.'.$alldata1['option_value_id'].'.'.$alldata2['option_value_id'];
					$price_add_= $alldata1['price']+$alldata2['price']; 
					
					
					$rowtext=$product_code_prefix.$id.$code_.';'.$name1.' '.$name_.';;'.$taxrate.';'. str_replace('.',',',       $price+$price_add).";$purchaseprice;$quantity;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
					$rowtext=str_ireplace("&amp;","&",$rowtext);
					$rowtext=str_ireplace("&quot;","'",$rowtext);
					$rowtext=str_ireplace("&#039;","'",$rowtext);
					echo $rowtext;	
					//echo $product_code_prefix.$id.$code_.';'.$name1.';'.$name_.';'.$taxrate.';'. str_replace('.',',',       $price+$price_add).";".";;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
					
				}
			}
			
			
			
			
		} else {
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
					$name_= $size_field.':'.$alldata1['name'].'\n'.$manufacturer.$barc; 
					$code_= '.'.$alldata1['option_value_id'];
					$price_add_= $alldata1['price']; 
					
					$rowtext=$product_code_prefix.$id.$code_.';'.$name1.';'.$name_.';'.$taxrate.';'.str_replace('.',',', $price+$price_add_ ).";$purchaseprice;$quantity;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
					$rowtext=str_ireplace("&amp;","&",$rowtext);
					$rowtext=str_ireplace("&quot;","'",$rowtext);
					$rowtext=str_ireplace("&#039;","'",$rowtext);
					echo $rowtext;	
					//echo $product_code_prefix.$id.$code_.';'.$name1.';'.$name_.';'.$taxrate.';'.str_replace('.',',', $price+$price_add_ ).";".";;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
					
				}
				
			} 
			/*
					else {
					
					//IF NO OPTION PER ITEM
					$rowtext=$product_code_prefix.$id.';'.$name1.';'.$manufacturer.$barc.';'.$taxrate.';'.str_replace('.',',',      $price).";;$quantity;".$measurement.";".$category.";".$photourl.$alldata['image'].";".$produrl.$alldata['product_id'].";<br>\n";			 
					$rowtext=str_ireplace("&amp;","&",$rowtext);
					$rowtext=str_ireplace("&quot;","'",$rowtext);
					$rowtext=str_ireplace("&#039;","'",$rowtext);
					
					echo $rowtext;	
					}
				*/
			
			
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
		
		(select ordt.value from ".$dbprefix."order_total as ordt where ordt.order_id =ord.order_id  
		and ordt.code='shipping' limit 0,1) as shipping,
		
		(select ordd.value from ".$dbprefix."order_total as ordd where ordd.order_id =ord.order_id  
		and ordd.code='GOP_COD_Fee' limit 0,1) as tcost
		
		FROM ".$dbprefix."order as ord
		where
		ord.order_status_id in (1,2)
		group by ord.order_id
		") or die(mysqli_error($link)); //
	
	
	echo "ORDER ID;CUSTOMER ID;SHIPPING COST;PAYMENT COST;DISCOUNT;DATE;NOTE;USER;<br>\n";
	
	while($alldata = mysqli_fetch_array($data ))
	{
		$id=$alldata['order_id'];  	 	
		$userid= $alldata['user_id']; 
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['timestamp'] ;
		
		$shipping=   str_replace('€','',       $alldata['shipping']); 
		$tcost=   str_replace('€','',       $alldata['tcost']);
		
		//$overall=(float)$shipping;
		$shipping=($shipping*100)/(100+$paymenttax);
		//$overall=(string)$overall;
		$shipping=str_replace('.',',',$shipping); 
		
		
		//$overall=(float)$tcost;
		$tcost=($tcost*100)/(100+$paymenttax);
		//$overall=(string)$overall;
		$tcost=str_replace('.',',',$tcost); 
		
		
		
		
		
		$comment=$alldata['comment'] ;
		
		
		if ($userid==0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";$shipping;$tcost;0;".$hmera.";".$comment.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";$shipping;$tcost;0;".$hmera.";".$comment.";<br>\n";
		}
		
	}
}


























if ($action == 'order') {
	////order
	
	
	$data = mysqli_query($link,"
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
		
		where ord.order_id=".$orderid."
		
		group by pro.model 
		
		
		") or die(mysqli_error($link)); 
	
	
	echo "PRODUCT ID;DESCRIPTION 1;DESCRIPTION 2;DESCRIPTION 3;QUANTITY;MEASUREMENT UNIT;PRICE;TAX;DISCOUNT;START DATE;END DATE;POSITION;ORDER ID;<br>\n";
	//echo '#'.mysql_num_rows ( $data).'#';
	
	
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['product']; 	
		$product_id = $alldata['product_code']; 			
		$product_quantity = $alldata['amount']; 
		$amount=number_format($alldata['price']/$product_quantity, 4, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;					
		$taxrate=$maintax;//number_format($alldata['rate_value'], 2, ',', '');				
		$monada = $measurement; 
		$product_attribute = $alldata['extra']; 
		
		
		//echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		
		////split prostheta   				
		$datap = mysqli_query($link,"
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
			
			
			
			where ord.order_id=".$orderid." and pord.product_id=".$alldata['product_id']
		
		
		
		."
			
			and 
			(
			(select pov.option_id from ".$dbprefix."product_option_value pov where pov.product_option_value_id=ord.product_option_value_id)=".$option_id1."
			or (select pov.option_id from ".$dbprefix."product_option_value pov where pov.product_option_value_id=ord.product_option_value_id)=".$option_id2."
			or (select pov.option_id from ".$dbprefix."product_option_value pov where pov.product_option_value_id=ord.product_option_value_id)=".$option_id3."
			)
			
			
			group by product_option_value_id
			order by order_option_id asc
			"
		
		
		) or die(mysqli_error($link)); 
		
		
		
		//echo $alldata['product_id'].'###';
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
		if ((mysqli_num_rows ($datap)==1) && ($valuep)  ) {
			$addonstext='';
			echo $product_code_prefix.$product_id.'.'.$valuep.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
			
		} else {
			
			echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
			
			
		}
		
		echo $addonstext;
		
		
		
		
		
		
		
		//$product_id = $alldata['product_code'].'.'.$alldata1['name'];
		
		
		
		
		
		
	}
	
	
	
	
	//
	//ADDITIONAL DISCOUNT AS PRODUCT				
	$query="SELECT value FROM ".$dbprefix."order_total where order_id=$orderid and code='coupon'";
	$data = mysqli_query($query) or die(mysqli_error($link)); //
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$subtotal_discount=$alldata['value'];
		$subtotal_discount=($subtotal_discount*100)/(100+$paymenttax);
		$subtotal_discount=number_format($subtotal_discount, 4, ',', '');  	 	
		
		if ($subtotal_discount<>0) {
			echo $discount_product_id.';'.$discount_title.';;;1;'.$monada.';'.$subtotal_discount.';'.$paymenttax.";0;<br>\n";
		}
		break;
		
		
	}
	
	
	
	
	
}





















































if ($action == 'confirmorder') {
	
	$data = mysqli_query($link,"update ".$dbprefix."order set order_status_id=5 where order_id in (".$orderid.")") or die(mysql_error());
	
	echo $hmera;
}



if ($action == 'updatestock') {
	//echo "update ".$dbprefix."product set quantity=".$stock."  where product_id='".substr($productid,strlen($product_code_prefix))."'"; 
	$data = mysqli_query($link,"update ".$dbprefix."product set quantity=".$stock."  where model='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
	
	
	$data = mysqli_query($link,"
		
		SELECT pov.product_option_value_id
		FROM ".$dbprefix."option_value_description ovd,".$dbprefix."product_option_value pov,".$dbprefix."product pro
		where ovd.option_value_id=pov.option_value_id  
		and pro.product_id=pov.product_id
		and language_id=2  
		and concat(model,'.',option_value_id)='".substr($productid,strlen($product_code_prefix))."'
		
		
		") or die(mysqli_error($link));
	$prodopvid ='';
	while($alldata = mysqli_fetch_array( $data ))
	{
		$prodopvid = $alldata['product_option_value_id']; 	
	}
	
	
	
	if ($prodopvid) {
		$data = mysqli_query($link,"
			
			update ".$dbprefix."product_option_value poov
			set poov.quantity=".$stock." where poov.product_option_value_id=".$prodopvid 
		
		) or die(mysqli_error($link));
		
		
	}
	
	
	//$data = mysql_query("update ".$dbprefix."product set quantity=".$stock."  where model='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
	
	
	
	
	
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
		
		if (mysqli_num_rows($data)<>0) {
			//GET PRODCUT ID
			while($alldata = mysqli_fetch_array( $link,$data ))
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
	
	
	
	
	$title=$_REQUEST['title'];
	$descr=$_REQUEST['descr'];    	
	$price=$_REQUEST['price'];
	$cat=$_REQUEST['cat']+100000;
	$subcat=$_REQUEST['subcat'];
	$tax=$_REQUEST['tax'];
	//$price=($price *100)/(100+$tax);
	
	$cattitle=trim($_REQUEST['cattitle']);      
	$subcattitle=trim($_REQUEST['subcattitle']);      
	
	$logtext=$pieces[0].'|'.$productid.'|'.$title.'|'.$descr.'|'.$price.'|'.$cat.'|'.$subcat.'|'.$tax.'|'.$cattitle.'|'.$subcattitle."\n";
	file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
	
	//
	//CHECK IF TAX EXISTS ELSE ADD
	$data = mysqli_query($link,"
		select * from ".$dbprefix."tax_rule as tru
		left join ".$dbprefix."tax_rate as tra on tru.tax_rate_id=tra.tax_rate_id
		left join ".$dbprefix."tax_class as tcl on  tru.tax_class_id=tcl.tax_class_id
		
		where title='EMDI $tax'
		
		") or die(mysqli_error($link));
	
	
	
	#$logtext="before_update";
	#file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
	
	
	if (mysqli_num_rows($data)==0) {
		
		//ADD DEFAULT EMDI TAX CLASS IF DOESN'T EXIST
		$data = mysqli_query($link,"
			INSERT INTO ".$dbprefix."tax_class (tax_class_id, title, description, date_added, date_modified) 
			VALUES (NULL, 'EMDI $tax', 'EMDI $tax', now(), '0000-00-00 00:00:00');
			") or die(mysqli_error($link));			
		
		
		//GET CLASS ID
		$data = mysql_query($link,"SELECT LAST_INSERT_ID() as id") or die(mysqli_error($link));					
		while($alldata = mysql_fetch_array( $data ))
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
		while($alldata = mysqli_fetch_array( $link,$data ))
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
		while($alldata = mysqli_fetch_array( $link,$data ))
		{
			$classid=$alldata['tax_class_id'];  	 	
			break;		
		}	
	}
	//
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// CREATE CATEGORY IF DOES NOT EXIST
	$data = mysqli_query($link,"
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
	
	
	
	
	
	
	
	
	// CREATE SUBCATEGORY IF DOES NOT EXIST
	$data = mysqli_query($link,"
		SELECT * FROM ".$dbprefix."category WHERE category_id=$subcat
		") or die(mysqli_error($link));
	if (mysqli_num_rows($data)==0) {
		
		
		
		
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
	
	// UPLOAD AND REPLACE PHOTO#2
	$uploadfolder=getcwd().'/image/data/';
	
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
			
		}
	} else {
		echo "Invalid file2";
	}
	//
	
	
	
	
	
	
	
	// ADD PRODUCT 
	$data = mysqli_query($link,"
		SELECT product_id FROM ".$dbprefix."product WHERE model = '".$productid."'
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
				$data = mysql_query("
				INSERT INTO ".$dbprefix."product_image (product_image_id, product_id, image, sort_order) 
				VALUES (NULL, '$id', 'data/".$_FILES["file"]["name"]."', '');
				") or die(mysql_error());					
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
		while($alldata = mysqli_fetch_array( $link,$data ))
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
			update ".$dbprefix."product set image='data/".$_FILES["file"]["name"]."', price='$price', tax_class_id='$classid', date_modified=now()
			where product_id=$id
			") or die(mysqli_error($link));				
		
		
		//UPDATE DESCRIPTION       
		$data = mysqli_query($link,"
			update ".$dbprefix."product_description set `name`='$title', `description`='$descr'
			where product_id=$id and language_id=$lang_id			
			") or die(mysqli_error($link));					
		
		
		//ADD CATEGORY
		$data = mysqli_query($link,"
			update ".$dbprefix."product_to_category set category_id='$subcat'
			where product_id=$id
			") or die(mysqli_error($link));					
		
		
		
	}
	
	
	
	
	
	
	
	
	
}






?> 					