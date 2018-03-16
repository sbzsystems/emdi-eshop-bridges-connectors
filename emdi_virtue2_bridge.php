<?php
	/*------------------------------------------------------------------------
		# EMDI - VIRTUEMART 2 BRIDGE by SBZ systems - Solon Zenetzis - version 1.9
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2015 sbzsystems.com. All Rights Reserved.
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
	//LANGUAGE
	$lang='el_gr';
	//MAIN TAX
	$maintax=23;
	// Connects to your Database
	$link=mysql_connect("$host", $user, $password) or die(mysql_error());
	mysql_select_db("$db") or die(mysql_error());
	mysql_set_charset('utf8',$link); 
	
	$product_code_prefix='P';
	$customer_code_prefix='C';
	$sta2= 'Χρώμα';     //expand custom field expanded
	$sta3= 'Μέγεθος';
	$sta4= 'Υλικό';
	
	$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
	$productid=$_REQUEST['productid'];
	$stock=$_REQUEST['stock'];
	$action=$_REQUEST['action'];       // PRODUCT CODE
	$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
	$key=$_REQUEST['key'];       // PRODUCT CODE
	if (!($key==$password)) { exit; }
	///////////////////////////////////
	//echo "\xEF\xBB\xBF";   //with bom
	
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
	
	
	
	
	if ($action == 'customers') {
		
		$file = $tmp_path."/customers_".$key; 
		$lastdate=0;
		if (file_exists($file)) {
			$handle = fopen($file, 'r'); 
			$lastdate = fread($handle, 11); 
			fclose($handle); 
		}
		
		$data = mysql_query("SELECT *".
		" FROM ".$dbprefix."virtuemart_userinfos ".
		"left join ".$dbprefix."users on id=virtuemart_user_id ".
		"left join ".$dbprefix."virtuemart_countries on ".$dbprefix."virtuemart_countries.virtuemart_country_id=".$dbprefix."virtuemart_userinfos.virtuemart_country_id ".
		"left join ".$dbprefix."virtuemart_states on ".$dbprefix."virtuemart_states.virtuemart_state_id=".$dbprefix."virtuemart_userinfos.virtuemart_state_id ".
		"where ".$dbprefix."virtuemart_userinfos.modified_on>'".date('Y-m-d H:i:s', $lastdate)."'" ) or die(mysql_error()); 
		//echo "SELECT * FROM ".$dbprefix."vm_user_info where mdate>".$lastdate;
		
		echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$id=$alldata['virtuemart_user_id'];  	 	
			$firstname= $alldata['first_name']; 
			$lastname=$alldata['last_name'];  	 	
			$address1=$alldata['address_1'];  	 	
			$postcode=$alldata['zip'];  	 	
			$country=$alldata['country_name'];  	 	
			$state=$alldata['state_name'];  	 	
			$city=$alldata['city'];  	 	
			$phonenumber=$alldata['phone_1'];  	 	
			$mobile=$alldata['phone_2'];  	 	
			$email=$alldata['email'];  	 	
			$companyname=$alldata['company'];  	 	
			
			echo $customer_code_prefix.$id.'ss;'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
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
		$query="SELECT 
		
		
		
		
		product_sku,product_name,calc_value,calc_value,product_price,product_price,category_name,file_url,
		mainpro.virtuemart_product_id
		
		
		,
		
		(
		SELECT max(v2.customsforall_value_label)
		
		FROM h4bt2_virtuemart_product_custom_plg_customsforall v1
		
		left join  h4bt2_virtuemart_custom_plg_customsforall_values v2
		on v2.customsforall_value_id=v1.customsforall_value_id
		
		left join h4bt2_virtuemart_customs v3
		on v3.virtuemart_custom_id=v2.virtuemart_custom_id
		
		where v1.virtuemart_product_id=mainpro.virtuemart_product_id
		and v3.custom_title='$sta2'
		) colort
		
		
		,
		
		(
		SELECT max(v2.customsforall_value_name)
		
		FROM h4bt2_virtuemart_product_custom_plg_customsforall v1
		
		left join  h4bt2_virtuemart_custom_plg_customsforall_values v2
		on v2.customsforall_value_id=v1.customsforall_value_id
		
		left join h4bt2_virtuemart_customs v3
		on v3.virtuemart_custom_id=v2.virtuemart_custom_id
		
		where v1.virtuemart_product_id=mainpro.virtuemart_product_id
		and v3.custom_title='$sta3'
		) sizet
		
		,
		
		(
		SELECT max(v2.customsforall_value_name)
		
		FROM h4bt2_virtuemart_product_custom_plg_customsforall v1
		
		left join  h4bt2_virtuemart_custom_plg_customsforall_values v2
		on v2.customsforall_value_id=v1.customsforall_value_id
		
		left join h4bt2_virtuemart_customs v3
		on v3.virtuemart_custom_id=v2.virtuemart_custom_id
		
		where v1.virtuemart_product_id=mainpro.virtuemart_product_id
		and v3.custom_title='$sta4'
		) materialt
		
		,
		
		
		(
		SELECT mf_name FROM ".$dbprefix."virtuemart_product_manufacturers man1,".$dbprefix."virtuemart_manufacturers_".$lang." man2
		where man1.virtuemart_manufacturer_id=man2.virtuemart_manufacturer_id
		and man1.virtuemart_product_id=mainpro.virtuemart_product_id 
		) manufact
		
		
	
		
		
		from ".$dbprefix."virtuemart_products mainpro
		
		
		
		
		
		left join ".$dbprefix."virtuemart_product_prices as prodpri
		on mainpro.virtuemart_product_id =prodpri.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_calcs
		on prodpri.product_tax_id=".$dbprefix."virtuemart_calcs.virtuemart_calc_id
		
		
		left join ".$dbprefix."virtuemart_product_categories as cat
		on mainpro.virtuemart_product_id =cat.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_categories_".$lang."
		on cat.virtuemart_category_id =".$dbprefix."virtuemart_categories_".$lang.".virtuemart_category_id
		
		left join ".$dbprefix."virtuemart_products_".$lang." 
		on mainpro.virtuemart_product_id =".$dbprefix."virtuemart_products_".$lang.".virtuemart_product_id
		
		
		
		
		left join ".$dbprefix."virtuemart_product_medias as pmed
		on mainpro.virtuemart_product_id =pmed.virtuemart_product_id
		
		left join ".$dbprefix."virtuemart_medias as vmed
		on pmed.virtuemart_media_id =vmed.virtuemart_media_id and vmed.file_type='product'
		
		
		
		

		
		
		
		
		
		
		
		
		where 	mainpro.published=1
		and mainpro.modified_on>'".date('Y-m-d H:i:s', $lastdate)."'
		
		and product_name<>''
		
		group by mainpro.virtuemart_product_id
		
		";
		
		
		//echo $query;
		$data = mysql_query($query) or die(mysql_error()); 
		
		
		
		//left join ".$dbprefix."vm_category
		//on ".$dbprefix."virtuemart_categories.category_id =".$dbprefix."virtuemart_categories.category_id
		
		echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$id=$alldata['product_sku'];  	 	//$alldata['virtuemart_product_id'];  	 	
			$name1= $alldata['product_name']; 
			//$name2= $alldata['attribute']; 
			$taxrate=$alldata['calc_value'];
			//$monada= $alldata['product_unit']; 
			
			//$price=$alldata['product_price']+($alldata['product_price']*$taxrate);
			$taxrate=number_format($alldata['calc_value'], 2, ',', '');	 	
			$price=number_format($alldata['product_price']      + (($alldata['product_price']*$taxrate)/100)                                 , 2, ',', '');
			// $price=number_format($price, 2, ',', '');
			$category= $alldata['category_name']; 
			//$category_id= $alldata['category_id']; 
			
			//$taxrate=number_format(100*$taxrate, 2, ',', '');	
			
			
			//http://www.gshoes.gr/
			
			 	
			
			if ($alldata['file_url']) {
				//$photo= 'http://'.$_SERVER['HTTP_HOST'].'/'.$alldata['file_url']; 
				$photo= "C:\\eshop-images\\". str_ireplace( 'images/stories/virtuemart/product/','', $alldata['file_url']); 
				
			} else {
				$photo='';
			}
			$url= 'http://'.$_SERVER['HTTP_HOST'].'/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$alldata['virtuemart_product_id']; 
			$name2=$alldata['product_desc'].'\nΧΡΩΜΑ:'.$alldata['colort'].'\nΜΕΓΕΘΟΣ:'.$alldata['sizet'].'\nΜΑΡΚΑ:'.$alldata['manufact'].'\nΥΛΙΚΟ:'.$alldata['materialt']; 
			//$name2=$alldata['product_desc']; 
			
			
			
			
			
			
			
			/////////////////////////////
			/////////////////////////////
			/*
				$ppid=$alldata['virtuemart_product_id'];
				
				$prv='';
				if ($sta2) {
				// COMBINE ATTRIBUTES
				
				echo 	"select * from	".$dbprefix."virtuemart_product_customfields 
				left join ".$dbprefix."virtuemart_customs on 
				".$dbprefix."virtuemart_customs.virtuemart_custom_id=".$dbprefix."virtuemart_product_customfields.virtuemart_custom_id
				where virtuemart_product_id=".$ppid."
				and ".$dbprefix."virtuemart_customs.custom_title='".$sta2."'";
				
				$data2 = mysql_query("select * from	".$dbprefix."virtuemart_product_customfields 
				left join ".$dbprefix."virtuemart_customs on 
				".$dbprefix."virtuemart_customs.virtuemart_custom_id=".$dbprefix."virtuemart_product_customfields.virtuemart_custom_id
			where virtuemart_product_id=".$ppid."
			and ".$dbprefix."virtuemart_customs.custom_title='".$sta2."'"
			) or die(mysql_error()); 
			
			while($alldata2 = mysql_fetch_array( $data2 ))
			{				
			$t2=$alldata2['custom_title'];
			$v2=$alldata2['custom_value'];
			$cpar2=$alldata2['custom_param'];
			
			//////////////////
			if ($v2=='drop') {
			$words = preg_split('/,/', 
			
			between(
			
			mb_convert_encoding(		 preg_replace("/\\\\u([0-9abcdef]{4})/", "&#x$1;", '\u'.$cpar2)
			, 'UTF-8', 'HTML-ENTITIES')
			
			,'custom_drop":"','"'
			
			));
			
			foreach ($words as $k => $word) {						
			$prv= ' '.$word;										//$t2.':'.
			} 			
			//echo $word.'#<br>';
			}
			}
			}
			*/
			//////////////////////////////////
			
			
			
			
			
			//echo $product_code_prefix.$id.';'.$name1.';'.mb_substr(   $word,0,2,"utf-8").''.';'.$taxrate.';'.$price.";;;".$monada.";".$category.";".$photo.";".$url.";<br>\n";		
			echo $product_code_prefix.$id.';'.$name1.';'.$name2.''.';'.$taxrate.';'.$price.";;;".$monada.";".$category.";".$photo.";".$url.";<br>\n";		
			
			
			
			
		}
		////
		
		
		
		
		
		
		
		
		
		//		echo $product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$monada.";".$category.";<br>\n";			 
		
		//if ($name2) {
		//	$words = preg_split('/;/', $name2);
		
		//	foreach ($words as $k => $word) {
		//		$prword = preg_split('/,/', $word);
	//		echo 'A'.$category_id.';'.$prword[0].';;'.$taxrate.';0;;;ΠΡΟΣΘΕΤΟ;'.$category.";<br>\n";			 
	//	} 
	//}
	
	
	
	////
	
	
	
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'orders') {
	$data = mysql_query("SELECT * FROM ".$dbprefix."virtuemart_orders  where order_status in ('P','U','C') and order_tax<>0 ") or die(mysql_error()); //
	
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
	$id=$alldata['virtuemart_order_id'];  	 	
	$userid= $alldata['virtuemart_user_id']; 
	
	//$hmera=gmdate("d/m/Y H:i:s", $alldata['modified_on'] + 3600*($timezone+date("I"))); 
	$hmera=$alldata['modified_on'] ; 
	
	
	
	
	echo $id.';C'.$userid.";0;0;0;".$hmera.";<br>\n";
	
	}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'order') {
	
	////PRODUCTS
	$data = mysql_query("SELECT * from ".$dbprefix."virtuemart_order_items
	
	left join ".$dbprefix."virtuemart_products as produ
	on produ.virtuemart_product_id =".$dbprefix."virtuemart_order_items.virtuemart_product_id
	
	left join ".$dbprefix."virtuemart_product_prices as prodpri
	on produ.virtuemart_product_id =prodpri.virtuemart_product_id
	
	left join ".$dbprefix."virtuemart_calcs
	on prodpri.product_tax_id=".$dbprefix."virtuemart_calcs.virtuemart_calc_id
	
	where virtuemart_order_id=".$orderid) or die(mysql_error()); 
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
	$description = $alldata['order_item_name']; 
	$product_id = $alldata['product_sku'];// $alldata['virtuemart_product_id']; 
	$product_quantity = $alldata['product_quantity']; 
	//$amount=number_format($alldata['product_final_price'], 2, ',', '');
	$amount=number_format($alldata['product_final_price'], 2, ',', '');
	
	
	$taxrate=number_format($alldata['calc_value'], 2, ',', '');
	//$monada = $alldata['product_unit']; 
	$product_attribute = $alldata['product_attribute']; 
	
	echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.";0;<br>\n";
	
	
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
	echo 'PRO;'.$phrase.";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;<br>\n";
	}
	
	} 
	
	
	
	
	
	}
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'confirmorder') {
	
	$data = mysql_query("UPDATE ".$dbprefix."virtuemart_orders SET order_status = 'S' WHERE virtuemart_order_id in (".$orderid.")") or die(mysql_error());
	
	echo $hmera;
	
	}
	
	
	
	if ($action == 'cancelorder') {
	
	$data = mysql_query("UPDATE ".$dbprefix."virtuemart_orders SET order_status = 'X' WHERE virtuemart_order_id in (".$orderid.")") or die(mysql_error());
	
	echo $hmera;
	
	}
	
	
	
	
	
	if ($action == 'updatestock') {
	
	//$data = mysql_query("UPDATE ".$dbprefix."virtuemart_products SET product_in_stock = ".$stock." WHERE product_sku ='".$productid."'") or die(mysql_error());
	$data = mysql_query("UPDATE ".$dbprefix."virtuemart_products SET product_in_stock = ".$stock." WHERE product_sku ='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());
	//echo '#'.$productid.'#';
	echo $hmera;
	}
	
	
	
	
	?> 
	
	
	
	
		