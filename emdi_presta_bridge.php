<?php
	/*------------------------------------------------------------------------
		# EMDI - PRESTA 2 BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2016 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/
	
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header('Content-Type: text/html; charset=UTF-8');
	
	require 'config/settings.inc.php';
	require 'config/defines.inc.php';
	
	
	
	$logfile = 'emdibridge.txt';
	$offset= '';
	$host = _DB_SERVER_;
	$user = _DB_USER_;
	$password = _DB_PASSWD_;
	$db = _DB_NAME_;
	$dbprefix = _DB_PREFIX_;
	$product_code_prefix='P';
	$customer_code_prefix='IC';
	$onetime_customer_code_prefix='AC';
	$lang_code='el';
	$decimal_point=',';
	$lang_id=1;
	$store_id=0;
	$tmp_path = 'tmp';
	$timezone=$config->offset; 
	$passkey='';
	$relatedchar='^';
	$addonid='PRO';
	$barcode_field='barcode';
	
	
	//////////////
	$measurement='ΤΕΜΑΧΙΑ';
	$measurementaddon='ΠΡΟΣΘΕΤΑ';
	
	//$vat_field='ΑΦΜ';
	//$tax_office_field='ΔΟΥ';
	$maintax=23;
	// Connects to your Database
	$link=mysql_connect("$host", $user, $password) or die(mysql_error());
	mysql_select_db("$db") or die(mysql_error());
	mysql_set_charset('utf8',$link); 
	
	$photourl= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}" ._PS_DIRECTORY_.    'img/p/';	
	$produrl='http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}" ._PS_DIRECTORY_.'index.php?controller=product&id_product=';
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
		$data = mysql_query("SELECT 
		addr.firstname,addr.lastname,cust.email,addr.company,
		addr.address1,addr.address2,addr.postcode,addr.city,addr.phone,addr.phone_mobile,
		addr.vat_number,addr.id_address,
		stt.name state,ctr.name country
		
		
		FROM ".$dbprefix."customer as cust
		left join ".$dbprefix."address addr on addr.id_customer=cust.id_customer
		left join ".$dbprefix."state stt on stt.id_state=addr.id_state
		left join ".$dbprefix."country_lang ctr on ctr.id_country=addr.id_country and ctr.id_lang=$lang_id
		
		
		where cust.active=1
		and (cust.date_add>'".date('Y-m-d H:i:s', $lastdate)."' or cust.date_upd>'".date('Y-m-d H:i:s', $lastdate)."')
		
		
		group by addr.id_address") or die(mysql_error());
		/////////////
		
		
		
		echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$id=$alldata['id_address'];  	 	
			$firstname= $alldata['firstname']; 
			$lastname=$alldata['lastname'];  	 	
			$address1=$alldata['address1'];  	 	
			$tu=$alldata['address2']; 
			
			$postcode=$alldata['postcode'];  	 
			$country=$alldata['country'];  	 	
			$state=$alldata['state'];  	 	
			$city=$alldata['city'];  	 	
			$phonenumber=$alldata['phone'];  	 	
			$mobile=$alldata['phone_mobile'];  	 	
			$email=$alldata['email'];  	 	
			$companyname=$alldata['company'];  	 	
			$afm=$alldata['vat_number'];  	 	
			//$doy=$alldata['doy'];  	 	
			//		$postcode=$alldata['date_added'];  	 	
			
			echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
			.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.';'.$tu.";<br>\n";
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
		
		$query="
		
		SELECT 
		
		
		(select prl.name from ".$dbprefix."product_lang as prl where prl.id_product=prd.id_product and prl.id_lang=$lang_id) as name,pas.id_product_attribute,
		
		
		
		/*DESCRIPTION'S ATTRIBUTES*/
		(
		SELECT GROUP_CONCAT(attl.name SEPARATOR '.') FROM ".$dbprefix."attribute_lang as attl ,".$dbprefix."product_attribute_combination as pac
		where attl.id_lang=$lang_id
		and pac.id_attribute=attl.id_attribute  
		AND pac.id_product_attribute=pas.id_product_attribute
		order by attl.id_attribute
		)  as dtname,
		
		
		/*CUSTOM FIELDS*/		
		(
		SELECT GROUP_CONCAT(concat(
		(SELECT grla.name FROM ".$dbprefix."attribute attr,".$dbprefix."attribute_group_lang grla
		where attr.id_attribute_group=grla.id_attribute_group
		and grla.id_lang=$lang_id
		and attr.id_attribute=pac.id_attribute
		limit 1)
		,':',attl.name) SEPARATOR '|')

		FROM ".$dbprefix."attribute_lang as attl ,".$dbprefix."product_attribute_combination as pac
		where attl.id_lang=$lang_id
		and pac.id_attribute=attl.id_attribute  
		AND pac.id_product_attribute=pas.id_product_attribute
		order by attl.id_attribute
		)  as attname,
		
		
		
		
		
		
		
		
		
		
		(SELECT avst.quantity FROM ".$dbprefix."stock_available avst
		where avst.id_product_attribute=pas.id_product_attribute
		and avst.id_product=prd.id_product) as tquantity,
		
		
		
		
		
		prd.price,prd.wholesale_price,prd.id_product,prd.ean13,quantity,id_category_default,
		
		(select rate from ".$dbprefix."tax_rule  psr,".$dbprefix."tax  pst
		where psr.id_tax=pst.id_tax and id_tax_rules_group=prd.id_tax_rules_group
		group by pst.id_tax) as ptax,
		
		(SELECT clng.name FROM ".$dbprefix."category_lang clng
		where clng.id_category=id_category_default and clng.id_lang=$lang_id) as pcat,
		
		
		
		(SELECT psi.id_image FROM ".$dbprefix."image psi 
		where psi.cover=1 and psi.id_product=prd.id_product order by psi.position limit 1) as imageid,
		
		
		
		(SELECT atim.id_image FROM ".$dbprefix."product_attribute_image atim where atim.id_product_attribute=pas.id_product_attribute limit 1) as imageidatt
		
		
		
		FROM  ".$dbprefix."product as prd,".$dbprefix."product_attribute_shop as pas
		
		
		
		where prd.active=1
		
		and pas.id_product=prd.id_product
		
		
		and (prd.date_add>'".date('Y-m-d H:i:s', $lastdate)."' or prd.date_upd>'".date('Y-m-d H:i:s', $lastdate)."')
		
		
		
		";
		//group by prd.id_product
		
		
		//echo $query;
		$data = mysql_query($query) or die(mysql_error()); 
		
		
		
		//---------------------------
		//date('Y-m-d H:i:s', $lastdate)
		
		echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ;<br>\n";
		
		
		
		//$photourl
		//$produrl
		
		
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$id=$alldata['id_product'];  	 	
			$name= $alldata['name'].' '.$alldata['dtname']; 
			$taxrate= $alldata['ptax'];
			$price=$alldata['price'];
			$wholesale_price=$alldata['wholesale_price'];
		    $custom=$barcode_field.':'.$alldata['ean13'].'\n'.str_replace('|','\n',$alldata['attname']).'\n';
			$quantity=$alldata['tquantity']; 
			$category= $alldata['pcat']; 
			$id_category_default= $alldata['id_category_default']; 
			$id_product_attribute= $alldata['id_product_attribute']; 
			
			
			
			//$price=number_format($price, 2, ',', '');
			
			$price=$price+(($price*$taxrate)/100);
			
			
			$price=str_replace('.',$decimal_point,$price);
			$taxrate=str_replace('.',$decimal_point,$taxrate);
			$wholesale_price=str_replace('.',$decimal_point,$wholesale_price);
			
			
			
			//IF ATTRIBUTE IMAGE
			$imageidatt= $alldata['imageidatt'];
			if ($imageidatt) {
				$imageid= $imageidatt; 
				} else {
				$imageid= $alldata['imageid']; 
			}
			
			
			if (strlen($imageid)>1) {
				$imgfolder=substr($imageid, 0, 1).'/'.substr($imageid, 1, 1);
				} else {
				$imgfolder=$imageid;
			}
			
			
			if ($id_product_attribute) {
				$id_product_attribute='.'.$id_product_attribute;
			}
			
			
			echo $product_code_prefix.$id.$id_product_attribute.';'.$name.';'.$custom.';'.$taxrate.';'.$price.';'.$wholesale_price.';'.$quantity.';'.
			
			$measurement.";".$category.";".
			
			$photourl.$imgfolder.'/'.$imageid.'-thickbox_default.jpg'.
			
			
			";".$produrl.$id.";".$id_category_default.";<br>\n";			 
			
			
			
			
			
		}
		////
		
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'orders') {
		
		
		
		$data = mysql_query("
		
		
		SELECT 
		ord.id_order as order_id,
		ord.id_address_delivery as user_id,
		ord.date_upd as timestamp,
		ord.total_shipping as shipping,
		ord.total_discounts as discount,
		ord.total_wrapping as delcost,
		ord.payment,
		(SELECT msg.message FROM ps_message msg where msg.id_order=ord.id_order) message

		FROM ".$dbprefix."orders as ord

		where
		ord.current_state in (1,10)
		

		
		") or die(mysql_error()); //
		
		
		echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;ΧΡΗΣΤΗΣ;<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$id=$alldata['order_id'];  	 	
			$userid= $alldata['user_id']; 
			//$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
			$hmera=$alldata['timestamp'] ;
			$shipping=   str_replace('€','',       $alldata['shipping']); 
			$shipping=   str_replace('.',',',   $shipping); 
			$message=$alldata['message'];
			if ($message) {
			$comment=$alldata['message'] .' '. $alldata['payment'];
			} else { $comment=$alldata['payment']; }
			
			
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";0;0;".$hmera.";".$comment.";<br>\n";
			
			
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'order') {
		////order
		
		
		$data = mysql_query("
		SELECT 
		ord.id_order as order_id,
		ord.product_name as product,
		concat(ord.product_id,'.',ord.product_attribute_id) as product_code,
		ord.unit_price_tax_incl as price,
		ord.unit_price_tax_excl as priceexcl,
		ord.product_quantity as amount,
		ord.original_product_price as priceb,
		ord.reduction_percent as discount

		FROM ".$dbprefix."order_detail as ord
		where ord.id_order=".$orderid) or die(mysql_error()); 
		
		
		echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$description = $alldata['product']; 	
			$product_id = $alldata['product_code']; 			
			$product_quantity = $alldata['amount'];
			
			$discount=number_format($alldata['discount'], 2, ',', '');	
			
			$price=$alldata['price'];
			$taxrate=number_format(     (($price*100)/$alldata['priceexcl'])-100                     , 2, ',', '');				
			$amount=number_format(                  ($price*100) /  (100-$discount)                    , 2, ',', '');
			
			
			
			$monada = $measurement; 
			$product_attribute = $alldata['extra']; 
					
			echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
				
		
			
			
			
			
			
			
			
			//$product_id = $alldata['product_code'].'.'.$alldata1['name'];
			
			
			
			
			
			
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'confirmorder') {
		
		$data = mysql_query("update ".$dbprefix."order set order_status_id=5 where order_id in (".$orderid.")") or die(mysql_error());
		
		echo $hmera;
	}
	
	
	
	if ($action == 'updatestock') {		
		
		//GET PRODUCT_ID AND PRODUCT OPTION VALUDE ID BASED ON MODEL
		$data = mysql_query("
		
		SELECT pov.product_option_value_id, pov.product_id
		FROM ".$dbprefix."option_value_description ovd,".$dbprefix."product_option_value pov,".$dbprefix."product pro
		where ovd.option_value_id=pov.option_value_id  
		and pro.product_id=pov.product_id
		and language_id=".$lang_id."  
		and concat(model,'.',name)='".substr($productid,strlen($product_code_prefix))."'
		
		") or die(mysql_error());
		$prodopvid ='';
		$productmain_id='';
		while($alldata = mysql_fetch_array( $data ))
		{
			$prodopvid = $alldata['product_option_value_id']; 	
			$productmain_id = $alldata['product_id']; 	
		}
		
		
		// SET QUANTITY BASED ON OPTION
		if ($prodopvid) {
			$data = mysql_query("
			
			update ".$dbprefix."product_option_value poov
			set poov.quantity=".$stock." where poov.product_option_value_id=".$prodopvid 
			
			) or die(mysql_error());					
		}
		
		
		
		
		
		
		//GET TOTAL QUANTITY OF OPTIONS
		$data = mysql_query("
		
		SELECT sum(pov.quantity) qua
		FROM ".$dbprefix."option_value_description ovd,".$dbprefix."product_option_value pov,".$dbprefix."product pro
		where ovd.option_value_id=pov.option_value_id  
		and pro.product_id=pov.product_id
		and language_id=".$lang_id."
		and pro.product_id=".$productmain_id."
		
		") or die(mysql_error());
		$totalqua ='';
		while($alldata = mysql_fetch_array( $data ))
		{
			$totalqua = $alldata['qua']; 	
		}
		
		
		
		
		file_put_contents($logfile, substr($productid,strlen($product_code_prefix)).'##'.$totalqua.'#'. $prodopvid .'#'.$productmain_id."\n", FILE_APPEND | LOCK_EX);	
		
		//SET TOTAL QUANTITY
		$data = mysql_query("update ".$dbprefix."product set quantity=".$totalqua."  where product_id=".$productmain_id) or die(mysql_error());
		
		
		
		
		
		echo $hmera;
	}
	
	
	
	if ($action == 'cancelorder') {
		
		$data = mysql_query("update ".$dbprefix."order set order_status_id=7 where order_id in (".$orderid.")") or die(mysql_error());
		
		echo $hmera;
		
	}
	
	
	
	//header("Location: $goto?expdate=$nextduedate");
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'redirect') {
		
		//customer_code_prefix
		
		
		// EDIT PRODUCT
		if ($productid) {
			$data = mysql_query("
			SELECT * FROM ".$dbprefix."product WHERE model = '".$productid."'
			") or die(mysql_error());
			
			//echo mysql_num_rows($data);
			
			if (mysql_num_rows($data)<>0) {
				//GET PRODCUT ID
				while($alldata = mysql_fetch_array( $data ))
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
		
		//$len=$_REQUEST['len'];
		$pieces = explode("|", $productid);
		
		//$productid = substr(base_enc($pieces[1]),0,$pieces[0]); 
		//$productid2 = substr(base_enc($pieces[1]),0,$pieces[0]); 
		$productid = trim(base_enc($pieces[1]));
		//$productid = substr(base64_decode($productid),0,$len); 
		
		
		
		
		$title=base_enc($_REQUEST['title']);
		$descr=base_enc($_REQUEST['descr']);    
		
		
		$price=$_REQUEST['price'];
		$cat=$_REQUEST['cat']+100000;
		$subcat=$_REQUEST['subcat'];
		$tax=$_REQUEST['tax'];
		
		$cattitle=trim(base_enc($_REQUEST['cattitle']));      
		$subcattitle=trim(base_enc($_REQUEST['subcattitle']));      
		
		
		
		$logtext=$pieces[0].'|'.$productid.'|'.$title.'|'.$descr.'|'.$price.'|'.$cat.'|'.$subcat.'|'.$tax.'|'.$cattitle.'|'.$subcattitle."\n";
		file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);
		
		//
		//CHECK IF TAX EXISTS ELSE ADD
		$data = mysql_query("
		select * from ".$dbprefix."tax_rule as tru
		left join ".$dbprefix."tax_rate as tra on tru.tax_rate_id=tra.tax_rate_id
		left join ".$dbprefix."tax_class as tcl on  tru.tax_class_id=tcl.tax_class_id
		
		where title='EMDI $tax'
		
		") or die(mysql_error());
		
		
		
		
		
		
		
		
		if (mysql_num_rows($data)==0) {
			
			//ADD DEFAULT EMDI TAX CLASS IF DOESN'T EXIST
			$data = mysql_query("
			INSERT INTO ".$dbprefix."tax_class (tax_class_id, title, description, date_added, date_modified) 
			VALUES (NULL, 'EMDI $tax', 'EMDI $tax', now(), '0000-00-00 00:00:00');
			") or die(mysql_error());			
			
			
			//GET CLASS ID
			$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
			while($alldata = mysql_fetch_array( $data ))
			{
				$classid=$alldata['id'];  	 	
				break;		
			}	
			
			//ADD TAX	
			$data = mysql_query("
			INSERT INTO ".$dbprefix."tax_rate (tax_rate_id, geo_zone_id, name, rate, type, date_added, date_modified) 
			VALUES (NULL, '0', '$tax%', '$tax', 'P', now(), '0000-00-00 00:00:00');
			") or die(mysql_error());			
			
			
			//GET TAX ID
			$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
			while($alldata = mysql_fetch_array( $data ))
			{
				$taxid=$alldata['id'];  	 	
				break;		
			}	
			
			//ADD RULE
			$data = mysql_query("
			INSERT INTO ".$dbprefix."tax_rule (tax_rule_id, tax_class_id, tax_rate_id, based, priority) 
			VALUES (NULL, '$classid', '$taxid', 'payment', '1');
			") or die(mysql_error());			
			
			
			
			
			
			} else {
			//GET TAX CLASS IF DOESN'T EXIST
			while($alldata = mysql_fetch_array( $data ))
			{
				$classid=$alldata['tax_class_id'];  	 	
				break;		
			}	
		}
		//
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		// CREATE CATEGORY IF DOES NOT EXIST
		/*$data = mysql_query("
			SELECT * FROM ".$dbprefix."category WHERE category_id=$cat
			") or die(mysql_error());
			if (mysql_num_rows($data)==0) {
			
			
			
			
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
			VALUES 
			('$cat', NULL, '0', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
			") or die(mysql_error());			
			
			//ADD CATEGORY DESCRIPTION
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
			VALUES ('$cat', '$lang_id', '$cattitle', '', '', '');	
			") or die(mysql_error());			
			
			//ADD CATEGORY STORE
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
			VALUES ('$cat', '$store_id');
			") or die(mysql_error());			
			
			
			//ADD CATEGORY PATH
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
			VALUES ('$cat', '$cat', '0')
			") or die(mysql_error());			
			
			
			
			
			
			}
			//
		*/
		
		
		
		
		
		
		
		// CREATE SUBCATEGORY IF DOES NOT EXIST
		/*
			$data = mysql_query("
			SELECT * FROM ".$dbprefix."category WHERE category_id=$subcat
			") or die(mysql_error());
			if (mysql_num_rows($data)==0) {
			
			
			
			
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category (category_id, image, parent_id, top, ".$dbprefix."category.column, sort_order, status, date_added, date_modified) 
			VALUES 
			('$subcat', NULL, '$cat', '0', '0', '0', '1', now(), '0000-00-00 00:00:00');
			") or die(mysql_error());			
			
			//ADD SUBCATEGORY DESCRIPTION
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category_description (category_id, language_id, name, description, meta_description, meta_keyword) 
			VALUES ('$subcat', '$lang_id', '$subcattitle', '', '', '');	
			") or die(mysql_error());			
			
			//ADD SUBCATEGORY STORE
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category_to_store (category_id, store_id) 
			VALUES ('$subcat', '$store_id');
			") or die(mysql_error());			
			
			
			//ADD SUBCATEGORY CATEGORY PATH
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
			VALUES ('$subcat', '$cat', '1')
			") or die(mysql_error());			
			
			//ADD SUBCATEGORY  PATH 
			$data = mysql_query("
			INSERT INTO ".$dbprefix."category_path (category_id ,path_id ,level) 
			VALUES ('$subcat', '$subcat', '2')
			") or die(mysql_error());			
			
			
			
			
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
		$data = mysql_query("
		SELECT * FROM ".$dbprefix."product WHERE model = '".$productid."'
		") or die(mysql_error());
		if (mysql_num_rows($data)==0) {
			
			//IF PRODUCT DOES NOT EXIST			
			$data = mysql_query("				
			INSERT INTO ".$dbprefix."product (product_id, model, sku, upc, ean, jan, isbn, mpn, location, quantity, 
			stock_status_id, image, manufacturer_id, shipping, price, points, tax_class_id, date_available, weight, 
			weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, status, date_added, 
			date_modified, viewed) 
			VALUES (
			NULL, '$productid', '', '', '', '', '', '', '', '0', '0', 'data/".$_FILES["file"]["name"]."', '0', '1', '$price', '0', '$classid', '10-10-2014', 
			'0.00000000', 0, '0.00000000', '0.00000000', '0.00000000',
			0, '1', '1', 0, 1, now(), '0000-00-00 00:00:00',0);				
			
			") or die(mysql_error());				
			
			
			//GET PRODCUT ID
			$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
			while($alldata = mysql_fetch_array( $data ))
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
			$data = mysql_query("
			INSERT INTO ".$dbprefix."product_description (product_id, language_id, name, 
			description, meta_description, meta_keyword, tag) 
			VALUES ('$id', '$lang_id', '$title', '$descr', '', '', '');
			") or die(mysql_error());					
			
			
			//ADD CATEGORY
			$data = mysql_query("
			INSERT INTO ".$dbprefix."product_to_category (product_id, category_id) 
			VALUES ('$id', '$subcat');
			") or die(mysql_error());					
			
			
			//ADD STORE                 
			$data = mysql_query("
			INSERT INTO ".$dbprefix."product_to_store (product_id, store_id) 
			VALUES ('$id', '$store_id');
			") or die(mysql_error());					
			
			
			
			} else {
			//IF PRODUCT EXISTS UPDATE FIELDS
			//GET TAX CLASS IF DOESN'T EXIST
			while($alldata = mysql_fetch_array( $data ))
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
			$data = mysql_query("				
			update ".$dbprefix."product set image='data/".$_FILES["file"]["name"]."', price='$price', tax_class_id='$classid', date_modified=now()
			where product_id=$id
			") or die(mysql_error());				
			
			
			//UPDATE DESCRIPTION       
			$data = mysql_query("
			update ".$dbprefix."product_description set name='$title', description='$descr'
			where product_id=$id
			") or die(mysql_error());					
			
			
			//ADD CATEGORY
			$data = mysql_query("
			update ".$dbprefix."product_to_category set category_id='$subcat'
			where product_id=$id
			") or die(mysql_error());					
			
			
			
		}
		
		
		
		
		
		
		
		
		
	}
	
	
	
	
	
	function base_enc($encoded) {
		$result='';
		for($i=0, $len=strlen($encoded); $i<$len; $i+=4){
			$result=$result.base64_decode( substr($encoded, $i, 4) );
		}
		return $result;
	}
	
	
?> 			