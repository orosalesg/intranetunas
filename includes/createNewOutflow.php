<?php
session_start();
include "mysqlconn.php";

$store = $_POST["store"];
$employee = $_SESSION["empID"];
$remarks = $_POST['remarks'];

// Discover next ID
$numQuery = "SELECT ID FROM OUTFLOWS ORDER BY ID DESC LIMIT 1";
$resultID = $dbhandle->query($numQuery);
if (!$resultID)
{
	die('Could not read data: ' . $resultID->errorInfo());
} else {
	$rowID = $resultID->fetch(PDO::FETCH_ASSOC);	
	$ID = $rowID["ID"] + 1;
}

// Discover next code
$codeQuery = "SELECT code FROM OUTFLOWS WHERE storeID = $store ORDER BY code DESC LIMIT 1";
$resultCode = $dbhandle->query($codeQuery);
if (!$resultCode)
{
	die('Could not read data Code: ' . $resultCodo->errorInfo());
} else {
	$rowCode = $resultCode->fetch(PDO::FETCH_ASSOC);
	if ($rowCode["code"] == "" || $rowCode["code"] == NULL) {
		$queryStore = $dbhandle->query("SELECT code FROM STORES WHERE ID = $store");
		$rowStore = $queryStore->fetch(PDO::FETCH_ASSOC);
		$code = $rowStore["code"]."-SAL-100001";
	} else {
		$curCode = str_split($rowCode["code"], 8);
		$code = $curCode[0].($curCode[1] + 1);
	}
}


// Enter Outflow to DB
$sql = "INSERT INTO OUTFLOWS (ID, code, storeID, empID, created_at, remarks) VALUES ('$ID', '$code', '$store', '$employee', CURRENT_TIMESTAMP, '$remarks')";
$retval = $dbhandle->query($sql);
if(! $retval )
{
  die('Could not enter data: ' . $retval->errorInfo());
}

// Enter Outflow Lines
foreach ($_POST['prodCode'] as $key => $value) {
	$quant = $_POST['quant'][$key];
	$sql = "INSERT INTO OUTLN (ID, outID, prodCode, qty) VALUES (NULL, '$ID', '$value', '$quant')";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $$retval->errorInfo());
	}
	
	// Modify inventory
	// Current QTY
	$myQuery = $dbhandle->query("SELECT qty FROM PRDL WHERE prodCode = '$value' AND storeID = '$store'");
	$row = $myQuery->fetch(PDO::FETCH_ASSOC);
	$curQty = $row['qty'];
	
	$newQty = $curQty - $quant;
	
	$sql = "UPDATE PRDL SET qty = '$newQty' WHERE prodCode = '$value' AND storeID = '$store'";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $retval->errorInfo());
	}
	
	
}

header('Location: ../outflows.php');
?>