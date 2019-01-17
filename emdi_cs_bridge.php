<?php
/*------------------------------------------------------------------------
# EMDI - CSCART BRIDGE by SBZ systems - Solon Zenetzis - version 1.4
# ------------------------------------------------------------------------
# author    SBZ systems - Solon Zenetzis
# copyright Copyright (C) 2014 sbzsystems.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.sbzsystems.com
# Technical Support:  Forum - http://www.sbzsystems.com
-------------------------------------------------------------------------*/

//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header('Content-Type: text/html; charset=UTF-8');

define('AREA', null);
require 'config.local.php';

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
$lang_code='EL';
//$coid=" and  ".$dbprefix."products.company_id=2";

	
//////////////
$measurement_field='Μονάδα μέτρησης';
$vat_field='Α.Φ.Μ.';
$tax_office_field='Δ.Ο.Υ.';
$occupation='Επάγγελμα';
$companytitle='Επωνυμία';

//$maintax=23;
// Connects to your Database
$link=mysql_connect("$host", $user, $password) or die(mysql_error());
mysql_select_db("$db") or die(mysql_error());
mysql_set_charset('utf8',$link); 


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











//echo getcwd().'/'.$tmp_path."/products_".$key.'#';
 
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


//// CUSTOMERS
$data = mysql_query("SELECT * FROM ".$dbprefix."users, ".$dbprefix."user_profiles where timestamp>".$lastdate
                .' and '.$dbprefix."users.user_id=".$dbprefix."user_profiles.user_id"
				.' and '.$dbprefix."users.status='A' and ".$dbprefix."users.user_type='C' ORDER BY ".$dbprefix."users.user_id"
				) or die(mysql_error());
										
echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['profile_id'];  	 	
  	 	$firstname= $alldata['firstname']; 
  	 	$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['b_address'];  	 	
		$postcode=$alldata['b_zipcode'];  	 	
		$country=$alldata['b_country'];  	 	
		$state=$alldata['b_state'];  	 	
		$city=$alldata['b_city'];  	 	
		$phonenumber=$alldata['b_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		//$companyname=$alldata['company'];  	 	

		
		$afm='';
		$doy='';
		$epaggelma='';
		$companyname='';
		
		//////////afm///////////////	
		if ($vat_field) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$vat_field."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='P'") or die(mysql_error());								
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $afm=$alldata2['value']; }
		}
		//////////doy///////////////		
		if ($tax_office_field) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$tax_office_field."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='P'") or die(mysql_error());						
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $doy=$alldata2['value']; }
		}
		//////////occupation///////////////		
		if ($occupation) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$occupation."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='P'") or die(mysql_error());						
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $epaggelma=$alldata2['value']; }
		}
		//////////companytitle///////////////		
		if ($companytitle) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$companytitle."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='P'") or die(mysql_error());						
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $companyname=$alldata2['value']; }
		}
		//////////////////////////////////////
		echo $customer_code_prefix.$alldata['user_id'].';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
		.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,";<br>\n";
}
///////////////////////
//// ONE TIME CUSTOMERS
/////////////////////// 
$data = mysql_query("SELECT * FROM ".$dbprefix."orders where timestamp>".$lastdate
                .' and '.$dbprefix."orders.user_id=0 and tax_exempt='N' ") or die(mysql_error());
							// status<>'C' and status<>'D' and status<>'I' and status<>'F' and 
						
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['order_id'];  	 	
  	 	$firstname= $alldata['firstname']; 
  	 	$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['b_address'];  	 	
		$postcode=$alldata['b_zipcode'];  	 	
		$country=$alldata['b_country'];  	 	
		$state=$alldata['b_state'];  	 	
		$city=$alldata['b_city'];  	 	
		$phonenumber=$alldata['b_phone'];  	 	
		$mobile=$alldata['phone'];  	 	
		$email=$alldata['email'];  	 	
		//$companyname=$alldata['company'];  	 	

		if (!$lastname) { $lastname=$alldata['email'];  }
		
		
		$afm='';
		$doy='';
		$epaggelma='';
		$companyname='';

		//////////afm///////////////	
		if ($vat_field) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$vat_field."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='O'") or die(mysql_error());						
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $afm=$alldata2['value']; }
		}
		//////////doy///////////////		
		if ($tax_office_field) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$tax_office_field."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='O'") or die(mysql_error());						
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $doy=$alldata2['value']; }
		}
		//////////occupation///////////////		
		if ($occupation) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$occupation."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='O'") or die(mysql_error());						
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $epaggelma=$alldata2['value']; }
		}
		//////////companytitle///////////////		
		if ($companytitle) {
		$data2 = mysql_query('SELECT * FROM '
							.$dbprefix.'profile_fields_data, '.$dbprefix.'profile_field_descriptions'
							.' where '.$dbprefix.'profile_fields_data.field_id='.$dbprefix.'profile_field_descriptions.object_id'
							." and ".$dbprefix."profile_field_descriptions.description='".$companytitle."'"
							.' and '.$dbprefix.'profile_fields_data.object_id='.$id.' and '.$dbprefix."profile_fields_data.object_type='O'") or die(mysql_error());						
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $companyname=$alldata2['value']; }
		}
		//////////////////////////////////////		
		echo $onetime_customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
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


$data = mysqli_query($link,"
SELECT  ".$dbprefix."products.product_id,
".$dbprefix."product_descriptions.product, ".$dbprefix."products.product_code,
".$dbprefix."tax_rates.rate_value,
".$dbprefix."product_prices.price,
".$dbprefix."category_descriptions.category,
".$dbprefix."products.amount,
".$dbprefix."products.timestamp
FROM 
".$dbprefix."products

left join ".$dbprefix."product_descriptions
on  ".$dbprefix."product_descriptions.product_id=".$dbprefix."products.product_id and ".$dbprefix."product_descriptions.lang_code='".$lang_code."'

left join ".$dbprefix."products_categories
on  ".$dbprefix."products_categories.product_id=".$dbprefix."products.product_id

left join ".$dbprefix."product_prices
on  ".$dbprefix."product_prices.product_id=".$dbprefix."products.product_id and lower_limit=1

left join ".$dbprefix."category_descriptions
on  ".$dbprefix."category_descriptions.category_id=".$dbprefix."products_categories.category_id and ".$dbprefix."category_descriptions.lang_code='".$lang_code."'

left join ".$dbprefix."tax_rates
on  ".$dbprefix."tax_rates.tax_id=".$dbprefix."products.tax_ids and ".$dbprefix."tax_rates.rate_type='P'

where ".$dbprefix."products.status='A'
and ".$dbprefix."products.timestamp>".$lastdate."

group by ".$dbprefix."products.product_id


") or die(mysqli_error($link)); 
echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ<br>\n";
		
while($alldata = mysqli_fetch_array( $data ))
{
		$id=trim($alldata['product_code']);  	 	
		
		$name1 = $alldata['product'];   
		$name1 = htmlentities($name1, null, 'utf-8');
		$name1 = str_replace("&nbsp;", " ", $name1);
		$name1 = str_replace("&amp;", ',', $name1);
		
		
  	 	//$name2= $alldata['attribute']; 
  	 	$taxrate=$alldata['rate_value'];
		//$taxrate=$maintax;
		//$monada= $alldata['product_unit']; 

		//$paytype=substr($alldata['paytype'],0,1);
		//$type=substr($alldata['keym'],0,1);
		//$servertype=$alldata['servertype'];  	
	    $taxrate=number_format($taxrate, 2, ',', '');	
		$price=$alldata['price'];
	    $price=number_format($price, 2, ',', '');
		
		$category= $alldata['category']; 
		$category = htmlentities($category, null, 'utf-8');
		$category = str_replace("&nbsp;", " ", $category);
		$category = str_replace("&amp;", ',', $category);
		
		
		
		
		if ($measurement_field) {
		$data2 = mysqli_query($link,"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			".$dbprefix."product_features_values pfv,
			".$dbprefix."product_features_descriptions pfd,
			".$dbprefix."product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='".$measurement_field."'
			and pfv.product_id=".$alldata['product_id']


		) or die(mysqli_error($link));						
		$nmonada=$monada;
		while($alldata2 = mysqli_fetch_array( $data2 ))
		{ $nmonada=$alldata2['variant']; }
		}
		
		
		$row=$product_code_prefix.$id.';'.$name1.';;'.$taxrate.';'.$price.";;;".$nmonada.";".$category.";<br>\n";			 
		$row=html_entity_decode($row);
		echo $row;
		

			
}
////






////PRODUCT OPTIONS

$query="
SELECT  ".$dbprefix."products.product_id,
prds.product, ".$dbprefix."product_options_inventory.product_code,
".$dbprefix."tax_rates.rate_value,
".$dbprefix."product_prices.price,
".$dbprefix."category_descriptions.category,
".$dbprefix."products.amount,
".$dbprefix."products.updated_timestamp,
povdscr.variant_name

FROM 
".$dbprefix."product_options_inventory



left join ".$dbprefix."products
on  ".$dbprefix."product_options_inventory.product_id=".$dbprefix."products.product_id

left join ".$dbprefix."product_descriptions prds
on  prds.product_id=".$dbprefix."products.product_id






left join ".$dbprefix."product_option_variants_descriptions povdscr
on  povdscr.variant_id=".$dbprefix."products.product_id




left join ".$dbprefix."products_categories
on  ".$dbprefix."products_categories.product_id=".$dbprefix."products.product_id

left join ".$dbprefix."product_prices
on  ".$dbprefix."product_prices.product_id=".$dbprefix."products.product_id

left join ".$dbprefix."category_descriptions
on  ".$dbprefix."category_descriptions.category_id=".$dbprefix."products_categories.category_id and ".$dbprefix."category_descriptions.lang_code='".$lang_code."'

left join ".$dbprefix."tax_rates
on  ".$dbprefix."tax_rates.tax_id=".$dbprefix."products.tax_ids and ".$dbprefix."tax_rates.rate_type='P'


where ".$dbprefix."products.status='A'
".$coid."
and ".$dbprefix."products.updated_timestamp>".$lastdate."


group by ".$dbprefix."product_options_inventory.product_code


";

//and prds.lang_code='".$lang_code."'
//echo $query;

$data = mysqli_query($link,$query) or die(mysqli_error($link)); 
echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ<br>\n";
		
while($alldata = mysqli_fetch_array( $data ))
{
		$id=trim($alldata['product_code']);  	 	
  	 	$name1= $alldata['product']; 
  	 	$variant_name= $alldata['variant_name']; 
  	 	$taxrate=$alldata['rate_value'];
		//$monada= $alldata['product_unit']; 

		//$paytype=substr($alldata['paytype'],0,1);
		//$type=substr($alldata['keym'],0,1);
		//$servertype=$alldata['servertype'];  	
	    $taxrate=number_format($taxrate, 2, ',', '');	
		$price=$alldata['price'];
	    $price=number_format($price, 2, ',', '');
		$category= $alldata['category']; 
		//$category_id= $alldata['category_id']; 
		
		
		//////////monada metrhshs///////////////	
		if ($measurement_field) {
		$data2 = mysqli_query($link,"
			SELECT distinct pfv.product_id, pfvd.variant FROM 
			".$dbprefix."product_features_values pfv,
			".$dbprefix."product_features_descriptions pfd,
			".$dbprefix."product_feature_variant_descriptions pfvd

			where
			pfd.feature_id=pfv.feature_id and 
			pfvd.variant_id=pfv.variant_id and
			pfd.description='".$measurement_field."'
			and pfv.product_id=".$alldata['product_id']


		) or die(mysqli_error($link));						
		$nmonada=$monada;
		while($alldata2 = mysqli_fetch_array( $data2 ))
		{ $nmonada=$alldata2['variant']; }
		}
 
		
		
		

        
		echo $product_code_prefix.$id.';'.$name1.' '.$variant_name.';;'.$taxrate.';'.$price.";;;".$monada.";".$category.";<br>\n";			 
		

			
}
////





}

if ($action == 'orders') {
$data = mysql_query("SELECT * FROM ".$dbprefix."orders where status<>'C' and status<>'D' and status<>'I' and status<>'F' and tax_exempt='N' order by order_id,user_id desc") or die(mysql_error()); //

//C D I F status='O'  


echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
		$id=$alldata['order_id'];  	 	
  	 	$userid= $alldata['user_id']; 
  	 

		
		$hmera=gmdate("d/m/Y H:i:s", $alldata['timestamp'] + 3600*($timezone+date("I"))); 

		
  	    //$shipping=   str_replace('€','',       $alldata['shipping']); 

		
		
		
		if ($userid==0) {
			echo $id.';'.$onetime_customer_code_prefix.$id.";".$shipping.";0;0;".$hmera.";<br>\n";
		} else {					
			echo $id.';'.$customer_code_prefix.$userid.";".$shipping.";0;0;".$hmera.";<br>\n";
		}

		
		
}
}


























if ($action == 'order') {


















////PRODUCTS
$data = mysql_query("SELECT  ".$dbprefix."order_details.amount,
".$dbprefix."product_descriptions.product,".$dbprefix."products.product_code,".$dbprefix."order_details.price,".$dbprefix."tax_rates.rate_value
,".$dbprefix."order_details.extra,
".$dbprefix."products.product_id

FROM ".$dbprefix."order_details,
".$dbprefix."products

left join ".$dbprefix."product_descriptions
on  ".$dbprefix."product_descriptions.product_id=".$dbprefix."products.product_id

left join ".$dbprefix."product_prices
on  ".$dbprefix."product_prices.product_id=".$dbprefix."products.product_id

left join ".$dbprefix."tax_rates
on  ".$dbprefix."tax_rates.tax_id=".$dbprefix."products.tax_ids and ".$dbprefix."tax_rates.rate_type='P'

where
".$dbprefix."order_details.product_id=".$dbprefix."products.product_id
 and ".$dbprefix."order_details.order_id=".$orderid.

" group by ".$dbprefix."products.product_id"


) or die(mysql_error()); 

echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
		
while($alldata = mysql_fetch_array( $data ))
{
  	 	$description = $alldata['product']; 
  	 	$product_id = $alldata['product_code']; 
		$product_quantity = $alldata['amount']; 
		$amount=number_format($alldata['price'], 2, ',', '');
		//$discount=number_format($alldata['percentage_discount'], 2, ',', '');	
		$discount=0;		
		
		$taxrate=number_format($alldata['rate_value'], 2, ',', '');	
		
	 	$monada = $alldata['product_unit']; 
		$product_attribute = $alldata['extra']; 
		
		//////////monada metrhshs///////////////	
		if ($measurement_field) {
		$data2 = mysql_query('SELECT * FROM '
		
							.$dbprefix.'product_features_values,'
							.$dbprefix.'product_features_descriptions,'
							.$dbprefix.'product_feature_variant_descriptions'
							.' where '
							.$dbprefix.'product_features_descriptions.feature_id='.$dbprefix.'product_features_values.feature_id'
							.' and '.$dbprefix.'product_feature_variant_descriptions.variant_id='.$dbprefix.'product_features_values.variant_id'
							.' and '.$dbprefix.'product_features_values.product_id='.$alldata['product_id']
							.' and '.$dbprefix."product_features_descriptions.description='".$measurement_field."'"
		) or die(mysql_error());						
		$monada='';
		while($alldata2 = mysql_fetch_array( $data2 ))
		{ $monada=$alldata2['variant']; }
		}

		
		
		
		echo $product_code_prefix.$product_id.';'.$description.';;;'.$product_quantity.';'.$monada.';'.$amount.';'.$taxrate.';'.$discount.";<br>\n";
		////split prostheta   
		$words = preg_split('/;/', $product_attribute);
			
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
		
		
		
		
		
}


}


















































 


if ($action == 'confirmorder') {

//echo"UPDATE ".$dbprefix."vm_orders SET order_status = 'S' WHERE order_id in (".$orderid.")";
//
//$data = mysql_query("UPDATE admin_whmcs.tblinvoices SET notes = 'ΚΟΠΗΚΕ ΤΙΜΟΛΟΓΙΟ ".$hmera."' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysql_error()); 
$data = mysql_query("UPDATE ".$dbprefix."orders SET status = 'C' WHERE order_id in (".$orderid.")") or die(mysql_error());

echo $hmera;
}





if ($action == 'updatestock') {

//echo"UPDATE ".$dbprefix."vm_orders SET order_status = 'S' WHERE order_id in (".$orderid.")";
//
//$data = mysql_query("UPDATE admin_whmcs.tblinvoices SET notes = 'ΚΟΠΗΚΕ ΤΙΜΟΛΟΓΙΟ ".$hmera."' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysql_error()); 

//echo "UPDATE ".$dbprefix."products SET amount = ".$stock." WHERE ".$dbprefix."products.product_id =".$productid;
//echo substr($productid,strlen($product_code_prefix));
$data = mysql_query("UPDATE ".$dbprefix."products SET amount = ".$stock." WHERE ".$dbprefix."products.product_code ='".substr($productid,strlen($product_code_prefix))."'") or die(mysql_error());

$data = mysqli_query($link,"UPDATE ".$dbprefix."product_options_inventory SET amount = ".$stock." WHERE product_code ='".substr($productid,strlen($product_code_prefix))."'") or die(mysqli_error($link));
		
echo $hmera;
}



//header("Location: $goto?expdate=$nextduedate");




?> 
