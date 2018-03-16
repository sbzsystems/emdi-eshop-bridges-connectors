<?php
	/*------------------------------------------------------------------------
		# EMDI - VIK BOOKING 2 BRIDGE by SBZ systems - Solon Zenetzis - version 1.0
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2016 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/
	// WARNING !!!  ADD AUTOMODIFIED FIELD "modified" IN  THE FOLLOWING TABLES:  
	// vikbooking_rooms
	// vikbooking_customers   
	// vikbooking_orders
	// INFO: http://www.sbzsystems.com/en/general-help-issues/add-auto-modified-timestamp-to-a-table-mysql/
	// ALTER TABLE `raldg_vikbooking_orders` ADD `modified` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL AFTER `phone`; 
	
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
	
	$logfile = 'emdibridge.log';
	$offset= $config->offset;
	$host = $config->host;
	$user = $config->user;
	$password = $config->password;
	$db = $config->db;
	$dbprefix = $config->dbprefix;
	$tmp_path = $config->tmp_path;
	$timezone=$config->offset; 
	$monada='ΔΙΑΝΥΚΤΕΡΕΥΣΗ';
	
	//////////////
	$dateformat="d/m/Y H:i:s";//"Y-m-d H:i:s"
	//LANGUAGE
	$lang='en_gb';
	//MAIN TAX
	$maintax=13;
	// Connects to your Database
	$link=mysql_connect("$host", $user, $password) or die(mysql_error());
	mysql_select_db("$db") or die(mysql_error());
	mysql_set_charset('utf8',$link); 
	
	
	$product_code_separator='ΔΩΜ#';
	$customer_code_prefix='C';
	$once_customer_code_prefix='V';
	
	
	$url = $_SERVER['REQUEST_URI']; //returns the current URL
	$parts = explode('/',$url);
	$dir = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") .$_SERVER['SERVER_NAME'];
	for ($i = 0; $i < count($parts) - 1; $i++) {
		$dir .= $parts[$i] . "/";
	}
	
	$photourl=$dir."components/com_vikbooking/resources/uploads/";
	$produrl=$dir."index.php?option=com_vikbooking&view=roomdetails&roomid=2&Itemid=";
	$customerid=$_REQUEST['customerid'];
	
	
	
	$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
	$productid=$_REQUEST['productid'];
	$stock=$_REQUEST['stock'];
	$action=$_REQUEST['action'];       // PRODUCT CODE
	$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
	$key=$_REQUEST['key'];       // PRODUCT CODE
	if (!($key=='')) { exit; }
	//if (!($key==$password)) { exit; }
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
		$file = $tmp_path."/orders_".$key; 
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
	if ($action == 'confirmorder') {
		$file = $tmp_path."/orders_".$key; 
		$handle = fopen($file, 'w');
		$data = time();
		fwrite($handle, $data); 
		fclose($handle); 	
	}
	
	
	
	
	if ($action == 'customers') {
		
		
		/*
			$data = mysql_query("
			
			
			
			
			CREATE TRIGGER raldg_vikbooking_customers_trigger
			BEFORE UPDATE ON raldg_vikbooking_customers
			FOR EACH ROW
			BEGIN
			IF NEW.modified = '0000-00-00 00:00:00' THEN
			SET NEW.modified = NOW();
			END IF;
			END;
			
			
			") or die(mysql_error()); 
		*/
		
		
		
		
		
		$file = $tmp_path."/customers_".$key; 
		$lastdate=0;
		if (file_exists($file)) {
			$handle = fopen($file, 'r'); 
			$lastdate = fread($handle, 11); 
			fclose($handle); 
		}
		
		$data = mysql_query("SELECT *
		FROM ".$dbprefix."vikbooking_customers 
		
		where ".$dbprefix."vikbooking_customers.modified>'".date('Y-m-d H:i:s', $lastdate)."'				
		
		" ) or die(mysql_error()); 
		
		
		echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;ΤΘ;<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{		 	
			$id=$alldata['id'];  	 	
			$firstname= $alldata['first_name']; 
			$lastname=$alldata['last_name'];  	
			
			$country=$alldata['country'];  	 	
			$state=$alldata['state_name'];  	 	
			$phonenumber=$alldata['phone'];  	 	
			$mobile=$alldata['phone_2'];  	 	
			$email=$alldata['email'];  	 	
			
			
			//{"2":"konstantinos","3":"karakonstantakis","4":"kostas@artweb.gr","5":"6977055784","6":"elefthernis 21","7":"71202","8":"Iraklion","10":"01-01-1990","11":"asdaaaaaaaaaaaaaaa"}					
			// SPLIT CFIELDS INTO FIELDS
			$cfields=$alldata['cfields'];  	
			$cfields=str_ireplace('{','",',$cfields);
			$cfields=str_ireplace('}',',"',$cfields);
			
			
			if ($cfields) {
				$words = preg_split('/","/', $cfields);
				
				foreach ($words as $k => $word) {
					$prword = preg_split('/":"/', $word);
					//echo $prword[0].'->'.$prword[1]."<br>\n";
					if ($prword[0]=='6') { $address1=$prword[1]; }
					if ($prword[0]=='7') { $postcode=$prword[1]; }
					if ($prword[0]=='8') { $city=$prword[1]; }
					// birth date    if ($prword[0]=='10') { $city=$prword[1]; } 
					// info          if ($prword[0]=='11') { $city=$prword[1]; }
				} 
			}
			///
			
			
			echo $customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
			.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,';'.$tu.";<br>\n";
			
			
			
		}
		
		
		
		//ONE TIME CUSTOMERS
		/*	
			custdata
			custmail
			adminnotes
			t_first_name
			t_last_name
		*/		
		//		
		$query="
		SELECT viko.id,t_first_name,t_last_name,custmail,custdata	
		FROM ".$dbprefix."vikbooking_orders viko 
		left join ".$dbprefix."vikbooking_customers_orders vco on vco.idorder=viko.id
		left join ".$dbprefix."vikbooking_customers vcs on vcs.id=idcustomer
		
		left join ".$dbprefix."vikbooking_ordersrooms vcrr on vcrr.idorder=viko.id
		left join ".$dbprefix."vikbooking_rooms vcro on vcro.id=vcrr.idroom
		
		
		where idcustomer is null
		and
		viko.modified>'".date('Y-m-d H:i:s', $lastdate)."'				
		";
		
		//Αμα υπάρχει το modified δεν φέρνει καμία παραγγελία,δεν ξερω αν παιζει ρολο το διαγραφή επιβεβαιώσεων
		/*where viko.status='confirmed'			
		and viko.modified>'".date('Y-m-d H:i:s', $lastdate)."'*/		
		//echo $query;           and (not viko.adminnotes = '1' ) or  viko.adminnotes is null
		
		
		
		
		
		
		
		$data = mysql_query($query) or die(mysql_error()); //
		
		
		while($alldata = mysql_fetch_array( $data ))
		{		 	
			$id=$alldata['id'];  	 	
			
			
			$pieces = explode("\n", $alldata['custdata']);
			$companyname=$alldata['custdata'];
			
			$firstname= $alldata['t_first_name']; 
			$lastname=$alldata['t_last_name'];  	 	 	
			
			$pieces = explode(" ", $pieces[0]);
			if (!$firstname) {
				$firstname = $pieces[0];
			}
			if (!$lasttname) {
				$lastname = $pieces[1];
			}
			
			
			
			
			$firstname= htmlspecialchars_decode($firstname); 
			$firstname = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $firstname));
			$lastname= htmlspecialchars_decode($lastname); 
			$lastname = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $lastname));					
			$companyname= htmlspecialchars_decode($companyname); 
			$companyname = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $companyname));
			
			
			
			
			if (!($first_name && $last_name)) {
				$lastname=$companyname ;
			}
			
			
			
			$country='';//$alldata['country'];  	 	
			$state='';//$alldata['state_name'];  	 	
			$phonenumber='';//$alldata['phone'];  	 	
			$mobile='';//$alldata['phone_2'];  	 	
			$email=$alldata['custmail'];  	 	
			
			
			
			$address1=''; 
			$postcode=''; 
			$city=''; 
			$afm=''; 
			$doy=''; 
			$epaggelma=''; 
			$tu=''; 
			
			
			//$pos =strpos(strtolower($lastname),'room');
			//if ($pos !== false) {
			//$firstname=$once_customer_code_prefix.$id.' '.$firstname;
			$lastname=$once_customer_code_prefix.$id.' '.$lastname;
			$companyname=$once_customer_code_prefix.$id.' '.$companyname;
			
			//}
			
			
			echo $once_customer_code_prefix.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
			.$phonenumber.';'.$mobile.';'.$email.';'.$afm.';'.$doy.';'.$companyname.';'.$epaggelma.';'.$language,';'.$tu.";<br>\n";
			
			
			
			
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
		
		
		/*
			$data = mysql_query("
			
			
			
			
			CREATE TRIGGER raldg_vikbooking_rooms_trigger
			BEFORE UPDATE ON raldg_vikbooking_rooms
			FOR EACH ROW
			BEGIN
			IF NEW.modified = '0000-00-00 00:00:00' THEN
			SET NEW.modified = NOW();
			END IF;
			END;
			
			
			") or die(mysql_error()); 
		*/
		
		
		
		
		////PRODUCTS
		$data = mysql_query("SELECT 
		
		vip.id,vip.name,vip.units,vip.img,
		dis.cost,vip.avail,cat.name as catname
		
		from ".$dbprefix."vikbooking_rooms as vip
		
		left join ".$dbprefix."vikbooking_dispcost as dis on dis.idroom=vip.id and dis.days=1
		left join ".$dbprefix."vikbooking_categories as cat on cat.id= vip.idcat
		
		
		where vip.avail=1
		and vip.modified>'".date('Y-m-d H:i:s', $lastdate)."'
		
		") or die(mysql_error()); 
		
		
		
		//left join ".$dbprefix."vm_category
		//on ".$dbprefix."virtuemart_categories.category_id =".$dbprefix."virtuemart_categories.category_id
		
		echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ;ΦΩΤΟΓΡΑΦΙΑ;URL<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$id=$alldata['id'];  	 	
			//$idmpn=$alldata['id'];  	 
			$name1= $alldata['name']; 
			//$name2= $alldata['attribute']; 
			//$monada= $alldata['product_unit'];
			$units=$alldata['units'];
			$avail=$alldata['avail'];
			$price=number_format($alldata['cost']      
			+ (($alldata['cost']*$taxrate)/100)                                 
			, 2, ',', '');
			
			
			$category= $alldata['catname']; 
			
			
			$photolink=$photourl.$alldata['img'];
			$urllink=$produrl.$id;
			
			
			
			
			
			
			
			for ($cc = 1; $cc <= $units; $cc++) {
				
				echo $id.$product_code_separator.$cc.';'.$name1.';;'.$maintax.';'.$price.";;".$avail.";".$monada.";".$category.";".$photolink.";".$urllink.";<br>\n";			 
				
			}
			
			
		}
		////
		
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'orders') {
		
		
		$file = $tmp_path."/orders_".$key; 
		$lastdate=0;
		if (file_exists($file)) {
			$handle = fopen($file, 'r'); 
			$lastdate = fread($handle, 11); 
			fclose($handle); 
		}
		
		$query="SELECT 
		viko.id,vco.idcustomer,vcs.cfields,viko.ts
		,viko.checkin,viko.checkout,vcro.id rid,vcro.name rname,viko.days,total,adminnotes
		
		FROM ".$dbprefix."vikbooking_orders viko 
		left join ".$dbprefix."vikbooking_customers_orders vco on vco.idorder=viko.id
		left join ".$dbprefix."vikbooking_customers vcs on vcs.id=idcustomer
		
		left join ".$dbprefix."vikbooking_ordersrooms vcrr on vcrr.idorder=viko.id
		left join ".$dbprefix."vikbooking_rooms vcro on vcro.id=vcrr.idroom
		
		
		
		
		where viko.status='confirmed'
		
		and viko.modified>'".date('Y-m-d H:i:s', $lastdate)."'
		
		
		";
		
		//Αμα υπάρχει το modified δεν φέρνει καμία παραγγελία,δεν ξερω αν παιζει ρολο το διαγραφή επιβεβαιώσεων
		/*where viko.status='confirmed'
			
		and viko.modified>'".date('Y-m-d H:i:s', $lastdate)."'*/
		
		
		//echo $query;           and (not viko.adminnotes = '1' ) or  viko.adminnotes is null
		
		
		$data = mysql_query($query) or die(mysql_error()); //
		
		
		echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$id=$alldata['id'];  	 	
			
			
			
			if ($alldata['idcustomer']) {
				$userid= $customer_code_prefix.$alldata['idcustomer']; 			
				} else {
				$userid= $once_customer_code_prefix.$alldata['id']; 		
			}
			
			
			
			$idpayment= $alldata['idpayment'];
			$idpayment = preg_split('/=/', $idpayment);
			
			$hmera=date($dateformat, $alldata['ts']); 
			
			
			
			// SPLIT CFIELDS INTO FIELDS
			$cfields=$alldata['cfields'];  	
			$cfields=str_ireplace('{','",',$cfields);
			$cfields=str_ireplace('}',',"',$cfields);
			
			
			
			$notes='';
			
			if ($cfields) {
				$words = preg_split('/","/', $cfields);
				
				foreach ($words as $k => $word) {
					$prword = preg_split('/":"/', $word);
					//echo $prword[0].'->'.$prword[1]."<br>\n";
					//if ($prword[0]=='6') { $address1=$prword[1]; }
					//if ($prword[0]=='7') { $postcode=$prword[1]; }
					//if ($prword[0]=='8') { $city=$prword[1]; }
					// birth date    if ($prword[0]=='10') { $city=$prword[1]; } 
					if ($prword[0]=='11') { 
						$notes=$prword[1]; 
						$notes=json_decode('"'.$notes.'"');	
					}
				} 
			}
			///
			
			
			if ($notes) { $notes=$notes.' '.$alldata['adminnotes']; } else { $notes=$alldata['adminnotes']; }
			
			
			$notes= htmlspecialchars_decode($notes); 
			$notes = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $notes));
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			/*
				
				$description = $alldata['rname']; 
				$product_id = $alldata['rid']; 
				$product_quantity = $alldata['days']; 
				$amount=number_format($alldata['total'], 2, ',', '');		
				$hmera=date($dateformat, $alldata['ts']); 		
				$description2=date("dmYHis", $alldata['checkin']).'|'.date("dmYHis", $alldata['checkout']);
				
				
				$room=$product_id.$product_code_separator.'|'.$description.'|'.$description2.'|'.$product_quantity.'|'.$amount;
				
				
			*/
			
			
			
			
			
			/*if ($userid) { 
				
				echo $id.';'.$customer_code_prefix.$userid.";0;0;0;".$hmera.";".$notes.' '.$idpayment[1].";<br>\n";
				
				} else {
				
				//echo $id.';'.$once_customer_code_prefix.$id.";0;0;0;".$hmera.";<br>\n";
				}
				
			*/
			
			echo $id.';'.$userid.";0;0;0;".$hmera.";".$notes.' '.$idpayment[1].";<br>\n"; 
			
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	if ($action == 'order') {
		
		if ($orderid) 
		{ $linesc="and viko.id=$orderid"; 
			} else { 
			$linesc=""; 
		} 
		
		$data = mysql_query("SELECT * FROM ".$dbprefix."vikbooking_orders viko 
		left join ".$dbprefix."vikbooking_customers_orders vco on vco.idorder=viko.id
		left join ".$dbprefix."vikbooking_ordersrooms vcrr on vcrr.idorder=viko.id
		left join ".$dbprefix."vikbooking_rooms vcro on vcro.id=vcrr.idroom
		
		
		where viko.status='confirmed'
		
		$linesc
		
		") or die(mysql_error()); //
		
		
		echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;ΕΝΑΡΞΗ;ΛΗΞΗ;ΘΕΣΗ;ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ<br>\n";
		
		
		while($alldata = mysql_fetch_array( $data ))
		{
			$description = $alldata['name']; 
			$product_id = $alldata['id']; 
			$product_quantity = $alldata['days']; 
			//$amount=number_format($alldata['product_final_price'], 2, ',', '');
			$amount=number_format($alldata['total']/$product_quantity, 2, ',', '');
			
			
			$taxrate=number_format($alldata['calc_value'], 2, ',', '');
			//$monada = $alldata['product_unit']; 
			$product_attribute = $alldata['product_attribute']; 
			
			
			
			$hmera=date($dateformat, $alldata['ts']); 
			
			//$description2=date("Y-m-d H:i:s", $alldata['checkin']).' '.date("Y-m-d H:i:s", $alldata['checkout']);
			
			
			
			
			
			
			
			
			
			
			
			echo $product_id.$product_code_separator.';'.$description.';'.$description2.';;'.$product_quantity.';'.$monada.';'.$amount.';'.$maintax.";0;".date($dateformat, $alldata['checkin']).";".date($dateformat, $alldata['checkout']).";;".$alldata['idorder']."<br>\n";
			
			
			
			
			
			
			
			
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//if ($action == 'confirmorder') {
	
	//$data = mysql_query("UPDATE ".$dbprefix."vikbooking_orders SET adminnotes = '1' WHERE id in (".$orderid.")") or die(mysql_error());
	
	//echo $hmera;
	
	//}
	
	
	
	if ($action == 'cancelorder') {
		
		$data = mysql_query("UPDATE ".$dbprefix."vikbooking_orders SET adminnotes = '2', status='canceled' WHERE id in (".$orderid.")") or die(mysql_error());
		
		echo $hmera;
		
	}
	
	
	
	
	
	if ($action == 'updatestock') {
		
		
		$data = mysql_query("UPDATE ".$dbprefix."virtuemart_products SET product_in_stock = ".$stock." WHERE product_sku ='".substr($productid,strlen($product_code_separator))."'") or die(mysql_error());
		
		echo $hmera;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	////////////////////////////////////
	////////////////////////////////////
	////////////////////////////////////
	
	
	
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

$pieces = explode("|", $productid);
$productid = trim(base_enc($pieces[1]));



if (substr($productid,0,strlen($product_code_separator))==$product_code_separator) {
$productid=substr( $productid,strlen($product_code_separator),        strlen($productid)-strlen($product_code_separator)                );
}




$productmpn=base_enc($_REQUEST['productmpn']);

$title=base_enc($_REQUEST['title']);

$descr=base_enc($_REQUEST['descr']);    
$descr ='';



$price=$_REQUEST['price'];
$cat=$_REQUEST['cat']+10000;
$subcat=$_REQUEST['subcat'];
$tax=$_REQUEST['tax'];

$cattitle=trim(base_enc($_REQUEST['cattitle']));      
$subcattitle=trim(base_enc($_REQUEST['subcattitle']));      

$logtext=$pieces[0].'|'.$productid.'|'.$title.'|'.$descr.'|'.$price.'|'.$cat.'|'.$subcat.'|'.$tax.'|'.$cattitle.'|'.$subcattitle."\n";
file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);

//
//CHECK IF TAX EXISTS ELSE ADD
$data = mysql_query("
select * from ".$dbprefix."virtuemart_calcs
where calc_name='EMDI $tax'

") or die(mysql_error());



if (mysql_num_rows($data)==0) {

//ADD DEFAULT EMDI TAX CLASS IF DOESN'T EXIST
$data = mysql_query("

INSERT INTO ".$dbprefix."virtuemart_calcs 
(virtuemart_calc_id, virtuemart_vendor_id, calc_jplugin_id, calc_name, calc_descr, calc_kind, calc_value_mathop, calc_value, calc_currency, calc_shopper_published, calc_vendor_published, publish_up, publish_down, for_override, calc_params, ordering, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
VALUES 
(NULL, '1', '0', 'EMDI $tax', '', 'Tax', '+%', '$tax', '47', '1', '1', now(), '0000-00-00 00:00:00.000000', '0', NULL, '0', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0'); 


") or die(mysql_error());			





//GET CLASS ID
$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
while($alldata = mysql_fetch_array( $data ))
{
$classid=$alldata['id'];  	 	
break;		
}	




} else {
//GET TAX CLASS IF EXIST
while($alldata = mysql_fetch_array( $data ))
{
$classid=$alldata['virtuemart_calc_id'];  	 	
break;		
}	
}
//








// CREATE CATEGORY IF DOES NOT EXIST
$data = mysql_query("
SELECT * FROM ".$dbprefix."virtuemart_categories WHERE virtuemart_category_id=$cat
") or die(mysql_error());
if (mysql_num_rows($data)==0) {





$data = mysql_query("

INSERT INTO ".$dbprefix."virtuemart_categories (virtuemart_category_id, virtuemart_vendor_id, category_template, category_layout, category_product_layout, products_per_row, limit_list_start, limit_list_step, limit_list_max, limit_list_initial, hits, metarobot, metaauthor, ordering, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
VALUES 
('$cat', '0', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '', '', '0', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');

") or die(mysql_error());			




$logtext="##catid=$cat  \n";
file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);



//ADD CATEGORY DESCRIPTION
$data = mysql_query("


INSERT INTO ".$dbprefix."virtuemart_categories_$lang (virtuemart_category_id, category_name, category_description, metadesc, metakey, customtitle, slug) 
VALUES 
('$cat', '$cattitle', '', '', '', '', '$cattitle');

") or die(mysql_error());			

//ADD CATEGORY 
$data = mysql_query("


INSERT INTO ".$dbprefix."virtuemart_category_categories (id, category_parent_id, category_child_id, ordering) 
VALUES 
(NULL, '0', '$cat', '0');


") or die(mysql_error());			







}
//




if ($subcat) {

// CREATE SUBCATEGORY IF DOES NOT EXIST
$data = mysql_query("
SELECT * FROM ".$dbprefix."virtuemart_categories WHERE virtuemart_category_id=$subcat
") or die(mysql_error());
if (mysql_num_rows($data)==0) {


$data = mysql_query("

INSERT INTO ".$dbprefix."virtuemart_categories (virtuemart_category_id, virtuemart_vendor_id, category_template, category_layout, category_product_layout, products_per_row, limit_list_start, limit_list_step, limit_list_max, limit_list_initial, hits, metarobot, metaauthor, ordering, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
VALUES 
('$subcat', '0', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '0', '', '', '0', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');

") or die(mysql_error());			





//ADD CATEGORY 
$data = mysql_query("

INSERT INTO ".$dbprefix."virtuemart_category_categories (id, category_parent_id, category_child_id, ordering) 
VALUES 
(NULL, '$cat', '$subcat', '0');

") or die(mysql_error());			





//ADD CATEGORY DESCRIPTION
$data = mysql_query("

INSERT INTO ".$dbprefix."virtuemart_categories_$lang (virtuemart_category_id, category_name, category_description, metadesc, metakey, customtitle, slug) 
VALUES 
('$subcat', '$subcattitle', '', '', '', '', '$subcattitle');

") or die(mysql_error());			



}
//

}







$logtext=$_FILES["file"]["name"]."\n";
file_put_contents($logfile, $logtext, FILE_APPEND | LOCK_EX);	





// UPLOAD AND REPLACE PHOTO
$uploadfolder=getcwd().'/images/stories/virtuemart/product/'; 

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
SELECT * FROM ".$dbprefix."virtuemart_products WHERE product_sku = '".$productid."'
") or die(mysql_error());


if (mysql_num_rows($data)==0) {

//IF PRODUCT DOES NOT EXIST			
$data = mysql_query("				


INSERT INTO ".$dbprefix."virtuemart_products (virtuemart_product_id, virtuemart_vendor_id, product_parent_id, product_sku, product_weight, product_weight_uom, product_length, product_width, product_height, product_lwh_uom, product_url, product_in_stock, product_ordered, low_stock_notification, product_available_date, product_availability, product_special, product_sales, product_unit, product_packaging, product_params, hits, intnotes, metarobot, metaauthor, layout, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by,product_mpn) 
VALUES 
(NULL, '1', '0', '$productid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00.000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0','$productmpn');


") or die(mysql_error());				


//GET PRODCUT ID
$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
while($alldata = mysql_fetch_array( $data ))
{
$id=$alldata['id'];  	 	
break;		
}	







//ADD DESCRIPTION       
$data = mysql_query("


INSERT INTO ".$dbprefix."virtuemart_products_$lang (virtuemart_product_id, product_s_desc, product_desc, product_name, metadesc, metakey, customtitle, slug) 
VALUES 
('$id', '', '$descr', '$title', '', '', '', '$title');


") or die(mysql_error());					







//ADD CATEGORY
$data = mysql_query("


INSERT INTO ".$dbprefix."virtuemart_product_categories (id, virtuemart_product_id, virtuemart_category_id, ordering) 
VALUES 
(NULL, '$id', '$subcat', '0');


") or die(mysql_error());					







//ADD PRICE
$data = mysql_query("


INSERT INTO ".$dbprefix."virtuemart_product_prices (virtuemart_product_price_id, virtuemart_product_id, virtuemart_shoppergroup_id, product_price, override, product_override_price, product_tax_id, product_discount_id, product_currency, product_price_publish_up, product_price_publish_down, price_quantity_start, price_quantity_end, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
VALUES 
(NULL, '$id', NULL, '$price', NULL, NULL, '$classid', NULL, '47', NULL, NULL, NULL, NULL, now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');


") or die(mysql_error());					







//ADD media
$data = mysql_query("


INSERT INTO ".$dbprefix."virtuemart_medias (virtuemart_media_id, virtuemart_vendor_id, file_title, file_description, file_meta, file_mimetype, file_type, file_url, file_url_thumb, file_is_product_image, file_is_downloadable, file_is_forSale, file_params, shared, published, created_on, created_by, modified_on, modified_by, locked_on, locked_by) 
VALUES 
(NULL, '1', '".$_FILES["file"]["name"]."', '', '', 'image/jpeg', 'product', 'images/stories/virtuemart/product/".$_FILES["file"]["name"]."', '', '0', '0', '0', '', '0', '1', now(), '0', now(), '0', '0000-00-00 00:00:00.000000', '0');



") or die(mysql_error());					


//GET MEDIA ID
$data = mysql_query("SELECT LAST_INSERT_ID() as id") or die(mysql_error());					
while($alldata = mysql_fetch_array( $data ))
{
$mid=$alldata['id'];  	 	
break;		
}	


//ADD media
$data = mysql_query("


INSERT INTO ".$dbprefix."virtuemart_product_medias (id, virtuemart_product_id, virtuemart_media_id, ordering) 
VALUES 
(NULL, '$id', '$mid', '0');


") or die(mysql_error());					



} else {
//IF PRODUCT EXISTS UPDATE FIELDS
//GET TAX CLASS IF DOESN'T EXIST
while($alldata = mysql_fetch_array( $data ))
{
$id=$alldata['virtuemart_product_id'];  	 	
break;		
}	


//UPDATE DESCRIPTION       
$data = mysql_query("
update ".$dbprefix."virtuemart_products_$lang set product_name='$title', product_desc='$descr', slug='$title'
where virtuemart_product_id=$id
") or die(mysql_error());					

//UPDATE mpn 
$data = mysql_query("
update ".$dbprefix."virtuemart_products set product_mpn='$productmpn', modified_on=now()
where virtuemart_product_id=$id
") or die(mysql_error());					

//UPDATE category
$data = mysql_query("
update ".$dbprefix."virtuemart_product_categories set virtuemart_category_id='$subcat'
where virtuemart_product_id=$id
") or die(mysql_error());					

//UPDATE price  override, product_override_price
$data = mysql_query("
update ".$dbprefix."virtuemart_product_prices set product_price='$price',product_tax_id='$classid'
where virtuemart_product_id=$id
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








