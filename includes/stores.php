<?php
include "mysqlconn.php";

$ID = $_POST["storeID"];
$code =  $_POST["code"];
$name = utf8_decode($_POST["name"]);
$phone =  $_POST["phone"];
$address = utf8_decode($_POST["address"]);
if(isset($_POST["active"])) {
	$active = 'Y';
} else {
	$active = 'N';
}

if($ID == "") {
	// Discover next ID
	$queryID = "SELECT ID FROM STORES ORDER BY ID DESC LIMIT 1";
	$resultID = $dbhandle->query($queryID);
	$rowID = $resultID->fetch(PDO::FETCH_ASSOC);
	$lastID = $rowID["ID"];
	$nextID = $lastID + 1;
		
	$query = "INSERT INTO STORES (ID, code, name, phone, address, active) VALUES ($nextID, '$code', '$name', '$phone', '$address', '$active')";
	
	$queryProducts = "SELECT code FROM PRODUCT";
	$resultProducts = $dbhandle->query($queryProducts);
	while($rowProducts = $resultProducts->fetch(PDO::FETCH_ASSOC)) {
		$queryLines = "INSERT INTO PRDL (ID, prodCode, storeID, qty, minq, maxq) VALUES (NULL, '".$rowProducts["code"]."', $nextID, 0, '1', '1')";
		$resultLines = $dbhandle->query($queryLines);
	};
	
} else {
	$query = "UPDATE STORES SET code = '$code', name = '$name', phone = '$phone', address = '$address', active = '$active' WHERE ID = '$ID'";
}
$result = $dbhandle->query($query);

if(!$result) {
	die('Could not enter data STORE: ' . $result->errorInfo());
}
?>