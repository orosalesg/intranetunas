<?php
session_start();
include "mysqlconn.php";

$baseOrd = "";
$orStore = $_POST["orStore"];
$dsStore = $_POST["dsStore"];
$employee = $_SESSION["empID"];
$remarks = $_POST['remarks'];

if (isset($_POST["ordCode"])) {
	$orStore = 100;
	$dsStore = $_POST["store"];
	
	$baseOrd = $_POST["ordCode"];
	
	$queryCloseOrder = "UPDATE ORDERS SET active = 'N', closed_at = CURRENT_TIMESTAMP WHERE code = '$baseOrd'";
	$resultCloseOrder = $dbhandle->query($queryCloseOrder);
	if (!$resultCloseOrder)
	{
		die('Could not write data CloseOrder: ' . $resultCloseOrder->errorInfo());
	}
	
	// Modify inventory
	foreach ($_POST['prodCode'] as $key => $value) {
		$quant = $_POST['quant'][$key];
		// Current QTY onOrder
		$myQuery = $dbhandle->query("SELECT OnOrder FROM PRDL WHERE prodCode = '$value' AND storeID = '$dsStore'");
		$row = $myQuery->fetch(PDO::FETCH_ASSOC);
		$curQty = $row["OnOrder"];
		
		$newQty = $curQty - $quant;
		
		$sql = "UPDATE PRDL SET OnOrder = '$newQty' WHERE prodCode = '$value' AND storeID = '$dsStore'";
		$retval = $dbhandle->query($sql);
		if(! $retval )
		{
		  die('Could not enter data: ' . $retval->errorInfo());
		}
	}
}

// Discover next ID
$numQuery = "SELECT ID FROM TRANSFERS ORDER BY ID DESC LIMIT 1";
$resultID = $dbhandle->query($numQuery);
if (!$resultID)
{
	die('Could not read data: ' . $resultID->errorInfo());
} else {
	$rowID = $resultID->fetch(PDO::FETCH_ASSOC);	
	$ID = $rowID["ID"] + 1;
}

// Discover next code
$codeQuery = "SELECT code FROM TRANSFERS WHERE dsStore = $dsStore ORDER BY code DESC LIMIT 1";
$resultCode = $dbhandle->query($codeQuery);
if (!$resultCode)
{
	die('Could not read data Code: ' . $resultCode->errorInfo());
} else {
	$rowCode = $resultCode->fetch(PDO::FETCH_ASSOC);
	if ($rowCode["code"] == "" || $rowCode["code"] == NULL) {
		$queryStore = $dbhandle->query("SELECT code FROM STORES WHERE ID = $dsStore");
		$rowStore = $queryStore->fetch(PDO::FETCH_ASSOC);
		$code = $rowStore["code"]."-TRN-100001";
	} else {
		$curCode = str_split($rowCode["code"], 8);
		$code = $curCode[0].($curCode[1] + 1);
	}
}



// Enter Transfer to DB
$sql = "INSERT INTO TRANSFERS (ID, code, baseOrd, orStore, dsStore, empID, created_at, account, remarks) VALUES ('$ID', '$code', '$baseOrd', '$orStore', '$dsStore', '$employee', CURRENT_TIMESTAMP, 'N', '$remarks')";
$retval = $dbhandle->query($sql);
if(! $retval )
{
  die('Could not enter data: ' . $retval->errorInfo());
}

// Enter Inflow Lines
foreach ($_POST['prodCode'] as $key => $value) {
	$quant = $_POST['quant'][$key];
	$sql = "INSERT INTO TRLN (ID, tranID, prodCode, qty) VALUES (NULL, '$ID', '$value', '$quant')";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $retval->errorInfo());
	}
	
	// Modify inventory
	// Current QTY
	$myQuery = $dbhandle->query("SELECT qty FROM PRDL WHERE prodCode = '$value' AND storeID = '$orStore'");
	$row = $myQuery->fetch(PDO::FETCH_ASSOC);
	$curQty = $row['qty'];
	
	$newQty = $curQty - $quant;
	
	$sql = "UPDATE PRDL SET qty = '$newQty' WHERE prodCode = '$value' AND storeID = '$orStore'";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $retval->errorInfo());
	}
	
	$myQuery = $dbhandle->query("SELECT qty FROM PRDL WHERE prodCode = '$value' AND storeID = '$dsStore'");
	$row = $myQuery->fetch(PDO::FETCH_ASSOC);
	$curQty = $row['qty'];
	
	$newQty = $curQty + $quant;
	
	$sql = "UPDATE PRDL SET qty = '$newQty' WHERE prodCode = '$value' AND storeID = '$dsStore'";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $retval->errorInfo());
	}
	
	
}

header('Location: ../transfers.php');
?>