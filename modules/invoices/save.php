<?php
checkLogin();
$smarty -> assign('pageActive', 'invoice_new');
$smarty -> assign('active_tab', '#invoice');

if(!isset($_POST['action'])) {
	exit("no save action");
}

$saved = false;

if ($_POST['action'] == "insert" ) {
	if(insertInvoice()) {
		$id = lastInsertId();//last invoice id.
		$saved = true;
	}

	if ($saved) {		
		$logger->log('Max items:'.$_POST['max_items'], Zend_Log::INFO);
		$i = 0;
		while ($i <= $_POST['max_items']) {
			$logger->log('i='.$i, Zend_Log::INFO);
			$logger->log('qty='.$_POST["quantity$i"], Zend_Log::INFO);
			if($_POST["quantity$i"] != null)
			{
				if (
				insertInvoiceItem(	$id,
							$_POST["trading_type_id"],
							$_POST["description$i"],
							$_POST["currencys$i"],
							$_POST["quantity$i"],
							$_POST["unit_price$i"],
							$_POST["charge$i"],
							$_POST["note_cost$i"],							
							$i
						)
				) 
				{
				addItemUpdateCurrencyNote($_POST["trading_type_id"],$_POST["currencys$i"],$_POST["quantity$i"],$_POST["unit_price$i"]);
					//$saved = true;
				} else {
					die(end($dbh->errorInfo()));
				}
			}
			$i++;
		}
	}
} 

elseif ( $_POST['action'] == "edit") {

	//Get type id - so do add into redirector header

	$id = $_POST['id'];
	
	if (updateInvoice($_POST['id'])) {
		//updateCustomFieldValues($_POST['categorie'],$_POST['invoice_id']);
		$saved = true;
	}
    
	$logger->log('Max items:'.$_POST['max_items'], Zend_Log::INFO);
	$i = 0;
	while ($i <= $_POST['max_items']) 
	{
//	for($i=0;(!empty($_POST["quantity$i"]) && $i < $_POST['max_items']);$i++) {
		$logger->log('i='.$i, Zend_Log::INFO);
		$logger->log('qty='.$_POST["quantity$i"], Zend_Log::INFO);
		$logger->log('currency='.$_POST["currencys$i"], Zend_Log::INFO);

		if($_POST["delete$i"] == "yes")
		{
			delete('invoice_items','id',$_POST["line_item$i"]);
		}
		if($_POST["delete$i"] !== "yes")
		{
		
		
			if($_POST["quantity$i"] != null)   {
	
				//new line item added in edit page
				if($_POST["line_item$i"] == "")
				{
					insertInvoiceItem(	$id,
								$_POST["trading_type_id"],
								$_POST["description$i"],
								$_POST["currencys$i"],
								$_POST["quantity$i"],
								$_POST["unit_price$i"],
								$_POST["charge$i"],
								$_POST["note_cost$i"],						
								$i
							);
					addItemUpdateCurrencyNote($_POST["trading_type_id"],$_POST["currencys$i"],$_POST["quantity$i"],$_POST["unit_price$i"]);
				}
				
				if($_POST["line_item$i"] != "")
				{
					updateInvoiceItem(						$i,
								$_POST["trading_type_id"],
								$_POST["description$i"],
								$_POST["currencys$i"],
								$_POST["quantity$i"],
								$_POST["unit_price$i"],
								$_POST["charge$i"],
								$_POST["note_cost$i"],
								$_POST["line_item$i"]
							);
					$saved;
				}
			}
		}
		$i++;
	}
}

//Get type id - so do add into redirector header
$smarty->assign('saved', $saved);
$smarty->assign('id', $id);
?>
