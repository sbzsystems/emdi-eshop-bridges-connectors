<?php
/*------------------------------------------------------------------------
		# EMDI - MAGENTO BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2017 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/
//echo "test";
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');
//error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(-1);
//ini_set('display_errors', 'On');


//GET DB SETTINGS FROM /app/etc/local.xml
$host = 'localhost';
$user = 'jolovies';
$password = '6M8u8Ceqz49QA5dt';
$db = 'jolovies';
$dbprefix = '';



$passkey='KGMx722'; // EMDI access password
$logfile = 'emdibridge.txt';
$offset= 0;
$tmp_path = 'tmp';
$timezone= 0; 
$shoppergroup1=2;  $emdipricelist1=9; //PRICE CATALOG 1 ID
$shoppergroup2=3;  $emdipricelist2=10; //PRICE CATALOG 2 ID
$model='ΜΑΡΚΑ'; //Model field in EMDI
$store_id=0;


//////////////
//LANGUAGE
$currencyid=47;
$lang='el_gr';
//MAIN TAX
$maintax=24;
$monada='ΤΕΜΑΧΙΑ';
// Connects to your Database
$link=mysqli_connect("$host", $user, $password,$db) or die(mysqli_error($link));	
mysqli_set_charset($link,'utf8');



$product_code_prefix='';
$customer_code_prefix='C';
$once_customer_code_prefix='O';



$url = $_SERVER['REQUEST_URI']; //returns the current URL
$parts = explode('/',$url);
$dir = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") .$_SERVER['SERVER_NAME'];
for ($i = 0; $i < count($parts) - 1; $i++) {
	$dir .= $parts[$i] . "/";
}

$photourl=$dir."media/catalog/product";
$produrl=$dir;
$customerid=$_REQUEST['customerid'];



$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 

$productid=$_REQUEST['productid'];

$stock=$_REQUEST['stock'];

$action=$_REQUEST['action'];       // PRODUCT CODE

$orderid=$_REQUEST['orderid'];       // PRODUCT CODE

$key=$_REQUEST['key'];       // PRODUCT CODE



//echo '#'.$key.'#';

if (!($key==$passkey)) { exit; }
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
	
	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
	$query="
		
		SELECT 
		
		
		(case when cus.customer_id is null then concat('$once_customer_code_prefix',cus.parent_id) else concat('$customer_code_prefix',cus.customer_id) end) custid,
		
		cus.parent_id,
		cus.customer_id,cus.customer_address_id,cus.fax,cus.region,cus.postcode,
		cus.lastname,cus.firstname,cus.street,cus.city,cus.email,cus.telephone,
		cus.country_id,cus.address_type,cus.prefix,cus.middlename,cus.suffix,cus.company,cus.vat_id,cus.country_id,
		
		(SELECT sub.updated_at  FROM ".$dbprefix."sales_order_grid sub where sub.entity_id=cus.parent_id) updated,
		
		-- GET VAT
		(SELECT sfo.customer_taxvat FROM sales_order sfo where sfo.entity_id=cus.parent_id) taxvat
		
		
		FROM ".$dbprefix."sales_order_address cus
		where cus.address_type='shipping'
		
		and 
		(SELECT sub.updated_at  FROM ".$dbprefix."sales_order_grid sub where sub.entity_id=cus.parent_id)
		>'".date('Y-m-d H:i:s', $lastdate)."'	
		
		
		group by
		cus.parent_id,cus.customer_id,cus.customer_address_id,cus.fax,cus.region,cus.postcode,cus.lastname,cus.firstname,cus.street,cus.city,cus.email,cus.telephone,
		cus.country_id,cus.address_type,cus.prefix,cus.middlename,cus.suffix,cus.company,cus.vat_id,
		(case when cus.customer_id is null then concat('$once_customer_code_prefix',cus.parent_id) else concat('$customer_code_prefix',cus.customer_id) end)
		
		
		";
	
	
	
	
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link));
	
	// echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		
		$id=$alldata['custid'];  
		//$id2=$alldata['parent_id'];  
		
		$firstname= $alldata['firstname'];
		$lastname=$alldata['lastname'];
		$vat=$alldata['vat_id'];
		$taxvat=$alldata['taxvat'];
		if (!$vat) {
			$vat=$taxvat;
		}
		
		
		
		$region=$alldata['region'];
		$zip=$alldata['postcode'];
		$country=$alldata['country_id'];
		
		$city=$alldata['city'];
		$company=$alldata['company'];
		$fax1=$alldata['fax1'];
		$street=$alldata['street'];
		$phonenumber=$alldata['telephone'];
		
		$email=$alldata['email'];		
		
		
		echo $id.';'.$firstname.';'.$lastname.';'.$street.';'.$zip.';'.$country.';'.$region.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$vat.';'.$doy.';'.$company.';'.$epaggelma.';'.$language.";<br>\n";
		
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
	$query="
		
		
		
		
		SELECT 
		pro.entity_id,
		pro.updated_at,
		pro.sku,
		
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_varchar sub where sub.entity_id=pro.entity_id and sub.attribute_id=73 and sub.store_id=2) product_name,
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_varchar sub where sub.entity_id=pro.entity_id and sub.attribute_id=73 and sub.store_id=$store_id) product_name_en,
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_varchar sub where sub.entity_id=pro.entity_id and sub.attribute_id=87 and sub.store_id=$store_id) image_file,
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_varchar sub where sub.entity_id=pro.entity_id and sub.attribute_id=98 and sub.store_id=$store_id) product_page,
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_varchar sub where sub.entity_id=pro.entity_id and sub.attribute_id=183 and sub.store_id=$store_id) EAN,
		
		
		
		-- BRAND
		(SELECT aopv.value  FROM  catalog_product_entity_int enti,eav_attribute_option_value aopv  
		where enti.store_id=$store_id and enti.attribute_id=81 and enti.entity_id=pro.entity_id 
		and enti.value=aopv.option_id and aopv.store_id=$store_id limit 1) brand,
		
		
			
		
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_text sub where sub.entity_id=pro.entity_id and sub.attribute_id=72 and sub.store_id=$store_id) description,
		
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_decimal sub where sub.entity_id=pro.entity_id and sub.attribute_id=77 and sub.store_id=$store_id) product_price,
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_decimal sub where sub.entity_id=pro.entity_id and sub.attribute_id=82 and sub.store_id=$store_id) product_weight,
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_decimal sub where sub.entity_id=pro.entity_id and sub.attribute_id=76 and sub.store_id=$store_id) special_price,
		(SELECT sub.value FROM ".$dbprefix."catalog_product_entity_decimal sub where sub.entity_id=pro.entity_id and sub.attribute_id=80 and sub.store_id=$store_id) weight_,
				
		(SELECT sub.qty FROM ".$dbprefix."cataloginventory_stock_item sub where sub.product_id=pro.entity_id ) stock,
		
		(SELECT sub.qty FROM ".$dbprefix."cataloginventory_stock_status sub where sub.product_id=pro.entity_id and sub.website_id=1) stock2,
		
		
		
		-- TAX RATE
		(
		SELECT 
		(SELECT rat.rate FROM tax_calculation cal,tax_calculation_rate rat
		where cal.tax_calculation_rate_id=rat.tax_calculation_rate_id
		and sub.tax_class_id=cal.product_tax_class_id 
		and rat.code='EU-GR'
		)
		FROM ".$dbprefix."catalog_product_index_price sub
		where sub.entity_id=pro.entity_id
		limit 1) tax_rate,
		
		
		-- PRICE
		(
		SELECT 
		sub.price
		FROM ".$dbprefix."catalog_product_index_price sub
		where sub.entity_id=pro.entity_id
		limit 1) mainprice,
		
		-- CATEGORY
		(SELECT ent.value FROM ".$dbprefix."catalog_category_product cat,".$dbprefix."catalog_category_entity_varchar ent
		where cat.category_id=ent.entity_id
		and cat.product_id=pro.entity_id and ent.attribute_id=120 limit 1) category_title,
		
		
		-- CATEGORY ID
		(SELECT cat.category_id FROM ".$dbprefix."catalog_category_product cat,".$dbprefix."catalog_category_entity_varchar ent
		where cat.category_id=ent.entity_id		
		and (cat.product_id=pro.entity_id 
		or cat.product_id=(SELECT prr.parent_id FROM ".$dbprefix."catalog_product_relation prr where prr.child_id=pro.entity_id limit 1))
		and ent.attribute_id=41 limit 1) category_id,
		
		
		-- ADDITIONAL FEE
	/*
		(
		SELECT adfe.feeamount FROM ".$dbprefix."additionalfees adfe
		where adfe.status=1 and feetype='Fixed' and
		adfe.category=
		(SELECT cat.category_id FROM ".$dbprefix."catalog_category_product cat,".$dbprefix."catalog_category_entity_varchar ent
		where cat.category_id=ent.entity_id
		and (cat.product_id=pro.entity_id 
		or cat.product_id=(SELECT prr.parent_id FROM ".$dbprefix."catalog_product_relation prr where prr.child_id=pro.entity_id limit 1))
		and ent.attribute_id=41 limit 1) limit 1
		) add_fee,
		
		*/
		
		0 add_fee,
		
		
		
		
		-- CATALOG PRICE 1
		(SELECT ctp.value FROM ".$dbprefix."catalog_product_entity_tier_price ctp
		where ctp.customer_group_id=$shoppergroup1 and ctp.entity_id=pro.entity_id limit 1) price_cat1,
		
		-- CATALOG PRICE 2
		(SELECT ctp.value FROM ".$dbprefix."catalog_product_entity_tier_price ctp
		where ctp.customer_group_id=$shoppergroup2 and ctp.entity_id=pro.entity_id limit 1) price_cat2
		
		
		
		
		FROM ".$dbprefix."catalog_product_entity pro
		
		
		
		
		where 
		pro.updated_at>'".date('Y-m-d H:i:s', $lastdate)."'
		
		
		
		";
	
	
	
	//	file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);
	//echo $query;	
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	
	
	
	//left join ".$dbprefix."vm_category
	//on ".$dbprefix."virtuemart_categories.category_id =".$dbprefix."virtuemart_categories.category_id
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ;ΒΑΡΟΣ<br>\n";

	
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		
		$virtuemart_product_id=$alldata['entity_id'];  	 	
		$id=$alldata['sku'];  	 	
		$shelf=$alldata['product_gtin'];
		$idmpn=$alldata['product_mpn'];  	 
		$name1= $alldata['product_name']; 
		$name2= $alldata['attribute']; 
		$add_fee= $alldata['add_fee']; 
		$brand=$alldata['brand'];
		$EAN=$alldata['EAN'];
		$product_name_en=$alldata['product_name_en'];
		
		
		$weight=$alldata['product_weight'];
		$weight=   str_replace('.',',',       $weight);
		$weight=1;
		//$weight=number_format($weight, 4, ',', '');
		
		
		
		$price=$alldata['product_price'];
		$mainprice=$mainprice['mainprice'];
		
		
		if ($add_fee<>0) {
			$price=$price+$add_fee;
		}
		
		if ($alldata['special_price']<>0) {
			$price=number_format($alldata['special_price'], 2, ',', '');
		}
		
		$category= $alldata['category_title']; 
		$category_id= $alldata['category_id']; 
		$product_page= $alldata['product_page']; 
		
		
		$price=number_format($price, 2, ',', '');
		//$price=$price +($price*$taxrate/100);
		 
		$stock=number_format($alldata['stock'], 2, ',', '');   
		
		if ($alldata['image_file']) {
			$photolink=$photourl.$alldata['image_file'];
		} else {$photolink='';}
		if ($product_page) {
			$urllink=$produrl.$product_page;
		} else {$urllink='';}
		
		
		$taxrate= $alldata['tax_rate']; 
		//$taxrate=number_format($alldata['tax_rate'], 2, ',', '');	
		//$taxrate=gettype($taxrate);
		
		
		if (!$taxrate) {
			$taxrate=$maintax;
			}
		else {$taxrate= $alldata['tax_rate'];}
		
		
		//$taxrate=number_format(100*$taxrate, 2, ',', '');	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$price_cat1='';
		$price_cat2='';
		if ($alldata['price_cat1']) {
			$price_cat1='|'.$emdipricelist1.':'.number_format($alldata['price_cat1'], 4, ',', '');
		}
		if ($alldata['price_cat2']) {
			$price_cat2='|'.$emdipricelist2.':'.number_format($alldata['price_cat2'], 4, ',', '');
		}
		
		
			   
		
		
		
		$genfields=$model.':'.$brand.'\n';
		
		//"|".$idmpn

		$rowtext=$product_code_prefix.$id.';'.$name1.';EAN:'.$EAN.'\nBRAND:'.$brand.'\nΑΓΓΛΙΚΗ_ΠΕΡΙΓΡΑΦΗ:'.$product_name_en.';'.$taxrate.';'.$price.$price_cat1.$price_cat2.";;$stock;".$monada.";".$category.";".$photolink.";".$urllink.";".$category_id.";".$weight.";<br>\n";
		//$rowtext=$product_code_prefix.$id.";".$name1.";ΡΑΦΙ:".$shelf.'\n'.";;;;;;".$category.";".$photolink.";".$urllink.";<br>\n";
		
		$rowtext=str_ireplace("&amp;","&",$rowtext);
		$rowtext=str_ireplace("&quot;","'",$rowtext);
		$rowtext=str_ireplace("&#039;","'",$rowtext);	
		$rowtext=str_ireplace("&#38;","&",$rowtext);
		$rowtext=str_ireplace("&#038;","&",$rowtext);
		$rowtext=str_ireplace("&#39;","'",$rowtext);	
		$rowtext=str_ireplace("&#62;",">",$rowtext);	
		$rowtext=str_ireplace("&gt;",">",$rowtext);	
		
			
		
		
		echo $rowtext;			 

		
		
	}
	////
	
	
	
	
	
}
















if ($action == 'orders') {
	
	
	$query="
		
		SELECT sal.updated_at,sal.created_at,sal.entity_id,
		sal.shipping_amount,sal.total_invoiced,sal.customer_note,
		sal.customer_id,sal.increment_id,
		
		(select sub.customer_id from ".$dbprefix."sales_order_address sub where sub.parent_id=sal.entity_id 
		and sub.address_type='shipping') customer_id2,
		
		
		-- GET SHIPPING DESCRIPTION
		(SELECT sfo.shipping_description FROM sales_order sfo where sfo.entity_id=sal.entity_id) shipping_description,
		
		-- GET SHIPPING AMOUNT
		(select sfo.base_shipping_incl_tax from sales_order sfo  where sfo.entity_id=sal.entity_id )
		 as shipping,
		 
		-- GET COD AMOUNT
		(select sfcod.amount  from amasty_cash_on_delivery_fee_order sfcod  where sfcod.order_id=sal.entity_id )
		 as cod
		
		FROM ".$dbprefix."sales_order sal
		where sal.status in ('processing','pending')
		
		order by sal.created_at
		
		";		
	//echo $query;
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
	// file_put_contents('debug.log',"SELECT * FROM ".$dbprefix."virtuemart_orders ord,".$dbprefix."virtuemart_paymentmethods_el_gr pay,".$dbprefix."virtuemart_shipmentmethods_el_gr ship  where pay.virtuemart_paymentmethod_id=ord.virtuemart_paymentmethod_id and ship.virtuemart_shipmentmethod_id=ord.virtuemart_shipmentmethod_id and ord.order_status in ('U') and ord.order_tax<>0 " , FILE_APPEND | LOCK_EX);
	
	
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['entity_id'];  	 	
		$userid= $alldata['customer_id2']; 
		$customer_address_id=$alldata['customer_address_id']; 
		$shipping_description=$alldata['shipping_description']; 
		$increment_id=$alldata['increment_id']; 
		
		
		
		//$hmera=gmdate("d/m/Y H:i:s", $alldata['modified_on'] + 3600*($timezone+date("I"))); 
		$hmera=$alldata['created_at'] ; 
		$comment=$alldata['customer_note'];
		$shipment=$alldata['shipping_amount'];
		//$payment=$alldata['payment_name'];		
		//$coupon_discount=$alldata['coupon_discount'];
		
		
		//$comment=.$comment.' '.$increment_id;
		$comment=$increment_id;
		
		//gia courier ama xreiastei
		//$comment=$comment.' '.$shipping_description;
		
		$comment=str_ireplace("\r",'',$comment);
		$comment=str_ireplace("\n",' ',$comment);
		$comment=str_ireplace(";",'',$comment);
		
		
		
		$shipping=$alldata['shipping'];
		$shipping=   str_replace('.',',',       $shipping);
		
		
		
		$cod=$alldata['cod'];
		$cod=$cod*1.24;
		$cod=number_format($cod, 2, ',', '');
		$cod=   str_replace('.',',',       $cod);
		
		
		
		
		
		
		if (!$userid) {
			echo $id.';'.$once_customer_code_prefix.$id.";".$shipping.";".$cod.";".$coupon_discount.";".$hmera.";".$comment."<br>\n";				
		} else {
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$cod.";".$coupon_discount.";".$hmera.";".$comment."<br>\n";				
		}
		
		
	}
}











































if ($action == 'order') {
	
	$query="
		
		SELECT 
		order_id,
		product_options,
		product_type,
		sku, 
		name,
		qty_ordered,
		row_total,
		tax_percent,
		price_incl_tax
		
		
		FROM ".$dbprefix."sales_order_item
		
		where order_id=$orderid
		and product_type<>'bundle'
		and qty_ordered<>0
		and row_total<>0
		
		";//	and qty_shipped<>0
	
	//file_put_contents($logfile, $query."####\n", FILE_APPEND | LOCK_EX);	
	$data = mysqli_query($link, $query) or die(mysqli_error($link)); 
	
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;ΕΝΑΡΞΗ;ΛΗΞΗ;ΘΕΣΗ;ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$description = $alldata['name']; 
		$product_id = $alldata['sku']; 
		$product_quantity = $alldata['qty_ordered']; 
		
		//$amount=number_format($alldata['product_final_price'], 2, ',', '');
		//$amount=number_format($alldata['row_total'], 2, ',', '');
		
		$amount=$alldata['price_incl_tax'];
		//$amount=($amount*100)/(100+$alldata['tax_percent']);
		
		$amount=number_format($amount, 2, ',', '');
		
		
		
		$order_id= $alldata['order_id']; 
		//$increment_id= $alldata['increment_id'];
		
		$taxrate=number_format($alldata['tax_percent'], 2, ',', '');
		
		//$monada = $alldata['product_unit']; 
		$product_attribute = $alldata['product_options']; 
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.";0;;;;".$order_id.";<br>\n";
		
		
		
		
		
		
	}
	
	
}





















































if ($action == 'confirmorder') {
	
	$data =mysqli_query($link,"
		
		update ".$dbprefix."sales_order 
		set status='complete'
		where entity_id=$orderid
		
		") or die(mysqli_error($link));
	
	//echo $hmera;
	
	
	
	echo $s = "php shell/indexer.php --reindex cataloginventory_stock";
	echo "<br>";
	echo exec($s,$o2,$r2); 
	print_r($o2);
	print_r($r2);
	
}



if ($action == 'cancelorder') {
	
	$data = mysqli_query($link,"
		
		update ".$dbprefix."sales_order 
		set status='canceled'
		where entity_id=$orderid
		
		") or die(mysqli_error($link));
	
	//echo $hmera;
	
	
	
	//echo $s = "php shell/indexer.php --reindex cataloginventory_stock";
	echo $s = "php shell/indexer.php --reindexall";	
	echo "<br>";
	echo exec($s,$o2,$r2); 
	print_r($o2);
	print_r($r2);
	
}





if ($action == 'updatestock') {
	
	
	
	
	
	$data = mysqli_query($link,"
		
		update ".$dbprefix."cataloginventory_stock_item 
		set qty=$stock,
		is_in_stock=(case when $stock=0 then 0 else 1 end)
		where product_id=
		(SELECT pro.entity_id FROM ".$dbprefix."catalog_product_entity pro where pro.sku='".substr($productid,strlen($product_code_prefix))."')"
	
	) or die(mysqli_error($link));
	
	
	$data = mysqli_query($link,"
		
		update ".$dbprefix."cataloginventory_stock_status 
		set qty=$stock,
		stock_status=(case when $stock=0 then 0 else 1 end)
		where product_id=
		(SELECT pro.entity_id FROM ".$dbprefix."catalog_product_entity pro where pro.sku='".substr($productid,strlen($product_code_prefix))."')"
	
	) or die(mysqli_error($link));
	
	
	$data = mysqli_query($link,"
		
		update ".$dbprefix."cataloginventory_stock_status_idx 
		set qty=$stock,
		stock_status=(case when $stock=0 then 0 else 1 end)
		where product_id=
		(SELECT pro.entity_id FROM ".$dbprefix."catalog_product_entity pro where pro.sku='".substr($productid,strlen($product_code_prefix))."')"
	
	) or die(mysqli_error($link));
	
	
	echo $hmera;
	

}








if ($action == 'reindex') {
	//echo $s = "php shell/indexer.php --reindex cataloginventory_stock";
	echo $s = "php shell/indexer.php --reindexall";	
	echo "<br>";
	echo exec($s,$o2,$r2); 
	print_r($o2);
	print_r($r2);
}













////////////////////////////////////
////////////////////////////////////
////////////////////////////////////
if ($action == 'redirect') {

}


















if ($action == 'uploadproduct') {

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
