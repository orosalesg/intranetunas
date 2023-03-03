<?php
session_start();
include "mysqlconn.php";

$baseOrd = "";
$store = $_POST["store"];
$employee = $_SESSION["empID"];
$remarks = $_POST['remarks'];

if (isset($_POST["ordCode"])) {
	$baseOrd = $_POST["ordCode"];
	
	$queryCloseOrder = "UPDATE ORDERS SET active = 'C', closed_at = CURRENT_TIMESTAMP, remarks = '$remarks' WHERE code = '$baseOrd'";
	$resultCloseOrder = $dbhandle->query($queryCloseOrder);
	if (!$resultCloseOrder)
	{
		die('Could not write data CloseOrder: ' . $resultCloseOrder->errorInfo());
	}
	
	// Modify inventory
	foreach ($_POST['prodCode'] as $key => $value) {
		$quant = $_POST['quant'][$key];
		// Current QTY onOrder
		$myQuery = $dbhandle->query("SELECT OnOrder FROM PRDL WHERE prodCode = '$value' AND storeID = '$store'");
		$row = $myQuery->fetch(PDO::FETCH_ASSOC);
		$curQty = $row["OnOrder"];
		
		$newQty = $curQty - $quant;
		
		$sql = "UPDATE PRDL SET OnOrder = '$newQty' WHERE prodCode = '$value' AND storeID = '$store'";
		$retval = $dbhandle->query($sql);
		if(! $retval )
		{
		  die('Could not enter data: ' . $retval->errorInfo());
		}
	}
}

header('Location: ../orders.php');
?>