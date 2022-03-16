<?php
/*------------------------------------------------------------------------
		# EMDI - WHMCS BRIDGE by SBZ systems - Solon Zenetzis - version 1.1
		# ------------------------------------------------------------------------
		# author    SBZ systems - Solon Zenetzis
		# copyright Copyright (C) 2013-2015 sbzsystems.com. All Rights Reserved.
		# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Websites: http://www.sbzsystems.com
		# Technical Support:  Forum - http://www.sbzsystems.com
	-------------------------------------------------------------------------*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header('Content-Type: text/html; charset=UTF-8');


require "configuration.php";
$maintax=24;
// Connects to your Database
$link=mysqli_connect("$db_host", $db_username, $db_password) or die(mysqli_error($link));
mysqli_select_db($link,"$db_name") or die(mysqli_error($link));

$ip=$_SERVER['REMOTE_ADDR'];   // USER'S IP 
$action=$_REQUEST['action'];       // PRODUCT CODE
$orderid=$_REQUEST['orderid'];       // PRODUCT CODE
$key=$_REQUEST['key'];       // PRODUCT CODE
$tmp_path=realpath(dirname(__FILE__)).'/templates_c';
$measure='ΤΕΜΑΧΙΑ';
$customerid=$_REQUEST['customerid'];
$productid=$_REQUEST['productid'];
$test=$_REQUEST['test'];
$relatedchar='^';


//if (!($key=='')) { exit; }


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
	//$Data = time(); 
	//fwrite($Handle, $Data); 
	//fclose($Handle); 	
	
	
	//GET SQL TIME
    $data = mysqli_query($link, "SELECT NOW() dtime") or die(mysqli_error($link));
    while ($alldata = mysqli_fetch_array($data)) {
        $dtime = $alldata['dtime'];
        break;
    }
    //
    fwrite($Handle, $dtime);
    fclose($Handle);
    
    mysqli_close($link);
	
	
}
if ($action == 'productsok') {
	$file = $tmp_path."/products_".$key; 
	$Handle = fopen($file, 'w');
	//$data = time();
	//fwrite($handle, $data); 
	//fclose($handle); 	
	
	
	
	//GET SQL TIME
    $data = mysqli_query($link, "SELECT NOW() dtime") or die(mysqli_error($link));
    while ($alldata = mysqli_fetch_array($data)) {
        $dtime = $alldata['dtime'];
        break;
    }
    //
    fwrite($Handle, $dtime);
    fclose($Handle);
    
    mysqli_close($link);
	
	
}












//$pcuser = iconv('ISO-8859-7', 'UTF-8', $pcuser);
//$pc = iconv('ISO-8859-7', 'UTF-8', $pc);


//if ($pc.$pcuser=='') {exit;}

//// GET PRODUCT ID 
//$query = "SET NAMES 'utf-8'";
//mysqli_query($link,$query);  


//$query = "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'";
//mysqli_query($link,$query);  



if ($action == 'customers') {
	
	$file = $tmp_path."/customers_".$key; 
	$lastdate=0;
	if (file_exists($file)) {
		$handle = fopen($file, 'r'); 
		$lastdate = fread($handle, 20); 
		fclose($handle); 
	}
	
	
	
	
	$data = mysqli_query($link," SELECT * FROM tblclients where '$test'='1' or lastdate>'" . $lastdate . "'") or die(mysqli_error($link)); 
	echo "ΚΩΔΙΚΟΣ;ΟΝΟΜΑ;ΕΠΙΘΕΤΟ;ΔΙΕΥΘΥΝΣΗ;ΤΚ;ΧΩΡΑ;ΠΟΛΗ/ΝΟΜΟΣ;ΠΕΡΙΟΧΗ;ΤΗΛΕΦΩΝΟ;ΚΙΝΗΤΟ;EMAIL;ΑΦΜ;ΔΟΥ;ΕΠΩΝΥΜΙΑ;ΕΠΑΓΓΕΛΜΑ;ΓΛΩΣΣΑ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
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
		
		
		$datain = mysqli_query($link,'SELECT * FROM tblcustomfieldsvalues where tblcustomfieldsvalues.relid='.$id.'
			ORDER BY tblcustomfieldsvalues.relid,tblcustomfieldsvalues.fieldid ASC') or die(mysqli_error($link)); 
		
		while($alldatain = mysqli_fetch_array( $datain ))
		{			
			$field_[$alldatain['fieldid']]=$alldatain['value']; 
		}
		
		$afm=$alldata['tax_id'];
		$doy=$field_[2];
		$mobile=$field_[4];
		$epaggelma=$field_[3];
		
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
		$lastdate = fread($handle, 20); 
		fclose($handle); 
	}
	
	
	
	
	
	
	
	////PRODUCTS
	$data = mysqli_query($link,"SELECT tblproducts.type as keym, tblproducts.paytype, tblpricing.type,tblproducts.id,tblproducts.name as name1,tbltax.taxrate,tblproducts.servertype,
		
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
		tblproducts.lastdate>'" . $lastdate . "'
		
		
		group by name1
		") or die(mysqli_error($link)); 
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΦΠΑ;ΤΙΜΗ1;ΤΙΜΗ2;ΔΙΑΘΕΣΙΜΟΤΗΤΑ;ΜΟΝΑΔΑ;ΚΑΤΗΓΟΡΙΑ<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
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
		//if (!($taxrate>0)) { $taxrate=0; }	
		
		$name=$name1;//.' '.$name2;
		
		if ($monthly>0){
			$price=$monthly;
		}
		else if ($annually>0){
			$price=$annually;
		}
		else if ($semiannually>0){
			$price=$semiannually;
		}
		else if ($quarterly>0){
			$price=$quarterly;
		}
		else if ($biennially>0){
			$price=$biennially;
		}
		else{
			$price=$triennially;
		}
		
		if (($paytype=='onetime') || ($paytype=='free')) {				
			echo $type.$id.';ΑΔΕΙΑ '.$name.';;'.$maintax.';'.number_format($price, 2, ',', '').";;;".$measure.";".$name2.";<br>\n";		
		} else {
			echo $type.$id.';'.$name.';;'.$maintax.';'.number_format($price, 2, ',', '').";;;".$measure.";".$name2.";<br>\n";
		}
		
		
	}
	
	/*
	////////DOMAINS
	$data = mysqli_query($link,"SELECT * FROM tbldomainpricing
		
		left join tblpricing
		on tblpricing.relid=tbldomainpricing.id and tblpricing.type like 'domain%'
		
		where
		tblpricing.lastdate>'" . $lastdate . "'
		group by type
		
		") or die(mysqli_error($link)); 
	
	while($alldata = mysqli_fetch_array( $data ))
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
		
		
		
		echo $type.';'.$typos.';;'.$maintax.';'.$fee.';;;'.$periodos.";<br>\n";
		
	}
	*/
	
	
}


































if ($action == 'orders') {
	
	$query="
	SELECT * 
	
	,
	case when status='Paid' then
	(SELECT group_concat(concat(acc.gateway,' ',DATE_FORMAT(acc.date,'%d/%m/%Y'),' ',acc.amountin)) FROM tblaccounts acc where acc.invoiceid=tblinvoices.id) 
	else '' end
	bank
	
	,
	case when status='Paid' then
	(SELECT acc.transid FROM tblaccounts acc where acc.invoiceid=tblinvoices.id limit 1) 
	else '' end
	tranID
    ,

	case when status='Paid' then
	(SELECT acc.gateway FROM tblaccounts acc where acc.invoiceid=tblinvoices.id limit 1) 
	else '' end
	payment
	,
	
	
	
    (SELECT GROUP_CONCAT(	tblinvoiceitems.description) FROM tblinvoiceitems where tblinvoiceitems.invoiceid=tblinvoices.id) description
	
	
	
	
	
	FROM tblinvoices 
	where status<>'Cancelled' 
	and status='Paid'
	and `date`>'2022-1-1'
	and notes='' 
	
	and (tax<>0 or (SELECT tblclients.taxexempt FROM tblclients where tblclients.id=tblinvoices.userid)=1) 
	
	and (SELECT tblclients.status FROM tblclients where tblclients.id=tblinvoices.userid)='Active'
	
	
	order by userid,id desc,`date` desc
	";
	
	$data = mysqli_query($link,$query) or die(mysqli_error($link)); //
	echo "ΚΩΔΙΚΟΣ ΠΑΡΑΓΓΕΛΙΑΣ;ΚΩΔΙΚΟΣ ΠΕΛΑΤΗ;ΚΟΣΤΟΣ ΜΕΤΑΦΟΡΙΚΩΝ;ΚΟΣΤΟΣ ΑΝΤΙΚΑΤΑΒΟΛΗΣ;ΕΚΠΤΩΣΗ;ΗΜΕΡΟΜΗΝΙΑ;ΣΧΟΛΙΟ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
	{
		$id=$alldata['id'];  	 	
		$userid= $alldata['userid']; 
		$docdate= strtotime(  $alldata['date']); 
		$docdate=date ("Y-m-d H:i:s", $docdate);
		$notes= $alldata['bank'];
		$payment= $alldata['payment'];
		$description= $alldata['description'];
		$tranID=$alldata['tranID'];
        $notes=$notes.' Transaction ID ='.$tranID.'';



		//if ($payment=='banktransfer_alpha') { $payment='ΚΑΤΑΘΕΣΗ - ALPHA'; }
		//if ($payment=='banktransfer_nbg') { $payment='ΚΑΤΑΘΕΣΗ - ΕΘΝΙΚΗ'; }
		//if ($payment=='banktransfer_piraeus') { $payment='ΚΑΤΑΘΕΣΗ - ΠΕΙΡΑΙΩΣ'; }
		//if ($payment=='banktransfer_eurobank') { $payment='ΚΑΤΑΘΕΣΗ - EUROBANK'; }
		//if ($payment=='banktransfer_viva') { $payment='ΚΑΤΑΘΕΣΗ - VIVA WALLET'; }
		//if ($payment=='banktransfer_revolut') { $payment='ΚΑΤΑΘΕΣΗ - REVOLUT'; }
		//if ($payment=='stripe') { $payment='ΚΑΡΤΑ - STRIPE'; }
		//if ($payment=='stripe_prebuilt1') { $payment='ΚΑΡΤΑ - STRIPE'; }
		//if ($payment=='vivapayments') { $payment='ΚΑΡΤΑ - VIVA WALLET'; }
		//if ($payment=='ethniki') { $payment='ΚΑΡΤΑ - ΕΘΝΙΚΗ'; }
		
		
		
		if ($payment=='vivapayments') { $payment='VIVA'; }
		if ($payment=='paypalcheckout') { $payment='PAYPAL'; }
		if ($payment=='banktransfer') { $payment='ΚΑΤΑΘΕΣΗ'; }
		if ($payment=='cash') { $payment='ΜΕΤΡΗΤΑ'; }
	/*	
		
	if ( stripos($description,'host') || 
	stripos($description,'dedicated') || 
	stripos($description,'domain') || 
	stripos($description,'.gr') ||
	stripos($description,'.com') ||
	stripos($description,'ΤΗΛΕΦΩΝΙΚΟ') ||
		stripos($description,'PLUGIN') )	  
		{
			$document='ΤΙΜΟΛΟΓΙΟ ΠΑΡΟΧΗΣ ΥΠΗΡΕΣΙΩΝ';
		} else {
			$document='ΤΙΜΟΛΟΓΙΟ ΠΑΡΟΧΗΣ ΛΟΓΙΣΜΙΚΟ';
		}
		
		
	*/ 
		
		
		
		
		echo $id.';C'.$userid.";0;0;0;$docdate;$notes;;;;;$payment;ONLINE;$document;<br>\n";
		
	}
}


























if ($action == 'order') {
	
	////HOSTING
	$data = mysqli_query($link,"SELECT 
		tblproducts.type as type1, tblhosting.billingcycle,
		tblhosting.packageid,
		tblinvoiceitems.description, tblinvoiceitems.amount, tblinvoiceitems.type
		
		,(
         (select tbltax.taxrate from tblinvoicedata,tbltax where tblinvoicedata.invoice_id=tblinvoiceitems.invoiceid and tbltax.country=tblinvoicedata.country limit 1)*taxed         
         *(case when (SELECT tblclients.taxexempt FROM tblinvoices,tblclients where tblinvoices.userid=tblclients.id and tblinvoices.id=tblinvoiceitems.invoiceid)=1 then 0 else 1 end)         
         ) tax
		
		FROM tblinvoiceitems
		
		left join tblhosting 
		on tblhosting.id=tblinvoiceitems.relid 
		and tblinvoiceitems.type='Hosting'
		
		left join tblproducts
		on tblproducts.id=tblhosting.packageid
		
		
		where invoiceid=".$orderid) or die(mysqli_error($link)); 
	echo "ΚΩΔΙΚΟΣ;ΠΕΡΙΓΡΑΦΗ1;ΠΕΡΙΓΡΑΦΗ2;ΠΕΡΙΓΡΑΦΗ3;ΠΟΣΟΤΗΤΑ;ΜΟΝΑΔΑ;ΤΙΜΗ;ΦΠΑ;ΕΚΠΤΩΣΗ;<br>\n";
	
	while($alldata = mysqli_fetch_array( $data ))
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
		
		//if (!($taxrate>0)) { $taxrate=$maintax; }			
		//if (!($taxrate>0)) { $taxrate=0; }	
		$taxrate=$alldata['tax'];
		
		
		
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
	

	if ($_REQUEST['docid']==95) {
		echo $hmera;
		exit;
	}
	

	
	$timezone=2; 
	$hmera=gmdate("d/m/Y H:i:s", time() + 3600*($timezone+date("I"))); 
	
		
	$data = mysqli_query($link,"UPDATE tblinvoices SET notes = 'ΚΟΠΗΚΕ ΤΙΜΟΛΟΓΙΟ ".$hmera."' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysqli_error($link)); 
	
	
	echo $hmera;
	
}

if ($action == 'cancelorder') {
	//$hmera=gmdate('d/m/Y H:i:s');
	
	$timezone=2; 
	$hmera=gmdate("d/m/Y H:i:s", time() + 3600*($timezone+date("I"))); 
	
	
	$data = mysqli_query($link,"UPDATE tblinvoices SET notes = '-' WHERE notes='' and tblinvoices.id in (".$orderid.")") or die(mysqli_error($link)); 
	
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