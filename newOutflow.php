<?php include 'head.php'; ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/js/select2.min.js"></script>

<div class="sectionTitle">REGISTRAR SALIDA</div>

<div class="format">
<form method="post" action="includes/createNewOutflow.php" id="newOutflow">
<table width="100%" border="0" cellspacing="20px" cellpadding="0">
  <tbody>
    <tr>
      <td width="50%">Almacén<br>
      	<div style="margin-top:10px">
        	<?php
			if ($_SESSION["role"] == 'Y') {
				$myQuery = $dbhandle->query("SELECT ID, CONCAT(name, ' (', ID, ')') name FROM STORES");
				echo "<select id='store' name='store' style='margin-top:10px;' required>";
				echo "<option value='' selected disabled>Selecciona...</option>";
				while($row = $myQuery->fetch(PDO::FETCH_ASSOC)){
					echo "<option value='".$row["ID"]."'>".$row["name"]."</option>";
				};
				echo "</select>";
			} else {
				$myQuery = $dbhandle->query("SELECT ID, CONCAT(name, ' (', ID, ')') name FROM STORES WHERE ID = '".$_SESSION["store"]."'");
				$row = $myQuery->fetch(PDO::FETCH_ASSOC);
				echo "<input type='text' class='inputText' required readonly id='storeName' name='storeName' value='".$row["name"]."'/><input type='hidden' id='store' name='store' value='".$row["ID"]."'>";
			}
			?>
        </div>
      </td>
      <td width="50%">
        </td>
    </tr>
    <tr>
      <td colspan="2">Partidas<br>
        <div id="itemContainer">
        	<table class="itemListHead" cellpadding="0" cellspacing="10px" width="100%">
            	<thead>
                	<tr>
                    	<td width="76px">Cantidad</td>
                    	<td width="250px">Código</td>
                        <td>Producto</td>
                        <td width="30px"></td>
                    </tr>
                </thead>
            </table>
            <!-- Item Lines -->
        </div>
        <button type="button" id="addItemBT" class="formButton greenB">Agregar partida</button></div>
	  </td>
    </tr>
    <tr>
      <td colspan="2">Comentarios<br><textarea id="remarks" name="remarks" maxlength="256"></textarea></td>
    </tr>
    <tr>
      <td align="left"><button type="button" class="formButton redB" onClick="cancel();">Cancelar</button></td>
      <td align="right"><button type="submit" class="formButton blueB" id="saveButton">Guardar</button></td>
    </tr>
  </tbody>
</table>
</form>
</div>

<script type="text/javascript">



$("#newOutflow").submit(function(e){
	e.preventDefault()
	let form = $(e.target)
	console.log( form.serializeArray() )
	let store = form.serializeArray()[0].value
	let body = {
		"store": store,
		"products": [],
		"quantities": []
	}

	let formProducts = $(".itemTable tr .prodCode")
	for( let i = 0 ; i < formProducts.length ; i++ ){
		let line = $(formProducts[ i ])
		body.products.push( line.val() )
	}
	let formQuantities = $(".itemTable tr .quant")
	for( let i = 0 ; i < formQuantities.length ; i++ ){
		let q = $(formQuantities[ i ])
		body.quantities.push( q.val() )
	}
	console.log(body)
	$.ajax({
		"method": "POST",
		"data": JSON.stringify(body),
		"url": "includes/checkStock.php",
		"contentType": "application/json",
		"success": function(response){
			console.log(response)
			let json = JSON.parse(response)
			if(json.code == 0){
				alert("No puedes generar esta entrada. El producto " + json.prodID + " tiene stock = " + json.stock)
			}else if(json.code == -1){
				console.log(json)
			}else{
				form.off("submit")
				form.submit()
			}
		}   
	})
	return false;

})



var itemLine = "<div class='item'>\
			<table class='itemTable' width='100%' cellpadding='0' cellspacing='10px'>\
				<tr>\
					<td width='76px'><input type='number' id='quant' name='quant[]' required class='inputText quant' style='width:70px !important' min='1' value='1'></td>\
					<td width='250px'><input type='text' class='inputText prodCode' name='prodCode[]' id='prodCode' required></td>\
					<td>\
						<input id='product' name='product[]' class='inputText itemProduct' required>\
					</td>\
					<td width='30px'><i class='fa fa-trash-o remove' aria-hidden='true'></i></td>\
				</tr>\
			</table>\
		</div>";

// Get Product when Code is inserted

var getProdFromCode = function(ind) {
	var code = $(".prodCode:eq("+ind+")").val();
	var storeID = $("#store").val();
	if (code.length >= 3) {
		$.ajax({
			type: "GET",
			url: "includes/prodDetails.php?by=code&param="+encodeURI(code)+"&storeID="+storeID,
			dataType: "json",
			cache: false,
			success: function(prodName){
				if (prodName["name"] != null && prodName["name"] != "") {
					$(".itemProduct:eq("+ind+")").val(prodName["name"]);
					$(".quant:eq("+ind+")").prop("max", prodName["quant"]);
				}
			}
		});
	}
};


// Get Code when Product is selected

var getCodeFromProd = function(ind) {
	var name = $(".itemProduct:eq("+ind+")").val();
	var storeID = $("#store").val();
	if (name.length >= 3) {
		$.ajax({
			type: "GET",
			url: "includes/prodDetails.php?by=name&param="+encodeURI(name)+"&storeID="+storeID,
			dataType: "json",
			cache: false,
			success: function(prodCode){
				if (prodCode["code"] != null && prodCode["code"] != "") {
					$(".prodCode:eq("+ind+")").val(prodCode["code"]).after(function() {
                        getProdFromCode(ind);
                    });
				}
			}
		});
	}
};
var addItem = function() {
	$("#itemContainer").append(itemLine);
	$(".prodCode:last-child").focus();
	$("#saveButton").prop("disabled", false);
};

addItem();

$(document).on("input", ".prodCode", function() {
	var ind = $(".prodCode").index(this);
	$(".prodCode:eq("+ind+")").autocomplete({
		minLength: 3,
		source: "includes/searchProd.php?by=code",
		select: function(event, ui) {
			var origEvent = event;
			while (origEvent.originalEvent !== undefined) {
				origEvent = origEvent.originalEvent;
			}
			if (origEvent.type == "click") {
				$(".prodCode:eq("+ind+")").val(ui.item.value);
			} else {
				$(".prodCode:eq("+ind+")").val(ui.item.value);
			}
			getProdFromCode(ind);
		},
		close: function() {
			getProdFromCode(ind);
		}
	});
	var input = $(this);
	var start = input[0].selectionStart;
	$(this).val(function (_, val) {
		return val.toUpperCase();
	});
	input[0].selectionStart = input[0].selectionEnd = start;
	if ($(".prodCode:eq("+ind+")").val().length >= 3) {
		getProdFromCode(ind);
	}
});

$(document).on("input", ".itemProduct", function() {
	var ind = $(".itemProduct").index(this);
	$(".itemProduct").autocomplete({
		minLength: 3,
        source: "includes/searchProd.php?by=name",
		select: function(event, ui) {
			var origEvent = event;
			while (origEvent.originalEvent !== undefined) {
				origEvent = origEvent.originalEvent;
			}
			if (origEvent.type == "click") {
				$(".itemProduct:eq("+ind+")").val(ui.item.value);
			} else {
				$(".itemProduct:eq("+ind+")").val(ui.item.value);
			}
			getCodeFromProd(ind);
		},
		close: function() {
			getCodeFromProd(ind);
		}
    });
	if ($(".prodCode:eq("+ind+")").val().length >= 3) {
		getCodeFromProd(ind);
	}
});


$(document).on('click', '.remove', function() {
	$(this).closest('.item').remove();
	if($("#itemContainer").children(".item").length == 0) {
		$("#saveButton").prop("disabled", true);
	}
});

$(document).on('keypress', function(e) {
	if(e.which == 13) {
		e.preventDefault();
		$("#addItemBT").click();
	}
});

$(document).ready(function() {
	<?php
	if ($_SESSION["role"] == 'Y') {
		echo "$('#store').select2();";
	}
	?>
});


$("#addItemBT").on('click', addItem);
		
function cancel() {
	window.location.href = 'outflows.php';
}
</script>
    
<?php include 'footer.php'; ?>