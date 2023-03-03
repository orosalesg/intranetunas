<?php
include 'head.php';

$storeIDs = array();
$storeNames = array();

$storesQuery = "SELECT ID, code, name FROM STORES ";
if ($_SESSION["role"] != 'Y') {
	$storesQuery.= "WHERE ID = '".$_SESSION["store"]."' ";
}
$storesQuery.= "ORDER BY ID ASC";
$storesResult = $dbhandle->query($storesQuery);
while ($storesRow = $storesResult->fetch(PDO::FETCH_ASSOC)) {
	$storeIDs[] = $storesRow["ID"];
	$storeCodes[] = $storesRow["code"];
	$storeNames[] = $storesRow["name"];
}

?>


<script type="text/javascript" charset="utf8" src="js/dt/js/jquery.dataTables.min.js"></script>


<div class="sectionTitle">INVENTARIO</div>

<div class='sButtons'>
<a class="sButton" href="products.php">REPORTE</a>
<?php
if($_SESSION["role"] == 'Y') {
	echo "<a href='newProduct.php' class='sButton'>NUEVO PRODUCTO</a>";
}
?>
</div>

<div class="searchDiv">
Mostrar existencias del almacén <select id="selectW" class="" name="">
    <?php
	if($_SESSION["role"] == "Y") {
		echo "<option value='0'>Global</option>";
	}
	foreach ($storeIDs as $i => $storeID) {
		echo "<option value='".$storeID."'>".$storeNames[$i]."</option>";
	}
	?>
</select><br>
Busqueda por
<input type="radio" name="searchBy[]" class="searchBy" value="CODIGO" checked> Código
<input type="radio" name="searchBy[]" class="searchBy" value="NOMBRE"> Nombre
<input type="radio" name="searchBy[]" class="searchBy" value="CATEGORIA"> Categorísa
<?php
if ($_SESSION["role"] == "Y") {
	echo "<input type='radio' name='searchBy[]' class='searchBy' value='PROVEEDOR'> Proveedor";
}
?>

</div>

<table id="prodList" class="display" cellspacing="0" cellpadding="0" width="100%">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Activo</th>
            <th>Categoría</th>
            <?php
			if($_SESSION["role"] == "Y") {
				echo "<th>Proveedor</th>";
			}
			?>
            <th>Existencia</th>
            <th>Por recibir</th>
            <?php
			if($_SESSION["role"] == "Y") {
				echo "<th width='25px'>Ver</th>";
			}
			?>
        </tr>
    </thead>
</table>

<div class="sButtons">
<a class="sButton" href="products.php">REPORTE</a>
<?php
if($_SESSION["role"] == 'Y') {
	echo "<a href='newProduct.php' class='sButton'>NUEVO PRODUCTO</a>";
}
?>
</div>

<script>
var aux_xhr;

$(document).ready(function() {
	var table = $('#prodList').on('preXhr.dt', function (e, settings, data) {
			console.log("PREXHR");
        }).DataTable({
		"order": [[ 1, "asc" ]],
		"aoColumnDefs": [
			{'bSortable': false, 'aTargets': [6] },
			{'className': 'dt-center', 'aTargets': [6] }
		],
		"scrollX": true,
		"processing": true,
		"serverSide": true,
		"ajax": {
			url: "includes/productList.php?by=CODIGO",
			type: "post",
			beforeSend: function(xhr, settings){
				if($("#searchParam").val().length <= 4 && $("#searchParam").val().length > 0)
					xhr.abort();
			}
		}
	});
	
	$("#searchParam").prop("placeholder", "CODIGO");
	
	var sendValues = function() {
		$("#searchParam").prop("placeholder", $(".searchBy:checked").val());
		table.ajax.url("includes/productList.php?wh="+$("#selectW").val()+"&by="+$(".searchBy:checked").val()).load();
		
	};
	
	$("#selectW").on("change", sendValues);
	$(".searchBy").on("change", sendValues);
});


</script>
    
<?php include 'footer.php'; ?>