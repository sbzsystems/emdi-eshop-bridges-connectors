<?php
/*------------------------------------------------------------------------
		# EMDI - WHMCS 7 BRIDGE by SBZ systems - Solon Zenetzis - version 1.2
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2013-2017 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header('Content-Type: text/html; charset=UTF-8');


require "configuration.php";


$maintax=24;
// Connects to your Database
mysql_connect("$db_host", $db_username, $db_password) or die(mysql_error());
mysql_select_db("$db_name") or die(mysql_error());

$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
$tmp_path=realpath(dirname(__FILE__)).'/templates_c';
$measure='ΤΕΜΑΧΙΟ';
$customerid=$_REQUEST['customerid'];
$productid=$_REQUEST['productid'];
$relatedchar='^';


if (!($key=='dfhtj56hfrgw1')) { exit; }


//echo $tmp_path;




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












//$pcuser = iconv('ISO-8859-7', 'UTF-8', $pcuser);
//$pc = iconv('ISO-8859-7', 'UTF-8', $pc);


//if ($pc.$pcuser=='') {exit;}

//// GET PRODUCT ID 
//$query = "SET NAMES 'utf-8'";
//mysql_query($query);  


//$query = "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'";
//mysql_query($query);  



if ($action == 'customers') {
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 11); 
		fclose($handle); 
	}
	
	
	
	
	$data = mysql_query(" SELECT * FROM tblclients where updated_at>FROM_UNIXTIME(".$lastdate.")") or die(mysql_error()); 
	echo "CUSTOMER ID;FIRST NAME;LAST NAME;ADDRESS;ZIP;COUNTRY;CITY/STATE;AREA;PHONE;MOBILE;EMAIL;VAT;TAX OFFICE;COMPANY;OCCUPATION;LANGUAGE;PO BOX;<br>\n";

	
	while($alldata = mysql_fetch_array( $data ))
	{
		
		$alldata=str_replace('&amp;', 'k', $alldata);
		$id=$alldata['id'];  	 	
		$firstname= $alldata['firstname']; 
		$lastname=$alldata['lastname'];  	 	
		$address1=$alldata['address1'];  	 	
		$postcode=$alldata['postcode'];  	 	
		$country=$alldata['country'];  	 	
		$state=$alldata['state'];  	 	
		$city=$alldata['city'];  	 	
		$phonenumber=$alldata['phonenumber'];  	 	
		
		$email=$alldata['email'];  	 	
		
		
		$companyname=$alldata['companyname'];  	 	
		
		
		$language= $alldata['language']; 
		//$datecreated=$alldata['datecreated'];  	 	
		//$notes=iconv('UTF-8','ISO-8859-7',  $alldata['notes']);  	 	
		
		
		$datain = mysql_query('SELECT * FROM tblcustomfieldsvalues where tblcustomfieldsvalues.relid='.$id.'
			ORDER BY tblcustomfieldsvalues.relid,tblcustomfieldsvalues.fieldid ASC') or die(mysql_error()); 
		
		while($alldatain = mysql_fetch_array( $datain ))
		{			
			$field_[$alldatain['fieldid']]=$alldatain['value']; 
		}
		
		$afm=$field_[2];
		$doy=$field_[3];
		$mobile=$field_[4];
		$epaggelma=$field_[5];
		
		//echo '<br>';
		
		echo 'C'.$id.';'.$firstname.';'.$lastname.';'.$address1.';'.$postcode.';'.$country.';'.$state.';'.$city.';'
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
	$data = mysql_query("SELECT tblproducts.type as keym, tblproducts.paytype, tblpricing.type,tblproducts.id,tblproducts.name as name1,tbltax.taxrate,tblproducts.servertype,
		
		tblpricing.monthly,  tblpricing.quarterly, tblpricing.semiannually, tblpricing.annually, tblpricing.biennially, tblpricing.triennially, tblpricing.asetupfee,
		tblproductgroups.name as name2
		
		FROM tblproducts
		left join tbltax
		on tbltax.level=tblproducts.tax
		
		left join tblpricing
		on tblpricing.relid=tblproducts.id and (not tblpricing.type like 'domain%')
		
		left join  tblproductgroups
		on tblproductgroups.id=tblproducts.gid
		
		where
		tblproducts.updated_at>FROM_UNIXTIME(".$lastdate.")
		
		
		group by name1
		") or die(mysql_error()); 
	echo "PRODUCT ID;DESCRIPTION1;DESCRIPTION2;TAX;PRICE;PURCHASE PRICE;AVAILABILITY;MEASUREMENT UNIT;CATEGORY;PHOTO;URL;CATEGORY ORDER;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		$paytype=substr($alldata['paytype'],0,1);
		$type=substr($alldata['keym'],0,1);
		$id=$alldata['id'];  	 	
		$name1= $alldata['name1']; 
		$name2= $alldata['name2']; 
		$taxrate=number_format($alldata['taxrate'], 2, ',', '');	
		$servertype=$alldata['servertype'];  	
		$monthly=$alldata['monthly'];
		$annually=$alldata['annually'];
		$semiannually=$alldata['semiannually'];
		$quarterly=$alldata['quarterly'];
		$biennially=$alldata['biennially'];
		$triennially=$alldata['triennially'];
		$asetupfee=number_format($alldata['asetupfee'], 2, ',', '');
		
		if (!($taxrate>0)) { $taxrate=$maintax; }
		
		$name=$name1;//.' '.$name2;
		
		
		if (($paytype=='onetime') || ($paytype=='free')) {				
			echo $type.$id.';ΑΔΕΙΑ '.$name.';;'.$taxrate.';'.number_format($monthly, 2, ',', '').";;;".$measure.";".$name2.";<br>\n";		
		} else {
			echo $type.$id.';'.$name.';;'.$taxrate.';'.number_format($monthly, 2, ',', '').";;;".$measure.";".$name2.";<br>\n";
		}
		
		
	}
	////////DOMAINS
	$data = mysql_query("SELECT * FROM tbldomainpricing
		
		left join tblpricing
		on tblpricing.relid=tbldomainpricing.id and tblpricing.type like 'domain%'
		
		where
		tblpricing.lastdate>FROM_UNIXTIME(".$lastdate.")
		
		
		") or die(mysql_error()); 
	
	while($alldata = mysql_fetch_array( $data ))
	{
		$id=$alldata['id'];  	 	
		$name= $alldata['extension']; 
		$taxrate=number_format($alldata['taxrate'], 2, ',', '');	
		$type=$alldata['type'];  	
		$msetupfee=number_format($alldata['msetupfee'], 2, ',', '');
		$qsetupfee=number_format($alldata['qsetupfee'], 2, ',', '');
		
		$periodos=$measure;
		if (!($taxrate>0)) { $taxrate=$maintax; }
		
		if ($type=='domainregister') { $typos='ΚΑΤΑΧΩΡΗΣΗ DOMAIN'; }
		if ($type=='domaintransfer') { $typos='ΜΕΤΑΦΟΡΑ DOMAIN'; }
		if ($type=='domainrenew') { 
			$typos='ΑΝΑΝΕΩΣΗ DOMAIN'; 
			$type='domainrenewal';
		}
		
		
		
		echo $type.';'.$typos.';;'.$taxrate.';'.$fee.';;;'.$periodos.";<br>\n";
		
	}
	
	
	
	
	
}


































if ($action == 'orders') {
	$data = mysql_query("SELECT * FROM tblinvoices where notes=''  order by userid,id desc") or die(mysql_error()); //and tax<>0
	echo "ORDER ID;CUSTOMER ID;SHIPPING COST;PAYMENT COST;DISCOUNT;DATE;NOTE;USER;VOUCHER;<br>\n";
	
	
	while($alldata = mysql_fetch_array( $data ))
	{
		$id=$alldata['id'];  	 	
		$userid= $alldata['userid']; 
		$docdate= strtotime(  $alldata['date']); 
		$docdate=date ("Y-m-d H:i:s", $docdate);
		$notes= $alldata['paymentmethod']; 
		
		
		
		
		
		echo $id.';C'.$userid.";0;0;0;$docdate;$notes;<br>\n";
		
	}
}


























if ($action == 'order') {
	
	////HOSTING
	$data = mysql_query("SELECT 
		tblproducts.type as type1, tblhosting.billingcycle,
		tblhosting.packageid,
		tblinvoiceitems.description, tblinvoiceitems.amount, tblinvoiceitems.type
		
		FROM tblinvoiceitems
		
		left join tblhosting 
		on tblhosting.id=tblinvoiceitems.relid 
		and tblinvoiceitems.type='Hosting'
		
		left join tblproducts
		on tblproducts.id=tblhosting.packageid
		
		
		where invoiceid=".$orderid) or die(mysql_error()); 
	echo "PRODUCT ID;DESCRIPTION 1;DESCRIPTION 2;DESCRIPTION 3;QUANTITY;MEASUREMENT UNIT;PRICE;TAX;DISCOUNT;START DATE;END DATE;POSITION;ORDER ID;<br>\n";
	
	while($alldata = mysql_fetch_array( $data ))
	{
		
		$type=$alldata['type'];
		$type1=substr($alldata['type1'],0,1);
		$packageid=$alldata['packageid'];  	 	
		$description = preg_replace("/[\n\r]/","", ($alldata['description'])); 
		$amount=number_format($alldata['amount'], 2, ',', '');
		$billingcycle=$alldata['billingcycle'];  
		$monada=$measure;
		if ($billingcycle=='Annually') { $monada='ΕΤΟΣ'; }
		if ($billingcycle=='Monthly') { $monada='ΜΗΝΑΣ'; }
		if ($billingcycle=='Biennially') { $monada='ΔΙΕΤΙΑ'; }
		
		if ($type=='Domain') { $type='DomainRenewal'; }
		if (substr($type,0,6)<>'Domain') { $type=$type1; }
		
		if (!($taxrate>0)) { $taxrate=$maintax; }			
		
		$patterns = array();
		$patterns[0] = '/Client Discount/';
		$patterns[1] = '/Domain Transfer/';
		$patterns[2] = '/Domain Registration/';
		$patterns[3] = '/Domain Renewal/';
		$replacements = array();
		$replacements[0] = 'ΕΚΠΤΩΣΗ';
		$replacements[1] = 'ΜΕΤΑΦΟΡΑ DOMAIN';
		$replacements[2] = 'ΚΑΤΑΧΩΡΗΣΗ DOMAIN';
		$replacements[3] = 'ΑΝΑΝΕΩΣΗ DOMAIN';
		$description=preg_replace($patterns, $replacements, $description);
		
		echo $type.$packageid.';'.$description.';;;1;'.$monada.';'.$amount.';'.$taxrate.";0;<br>\n";
		
	}
	
	
}





















































if ($action == 'confirmorder') {
	//$hmera=gmdate('d/m/Y H:i:s');
	
	$timezone=2; 
	$hmera=gmdate("d/m/Y H:i:s", time() + 3600*($timezone+date("I"))); 
	
	
	$data = mysql_query("UPDATE tblinvoices SET notes = 'ΚΟΠΗΚΕ ΤΙΜΟΛΟΓΙΟ ".$hmera."' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysql_error()); 
	
	echo $hmera;
	
}

if ($action == 'cancelorder') {
	//$hmera=gmdate('d/m/Y H:i:s');
	
	$timezone=2; 
	$hmera=gmdate("d/m/Y H:i:s", time() + 3600*($timezone+date("I"))); 
	
	
	$data = mysql_query("UPDATE tblinvoices SET notes = '-' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysql_error()); 
	
	echo $hmera;
	
}



//header("Location: $goto?expdate=$nextduedate");
























if ($action == 'redirect') {
	
	//customer_code_prefix
	
	
	// EDIT PRODUCT
	if ($productid) {
		$productid=str_replace('H','', $productid); 
		$productid=str_replace('D','', $productid); 
		$productid=str_replace('S','', $productid); 
		$productid=str_replace('R','', $productid); 
		$productid=str_replace('O','', $productid); 
		header('Location: '."admin/configproducts.php?action=edit&id=".$productid);
		
	}
	
	// EDIT CUSTOMER
	if ($customerid) {
		//customer_code_prefix
		$customerid=str_replace('C','', $customerid); 
		header('Location: '."admin/clientsprofile.php?userid=".$customerid);
		
	}
	
	
	// EDIT ORDER
	if ($orderid) {
		$orderid=str_replace($relatedchar,'', $orderid); 
		header('Location: '."admin/invoices.php?action=edit&id=".$orderid);
		
	}
	
	
	
	
	
	
}



?> 		