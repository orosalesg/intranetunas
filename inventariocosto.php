<?php
session_start();

$timeout = 7200; // 2 hours
 if(isset($_SESSION['timeout'])) {
    $duration = time() - (int)$_SESSION['timeout'];
    if($duration > $timeout) {
        session_destroy();
        session_start();
    }
}
$_SESSION['timeout'] = time();

if($_SESSION["authenticated_user"] != true) {
	header("Location: index.php");
	die();
}

include_once "includes/mysqlconn.php";

require('fpdf/mc_table_inventario.php');

//Lineas inventario

$query = "SELECT T1.code, T1.name product";
if ($_SESSION["role"] == 'Y') {
	$query.= ", T1.price, (SELECT SUM(qty) FROM PRDL T4 WHERE T4.prodCode = T1.code) total";
}
$query.= " FROM PRODUCT T1  ORDER BY T1.name ASC";
$result = $dbhandle->query($query);
$registros = count($result->fetch(PDO::FETCH_ASSOC));

if ($registros > 0) {
   require_once 'PHPExcel/Classes/PHPExcel.php';
   $objPHPExcel = new PHPExcel();
   
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("intranet")
        ->setLastModifiedBy("intranet USYM")
        ->setTitle("Inventario USYM")
        ->setSubject("Inventario")
        ->setDescription("Documento generado con PHPExcel")
        ->setCategory("Confidencial");    

   $e = 2;   
  while($row = $result->fetch(PDO::FETCH_OBJ)){
   
       
      $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Codigo")
            ->setCellValue('B1', "Descripcion")
            ->setCellValue('C1', "Costo")
            ->setCellValue('D1', "Inv-Global")
            ->setCellValue('E1', "UNO")
            ->setCellValue('F1', "TVS")
            ->setCellValue('G1', "ARB")
            ->setCellValue('H1', "LFT")
            ->setCellValue('I1', "DPE")
            ->setCellValue('J1', "ISL")
            ->setCellValue('K1', "VIP")
            ->setCellValue('L1', "SFL")
            ->setCellValue('M1', "DAS")
            ->setCellValue('N1', "UCH")
            ->setCellValue('O1', "LAS")
            ->setCellValueExplicit('A'.$e, $row->code,PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValueExplicit('B'.$e, $row->product,PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValue('C'.$e, $row->price)
            ->setCellValue('D'.$e, $row->total);
            
    $objPHPExcel->getActiveSheet()
    ->getStyle('C'.$e)
    ->getNumberFormat()
    ->setFormatCode('"$"#,##0.00_-');
    
    $objPHPExcel->getActiveSheet()
    ->getStyle('D'.$e)
    ->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
      $e++;
      
      
      $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
  
}	

	
	/* inventario sucursal 100 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='100') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res100 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc100 = $res100->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E'.$i, $suc100->cant);
      
       $i++;
    	 }
    	 

/* inventario sucursal 101 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='101') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res101 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc101 = $res101->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('F'.$i, $suc101->cant);
      
       $i++;
    	 }
    	 
    	 
/* inventario sucursal 102 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='102') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res102 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc102 = $res102->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('G'.$i, $suc102->cant);
      
       $i++;
    	 }

/* inventario sucursal 103 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='103') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res103 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc103 = $res103->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('H'.$i, $suc103->cant);
      
       $i++;
    	 }
    	 
/* inventario sucursal 104 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='104') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res104 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc104 = $res104->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('I'.$i, $suc104->cant);
      
       $i++;
    	 }
    	 
    	 
/* inventario sucursal 105 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='105') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res105 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc105 = $res105->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('J'.$i, $suc105->cant);
      
       $i++;
    	 }


/* inventario sucursal 106 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='106') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res106 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc106 = $res106->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('K'.$i, $suc106->cant);
      
       $i++;
    	 }
    	 
/* inventario sucursal 107 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='107') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res107 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc107 = $res107->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('L'.$i, $suc107->cant);
      
       $i++;
    	 }
    	 
    	 
/* inventario sucursal 108 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='108') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res108 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc108 = $res108->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('M'.$i, $suc108->cant);
      
       $i++;
    	 }
    	 
/* inventario sucursal 109 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='109') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res109 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc109 = $res109->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('N'.$i, $suc109->cant);
      
       $i++;
    	 }
    	 
/* inventario sucursal 110 */
	$byStqry = "SELECT T1.code, T1.name product";
	if ($_SESSION["role"] == 'Y') {
	$byStqry.= ", T1.price, (SELECT qty FROM PRDL T4 WHERE T4.prodCode = T1.code and T4.storeID='110') cant ";
	}
	$byStqry.= " FROM PRODUCT T1 ORDER BY T1.name ASC";
	$res110 = $dbhandle->query($byStqry);
	
	$i = 2;   
  while($suc110 = $res110->fetch(PDO::FETCH_OBJ)){
          
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('O'.$i, $suc110->cant);
      
       $i++;
    	 }


	  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
};


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="inventario.xlsx"');
header('Cache-Control: max-age=0');

$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
$objWriter->save('php://output');
exit;
mysql_close();

?>