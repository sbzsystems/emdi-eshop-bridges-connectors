<?php
/*------------------------------------------------------------------------
		# EMDI - CSCART BRIDGE by SBZ systems - Solon Zenetzis - version 2.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2015 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/


header('Content-type: text/html; charset=utf-8');

//updated_timestamp ->timestamp
define('AREA', null);
//require 'config.local.php';

$offset= '';
$host = 'localhost';//$config['db_host'];
$user = 'dbuser';//$config['db_user'];
$password = 'WpEfNOAPZb2394';//$config['db_password'];
$db = 'db_csshop';//$config['db_name'];
$dbprefix = 'cscart_';
$product_code_prefix='';
$customer_code_prefix='C';
$once_customer_code_prefix='O';
$discount_title='ΕΚΠΤΩΣΗ ΑΠΟ ΚΟΥΠΟΝΙΑ';
$discount_product_id='ΕΚΚ';


$MAX_FILES_IN_DIR=1000;  //CHECK config.local.php FOR MAX_FILES_IN_DIR
$imageurl='https://www.mysite.gr/images/detailed/';



$lang_code='el';
$tmp_path = $_SERVER['DOCUMENT_ROOT'].'/tmp';
$timezone=$config->offset; 

/*
		
		
		$offset= '';
		$host = $config['db_host'];
		$user = $config['db_user'];
		$password = $config['db_password'];
		$db = $config['db_name'];
		$dbprefix = 'cscart_';
		$product_code_prefix='';
		$customer_code_prefix='IC';
		$onetime_customer_code_prefix='AC';
		$tmp_path = getcwd().'/tmp';
		$timezone=$config->offset; 
	*/



//////////////
$measurement_field='ΜΟΝΑΔΑ ΜΕΤΡΗΣΗΣ';
$monada='ΤΕΜΑΧΙΑ';
$vat_field='ΑΦΜ';
$tax_office_field='ΔΟΥ';
$maintax=24;
// Connects to your Database
$link=mysqli_connect("$host", $user, $password) or die(mysqli_error());
mysqli_select_db($link,"$db") or die(mysqli_error());
mysqli_set_charset($link,"utf8");

$productid=$_REQUEST['productid'];
$stock=$_REQUEST['stock'];
$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
if (!($key==$password)) { exit; }
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




if ($action == 'customers') {
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 11); 
		fclose($handle); 
	}
	
	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
	
	
	//$query="SELECT * FROM ".$dbprefix."users, ".$dbprefix."user_profiles where timestamp>".$lastdate
	//              .' and '.$dbprefix."users.user_id=".$dbprefix."user_profiles.user_id";
	
	$query="
		
		SELECT 
		
		(case when user_id=0 then concat('$once_customer_code_prefix',order_id) else concat('$customer_code_prefix',user_id) end) user_id
		
		,firstname,lastname,s_address,s_zipcode,s_country,s_state,s_city,s_phone,
		phone,email,company,notes
		
		FROM ".$dbprefix."orders
		where timestamp>".$lastdate;
	
	//echo $query;
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));										
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['user_id'];  	 	
		$firstname= $alldata['firstname']; 
		$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['s_address'];  	 	
		$postcode=$alldata['s_zipcode'];  	 	
		$country=$alldata['s_country'];  	 	
		$state=$alldata['s_state'];  	 	
		$city=$alldata['s_city'];  	 	
		$phonenumber=$alldata['s_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		$companyname=$alldata['company'];  	 			
		
		
		/*
				//////////afm///////////////	
				if ($vat_field) {			
				$data2 = mysqli_query($link,
				'SELECT * FROM '.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
				.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
				." and ".$dbprefix."profile_field_descriptions.description='".$vat_field."'"
				.' and '.$dbprefix.'profile_fields_data.object_id='.$id) or die(mysqli_error());
				$afm='';
				while($alldata2 = mysqli_fetch_array( $data2 ))
				{ $afm=$alldata2['value']; }
				}
				
				//////////doy///////////////		
				if ($tax_office_field) {
				$data2 = mysqli_query($link,
				'SELECT * FROM '.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
				.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
				." and ".$dbprefix."profile_field_descriptions.description='".$tax_office_field."'"
				.' and '.$dbprefix.'profile_fields_data.object_id='.$id) or die(mysqli_error());
				$doy='';
				while($alldata2 = mysqli_fetch_array( $data2 ))
				{ $doy=$alldata2['value']; }
				}
				//
				//
			*/
		
		
		
		$rows=
		$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language.";<br>\n";
		
		echo $rows;
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
		pro.product_id,
		prd.product, 
		pra.rate_value,
		prr.price,
		pro.list_price,
		prcd.category,
		pro.amount,
		pro.timestamp,
		pri.product_code,
		pri.combination,
		pri.amount,
		
		(
		SELECT imgs.image_path FROM ".$dbprefix."images_links iml,".$dbprefix."images imgs
		
		where ((iml.object_type='product_option' and iml.object_id=pri.combination_hash)
		)	 
		and imgs.image_id=iml.detailed_id
		) photo,
		
		
		(
		SELECT imgs.image_id FROM ".$dbprefix."images_links iml,".$dbprefix."images imgs
		
		where ((iml.object_type='product_option' and iml.object_id=pri.combination_hash) 
		)	 
		and imgs.image_id=iml.detailed_id
		) image_id,
		
		
		(
		SELECT imgs.image_path FROM ".$dbprefix."images_links iml,".$dbprefix."images imgs
		
		where 
		(iml.object_type='product' and iml.object_id=pro.product_id)
		and imgs.image_id=iml.detailed_id and type='M'
		) photo2,
		
		
		(
		SELECT imgs.image_id FROM ".$dbprefix."images_links iml,".$dbprefix."images imgs
		
		where 
		(iml.object_type='product' and iml.object_id=pro.product_id)
		and imgs.image_id=iml.detailed_id and type='M'
		) image_id2
		
		
		
		
		
		
		
		
		FROM ".$dbprefix."product_options proo
		
		left join ".$dbprefix."product_options_inventory pri on pri.product_id=proo.product_id
		left join ".$dbprefix."products pro on pro.product_id=proo.product_id
		
		left join ".$dbprefix."product_descriptions prd on prd.product_id=pro.product_id and prd.lang_code='$lang_code'
		left join ".$dbprefix."products_categories prc on prc.product_id=pro.product_id
		left join ".$dbprefix."product_prices prr on prr.product_id=pro.product_id and lower_limit=1
		left join ".$dbprefix."category_descriptions prcd on prcd.category_id=prc.category_id and prcd.lang_code='$lang_code'
		left join ".$dbprefix."tax_rates pra on pra.tax_id=pro.tax_ids and pra.rate_type='P'
		where 
		pri.product_code is not null
		
		and pro.timestamp>".$lastdate."
		
		group by concat(pro.product_id,pro.product_code,pri.product_code)
		";
	//	and pro.status='A'
	
	// echo $query;
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error()); 
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ<br>\n";	      
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$image_id=$alldata['image_id'];  	
		$id=$alldata['product_code'];
		
		$photo = $alldata['photo'];   
		if ($photo) {
			$photo = $imageurl.floor($image_id / $MAX_FILES_IN_DIR).'/'.$photo;   
		} else {
			
			$photo2 = $alldata['photo2'];   
			if ($photo2) {
				$image_id2=$alldata['image_id2'];  	
				$photo =$imageurl.floor($image_id2 / $MAX_FILES_IN_DIR).'/'.$photo2;   
			}
		}
		
		
		$name1 = $alldata['product'];   
		$name1 = htmlentities($name1, null, 'utf-8');
		$name1 = str_replace("&nbsp;", " ", $name1);
		$name1 = str_replace("&amp;", ',', $name1);
		$taxrate=$maintax;
		//$monada= $alldata['product_unit']; 
		$taxrate=number_format($taxrate, 2, ',', '');	
		
		$price=$alldata['price'];
		$price=number_format($price, 2, ',', '');
		
		$list_price = $alldata['list_price']; 
		$list_price=number_format($list_price, 2, ',', '');
		
		$amount=$alldata['amount'];
		
		$category= $alldata['category']; 
		$category = htmlentities($category, null, 'utf-8');
		$category = str_replace("&nbsp;", " ", $category);
		$category = str_replace("&amp;", ',', $category);
		//		
		$descr='';
		$inprod='';
		$combination =  $alldata['combination'];
		$combination = explode('_', $combination);
		$tnum=0;
		foreach($combination as $pkey) {  
			$tnum++;
			if ($tnum % 2 == 0) {
				$data2 = mysqli_query($link,"
					
					SELECT option_name,variant_name
					FROM ".$dbprefix."product_option_variants_descriptions opde,".$dbprefix."product_option_variants opds,".$dbprefix."product_options_descriptions oppt
					where opde.variant_id=$pkey and opde.lang_code='$lang_code'
					and opds.variant_id=opde.variant_id
					and oppt.option_id=opds.option_id
					
					") or die(mysqli_error());									
				while($alldata2 = mysqli_fetch_array( $data2 ))
				{ 
					$variant_name=$alldata2['variant_name']; 
					$option_name=$alldata2['option_name']; 
				}
				
				$descr=$descr.$option_name.':'.$variant_name.'\n';
				$inprod=$inprod.' '.$variant_name;
			}
		}
		
		
		
		//////////monada metrhshs///////////////	
		if ($measurement_field) {
			$data2 = mysqli_query($link,"SELECT distinct pfv.product_id, pfvd.variant FROM 
				".$dbprefix."product_features_values pfv,
				".$dbprefix."product_features_descriptions pfd,
				".$dbprefix."product_feature_variant_descriptions pfvd
				
				where
				pfd.feature_id=pfv.feature_id and 
				pfvd.variant_id=pfv.variant_id and
				pfd.description='".$measurement_field."'
				and pfv.product_id=".$alldata['product_id']
			
			
			) or die(mysqli_error());						
			$nmonada=$monada;
			while($alldata2 = mysqli_fetch_array( $data2 ))
			{ $nmonada=$alldata2['variant']; }
		}
		
		
		
		if ($list_price<>0) {
			$fprice="$price|9:$list_price";
		} else {
			$fprice="$price|9:$price";
		}
		
		$row="$product_code_prefix$id;$name1$inprod;$descr;$taxrate;$fprice;;$amount;$nmonada;$category;$photo;$url;<br>\n";			 
		
		$row=html_entity_decode($row);
		echo $row;
		
		
		
	}
	////
	
	
	
	
	
}































////ORDERS


if ($action == 'orders') {
	
	
	
	$query="SELECT 
		ord.order_id,
		ord.user_id,
		ord.status,
		ord.notes,
		ord.timestamp,
		
		ord.payment_surcharge,
		ord.shipping_cost
		,
		
		
		(SELECT group_concat(shi.tracking_number) FROM ".$dbprefix."shipment_items sht,".$dbprefix."shipments shi
		where sht.shipment_id=shi.shipment_id and sht.order_id=ord.order_id) tracking_number
		
		
		
		FROM ".$dbprefix."orders ord 
		
		
		where (status='P' or status='O') and tax_exempt='N' 
		
		
		
		
		order by order_id,user_id desc";
	//echo $query;
	
	$data = mysqli_query($link,$query) or die(mysqli_error()); //
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;ΧΡΗΣΤΗΣ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['order_id'];  	 	
		$userid= $alldata['user_id']; 
		$test=$alldata['status']; 
		$tracking_number=$alldata['tracking_number']; 
		$notes=$alldata['notes']; 
		
		
		$shipping=   str_replace('€','',       $alldata['shipping_cost']); 
		$shipping=   str_replace('.',',',       $shipping); 
		
		$surcharge=   str_replace('€','',       $alldata['payment_surcharge']); 
		$surcharge=   str_replace('.',',',       $surcharge); 
		
		
		if ($tracking_number) {
			$notes=$notes	.' Tracking #:'.$tracking_number;
		}
		
		
		
		$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 
		
		if ($userid==0) {
			echo $id.';'.$once_customer_code_prefix.$id.";$shipping;$surcharge;0;$hmera;$notes;<br>\n";
		} else {
			echo $id.';'.$customer_code_prefix.$userid.";$shipping;$surcharge;0;$hmera;$notes;<br>\n";
		}
		
		
		
	}
}


























if ($action == 'order') {
	
	
	////PRODUCTS
	$query="
		
		SELECT  
		
		ordet.amount,
		prod.product,
		ordet.product_code,
		ordet.price,
		prot.rate_value,
		ordet.extra,
		pro.product_id
		
		
		FROM ".$dbprefix."order_details ordet, ".$dbprefix."products pro
		
		left join ".$dbprefix."product_descriptions prod on  prod.product_id=pro.product_id and prod.lang_code='$lang_code'
		left join ".$dbprefix."product_prices prop on  prop.product_id=pro.product_id		
		left join ".$dbprefix."tax_rates prot	on  prot.tax_id=pro.tax_ids and prot.rate_type='P'
		
		where
		ordet.product_id=pro.product_id
		and ordet.order_id=$orderid 
		
		
		group by pro.product_id
		
		
		";
	
	//echo $query;
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error()); 
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['product']; 
		$product_id = $alldata['product_code']; 
		$product_quantity = $alldata['amount']; 
		$amount=number_format($alldata['price'], 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;		
		
		//$taxrate=number_format($alldata['rate_value'], 2, ',', '');	
		$taxrate=$maintax;
		
		$monada = $alldata['product_unit']; 
		$product_attribute = $alldata['extra']; 
		
		//////////monada metrhshs///////////////	
		if ($measurement_field) {
			$data2 = mysqli_query($link,'SELECT * FROM '
			
			.$dbprefix.'product_features_values,'
			.$dbprefix.'product_features_descriptions,'
			.$dbprefix.'product_feature_variant_descriptions'
			.' where '
			.$dbprefix.'product_features_descriptions.feature_id='.$dbprefix.'product_features_values.feature_id'
			.' and '.$dbprefix.'product_feature_variant_descriptions.variant_id='.$dbprefix.'product_features_values.variant_id'
			.' and '.$dbprefix.'product_features_values.product_id='.$alldata['product_id']
			.' and '.$dbprefix."product_features_descriptions.description='".$measurement_field."'"
			) or die(mysqli_error());						
			$monada='';
			while($alldata2 = mysqli_fetch_array( $data2 ))
			{ $monada=$alldata2['variant']; }
		}
		
		
		
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}
	
	//
	//ADDITIONAL DISCOUNT AS PRODUCT				
	$query="SELECT subtotal_discount FROM ".$dbprefix."orders where order_id=$orderid and subtotal_discount<>0";
	$data = mysqli_query($link,$query) or die(mysqli_error()); //
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$subtotal_discount=$alldata['subtotal_discount'];  	 	
		
		echo $discount_product_id.';'.$discount_title.';;;1;'.$monada.';'.-$subtotal_discount.';'.$taxrate.";0;<br>\n";
		break;
		
		
	}
	
	
	
}













































if ($action == 'confirmorder') {
	
	$data = mysqli_query($link,"UPDATE ".$dbprefix."orders SET status = 'C' WHERE order_id in (".$orderid.")") or die(mysqli_error());
	
	echo $hmera;
}








if ($action == 'cancelorder') {
	
	$data = mysqli_query($link,"UPDATE ".$dbprefix."orders SET status = 'I' WHERE order_id in (".$orderid.")") or die(mysqli_error());
	
	echo $hmera;
}





if (($action == 'updatestock') && (substr($productid,strlen($product_code_prefix)))) {
	
	
	
	//CHECK FOR PENDING QUANTITY OF THE PRODUCT
	$query="
	
	SELECT  
		sum(ordet.amount) psum,
		prod.product,
		ordet.product_code,
		pro.product_id
				
		FROM ".$dbprefix."order_details ordet, ".$dbprefix."products pro
		
		left join ".$dbprefix."product_descriptions prod on  prod.product_id=pro.product_id and prod.lang_code='el'
		
		where
		ordet.product_id=pro.product_id
		
		and ordet.order_id in   
						   
        (        
        SELECT ord.order_id		
		FROM ".$dbprefix."orders ord 
		where (status='P' or status='O') and tax_exempt='N' 
		order by order_id,user_id desc        
        )
                
        and ordet.product_code = '".substr($productid,strlen($product_code_prefix))."'
                  
		group by ordet.product_code	
	";
	$data = mysqli_query($link,$query) or die(mysqli_error()); 
	$psum=0;
	while($alldata = mysqli_fetch_array( $data ))
	{
		$psum=$alldata['psum'];  	 	
		break;		
	}
	
	//echo "##$psum##";

	
	
	// UPDATE PRODUCT STOCK
	$query="update ".$dbprefix."product_options_inventory set amount=".($stock-$psum)." where product_code='".substr($productid,strlen($product_code_prefix))."'";	
	//echo $query;
	
	$data = mysqli_query($link,$query) or die(mysqli_error()); 
	echo $hmera;
}



//header("Location: $goto?expdate=$nextduedate");




?> 					