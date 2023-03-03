<?php
session_start();
include "mysqlconn.php";

$store = $_POST["storeID"];
$employee = $_SESSION["empID"];
$remarks = $_POST['remarks'];

// Discover next ID
$numQuery = "SELECT ID FROM ORDERS ORDER BY ID DESC LIMIT 1";
$resultID = $dbhandle->query($numQuery);
if (!$resultID)
{
	die('Could not read data ID: ' . $resultID->errorInfo());
} else {
	$rowID = $resultID->fetch(PDO::FETCH_ASSOC);	
	$ID = $rowID["ID"] + 1;
}

// Discover next code
$codeQuery = "SELECT code FROM ORDERS WHERE storeID = $store ORDER BY code DESC LIMIT 1";
$resultCode = $dbhandle->query($codeQuery);
if (!$resultCode)
{
	die('Could not read data Code: ' . $resultCode->errorInfo());
} else {
	$rowCode = $resultCode->fetch(PDO::FETCH_ASSOC);
	if ($rowCode["code"] == "" || $rowCode["code"] == NULL) {
		$queryStore = $dbhandle->query("SELECT code FROM STORES WHERE ID = $store");
		$rowStore = $queryStore->fetch(PDO::FETCH_ASSOC);
		$code = $rowStore["code"]."-PED-100001";
	} else {
		$curCode = str_split($rowCode["code"], 8);
		$code = $curCode[0].($curCode[1] + 1);
	}
}

// Enter Inflow to DB
$sql = "INSERT INTO ORDERS (ID, code, storeID, empID, created_at, remarks) VALUES ('$ID', '$code', $store, '$employee', CURRENT_TIMESTAMP, '$remarks')";
$retval = $dbhandle->query($sql);
if(! $retval )
{
  die('Could not enter data: ' . $retval->errorInfo());
}

// Enter Inflow Lines
foreach ($_POST['prodCode'] as $key => $value) {
	$quant = $_POST['quant'][$key];
	$sql = "INSERT INTO ORDL (ID, ordID, prodCode, qty) VALUES (NULL, '$ID', '$value', '$quant')";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $retval->errorInfo());
	}
	
	// Modify inventory
	// Current QTY onOrder
	$myQuery = $dbhandle->query("SELECT OnOrder FROM PRDL WHERE prodCode = '$value' AND storeID = '$store'");
	$row = $myQuery->fetch(PDO::FETCH_ASSOC);
	$curQty = $row["OnOrder"];
	
	$newQty = $curQty + $quant;
	
	$sql = "UPDATE PRDL SET OnOrder = '$newQty' WHERE prodCode = '$value' AND storeID = '$store'";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $retval->errorInfo());
	}
}

header('Location: ../orders.php');
?>