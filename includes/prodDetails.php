<?php
session_start();

include "mysqlconn.php";

$by = $_REQUEST["by"];
$param =  utf8_decode($_GET["param"]);
$storeID = $_REQUEST["storeID"];

$queryProd = "SELECT code, name, price FROM PRODUCT WHERE $by = '" . urldecode($param) . "'";
$resultProd = $dbhandle->query($queryProd);
$rowProd = $resultProd->fetch(PDO::FETCH_ASSOC);
$code = $rowProd["code"];

$queryQuant = "SELECT qty FROM PRDL WHERE prodCode = '$code' AND storeID = $storeID";
$resultQuant = $dbhandle->query($queryQuant);
$rowQuant = $resultQuant->fetch(PDO::FETCH_ASSOC);


if(count($rowProd) != 0) {
	$rowProd["code"] = utf8_encode($rowProd["code"]);
	$rowProd["name"] = utf8_encode($rowProd["name"]);
	$rowProd["price"] = $rowProd["price"];
	$rowProd["quant"] = $rowQuant["qty"];
	
	echo json_encode($rowProd);
}
?>