<?php
session_start();
include "mysqlconn.php";

$store = $_POST["storeID"];
$employee = $_SESSION["empID"];
$fromDate = $_POST["fromDate"];
$toDate = $_POST["toDate"];

// Discover next ID
$numQuery = "SELECT ID FROM PAYMENT ORDER BY ID DESC LIMIT 1";
$resultID = $dbhandle->query($numQuery);
if (!$resultID)
{
	die('Could not read data ID: ' . $resultID->errorInfo());
} else {
	$rowID = $resultID->fetch(PDO::FETCH_ASSOC);	
	$ID = $rowID["ID"] + 1;
}

// Discover next code
$codeQuery = "SELECT code FROM PAYMENT WHERE storeID = $store ORDER BY code DESC LIMIT 1";
$resultCode = $dbhandle->query($codeQuery);
if (!$resultCode)
{
	die('Could not read data Code: ' . $resultCode->errorInfo());
} else {
	$rowCode = $resultCode->fetch(PDO::FETCH_ASSOC);
	if ($rowCode["code"] == "" || $rowCode["code"] == NULL) {
		$queryStore = $dbhandle->query("SELECT code FROM STORES WHERE ID = $store");
		$rowStore = $queryStore->fetch(PDO::FETCH_ASSOC);
		$code = $rowStore["code"]."-PGO-100001";
	} else {
		$curCode = str_split($rowCode["code"], 8);
		$code = $curCode[0].($curCode[1] + 1);
	}
}

// Enter Payment to DB
$sql = "INSERT INTO PAYMENT (ID, code, created_at, storeID, empID, active, fromDate, toDate) VALUES ('$ID', '$code', CURRENT_TIMESTAMP, $store, $employee, 'Y', '$fromDate', '$toDate')";
$retval = $dbhandle->query($sql);
if(! $retval )
{
  die('Could not enter data: ' . $retval->errorInfo());
}

// Modify Transfer Lines
foreach ($_POST["tranCode"] as $key => $value) {
	$sql = "UPDATE TRANSFERS SET account = 'Y', pmntCode = '$code' WHERE code = '$value'";
	$retval = $dbhandle->query($sql);
	if(! $retval )
	{
	  die('Could not enter data: ' . $retval->errorInfo());
	}
}

header('Location: ../payments.php');
?>