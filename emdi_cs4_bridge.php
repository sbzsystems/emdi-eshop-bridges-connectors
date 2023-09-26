<?php
error_reporting(0);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header('Content-Type: text/html; charset=UTF-8');
define('AREA', null);
//require 'config.local.php';
$offset = '';
$host = 'localhost';
$user = "dbusername";
$password = "dbpassword";
$db = "dbname";


$dbprefix = 'cscart_';
$product_code_prefix = '';
$customer_code_prefix = 'IC';
$onetime_customer_code_prefix = 'EC';
$shipping_customer_code_prefix = 'SC';
$lang_code = 'EL';
$tmp_path = $_SERVER['DOCUMENT_ROOT'] . '/tmp';
$timezone = $config->offset;
$lang_code = 'EL';
//$coid=" and  ".$dbprefix."products.company_id=2";

//////////////
$mpn = 'MPN';
$measurement_field = 'Μονάδα μέτρησης';
$brand = 'Brand';
$xrwma = 'Χρώμα';
$megethos = 'Μέγεθος';
$gtin = 'GTIN';
$season = 'Σεζόν';
//////////////////////////////////////////
$vat_field = 'ΑΦΜ';
$tax_office_field = 'ΔΟΥ';
$occupation = 'Δραστηριότητα';
$companytitle = 'Επωνυμία';
$monada = 'ΤΕΜ';
//$maintax=23;
// Connects to your Database
$link = mysqli_connect("$host", $user, $password) or die(mysqli_error($link));
mysqli_select_db($link, "$db") or die(mysqli_error($link));
mysqli_set_charset($link, 'utf8');

$productid = $_REQUEST['productid'];
$stock = $_REQUEST['stock'];
$ip = $_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action = $_REQUEST['action'];       // PRODUCT CODE
$orderid = $_REQUEST['orderid'];       // PRODUCT CODE
$key = $_REQUEST['key'];       // PRODUCT CODE
//if (!($key==$password)) { exit; }
///////////////////////////////////
if (!is_dir($tmp_path)) {
	mkdir($tmp_path);
}
if ($action == 'deletetmp') {
	$File = $tmp_path . "/customers_" . $key;
	unlink($File);
	$file = $tmp_path . "/products_" . $key;
	unlink($file);
}
//echo getcwd().'/'.$tmp_path."/products_".$key.'#';

if ($action == 'customersok') {
	$File = $tmp_path . "/customers_" . $key;
	$Handle = fopen($File, 'w');
	$Data = time() - (48 * 60 * 60);
	fwrite($Handle, $Data);
	fclose($Handle);
}
if ($action == 'productsok') {
	$file = $tmp_path . "/products_" . $key;
	$handle = fopen($file, 'w');
	$data = time();
	fwrite($handle, $data);
	fclose($handle);
}




if ($action == 'customers') {



	$file = $tmp_path . "/customers_" . $key;
	$lastdate = 0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r');
		$lastdate = fread($handle, 11);
		fclose($handle);
	}

	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΠΡΟΣΘΕΤΑ ΠΕΔΙΑ;ΤΘ;<br>\n";



	///////////////////////
	//// ONE TIME CUSTOMERS
	/////////////////////// 
	$query = "
	SELECT 
	*,
	
	(SELECT value FROM " . $dbprefix . "profile_fields_data where object_id=order_id and object_type='O' and field_id=38 limit 1) company,
	(SELECT value FROM " . $dbprefix . "profile_fields_data where object_id=order_id and object_type='O' and field_id=40 limit 1) vat,
	(SELECT value FROM " . $dbprefix . "profile_fields_data where object_id=order_id and object_type='O' and field_id=42 limit 1) taxoffice,
	(SELECT value FROM " . $dbprefix . "profile_fields_data where object_id=order_id and object_type='O' and field_id=44 limit 1) occupation
	FROM " . $dbprefix . "orders
	where timestamp>" . $lastdate
		. " and tax_exempt='N' 
	
	
		group by
		order_id,
		(case when user_id=0 or email like '%demo%' then order_id 
		else concat(user_id,s_address,s_city,s_county,s_state,s_zipcode,b_address,b_city,b_county,b_state,b_zipcode,vat,company) 
		end)
	
	
	";




	$data = mysqli_query($link, $query) or die(mysqli_error($link));
	// status<>'C' and status<>'D' and status<>'I' and status<>'F' and 


	while ($alldata = mysqli_fetch_array($data)) {

		$id = $alldata['order_id'];
		$user_id = $alldata['user_id'];

		$firstname = $alldata['firstname'];
		$lastname = $alldata['lastname'];
		$address1 = $alldata['b_address'];
		$postcode = $alldata['b_zipcode'];
		$country = $alldata['b_country'];
		$state = $alldata['b_city'];
		$city = $alldata['b_state'];
		$phonenumber = $alldata['b_phone'];

		$address1_s = $alldata['s_address'];
		$postcode_s = $alldata['s_zipcode'];
		$country_s = $alldata['s_country'];
		$state_s = $alldata['s_city'];
		$city_s = $alldata['s_state'];
		$phonenumber_s = $alldata['s_phone'];

		$mobile = $alldata['phone'];
		$email = $alldata['email'];

		if (!$lastname) {
			$lastname = $alldata['email'];
		}


		$afm = $alldata['vat'];
		$doy = $alldata['taxoffice'];
		$epaggelma = $alldata['occupation'];
		$companyname = $alldata['company'];


		if ($user_id != 0) {
			$id = $customer_code_prefix . $user_id;
		} else {
			$id = $onetime_customer_code_prefix . $id;
		}




		echo $id . ';' . $firstname . ';' . $lastname . ';' . $address1 . ';' . $postcode . ';' . $country . ';' . $state . ';' . $city . ';'
			. $phonenumber . ';' . $mobile . ';' . $email . ';' . $afm . ';' . $doy . ';' . $companyname . ';' . $epaggelma . ';' . $language, ";;<br>\n";


		if ($address1_s . $postcode_s . $country_s . $state_s . $city_s <> $address1 . $postcode . $country . $state . $city) {

			echo $id . '.S;' . $firstname . ';' . $lastname . ';' . $address1_s . ';' . $postcode_s . ';' . $country_s . ';' . $state_s . ';' . $city_s . ';'
				. $phonenumber_s . ';' . $mobile . ';' . $email . ';' . ';' . ';' . ';' . ';' . $language, ";;<br>\n";
		}
	}
}














if ($action == 'products') {


	$file = $tmp_path . "/products_" . $key;
	$lastdate = 0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r');
		$lastdate = fread($handle, 11);
		fclose($handle);
	}

	////PRODUCTS


	$query = "
SELECT  " . $dbprefix . "products.product_id,
" . $dbprefix . "product_descriptions.product, " . $dbprefix . "products.product_code,
" . $dbprefix . "tax_rates.rate_value,
" . $dbprefix . "product_prices.price,
" . $dbprefix . "category_descriptions.category,
" . $dbprefix . "products.amount,
" . $dbprefix . "products.weight,
" . $dbprefix . "images.image_id as imageid,
" . $dbprefix . "images.image_path as imagefile,
" . $dbprefix . "products.updated_timestamp,
" . $dbprefix . "product_features_values.variant_id

,


(select csc.id_path from " . $dbprefix . "categories csc where csc.category_id=" . $dbprefix . "products_categories.category_id limit 1)  all_categories,







(
select group_concat( distinct concat('\"',csd.category_id,'\"=>\"',csd.category,'\"')) from " . $dbprefix . "category_descriptions csd where  csd.lang_code='el'

and 

concat('/', (select csc.id_path from " . $dbprefix . "categories csc where csc.category_id=" . $dbprefix . "products_categories.category_id limit 1)  ,'/')

like 

concat('%/', csd.category_id ,'/%')

order by category_position desc

) as all_categories_titles













FROM 
" . $dbprefix . "products

left join " . $dbprefix . "product_descriptions
on  " . $dbprefix . "product_descriptions.product_id=" . $dbprefix . "products.product_id and " . $dbprefix . "product_descriptions.lang_code='" . $lang_code . "'

left join " . $dbprefix . "products_categories
on  " . $dbprefix . "products_categories.product_id=" . $dbprefix . "products.product_id


left join " . $dbprefix . "product_prices
on  " . $dbprefix . "product_prices.product_id=" . $dbprefix . "products.product_id and lower_limit=1

left join " . $dbprefix . "category_descriptions
on  " . $dbprefix . "category_descriptions.category_id=" . $dbprefix . "products_categories.category_id and " . $dbprefix . "category_descriptions.lang_code='" . $lang_code . "'

left join " . $dbprefix . "tax_rates
on  " . $dbprefix . "tax_rates.tax_id=" . $dbprefix . "products.tax_ids and  " . $dbprefix . "tax_rates.destination_id=1 and " . $dbprefix . "tax_rates.rate_type in ('P','F')

left join " . $dbprefix . "images_links
on  " . $dbprefix . "products.product_id=" . $dbprefix . "images_links.object_id and " . $dbprefix . "images_links.type='M'

left join " . $dbprefix . "images
on  " . $dbprefix . "images_links.detailed_id=" . $dbprefix . "images.image_id


left join " . $dbprefix . "seo_names
on  " . $dbprefix . "products.product_id=" . $dbprefix . "seo_names.object_id

left join " . $dbprefix . "product_features_values
on  " . $dbprefix . "product_features_values.product_id=" . $dbprefix . "products.product_id

where " . $dbprefix . "products.updated_timestamp>" . $lastdate . "

group by " . $dbprefix . "products.product_id," . $dbprefix . "product_prices.price," . $dbprefix . "category_descriptions.category," . $dbprefix . "images.image_id," . $dbprefix . "product_features_values.variant_id," . $dbprefix . "products_categories.category_id


";

	//echo $query;






	$data = mysqli_query($link, $query) or die(mysqli_error($link));
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL;ΣΕΙΡΑ ΚΑΤΗΓΟΡΙΑΣ;ΒΑΡΟΣ;ΕΝΕΡΓΟ;<br>\n";

	while ($alldata = mysqli_fetch_array($data)) {
		$id = trim($alldata['product_code']);

		$name1 = $alldata['product'];
		//$name1 = htmlentities($name1, null, 'utf-8');
		$name1 = str_replace("&nbsp;", "", $name1);
		$name1 = str_replace("&amp;", 'κ', $name1);
		$name1 = str_replace("&quot;", "", $name1);
		$name1 = str_replace(";", "?", $name1);
		//$name1 = str_replace(""""," ",$name1);

		//$name2= $alldata['attribute']; 
		$taxrate = $alldata['rate_value'];
		//$taxrate=$maintax;


		//$paytype=substr($alldata['paytype'],0,1);
		//$type=substr($alldata['keym'],0,1);
		//$servertype=$alldata['servertype'];  	
		$taxrate = number_format($taxrate, 2, ',', '');
		$price = $alldata['price'];
		$amount = $alldata['amount'];




		$all_titles = array($alldata['all_categories_titles']);








		$myarray = $alldata['all_categories_titles'];

		// Remove the extra double quotes and explode the string into key-value pairs
		$keyValuePairs = explode(',', str_replace('"', '', $myarray));

		$finalArray = [];

		foreach ($keyValuePairs as $pair) {
			// Split each pair into key and value
			list($key, $value) = explode('=>', $pair);

			// Trim the key and value, and add them to the final array
			$finalArray[trim($key)] = trim($value);
		}

		// Print the resulting array
		//print_r($finalArray);














		$all_cat = explode('/', $alldata['all_categories']);
		$category = '';
		foreach ($all_cat as $value) {
			$category = $category . $finalArray[$value] . ',';
		}
		//$category= print_r($all_titles);







		//$category= $alldata['all_categories']; 
		$category = htmlentities($category);


		$category = str_replace("&nbsp;", " ", $category);
		$category = str_replace("&amp;", '-', $category);
		//$category = 'Κατηγορία_eShop:'.$category.'\n';

		//////////////////////
		$parent_category = $alldata['parent_id'];
		///////////////////

		$weight = $alldata['weight'];
		$weight = str_replace(".", ",", $weight);

		$image = $alldata['imagefile'];
		$imageid = $alldata['imageid'];
		$maxfiles = '1000';
		$siteurl = 'https://www.yoursite.com/';
		$imageurl = $siteurl . '/images/detailed/' . floor($imageid / $maxfiles) . '/' . $image;

		$prodid = $alldata['product_id'];
		$url = $siteurl . '/index.php?dispatch=products.view&product_id=' . $prodid;

		$variant_id = $alldata['variant_id'];


		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if ($measurement_field) {
			$data2 = mysqli_query(
				$link,
				"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			" . $dbprefix . "product_features_values pfv,
			" . $dbprefix . "product_features_descriptions pfd,
			" . $dbprefix . "product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='" . $measurement_field . "'
			and pfv.product_id=" . $alldata['product_id']


			) or die(mysqli_error($link));

			$nmonada = $monada;

			while ($alldata2 = mysqli_fetch_array($data2)) {
				$nmonada = $alldata2['variant'];
			}
		}


		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if ($mpn) {
			$data2 = mysqli_query(
				$link,
				"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			" . $dbprefix . "product_features_values pfv,
			" . $dbprefix . "product_features_descriptions pfd,
			" . $dbprefix . "product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='" . $mpn . "'
			and pfv.product_id=" . $alldata['product_id']


			) or die(mysqli_error($link));

			//$nmpn=$mpn;


			while ($alldata2 = mysqli_fetch_array($data2)) {
				$nmpn = $alldata2['variant'];
			}
		}




		if ($brand) {
			$data2 = mysqli_query(
				$link,
				"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			" . $dbprefix . "product_features_values pfv,
			" . $dbprefix . "product_features_descriptions pfd,
			" . $dbprefix . "product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='" . $brand . "'
			and pfv.product_id=" . $alldata['product_id']


			) or die(mysqli_error($link));

			//$nbrand=$brand;


			while ($alldata2 = mysqli_fetch_array($data2)) {
				$nbrand = $alldata2['variant'];
			}
		}





		if ($xrwma) {
			$data2 = mysqli_query(
				$link,
				"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			" . $dbprefix . "product_features_values pfv,
			" . $dbprefix . "product_features_descriptions pfd,
			" . $dbprefix . "product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='" . $xrwma . "'
			and pfv.product_id=" . $alldata['product_id']


			) or die(mysqli_error($link));

			//$nxrwma=$xrwma;


			while ($alldata2 = mysqli_fetch_array($data2)) {
				$nxrwma = $alldata2['variant'];
			}
		}





		if ($megethos) {
			$data2 = mysqli_query(
				$link,
				"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			" . $dbprefix . "product_features_values pfv,
			" . $dbprefix . "product_features_descriptions pfd,
			" . $dbprefix . "product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='" . $megethos . "'
			and pfv.product_id=" . $alldata['product_id']


			) or die(mysqli_error($link));

			//$nmegethos=$megethos;


			while ($alldata2 = mysqli_fetch_array($data2)) {
				$nmegethos = $alldata2['variant'];
			}
		}






		if ($gtin) {
			$data2 = mysqli_query(
				$link,
				"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			" . $dbprefix . "product_features_values pfv,
			" . $dbprefix . "product_features_descriptions pfd,
			" . $dbprefix . "product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='" . $gtin . "'
			and pfv.product_id=" . $alldata['product_id']


			) or die(mysqli_error($link));

			$ngtin = $gtin;


			while ($alldata2 = mysqli_fetch_array($data2)) {
				$ngtin = $alldata2['variant'];
			}
		}




		if ($season) {
			$data2 = mysqli_query(
				$link,
				"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			" . $dbprefix . "product_features_values pfv,
			" . $dbprefix . "product_features_descriptions pfd,
			" . $dbprefix . "product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='" . $season . "'
			and pfv.product_id=" . $alldata['product_id']


			) or die(mysqli_error($link));




			while ($alldata2 = mysqli_fetch_array($data2)) {
				$nseason = $alldata2['variant'];
			}
		}


















		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//$price=$price*100/(100+$taxrate);
		$price = number_format($price, 2, ',', '');
		//$price='|9:'.$price;
		//$taxrate='';

		//	$row=$product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$nmonada.";".$category.";".$imageurl.";".$url.";;".$weight.";1;<br>\n";			


		$row = $product_code_prefix . $id . ';' . $name1 . ';MPN:' . $nmpn . '\nBRAND:' . $nbrand . '\nΜΕΓΕΘΟΣ:' . $nmegethos . '\nΧΡΩΜΑ:' . $nxrwma . '\nGTIN:' . $ngtin . '\nSEASON:' . $nseason . ';' . $taxrate . ';' . $price . ";;" . $amount . ";" . $nmonada . ";" . $category . ";" . $imageurl . ";" . $url . ";;" . $weight . ";1;<br>\n";


		$row = html_entity_decode($row);
		echo $row;
		//echo "test";


	}
}
















if ($action == 'orders') {

	$data = mysqli_query($link, "SELECT * FROM " . $dbprefix . "orders

left join " . $dbprefix . "payment_descriptions on    cscart_orders.payment_id=cscart_payment_descriptions.payment_id and
 cscart_payment_descriptions.lang_code='EL' 
 
 
 where 

status='P' OR  status='O'  and tax_exempt='N' 
 

 
 group by order_id
 
 order by order_id,user_id desc") or die(mysqli_error($link)); //


	//C D I F status='O'
	//and order_id>5930
	/*

$data = mysqli_query($link,"SELECT * FROM ".$dbprefix."orders
 where status<>'D' and status<>'C' and status<>'N' and status<>'I' and status<>'E' and status<>'F' and status<>'J' and order_id > 7443 and tax_exempt='N'
 order by order_id,user_id desc") or die(mysqli_error($link)); //
//C D I F status='O'

*/





	//echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;<br>\n";
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;ΧΡΗΣΤΗΣ;VOUCHER;ΚΑΤΑΣΤΑΣΗ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ ΑΠΟΣΤΟΛΗΣ;<br>\n";





	while ($alldata = mysqli_fetch_array($data)) {
		$id = $alldata['order_id'];
		$userid = $alldata['user_id'];
		$shipping = $alldata['shipping_cost'];
		$handling = $alldata['payment_surcharge'];
		$discount = $alldata['subtotal_discount'];
		$discount = -$alldata['subtotal_discount'];
		$notes = $alldata['notes'];
		// $total= $alldata['subtotal'];   

		$hmera = gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600 * ($timezone + date("I")));

		$shipping =   str_replace('€', '',       $shipping);
		$shipping =   str_replace('.', ',',       $shipping);
		$handling =   str_replace('€', '',       $handling);
		$handling =   str_replace('.', ',',       $handling);
		$discount =   str_replace('€', '',       $discount);
		$discount =   str_replace('.', ',',       $discount);
		//  $total=   str_replace('€','',       $total);
		//$discount=	$discount*100/$total;	
		//$total=   str_replace('.',',',       $total);	
		$paymentr = $alldata['payment'];

		if ($userid == 0) {
			//echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";".$handling.";".$discount.";".$hmera.";".$notes.";;;;;".$paymentr.";<br>\n";
			echo $id . ';' . $onetime_customer_code_prefix . $id . ";" . $shipping . ";" . $handling . ";" . $discount . ";" . $hmera . ";" .  preg_replace("/\r|\n/", "", $notes) . ";;;;" . $shipping_customer_code_prefix . $id . ";" . $paymentr . ";<br>\n";
		} else {
			//echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";".$handling.";".$discount.";".$hmera.";".$notes.";;;;;".$paymentr.";<br>\n";
			echo $id . ';' . $customer_code_prefix . $userid . ";" . $shipping . ";" . $handling . ";" . $discount . ";" . $hmera . ";" .  preg_replace("/\r|\n/", "", $notes) . ";;;;" . $shipping_customer_code_prefix . $userid . ";" . $paymentr . ";<br>\n";
		}
	}
}




if ($action == 'order') {
	////PRODUCTS
	$data = mysqli_query(
		$link,
		"SELECT  " . $dbprefix . "order_details.amount,
" . $dbprefix . "product_descriptions.product," . $dbprefix . "products.product_code," . $dbprefix . "order_details.price," . $dbprefix . "tax_rates.rate_value
," . $dbprefix . "order_details.extra,
" . $dbprefix . "products.product_id
FROM " . $dbprefix . "order_details,
" . $dbprefix . "products
left join " . $dbprefix . "product_descriptions
on  " . $dbprefix . "product_descriptions.product_id=" . $dbprefix . "products.product_id and " . $dbprefix . "product_descriptions.lang_code='" . $lang_code . "'
left join " . $dbprefix . "product_prices
on  " . $dbprefix . "product_prices.product_id=" . $dbprefix . "products.product_id
left join " . $dbprefix . "tax_rates
on  " . $dbprefix . "tax_rates.tax_id=" . $dbprefix . "products.tax_ids and " . $dbprefix . "tax_rates.destination_id=1 and " . $dbprefix . "tax_rates.rate_type in ('P','F')
where
" . $dbprefix . "order_details.product_id=" . $dbprefix . "products.product_id
 and " . $dbprefix . "order_details.order_id=" . $orderid .
			" group by " . $dbprefix . "order_details.item_id"
	) or die(mysqli_error($link));


	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";

	while ($alldata = mysqli_fetch_array($data)) {
		$description = $alldata['product'];
		$description = str_replace("&", 'κ', $description);
		$description = str_replace(";", "?", $description);

		$product_id = $alldata['product_code'];
		$product_quantity = $alldata['amount'];
		$amount = number_format($alldata['price'], 2, ',', '');
		$discount = number_format($alldata['percentage_discount'], 2, ',', '');
		//$discount=0;		

		$taxrate = number_format($alldata['rate_value'], 2, ',', '');

		$monada = $alldata['product_unit'];
		$product_attribute = $alldata['extra'];

		//////////monada metrhshs///////////////	
		if ($mpn) {
			$data2 = mysqli_query(
				$link,
				'SELECT * FROM '

					. $dbprefix . 'product_features_values,'
					. $dbprefix . 'product_features_descriptions,'
					. $dbprefix . 'product_feature_variant_descriptions'
					. ' where '
					. $dbprefix . 'product_features_descriptions.feature_id=' . $dbprefix . 'product_features_values.feature_id'
					. ' and ' . $dbprefix . 'product_feature_variant_descriptions.variant_id=' . $dbprefix . 'product_features_values.variant_id'
					. ' and ' . $dbprefix . 'product_features_values.product_id=' . $alldata['product_id']
					. ' and ' . $dbprefix . "product_features_descriptions.description='" . $mpn . "'"
			) or die(mysqli_error($link));
			$monada = '';
			while ($alldata2 = mysqli_fetch_array($data2)) {
				$monada = $alldata2['variant'];
			}
		}



		////split prostheta   


		//	echo '##'.$product_attribute.'##';
		$cfld = unserialize($product_attribute);
		//$cfld=json_decode($product_attribute);
		//var_dump($cfld);


		$customaddons = $cfld['product_options_value'][0]['option_name'] . ' ' . $cfld['product_options_value'][0]['variant_name'];



		echo $product_code_prefix . $product_id . ';' . $description . " " . $customaddons . ';;;' . $product_quantity . ';' . $monada . ';' . $amount . ';' . $taxrate . ';' . $discount . ";<br>\n";

		/*
		
		
		//echo '##'.$customaddons.'##';
		
		$words = preg_split('/;/', $product_attribute);
		//$words = preg_split('/;/', $customaddons);

		
		$sel=0; $prv=''; $cou=0;
		foreach ($words as $k => $word) {
			preg_match('/"([^"]+)"/', $word, $result);		
					
			if ($sel==1) {
				if ($cou>0) {
					echo 'PRO;'.$prv.':'.$result[1].";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;<br>\n";
					$prv='';
					$cou=0;					
				} else {
					//echo 'PRO;'.$prv.':'.$result[1].";;;1;ΠΡΟΣΘΕΤΟ;0;0;0;<br>\n";
					$prv=$result[1];
					$cou++;
				}
				$sel=0;				
			}
	
			if ((stripos($word, "option_name") !== false) || (stripos($word, "variant_name") !== false)) { $sel=1; }
			
			
        } 
		*/
	}
}









if ($action == 'confirmorder') {
	//echo"UPDATE ".$dbprefix."vm_orders SET order_status = 'S' WHERE order_id in (".$orderid.")";
	//
	//$data = mysql_query("UPDATE admin_whmcs.tblinvoices SET notes = 'ΚΟΠΗΚΕ ΤΙΜΟΛΟΓΙΟ ".$hmera."' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysql_error()); 
	$data = mysqli_query($link, "UPDATE " . $dbprefix . "orders SET status = 'H' WHERE order_id in (" . $orderid . ")") or die(mysqli_error($link));
	echo $hmera;
}











if ($action == 'updatestock') {



	//echo"UPDATE ".$dbprefix."vm_orders SET order_status = 'S' WHERE order_id in (".$orderid.")";
	//
	//$data = mysql_query("UPDATE admin_whmcs.tblinvoices SET notes = 'ΚΟΠΗΚΕ ΤΙΜΟΛΟΓΙΟ ".$hmera."' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysql_error()); 
	//echo "UPDATE ".$dbprefix."products SET amount = ".$stock." WHERE ".$dbprefix."products.product_id =".$productid;
	//echo substr($productid,strlen($product_code_prefix));
	$product_code = substr($productid, strlen($product_code_prefix));



	//ΑΛΛΑΓΗ ΔΙΑΘΕΣΙΜΟΤΗΤΑΣ ΣΕ ΕΙΔΟΣ
	$data = mysqli_query($link, "UPDATE " . $dbprefix . "products SET amount = " . $stock . " WHERE product_code ='" . $product_code . "'") or die(mysqli_error($link));
	//ΑΛΛΑΓΗ ΔΙΑΘΕΣΙΜΟΤΗΤΑΣ ΣΕ ΠΑΡΑΛΛΑΓΗ
	//$data = mysqli_query($link, "UPDATE " . $dbprefix . "product_options_inventory SET amount = " . $stock . " WHERE product_code ='" . $product_code . "'") or die(mysqli_error($link));




	//ΛΗΨΗ PRODUCT_ID ΚΑΙ GROUP_ID    HL2274_4066747952633
	//ΑΝΑΖΗΤΗΣΗ ΓΙΑ ΤΟ ΕΙΔΟΣ - ΑΝ ΕΙΝΑΙ ΚΥΡΙΟ ΜΕ ΜΗΔΕΝΙΚΗ ΠΟΣΟΤΗΤΑ
	$product_id = null;
	$product_type = null;
	$status = null;
	$amount = null;
	//$parent_id = null;
	$group_parent_id = null;
	$group_id = null;
	$query = "SELECT csp.product_id,csp.product_type,csp.status,csp.amount,csp.parent_product_id parent_id,csgp.parent_product_id group_parent_id,csgp.group_id
			  FROM cscart_products csp,cscart_product_variation_group_products csgp 
			  where csp.product_code ='$product_code' and csgp.product_id=csp.product_id
			  and csp.product_type='P'
			  and csp.amount=0
			  limit 1";
	$data = mysqli_query($link, $query) or die(mysqli_error($link));
	while ($alldata = mysqli_fetch_array($data)) {
		$product_id = $alldata['product_id'];
		$product_type = $alldata['product_type'];
		$status = $alldata['status'];
		$amount = $alldata['amount'];
		//$parent_id = $alldata['parent_id'];
		$group_parent_id = $alldata['group_parent_id'];
		$group_id = $alldata['group_id'];
		break;
	}

	//echo '<pre>' . $query . '</pre>';

	if ($product_id) {

		//echo '#product_id:' . $product_id . '#product_type:' . $product_type . '#status:' . $status . '#amount:' . $amount . '#group_parent_id:' . $group_parent_id . '#group_id:' . $group_id . '#';

		// ΕΥΡΕΣΗ ΕΙΔΟΥΣ ΜΕ ΘΕΤΙΚΗ ΠΟΣΟΤΗΤΑ ΣΤΟ ΙΔΙΟ GROUP
		$new_parent = null;
		$query = "SELECT * FROM `cscart_products` where parent_product_id=$product_id and amount>0 limit 1";
		$data = mysqli_query($link, $query) or die(mysqli_error($link));
		while ($alldata = mysqli_fetch_array($data)) {
			$new_parent = $alldata['product_id'];
			break;
		}

		//echo '<pre>' . $query . '</pre>';

		if ($new_parent) {

			echo '#new_parent:' . $new_parent . '#product_id:' . $product_id . '#product_type:' . $product_type . '#status:' . $status . '#amount:' . $amount . '#group_parent_id:' . $group_parent_id . '#group_id:' . $group_id . '#';

			// ΑΛΛΑΓΗ ΤΟΥ ΠΑΛΙΟΥ ΓΟΝΙΚΟΥ ΣΕ CHILD
			$query = "update cscart_products set product_type='V' where product_id=$product_id";
			$data = mysqli_query($link, $query) or die(mysqli_error($link));

			$query = "update cscart_products set parent_product_id=$new_parent where parent_product_id=$product_id or product_id=$product_id";
			$data = mysqli_query($link, $query) or die(mysqli_error($link));

			$query = "update cscart_product_variation_group_products set parent_product_id=$new_parent where parent_product_id=$product_id or product_id=$product_id";
			$data = mysqli_query($link, $query) or die(mysqli_error($link));

			// ΟΡΙΣΜΟΣ ΤΟΥ ΝΕΟΥ ΓΟΝΙΚΟΥ 
			$query = "update cscart_products set product_type='P', parent_product_id=0 where product_id=$new_parent";
			$data = mysqli_query($link, $query) or die(mysqli_error($link));

			$query = "update cscart_product_variation_group_products set parent_product_id=0 where product_id=$new_parent";
			$data = mysqli_query($link, $query) or die(mysqli_error($link));
		}
	}

	echo $hmera;
}
//header("Location: $goto?expdate=$nextduedate");
