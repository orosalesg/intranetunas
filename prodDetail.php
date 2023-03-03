<?php
include 'head.php';

$prodID = $_REQUEST["prodID"];

$queryProd = "SELECT ID, code, name, detail, catID, vendorID, cost, price, active, remarks FROM PRODUCT WHERE ID = '$prodID'";
$resultProd = $dbhandle->query($queryProd);
$rowProd = $resultProd->fetch(PDO::FETCH_ASSOC);

$code = $rowProd["code"];
$name = $rowProd["name"];
$detail = $rowProd["detail"];
$catID = $rowProd['catID'];
$vendorID = $rowProd['vendorID'];
$cost = $rowProd['cost'];
$price = $rowProd['price'];
$active = $rowProd['active'];
$remarks = $rowProd['remarks'];

$queryNext = "SELECT MIN(ID) nextID FROM PRODUCT WHERE ID > '$prodID'";
$resultNext = $dbhandle->query($queryNext);
$rowNext = $resultNext->fetch(PDO::FETCH_ASSOC);
$nextID = $rowNext["nextID"];

$queryPrev = "SELECT MAX(ID) prevID FROM PRODUCT WHERE ID < '$prodID'";
$resultPrev = $dbhandle->query($queryPrev);
$rowPrev = $resultPrev->fetch(PDO::FETCH_ASSOC);
$prevID = $rowPrev["prevID"];

?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/js/select2.min.js"></script>

<div class="sectionTitle">
    <a href="prodDetail.php?prodID=<?php echo $prevID; ?>"><i class="fa fa-arrow-left prev" aria-hidden="true"></i></a>
    DETALLE DE PRODUCTO <?php echo $prodID; ?>
    <a href="prodDetail.php?prodID=<?php echo $nextID; ?>"><i class="fa fa-arrow-right next" aria-hidden="true"></i></a>
</div>

<div class="format">
<form method="post" action="includes/editProduct.php">
<input type="hidden" id="prodID" name="prodID" value="<?php echo $prodID; ?>">
<table width="100%" border="0" cellspacing="20px" cellpadding="0">
  <tbody>
    <tr>
      <td width="50%">Nombre<br>
      	<div style="margin-top:10px"><input type="text" id="name" name="name" class="inputText" value="<?php echo $name; ?>" required></div>
      </td>
      <td width="50%">Código<br>
        <div style="margin-top:10px"><input type="text" readonly id="code" name="code" class="inputText" value="<?php echo $code; ?>"></div>
        </td>
    </tr>
    <tr>
      <td>Descripción<br>
      	<div style="margin-top:10px"><input type="text" id="detail" name="detail" class="inputText" value="<?php echo $detail; ?>"></div>
      </td>
      <td>Activo<br>
      	<input type="checkbox" name="active" id="active" <?php if($active == 'Y') {echo "checked";}; ?>>
      </td>
    </tr>
    <tr>
      <td>Categoría<br>
      	<div style="margin-top:10px"><select id="cat" name="cat" style="margin-top:10px;">
        	<?php
			$myQuery = $dbhandle->query("SELECT ID, CONCAT(catName, ' (', ID, ')') catName FROM CAT ORDER BY catName");
			while($row = $myQuery->fetch(PDO::FETCH_ASSOC)){
				if ($catID == $row["ID"])
					{
						$selected = 'selected="selected"';
					}
					else
					{
						$selected = '';
					}
				echo "<option value='".$row['ID']."' ".$selected.">".$row['catName']."</option>";		
			};
			?>
        </select></div>
      </td>
      <td>Proveedor<br>
        <div style="margin-top:10px"><select id="vendor" name="vendor">
        	<?php
			$myQuery = $dbhandle->query("SELECT ID, CONCAT(name, ' (', ID, ')') name FROM VENDOR ORDER BY name");
			while($row = $myQuery->fetch(PDO::FETCH_ASSOC)){			
				if ($vendorID == $row["ID"])
					{
						$selected = 'selected="selected"';
					}
					else
					{
						$selected = '';
					}
				echo "<option value='".$row['ID']."' ".$selected.">".$row['name']."</option>";	
			};
			?>
        </select></div>
        </td>
    </tr>
    <tr>
      <td>Costo ($)<br>
      	<div style="margin-top:10px"><input type="number" id="cost" name="cost" class="inputText" min="0" step="any" value="<?php echo $cost; ?>" required></div>
      </td>
      <td>Precio de venta ($)<br>
        <div style="margin-top:10px"><input type="number" id="price" name="price" class="inputText" min="0" step="any" value="<?php echo $price; ?>" required></div>
        </td>
    </tr>
    <tr>
      <td colspan="2">Comentarios<br><textarea id="remarks" name="remarks" maxlength="256"><?php echo $remarks; ?></textarea></td>
    </tr>
    <tr>
      <td colspan="2">Existencias<br>
        <div id="itemContainer">
        	<div class='itemHeader'>
				<table class='itemTable' width='100%' cellpadding='0' cellspacing='10px'>
					<tr>
                    	<td>Almacén</td>
						<td width="100px">Existencia</td>
                        <td width="100px">Por recibir</td>
						<td width="76px">Min</td>
                        <td width="76px">Max</td>
                        <td width="76px">Dif</td>
                        <td width="50px">Kardex</td>
					</tr>
				</table>
			</div>
        	<?php
        	$myQuery = $dbhandle->query("SELECT T1.ID, T1.prodCode, T2.ID storeID, CONCAT(T2.name, ' (', T2.ID, ')') store, T1.qty, T1.OnOrder, T1.minq, T1.maxq FROM PRDL T1 JOIN STORES T2 ON T1.storeID = T2.ID WHERE T1.prodCode = '$code' ORDER BY T2.name");
						
			while($row = $myQuery->fetch(PDO::FETCH_ASSOC)){
				$storeID = $row["storeID"];
				$dif = $row['qty'] - $row['maxq'];
				if ($dif < 0) {
					$color = 'c60909';
				} elseif ($dif > 0) {
					$color = '1279e0';
				} else {
					$color = '000000';
				}
				echo "
					<div class='item'>
						<table class='itemTable' width='100%' cellpadding='0' cellspacing='10px'>
							<tr>
								<td>".$row['store']."<input type='hidden' name='storeID[]' value='".$row['storeID']."'></td>
								<td width='100px'>".$row['qty']."</td>
								<td width='100px'>".$row['OnOrder']."</td>
								<td width='76px'><input type='number' min='0' id='min' name='min[]' class='inputText' style='width:70px; text-align:center; !important' value='".$row['minq']."' required></td>
								<td width='76px'><input type='number' min='0' id='max' name='max[]' class='inputText' style='width:70px; text-align:center; !important' value='".$row['maxq']."' required></td>
								<td width='76px' style='color:#".$color."'>".$dif."</td>
								<td width='50px' style='font-size: 2em'><a href='kardex.php?prodCode=".$code."&storeID=".$storeID."&prodID=".$prodID."'><i class='fa fa-history' aria-hidden='true'></a></i></td>
							</tr>
						</table>
					</div>
				";
			};
			?>
        </div>
	  </td>
    </tr>
    <tr>
      <td align="left"><button type="button" class="formButton redB" onClick="cancel();">Cancelar</button></td>
      <td align="right"><button type="submit" class="formButton blueB">Guardar</button></td>
    </tr>
  </tbody>
</table>
</form>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("#cat").select2();
		$("#vendor").select2();
	});
	
	function cancel() {
		window.location.href = 'products2.php';
	}
</script>
    
<?php include 'footer.php'; ?>