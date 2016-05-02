<?php 
# In PHP 5.2 or higher we don't need to bring this in
# it's to use json_encode and json_decode
require "librerias/jsonwrapper/jsonwrapper.php";

/* --------------------------------------------------*/
	session_start();
	include_once("connection.php");
	$obj=new connection();
	$obj->session_out();
	$user= $_SESSION['nom_user'];
	$cmd="call sp_sis_get_clientes('$user');";
	$result= mysqli_query($obj->cn,$cmd) or die(mysqli_error($obj->cn));	
	$lst_columns=$obj->get_columns_from($result);
	$lst_datos=$obj->get_array_from($result);
	$obj->get_free_results($obj->cn);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Catalogo Clientes</title>
     <link rel="stylesheet" href="librerias/tinytablev3.0/style.css" />
    <link rel="stylesheet" href="librerias/calendarview/calendarview.css" />
    <script type="text/javascript"  src="librerias/prototype_1.7/prototype.js"></script>
    <script type="text/javascript" src="librerias/calendarview/calendarview.js"></script>
    <script type="text/javascript"  src="librerias/prototype_1.7/src/scriptaculous.js?load=effects"></script>
    <script type="text/javascript">
		function BeginEvents()
		{
		   Event.observe("bt_agregar","click",BtAgregar_Click);
		   Event.observe('bt_cancelar','click',BtCancel_Click);
		   Event.observe('bt_guardar','click',BtGuardar_Click);
		   setupCalendars();
		}
		function setupCalendars() {
                                        // Embedded Calendar
                                        // Popup Calendar
                                           Calendar.setup(
                                                                {
                                                                   dateField: 'txt_birthday',
                                                                   triggerElement: 'bt_birthday'
                                                                }
                                                        )
	      }

		function BtAgregar_Click()
		{ if(confirm("Desea agregar un nuevo cliente?"))
		  {add_customer();}
		}
		
		Event.observe(window,"load",BeginEvents)
    </script>
    <style type="text/css">
		.big{  height:100%; width:100%; position:absolute; top:0px; left:0px;  
			background-color:#999; opacity:0.4; filter: alpha(opacity=100);}
		.hidden{ height:100%; width:100%; position:absolute; top:0px; left:0px; }
		
		
	        #div_header{ width:auto; height:32px; background-color:#FFF; 
                        padding-top:10px; margin-bottom:3px; border:1px solid #c6d5e1;
                        padding-left:4px}
		#div_header label{ font: 14px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
						font-weight:bold; text-transform:uppercase }
				
		#layer{ position: absolute; top:20%; left:0; right:0; height:auto; width:auto; /*padding:10px 10px 10px 10px;  */
				background-color:#FFF; /*background:url(../../../librerias/tinytablev3.0/images/bg.gif) repeat-x; */
				padding:6px; background-color:#FFF; margin:0px 150px 0px 150px ; border:solid 1px #}
	     	#layer div#div_layer{ width:auto; height:auto;
						padding:20px 0px 10px 10px;  border:1px solid #c6d5e1; background-color:#F8F8F9 }
		#layer div#div_layer table#tbdatos{ width:auto; }
		#layer div#div_layer table#tbdatos tr td#label{ /*width:20%; */ }
	        #layer div#div_layer table#tbdatos tr td#value{ /*width:80%; */} 
		#layer div#div_layer table#tbdatos tr td { padding:3px 0px 3px 15px; font-size:13px;}
		#layer div#div_layer table#tbdatos tr td input[type="text"], 
		#layer div#div_layer table#tbdatos tr td select { padding:4px; font-size:12px; text-transform:uppercase;
								 font-family: 'Lucida Grande', Tahoma, Verdana, sans-serif;}
		#layer div#div_layer table#tbdatos tr td select{ width:215px}
		#layer div#div_layer table#tbdatos tr td input[type="button"]{ width:100px; padding:4px;cursor:pointer; 
					font:bold 12px Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif}
					
		#layer div#div_layer table#tbdatos tr td input[name="txt_nombre"], #layer div#div_layer table#tbdatos tr td input[name="txt_direccion"]{ width:350px;}
		
		
		.usuarios_hide{ display:none;}
		.usuarios_show{display:; position:absolute; top:0px; left:0px;}
		#div_usuarios{width:auto; height:auto; max-height:300px; overflow-y:scroll; border:1px solid #DDE9F7}
		.item_under{ height:20px; width:600px; background-color:#FFF; color:#000; padding-left:4px;
						 font:12px solid Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif}
		.item_over{ background-color:#538ECB; width:600px; color:#FFF; height:20px;  cursor:pointer;
				font:12px solid Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;padding-left:4px;}
				
		input[type="text"]:focus,select:focus, textarea:focus{  background-color:#EFF5FF; border:solid 1px #73A6FF; }
                input[type="text"],select, textarea{ border:solid 1px #C6D5E1; padding:3px;}
    </style>
</head>

<body>

      <div id="big" class="big" style="display:none;">
	      	<div class="child"></div>
      </div> 
          <div id="div_header" >
          		<label>Catalogo Clientes </label>
          </div>

          	<div id="tablewrapper">
					<div id="tableheader">
								<div class="search">
                                        <select id="columns" onchange="sorter.search('query')" style="font-size:12px"></select>
                                        <input type="text" id="query" onkeyup="sorter.search('query')" style="font-size:12px" />
								</div>
                                <img id="bt_agregar" src="imagenes/pluss.png" title="Agregar Nuevo Registro" 
                                			style="cursor:pointer; margin-left:5px">
								<span class="details">
									<div>Registros <span id="startrecord"></span>-<span id="endrecord">
                                    					</span> de <span id="totalrecords"></span></div>
									<div><a href="javascript:sorter.reset()">Resetear</a></div>
								</span>
					 </div>
				<table cellpadding="0" cellspacing="0" border="0" id="table"
						 class="tinytable" style="font-size:12px" width="100%">
					<thead>
						<tr>
						     <?
							foreach($lst_columns as $column)
							{ echo ($column=='modif'?"<th><h3></h3></th>":"<th ><h3>$column</h3></th>");}	
						     ?>
						</tr>
						<tbody>
							<?php
								foreach($lst_datos as $row)
								{ 
								  echo "<tr>";
								  foreach($lst_columns as $column)
								  { 
								     switch($column)
								     {
									 case 'modif': echo "<td style='text-align:center'>".$row[$column]."</td>"; break;
									 default: echo "<td>".utf8_encode($row[$column])."</td>"; break;
								     }
								  }
								  echo "</tr>";
								}
							?>
						</tbody>
					</thead>
				</table>
					 <div id="tablefooter">
						 <div id="tablenav">
								<div>
                                     <img src="librerias/tinytablev3.0/images/first.gif" width="16" 
                                              	  height="16" alt="First Page" onclick="sorter.move(-1,true)" />
                                    <img src="librerias/tinytablev3.0/images/previous.gif" 
                                          	  width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
                                    <img src="librerias/tinytablev3.0/images/next.gif" width="16" 
                                         	   height="16" alt="First Page" onclick="sorter.move(1)" />
                                    <img src="librerias/tinytablev3.0/images/last.gif" width="16" 
                                           	 height="16" alt="Last Page" onclick="sorter.move(1,true)" />
								</div>
								<div>
									<select id="pagedropdown"></select>
								</div>
								<div>
									<a href="javascript:sorter.showall()">Ver Todo</a>
								</div>
						 </div>
						<div id="tablelocation">
								<div>
									<select onchange="sorter.size(this.value)">
									<option value="5">5</option>
										<option value="10">10</option>
										<option value="20" selected="selected">20</option>
										<option value="50">50</option>
										<option value="100">100</option>
									</select>
									<span>Entrada por pagina</span>
								</div>
								<div class="page">Pagina <span id="currentpage"></span> de <span id="totalpages"></span></div>
						</div>
					</div>
				</div>
	
		<script type="text/javascript" src="librerias/tinytablev3.0/script.js"></script>
		<script type="text/javascript">
		var sorter = new TINY.table.sorter('sorter','table',{
			headclass:'head',
			ascclass:'asc',
			descclass:'desc',
			evenclass:'evenrow',
			oddclass:'oddrow',
			evenselclass:'evenselected',
			oddselclass:'oddselected',
			paginate:true,
			size:20,
			colddid:'columns',
			currentid:'currentpage',
			totalid:'totalpages',
			startingrecid:'startrecord',
			endingrecid:'endrecord',
			totalrecid:'totalrecords',
			hoverid:'selectedrow',
			pageddid:'pagedropdown',
			navid:'tablenav',
			sortcolumn:1,
			sortdir:1,
			/*sum:[8],
			avg:[6,7,8,9],
			columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],*/
			init:true
		});
	  </script>

	  <div id="layer" style="display:none; ">
		<form id="frm_edit" name="frm_edit">
			<input type="hidden" id="txt_idcliente" name="txt_idcliente" value="">
       		<div id="div_layer" >
				<table id="tbdatos">
					<tr>
						<td id="label"></td>
						<td id="value" colspan=3></td>
					</tr>
					<tr>
						<td>Fch. Ingreso</td>
						<td colspan=3><input type="text" name="txt_fch_ingreso"  readonly="true" id="txt_fch_ingreso"></td>
					</tr>
					<tr>
                                                <td>ID</td>
                                                <td colspan=3><input type="text" name="txt_id"   id="txt_id" maxlength=10></td>
                                        </tr>
					<tr>
						<td>Nombre</td>
						<td colspan=3><input type="text" name="txt_nombre"   id="txt_nombre"></td>
					</tr>
					<tr>
						<td>Direccion</td>
						<td colspan=3><input type="text" name="txt_direccion"  id="txt_direccion"></td>
					</tr>
					<tr>
						<td>Ciudad</td>
						<td ><input type="text" name="txt_ciudad"  id="txt_ciudad"></td>
						<td>ZIP Code</td>
                                                <td ><input type="text" name="txt_zipcode"   id="txt_zipcode"></td>
					</tr>
					<tr>
                                                <td>State</td>
                                                <td colspan=3><input type="text" name="txt_state"   id="txt_state"></td>
                                        </tr>

					<tr>
						<td>Pais</td>
						<td >
							<select id="cb_pais" name="cb_pais">
								<option value="">Seleccione...</option>
								<?
									$query="select pais as nom_pais from sis_paises where activo=1 ;";
									$result=$obj->get_array_from(mysqli_query($obj->cn,$query));
									foreach($result as $row)
									{ echo "<option value='".$row['nom_pais']."'>".$row['nom_pais']."</option>";} 
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Telefono</td>
						<td ><input type="text" name="txt_telefono"  id="txt_telefono"></td>
					</tr>
					<tr>
						<td>Telefono 2</td>
						<td ><input type="text" name="txt_telefono2"  id="txt_telefono2"></td>
					</tr>						
					<tr>
						<td>Email</td>
						<td ><input type="text" name="txt_email"  id="txt_email"></td>
					</tr>	
					<tr>
						<td>Contacto</td>
						<td ><input type="text" name="txt_contacto"  id="txt_contacto"></td>
						<td>Birthday</td>
                                                <td ><input type="text" name="txt_birthday"  id="txt_birthday">
						     <input type="button" name="bt_birthday" 
								id="bt_birthday" value=".." style="width:20px;"/>
						</td>
					</tr>	
					<tr>
						<td>Habilitado</td>
						<td><select name="cb_estado" id="cb_estado">
							<option  value="">Seleccione..</option>
							<option  value="1">ACTIVO</option>
							<option  value="0">NO ACTIVO</option>
						</select></td>
					</tr>
					<tr>
						<td colspan="4">
							<input type="button" id="bt_guardar" value="Guardar">
							<input type="button" id="bt_cancelar" value="Cancelar">
						  <!--  <input type="button" id="bt_prueba" value="Prueba" onclick="GetUsuarios();"> -->
						</td>
					</tr>
				</table>
        	 </div> 
		</form>
      </div>	
      <div id="div_usuarios" class="usuarios_hide">
      </div>

  
 <script type="text/javascript" >

	function FieldsClean()
	{
		var fields=$('frm_edit');
		for(nitem=0;nitem<=fields.length-1;nitem++)
		{ var field=fields[nitem];
		 if(field.type!='button'){$(fields[nitem]).value='';}
		}
	}

	
	function BtGuardar_Click()
	{
		if(document.getElementById('cb_estado').value!="")
		{
			if(confirm('Desea proceder a guardar los datos?'))
			{
				var operacion='save_customer';
				var data="tipo_operacion="+operacion+"&"+Form.serialize('frm_edit');
				new Ajax.Request("procesos.php",{method:'post',parameters:data,
										onSuccess: function(resultado){var response = resultado.responseText;
														var obj=JSON.parse(response);
														if(obj.id==1)
														{ alert(obj.descripcion);
														parent.middleside.location='catalogo_clientes.php';}
														else
														{ alert(obj.descripcion); } 
												}});	  
			}
		}
		else
		{alert("Error, debe seleccionar un estado ACTIVO / NO ACTIVO.");}
	}
	function BtCancel_Click()
	{ 	
		var result=confirm("Desea cancelar los cambios?");
		if(result)
		{
			var fondo= document.getElementById('big');
			Effect.toggle('layer','appear');
			Effect.toggle('big','appear');
			FieldsClean();
		}
	}
	
	function add_customer()
	{
		 var fondo= document.getElementById('big');
	         fondo.className="big";
		 Effect.toggle('big','appear');
		 Effect.toggle('layer','appear');
		 if($('txt_id').getAttribute('readonly')){$('txt_id').removeAttribute('readonly');}
	}
	function edit_customer(nfila)
	{
		
		if($('txt_id').getAttribute('readonly')){$('txt_id').removeAttribute('readonly');}
		$('txt_id').setAttribute('readonly','readonly');
		
		var fondo= document.getElementById('big');
		fondo.className="big";
		Effect.toggle('big','appear');

		var operacion='get_values';
		var data="tipo_operacion="+operacion+"&idcliente="+ nfila;
		
		new Ajax.Request("procesos.php",{method:'post',parameters:data,
								onSuccess: function(resultado){var response = resultado.responseText;
													var result=JSON.parse(response);  
													if(result[0].nombre!=null)
													{
													  $('txt_idcliente').value=result[0].codigo;
													  $('txt_fch_ingreso').value=result[0].fch_ingreso;
													  $('txt_nombre').value=result[0].nombre;
													  $('txt_direccion').value=result[0].direccion;
													  $('txt_ciudad').value=result[0].ciudad;
													  $('txt_telefono').value=result[0].telefono;
													  $('txt_telefono2').value=result[0].telefono2;
													  $('txt_email').value=result[0].email;
													  $('txt_contacto').value=result[0].contacto;
													  $('cb_estado').value=result[0].activo;
													  $('cb_pais').value=result[0].pais;
													  $('txt_id').value=result[0].id;
													  $('txt_zipcode').value=result[0].zip_code;
													  $('txt_state').value=result[0].state;
													  $('txt_birthday').value=result[0].birthday;	
													 }
													Effect.toggle('layer','appear');
													}});	 
													
	}
 </script>
</body>
</html>
