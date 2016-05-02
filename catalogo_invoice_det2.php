<?php 
	session_start();
	
/*
include '../../../../librerias/PhpExcel/Classes/PHPExcel.php';
include '../../../../librerias/PhpExcel/Classes/PhpExcel/Writer/Excel2007.php';

$objPHPExcel = new PHPExcel();
$directory_download="download_xls";
$file_name= $directory_download.'/xls_'.date('YmdHis').'.xls';
PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

*/
	include_once("connection.php");
    $obj=new connection();
	$p_idinvoice=(isset($_REQUEST['idinvoice'])?$_REQUEST['idinvoice']:"");	
	if(isset($_REQUEST['idinvoice']))
	{
		# Get Query Master
		$p_query="select s.*,c.direccion as cliente_direccion, concat(c.ciudad,' ',c.pais) as cliente_ciudad,
					c.telefono as cliente_telefono, c.telefono2 as cliente_telefono2 
				from sis_invoice as s	
					left join sis_clientes as c on c.codigo=s.id_cliente and c.activo=1 
				where s.idinvoice='$p_idinvoice';";

		$result=mysqli_query($obj->cn,$p_query) or die("Error ".mysqli_error($obj->cn).", trying to execute query: $p_query");
		if($result){$objFields=mysqli_fetch_array($result);}
		
		# Get Query Details
		$p_query_det="select cantidad,idproducto,convert(producto,binary) as producto, color,talla,peso,subtotal,total 
				     from sis_invoice_det where idinvoice='$p_idinvoice' order by item asc ;" ;
		unset($result);
     	$result=mysqli_query($obj->cn,$p_query_det) or die("Error ".mysqli_error($obj->cn).", trying to execute query: $p_query_det");
		if($result){$lst_fields=$obj->get_columns_from($result); $lst_datos=$obj->get_array_from($result);}
	}
	$p_fch_ingreso=(isset($_REQUEST['idinvoice'])?date("Y-m-d",strtotime($objFields['fch_ingreso'])):date("Y-m-d"));	
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Catalogo de Invoices</title>
  <link rel="stylesheet" href="librerias/calendarview/calendarview.css" />
  <link rel="stylesheet" href="librerias/tinytablev3.0/style.css" />
  <script type="text/javascript"  src="librerias/prototype_1.7/prototype.js"></script>
  <script type="text/javascript" src="librerias/calendarview/calendarview.js"></script>

    <style type="text/css">
		.big{  height:100%; width:100%; position:absolute; top:0px
                                        ; left:0px; display:block; background-color:#999; opacity:0.4; display:block; filter: alpha(opacity=100);}
        .hidden{ height:100%; width:100%; position:absolute; top:0px; left:0px; display:none}

		#table tr td{ padding-left:5px;}
		.div-buttons input[type="button"]{ padding:4px; font:bold 11px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif ;
						cursor:pointer;}
						
		input[type="text"]:focus,select:focus, textarea:focus{  background-color:#EFF5FF;  border:solid 1px #73A6FF; }						
		input[type="text],select, textarea{ background-color: #F8F8F8; outline: none; border: solid 1px #B5B5B5; padding:3px} 
		
		#div_header{ width:auto; height:32px; background-color:#FFF; 
							padding-top:10px; margin-bottom:6px; border:1px solid #c6d5e1;
							padding-left:4px}
			#div_header label{ font: 13px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
							font-weight:bold;  text-transform:uppercase }
							
			#div_search{ width:auto; height:auto; background-color:#FFF; 
						padding:10px 0px 10px 4px; margin:3px 0px 6px 0px; border:1px solid #c6d5e1;}
						
			#div_search table#tb tr td{ padding:3px 3px 3px 5px; font:12px bold 'Lucida Grande', Tahoma, Verdana, sans-serif;}
			#div_search table#tb tr td input[type="button"]:hover{ cursor: pointer; }
			#div_search table#tb tr td input[type="button"]{ padding:2px; font: 12px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif ;
							font-weight:bold }
			#div_search table#tb tr td input[type="button"]#bt_add{  width:100px;float:left; margin-right:6px}
			
			#div_search table#tb tr td iframe#layer_rpt{ height:27px; padding:3px; width:300px; float:left; overflow:none;}
			#div_search table#tb tr td input[type="text"],
			#div_search table#tb tr td select,
			#div_search table#tb tr td textarea{  text-transform:uppercase; border:solid 1px #C6D5E1; padding:2px;
										font: normal 12px Gotham, 'Lucida Grande', Tahoma, Verdana, sans-serif;  outline: none;} 
										
			#div_search table#tb tr td input[name="txt_direccion"]{ width:500px}
			#div_search table#tb tr td input[name="txt_ciudad"]{ width:200px}
			#div_search table#tb tr td select{ width:300px;text-transform:uppercase;}
			
			
		#div_report{ width:auto; height:auto; background-color:#FFF; 
						padding:10px 0px 10px 4px; margin-top:3px; border:1px solid #c6d5e1; }

		#layer{ position: absolute; top:20%; left:0; right:0; height:auto; width:auto;
               background-color:#FFF; padding:10px; margin:0px 150px 0px 150px ; border:solid 1px #A0B9CD}
		#layer div#div_layer{ width:auto; height:auto;
									padding:20px 0px 10px 10px;  border:1px solid #A0B9CD; background-color:#FFF }
									
		#layer div#div_layer table#tbdatos{ width:100%; }
		#layer div#div_layer table#tbdatos tr td { padding:3px 0px 3px 5px; font:normal 12px 'Lucida Grande', Tahoma, Verdana, sans-serif; }
		#layer div#div_layer table#tbdatos tr td input[type="text"],
		#layer div#div_layer table#tbdatos tr td select{ padding:2px; border: solid 1px #B5B5B5; text-transform:uppercase ;
								font: normal 12px Gotham, 'Lucida Grande', Tahoma, Verdana, sans-serif; }

		#layer div#div_layer table#tbdatos tr td input[type="text"]:focus,
                #layer div#div_layer table#tbdatos tr td select:focus{ background-color:#EFF5FF;  border:solid 1px #73A6FF;  }

		#layer div#div_layer table#tbdatos tr td input[type="button"]{ width:100px; padding:6px; cursor:pointer;
									font:bold 13px Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif}

		#layer div#div_layer table#tbdatos tr td input[name="txt_nombre"], #layer div#div_layer table#tbdatos tr td input[name="txt_direccion"]{ width:300px;}

		
			
    </style>
    <script type="text/javascript">
		function BeginEvents(){ 
	  		
		//Event.observe('bt_excel','click',ButtonExcel);
	  		Event.observe('bt_add','click',bt_add_click);
			Event.observe('cb_cliente','change',cb_cliente_changed);
			Event.observe('cb_catalogos','change',cb_catalogos_changed);
			Event.observe('cb_pagina','change',cb_pagina_changed);
			Event.observe('bt_cancelar','click',bt_cancel_click);
			Event.observe('bt_agregar','click',bt_agregar_click);
			Event.observe('bt_procesar','click',bt_procesar_click);
			Event.observe('bt_salir','click',bt_salir_click);	
			setupCalendars();  		
	   	}
	function get_list_details()
	{
	  var list=[];
	  var tb=document.getElementById('table').rows;
	  for(nitem=0;nitem<=tb.length-1;nitem++)
	  { 
	    if(nitem==0){continue;}
	    var item={cantidad:tb[nitem].cells[0].innerHTML,
					idproducto:tb[nitem].cells[1].innerHTML,
					descripcion:tb[nitem].cells[2].innerHTML,
					color:tb[nitem].cells[3].innerHTML,
					tamanio:tb[nitem].cells[4].innerHTML,
					peso:tb[nitem].cells[5].innerHTML,
					punitario:tb[nitem].cells[6].innerHTML,
					total:tb[nitem].cells[7].innerHTML};
					list[nitem-1]=item;
	  }
          return list;
	}	
	function bt_procesar_click()
	{
		var details=get_list_details();
        var data="lst_details="+JSON.stringify(details)+"&"+Form.serialize('frm_edit');
        new Ajax.Request("catalogo_invoice_update.php",{method:'post',parameters:data,
                                              onSuccess: function(resultado){var response = resultado.responseText;
																	var lst=JSON.parse(response);
																	if(lst.id==0)
																	{ alert(lst.descripcion);}
																	else
																	{ alert('Datos fueron actualizados satisfactoriamente');
																	  window.location='catalogo_invoice_det.php?idinvoice='+ lst.descripcion; }															 
                                                        }});  
	 }
	function bt_salir_click(){ if(confirm('Desea salir del registro de invoice?')){window.close();}}
	
	function get_select_text(obj){ return obj.options[obj.selectedIndex].text;}
	
	function bt_agregar_click()
	{
	  var bt_edit='<img src="imagenes/ico_edit2.png" title="Modificar" style="border:0px; cursor:pointer;">';
	  var bt_remove='<img src="imagenes/ico_remove.png" title="Eliminar" style="border:0px; cursor:pointer;">';
	  var lst_values= new Array($F('txt_cantidad'),$F('cb_producto'),get_select_text($('cb_producto')),$F('txt_color'),
				 $F('txt_talla'),$F('txt_peso'),$F('txt_punitario'),$F('txt_total'),bt_edit,bt_remove);
	  var row="";
	  var tr=document.createElement("tr"); 
	  for(nitem=0;nitem<=lst_values.length-1; nitem++)
	  { row= row+"<td "+((nitem==8 || nitem==9)?"style='text-align:center;'":"")+">"+lst_values[nitem]+"</td>";}
	  tr.innerHTML=row;
	  $('table').appendChild(tr);
	  // hide div fields
	  var fondo= document.getElementById('big');
          fondo.className="hidden";
          document.getElementById('layer').style.display="none";
          fields_clean();

	}
	 function cb_pagina_changed()
                {
                        var p_value = $('cb_pagina').value;
                        if(p_value!='')
                        {
                                        var operacion='get-catalogos-products';
                                        var data="tipo_operacion="+operacion+"&idcatalogo="+$('cb_catalogos').value+"&idpagina="+$('cb_pagina').value;
                                        new Ajax.Request("procesos.php",{method:'post',parameters:data,
                                                                onSuccess: function(resultado){var response = resultado.responseText;
                                                                                                  $('cb_producto').update(response);
                                                                                          }});
                        }
                        else
                        { $('cb_producto').update("<option value=''>Seleccione...</option>");}
                }

	 function cb_catalogos_changed()
                {
                        var p_value = $('cb_catalogos').value;
                        if(p_value!='')
                        {
                                        var operacion='get-catalogos-pages';
                                        var data="tipo_operacion="+operacion+"&idcatalogo="+$('cb_catalogos').value;
                                        new Ajax.Request("procesos.php",{method:'post',parameters:data,
                                                                onSuccess: function(resultado){var response = resultado.responseText;
                                                                                                  $('cb_pagina').update(response);
                                                                                          }});
                        }
                        else
                        { $('cb_pagina').update("<option value=''>Seleccione...</option>");}
                }

	function fill_cb_catalogo()
	{
		 var operacion='get-catalogos';
                 var data="tipo_operacion="+operacion;
                new Ajax.Request("procesos.php",{method:'post',parameters:data,
                                                onSuccess: function(resultado){var response = resultado.responseText;
                                                                              $('cb_catalogos').update(response);
                                                        }});
        }
	function bt_add_click()
	{
		var rst=confirm("Desea agregar un registro?");
		if(rst)
		{  
			fill_cb_catalogo();
			var fondo= document.getElementById('big');
                        fondo.className="big";
                        document.getElementById('layer').style.display="block";
					
		}
	}	
	function bt_cancel_click()
        {
                var result=confirm("Desea cancelar el agregado?");
                if(result)
                {
                        var fondo= document.getElementById('big');
                        fondo.className="hidden";
                        document.getElementById('layer').style.display="none";
                       	fields_clean();
                }
        }
	 function fields_clean()
        {
		var fields=document.getElementById('layer').getElementsByTagName('input');
                for(nitem=0;nitem<=fields.length-1;nitem++)
                { var field=fields[nitem];if(field.type!='button'){$(fields[nitem]).value='';}}
	
		var fields2=document.getElementById('layer').getElementsByTagName('select');
                for(nitem=0;nitem<=fields2.length-1;nitem++)
                { var field=fields2[nitem];$(fields2[nitem]).update("<option value=''>Seleccione...</option>");}		 
        }


	 function cb_cliente_changed()
        {
		if($('cb_cliente').value==''){ return false;}
                var operacion='get_customer_values';
                var data="tipo_operacion="+operacion+"&idcliente="+$('cb_cliente').value;
                new Ajax.Request("procesos.php",{method:'post',parameters:data,
										onSuccess: function(resultado){var response = resultado.responseText;
												var result=JSON.parse(response);
												if(result[0].nombre!=null)
												{
												  $('txt_direccion').value=result[0].direccion;
												  $('txt_ciudad').value=result[0].ciudad+" "+result[0].pais;
												  $('txt_telefono').value=result[0].telefono;
												  $('txt_telefono2').value=result[0].telefono2;
												 }
												}});

        }

	 function setupCalendars() {	
				// Embedded Calendar
				// Popup Calendar
				Calendar.setup(
					{
					   dateField: 'txt_fch_ingreso',
					   triggerElement: 'bt_fch_ingreso'
					}
				)
		}
		
	  function ButtonProcess()
	  {
		var est=confirm('Se procedera a realizar busqueda, desea continuar?');
		if(est)
		{
			document.getElementById('frm_edit').action='catalogo_invoice.php';
			document.getElementById('frm_edit').method='post';
			document.getElementById('frm_edit').submit();
		}
	   }
	   
	   function ButtonExcel()
	   {
		 /* window.open('<?php echo $file_name; ?>'); */
	/*	var frame=document.getElementById('layer_rpt'); 
		frame.target='_top';
		
		document.getElementById('frm_values').target='layer_rpt';
		document.getElementById('frm_values').action = 'rpt_caliddad2_xls.php';
		document.getElementById('frm_values').method = 'POST';
		document.getElementById('frm_values').submit();	 */
	  }
		
       Event.observe(window, 'load', BeginEvents);
    </script>
</head>

<body>

<form id="frm_edit" name="frm_edit" enctype="multipart/form-data">
	<input type="hidden" value="<?= $p_idinvoice ?>" id="txt_idinvoice" name="txt_idinvoice" />
        <div id="div_header" >
       			 <label>Registro de Invoice <?= (isset($_REQUEST['idinvoice'])?"Nro. $p_idinvoice":''); ?></label>
        </div>
        <div id="div_search" >
                <table cellpadding="0" cellspacing="1" style="width:80%" id="tb">
                     <tr>
                        <td>Fch.Ingreso</td>
                        <td colspan=3>
				<input type="text" id="txt_fch_ingreso" name="txt_fch_ingreso" value="<?= $p_fch_ingreso ?>" />
				<input type="button" id="bt_fch_ingreso" name="bt_fch_ingreso" value="..." />
			</td>
                    </tr>
		    <tr>
                        <td>Cliente</td>
                        <td colspan=3>
				<select id="cb_cliente" name="cb_cliente" >
					<option value="">Seleccione...</option>
					<?
					  $query="select codigo, nombre from sis_clientes where activo=1 order by nombre";
					  $result=$obj->get_array_from(mysqli_query($obj->cn,$query));
					  foreach($result as $row)
					  { echo "<option value='".$row['codigo']."' ".
						($objFields['id_cliente']==$row['codigo']?'selected':'').">".$row['nombre']."</option>";}
					?>
				</select>
			</td>
		    </tr>
		    <tr>
                        <td>Direccion</td>
			<td><input type="text" id="txt_direccion" name="txt_direccion" value="<?= $objFields['cliente_direccion']?>" /></td>
                        <td>Ciudad</td>
                        <td><input type="text" id="txt_ciudad" name="txt_ciudad" value="<?= $objFields['cliente_ciudad']?>" /></td>
                    </tr> 
		    <tr>
                        <td>Telefono Casa</td>
                        <td><input type="text" id="txt_telefono" name="txt_telefono" value="<?= $objFields['cliente_telefono']?>" /></td>
                        <td>Telefono Celular</td>
                        <td><input type="text" id="txt_telefono2" name="txt_telefono2" value="<?= $objFields['cliente_telefono2']?>" /></td>
                    </tr>
		    <tr>
			 <td>Observaciones</td>
			 <td colspan=3><textarea name="txt_observaciones" id="txt_observaciones"
                               cols="45" rows="3"><?= utf8_encode(trim($objFields['observaciones']))?></textarea></td>
		    </tr>
                    <tr> <td colspan="4">
                        <input type="button" name="bt_add" id="bt_add" value="Agregar Item" />
                            <input type="button" name="bt_excel" id="bt_excel" value="Excel" style="display:none;" />
                    </td></tr>
                </table>
       </div>
       <div id="tablewrapper"  style="margin-bottom:15px">
                      <div id="tableheader" style="display:none">
                                    <div class="search">
                                            <select id="columns" onchange="sorter.search('query')" style="font-size:12px"></select>
                                            <input type="text" id="query" onkeyup="sorter.search('query')" style="font-size:12px" />
                                    </div>
                                    <span class="details">
                                        <div>Registros <span id="startrecord"></span>-<span id="endrecord">
                                                            </span> de <span id="totalrecords"></span></div>
                                        <div><a href="javascript:sorter.reset()">Resetear</a></div>
                                    </span>
                         </div>
                  <div style=" width:auto;  height:auto; overflow-y:; "> 
                            <table cellpadding="0" cellspacing="0" border="0" id="table"  class="tinytable" 
                                style="font-size:12px; font-family: Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif " 
                                        width=100% >
                                        
                                <thead>
                                    <tr>
                                        <?php 
											  $lst_columns= array('Quantity','Product Code','Description',
												'Color','Size','Weight','Unit Price','Total Price','','');
											  foreach($lst_columns as $column)
											  { echo "<th class='nosort'><h3>".$column."</h3></th>"; }  
                                        ?>
                                    </tr>
                                    <tbody>
                                        <?php 
                                            foreach($lst_datos as $row)
											{
												echo "<tr>";
												foreach($lst_fields as $column)
												{ echo " <td>".$row[$column]."</td>";}
												  echo "<td style='text-align:center'>".
														"<img src='imagenes/ico_edit2.png' title='Modificar' style='border:0px; cursor:pointer;'></td>";
												  echo "<td style='text-align:center'>".
														"<img src='imagenes/ico_remove.png' title='Eliminar' style='border:0px; cursor:pointer;'></td>";
												echo "</tr>";											
											} 
                                        ?>
                                    </tbody>
                               </thead>
                            </table> 
                 </div>     
                 <div id="tablefooter" style="display:none">
                         <div id="tablenav">
                            <div>
                                <img src="librerias/tinytablev3.0/images/first.gif"
                                         width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
                                <img src="librerias/tinytablev3.0/images/previous.gif"
                                 width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
                                <img src="librerias/tinytablev3.0/images/next.gif" 
                                 width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
                                <img src="librerias/tinytablev3.0/images/last.gif" 
                                        width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
                            </div>
                            <div>
                                <select id="pagedropdown"></select>
                            </div>
                            <div>
                                <a href="javascript:sorter.showall()">Ver Todos</a>
                            </div>
                        </div>
                        <div id="tablelocation">
                            <div>
                                <select onchange="sorter.size(this.value)">
                                <option value="5">5</option>
                                    <option value="10"  >10</option>
                                    <option value="20" selected="selected">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span>Registros por pagina</span>
                            </div>
                            <div class="page">Pagina <span id="currentpage"></span> de <span id="totalpages"></span></div>
                        </div>
                  </div>    
        </div>
	<div class="div-buttons">
	   <input type="button" id="bt_procesar" name="bt_procesar" value="Guardar Datos" />
	   <input type="button" id="bt_salir" name="bt_salir"  value="Salir" />
	</div>
</form>
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
		//sortcolumn:0,
		sortdir:1,
	//		sum:[3,4],
	//		avg:[6,7,8,9],
		//columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	}); 
</script>

  <iframe id="if_carga" name="if_carga" style="display:; border:none; width:200px; height:0px;" >
  </iframe> 
     <div id="big">
        <div class="child"></div>
      </div>

     <div id="layer" style="display:none; ">
                <div id="div_layer" >
               <table id="tbdatos">
							<tr>
									<td id="label"></td>
									<td id="value" colspan=5></td>
							</tr>
							<tr>
								<td>Catalogo</td>
								<td colspan=5>
									<select id="cb_catalogos" name="cb_catalogos">
										<option value="">Seleccione..</option>
									<?
									   $cmd="select codigo ,ucase(catalogo_nombre) as catalogo_nombre 
										 from sis_carga_catalogo order by catalogo_nombre asc" ;
									   $result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
									   foreach($result as $row)
									   { echo "<option value='".$row['codigo']."'>".$row['catalogo_nombre']."</option>";}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td>Pagina</td>
								<td > <select id="cb_pagina" name="cb_pagina"></select></td>
								<td>Producto</td>
								<td colspan=3> <select id="cb_producto" name="cb_producto"></select></td>
							</tr>
							<tr>
								<td>Cantidad</td>
								<td><input type="text" id="txt_cantidad" name="txt_cantidad" /></td>
								<td>Color</td>
								<td><input type="text" id="txt_color" name="txt_color" />
								</td>
								<td>Talla</td>
								<td><input type="text" id="txt_talla" name="txt_talla" /></td>
							</tr>
							<tr>
								<td>Peso</td>
								<td><input type="text" id="txt_peso" name="txt_peso" /></td>
								<td>P.Unitario</td>
								<td><input type="text" id="txt_punitario" name="txt_punitario" />
								</td>
								 <td>Total</td>
								<td><input type="text" id="txt_total" name="txt_total" /></td>
							</tr>
							<tr>
								<td colspan="6">
										<input type="button" id="bt_agregar" name="bt_agregar"  value="Agregar">
										<input type="button" id="bt_cancelar" name="bt_cancelar" value="Cancelar">
								</td>
							</tr>
				</table>
                 </div>
      </div>

</body>
</html>
