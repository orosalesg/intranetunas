<?php
include_once "includes/mysqlconn.php";

require('fpdf/mc_table_pedidoGlobal.php');

$ordID = $_GET["ordID"];
$pdf = new PDF_MC_Table();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',8, 'C');



//Datos entrega
$query1 = "SELECT T1.ID, T2.ID dsStoreID, T2.name dsStore, CONCAT(T3.first, ' ', T3.last) emp, DATE_FORMAT(T1.created_at, '%d-%m-%Y %T') created_at, T1.remarks FROM ORDERS T1 INNER JOIN STORES T2 ON T1.storeID = T2.ID INNER JOIN CREW T3 ON T1.empID = T3.ID WHERE T1.ID = '$ordID'";
$result1 = $dbhandle->query($query1);
$row1 = $result1->fetch(PDO::FETCH_ASSOC);

$dsStoreID = $row1["dsStoreID"];
$remarks= $row1["remarks"];
	
	
	
// Lineas
$queryTran = "SELECT SUM(T1.qty) qty, T1.prodCode, T3.name, T4.name vendor, T3.price, (SUM(T1.qty) * T3.price) import FROM ORDL T1 JOIN ORDERS T2 ON T1.ordID = T2.ID JOIN PRODUCT T3 ON T1.prodCode = T3.code JOIN VENDOR T4 ON T3.vendorID = T4.ID WHERE T2.active = 'Y' GROUP BY T1.prodCode ORDER BY T3.name ASC";
$resultTran = $dbhandle->query($queryTran);
$pdf->SetDrawColor(255,255,255);
$total = 0;
while($rowTran = $resultTran->fetch(PDO::FETCH_ASSOC)) {
	$pdf->SetWidths(array(16,40,65,35,20,20));
	$pdf->SetAligns(array('','','','','R','R'));
	$pdf->Row(array($rowTran["qty"],$rowTran["prodCode"],$rowTran["name"],$rowTran["vendor"],"$ ".$rowTran["price"],"$ ".$rowTran["import"]));
	$total += $rowTran["import"];
}
$pdf->SetDrawColor(0,0,0);


////////////////////////////////////////////////////////////////////

$pdf->Cell(196,5,'',0,1,'');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(156,5,'',0,0);
$pdf->Cell(40,5,'Total',1,1,'C');
$pdf->SetFont('Arial','B',14);
$pdf->Cell(156,10,'',0,0);
$pdf->Cell(40,10,'$ '.number_format($total, 2,'.',','),1,1,'R');


$pdf->Cell(148);
//////////////////////////////////////////////////////////////////////

$pdf->Ln(2);

$pdf->SetFont('Arial','B',8);
$pdf->Cell(196,.1,'',0,1,'',true);
$pdf->SetFont('Arial','B',8);
// Page number
$pdf->Cell(30,6,'Comentarios: ',0,1,'L');
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(190, 8, $remarks,0,'L', false);
$pdf->Cell(196,.1,'',0,1,'',true);
$pdf->SetFont('Arial','B',8);
$pdf->Ln(8);
$pdf->Cell(20, 5,'',0,0, 'L');
$pdf->Cell(58, 5,'Elabora:',0,0, 'L');
$pdf->Cell(40, 5,'',0,0, 'L');
$pdf->Cell(58, 5,'Autoriza:',0,0, 'L');
$pdf->Cell(20, 5,'',0,1,'L');
$pdf->Cell(20, 20,'',0,0,'L');
$pdf->Cell(58, 20,'','B',0, 'L');
$pdf->Cell(40, 20,'',0,0, 'L');
$pdf->Cell(58, 20,'','B',0, 'L');
$pdf->Cell(20, 20,'',0,1, 'L');
$pdf->Cell(20, 5,'',0,0, 'L');
$pdf->Cell(58, 5,'',0,0, 'C');
$pdf->Cell(40, 5,'',0,0, 'L');
$pdf->Cell(58, 5,$nameTo,0,0, 'C');
$pdf->Cell(20, 5,'',0,1,'L');
$pdf->Cell(20, 5,'',0,0, 'L');
$pdf->Cell(58, 5,'Fecha y hora:',0,0, 'L');
$pdf->Cell(40, 5,'',0,0, 'L');
$pdf->Cell(58, 5,'Fecha y hora:',0,0, 'L');
$pdf->Cell(20, 5,'',0,1,'L');
$pdf->SetFont('Arial','',8);

$pdf->Output();
?>