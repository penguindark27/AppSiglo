<?php 
	session_start();
	include_once("connection.php");
	$obj=new connection();
	$obj->session_out();
        $user= $_SESSION['nom_user'];
	$p_idinvoice=(isset($_REQUEST['idinvoice'])?$_REQUEST['idinvoice']:"");	
	if(isset($_REQUEST['idinvoice']))
	{
		# Get Query Master
		$p_query="select s.*,c.nombre as nomcliente, s.cliente_direccion, s.cliente_ciudad,
					s.cliente_telefono, s.cliente_celular as cliente_telefono2,
					s.cliente_state,s.cliente_zipcode 
				from sis_invoice as s	
					left join sis_clientes as c on c.codigo=s.id_cliente and c.activo=1 
				where s.idinvoice='$p_idinvoice';";

		$result=mysqli_query($obj->cn,$p_query) or die("Error ".mysqli_error($obj->cn).", trying to execute query: $p_query");
		if($result){$objFields=mysqli_fetch_array($result);}
		
		# Get Query Details
		$p_query_det="select cantidad,idcatalogo,pagina,sufijo,idproducto,color,talla,peso,subtotal,total,tipo  
				     from sis_invoice_det where idinvoice='$p_idinvoice' order by item asc ;" ;
		unset($result);
     	$result=mysqli_query($obj->cn,$p_query_det) or die("Error ".mysqli_error($obj->cn).", trying to execute query: $p_query_det");
		if($result){$lst_fields=$obj->get_columns_from($result); $lst_datos=$obj->get_array_from($result);}
	}
	$p_fch_ingreso=(isset($_REQUEST['idinvoice'])?date("Y-m-d",strtotime($objFields['fch_ingreso'])):date("Y-m-d"));	

	## Get default message to show in rpt invoice	
	
	$cmd_message='select descripcion as message from sis_config_parameters where tipo=1 and activo=1';
	$p_message='';
	$rsmessage=mysqli_query($obj->cn,$cmd_message);
	if($rsmessage){
		if($row=mysqli_fetch_array($rsmessage)){$p_message=$row['message'];}
	}
	$p_message=(isset($objFields['message'])?utf8_encode(trim($objFields['message'])):$p_message);
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

		.div-buttons input[type="button"]{ padding:4px; font:bold 11px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif ;
						cursor:pointer;}
						
		input[type="text"]:focus,select:focus, textarea:focus{  background-color:#EFF5FF;  border:solid 1px #73A6FF; }						
		input[type="text"],select, textarea{ background-color: #F8F8F8; outline: none; border: solid 1px #B5B5B5; padding:3px} 
		
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
			#div_search table#tb tr td input[type="button"]#bt_add,
			#div_search table#tb tr td input[type="button"]#bt_add_obs,
			#div_search table#tb tr td input[type="button"]#bt_excel{  width:auto;float:left; margin-right:6px}
			
			#div_search table#tb tr td iframe#layer_rpt{ height:27px; padding:3px; width:300px; float:left; overflow:none;}
			#div_search table#tb tr td input[type="text"],
			#div_search table#tb tr td select,
			#div_search table#tb tr td textarea{  text-transform:uppercase; border:solid 1px #C6D5E1; padding:2px;
										font: normal 12px Gotham, 'Lucida Grande', Tahoma, Verdana, sans-serif;  outline: none;} 
										
			#div_search table#tb tr td input[name="txt_direccion"]{ width:500px}
			#div_search table#tb tr td input[name="txt_ciudad"]{ width:200px}
			#div_search table#tb tr td select{ width:300px;text-transform:uppercase;}

		#layer{ position: absolute; top:20%; left:0; right:0; height:auto; width:auto;
               background-color:#FFF; padding:10px; margin:0px 150px 0px 150px ; border:solid 1px #A0B9CD}
		#layer div#div_layer{ width:auto; height:auto;
									padding:20px 0px 10px 10px;  border:1px solid #A0B9CD; background-color:#FFF }
									
		.field-style{width:80px; padding:2px; font-size:11px; text-transform:uppercase;}
		.select-style{padding:2.5px; font-size:11px; width:auto; max-width:250px}	
    </style>
    <script type="text/javascript">
		function BeginEvents(){ 
                        Event.observe('bt_excel','click',bt_excel_click);	  		
			Event.observe('bt_add','click',bt_agregar_click);
			Event.observe('bt_add_obs','click',bt_agregar_click);
			Event.observe('cb_cliente','change',cb_cliente_changed);
			Event.observe('bt_procesar','click',bt_procesar_click);
			Event.observe('bt_salir','click',bt_salir_click);	
			setupCalendars();  		
	   	}
		
	  function txt_peticion_atis_keypress(e)
		  {
			if(!e) var e=window.event;
			var letter=!e.which?e.keyCode:e.which;
			// not allow key 32:space
			if(letter==32) 
			{e.returnValue=false; if(e.preventDefault){ e.preventDefault();}}
			var p_value=String.fromCharCode(letter);
			var allowed='1234567890.';
			var estado=false;
			// allow numbers and keys : 8:backspace, 9: horizontal-tab
			if(allowed.indexOf(p_value)>-1 || letter==8 || letter==9) estado=true;
			if(estado==false) { e.returnValue=false; if(e.preventDefault){ e.preventDefault();}}
		  }		  
	function get_list_details()
	{
	  var list=[];
	  var tb=document.getElementById('table').rows;
	  for(nitem=0;nitem<=tb.length-1;nitem++)
	  { 
	    if(nitem==0){continue;}
		var nposition=tb[nitem].getElementsByTagName('input')[0].value;
		var status= $('row_type'+nposition).value=='automatic'?true:false;
		var item={
					cantidad: (status==false?'':$('txt_cantidad'+nposition).value),
					idcatalogo:(status==false?'':$('cb_catalogo'+nposition).value),
					idproducto:$('cb_producto'+nposition).value,
					idpagina:(status==false?'':$('cb_pagina'+nposition).value),
					sufijo:(status==false?'':$('txt_sufijo'+nposition).value),				
					color:(status==false?'':$('txt_color'+nposition).value),
					tamanio:(status==false?'':$('txt_longitud'+nposition).value),
					peso:(status==false?'':$('txt_peso'+nposition).value),
					punitario:(status==false?'':$('txt_punitario'+nposition).value),
					total:(status==false?'':$('txt_total'+nposition).value),
					tipo:(status==false?'2':'1')
				 } 
		list[nitem-1]=item; 
	  }
      return list;
	}	
	function bt_procesar_click()
	{
		if(confirm('Desea proceder a guardar los datos?'))
		{
			var details=get_list_details();
			//alert(details);
        	
        	var data="lst_details="+JSON.stringify(details)+"&"+Form.serialize('frm_edit');
	        new Ajax.Request("catalogo_invoice_update.php",{method:'post',parameters:data,
                                              onSuccess: function(resultado){var response = resultado.responseText;
									var lst=JSON.parse(response);
								if(lst.id==0)
									{ alert(lst.descripcion);}
									else
									{ alert('Datos fueron actualizados satisfactoriamente');
									  parent.middleside.location='catalogo_invoice_det.php?idinvoice='+ lst.descripcion; }
                                                        }});
             
		}
	 }
	function bt_salir_click(){ if(confirm('Desea salir del registro de invoice?')){parent.middleside.location='catalogo_invoice.php';}}
	
	function get_select_text(obj){ return obj.options[obj.selectedIndex].text;}
	

	function remove_item(fila)
	{
	  var result=confirm('Esta seguro de eliminar el registro?');
	  if(result)
	  {
		var obj=$$("input[id='nposition'][Value="+fila+"]").first();
		obj.up().remove();
	  }
	}
	function bt_agregar_click()
	{
		var p_operacion= this.id=='bt_add'?'add-item':'add-item-obs';
		var nitem=$('table').rows.length-1;
		var data="tipo_operacion="+p_operacion+"&nitem="+nitem;
		new Ajax.Request("procesos.php",{method:'post',parameters:data,
									  onSuccess: function(resultado){var response = resultado.responseText;
													var row="";
													var tr=document.createElement("tr");
													tr.innerHTML=response;
													$('table').appendChild(tr);
												}});

	}

	 function cb_pagina_changed(obj)
                {
                        var p_value = $(obj.id).value;
			var nposition= obj.id.replace("cb_pagina","");
                        if(p_value!='')
                        {
                                        var operacion='get-catalogos-products';
                                        var data="tipo_operacion="+operacion+"&idcatalogo="+$('cb_catalogo'+nposition).value+"&idpagina="+$(obj.id).value;
                                        new Ajax.Request("procesos.php",{method:'post',parameters:data,
                                                                onSuccess: function(resultado){var response = resultado.responseText;
                                                                                                  $('cb_producto'+nposition).update(response);
                                                                                          }});
                        }
                        else
                        { $('cb_producto'+nposition).update("<option value=''>Seleccione...</option>");}
                }
	function cb_producto_changed(obj)
                {
                        var p_value = $(obj.id).value;
			var nposition=obj.id.replace('cb_producto','');
                        //var nposition=obj.id.substring(obj.id.length-1,obj.id.length);
                        if(p_value!='')
                        {
                                        var operacion='get-product-value';
                                        var data="tipo_operacion="+operacion+"&idcatalogo="+$('cb_catalogo'+nposition).value+"&idpagina="+
						$('cb_pagina'+nposition).value+"&idproducto="+$(obj.id).value;
					//alert(data);
                                        new Ajax.Request("procesos.php",{method:'post',parameters:data,
                                                                onSuccess: function(resultado){var response = resultado.responseText;
                                                                                                  $('txt_punitario'+nposition).setValue(response);
												$('txt_total'+nposition).setValue(response);
                                                                                          }});
                        }
                        else
                        { 
			   $('txt_total'+nposition).setValue("0.00");
                           $('txt_punitario'+nposition).setValue("0.00");
			}
                }


	 function cb_catalogos_changed(obj)
                {
                        var p_value = $(obj.id).value;
			var nposition= obj.id.replace('cb_catalogo','');
			//var nposition=obj.id.substring(obj.id.length-1,obj.id.length);
                        if(p_value!='')
                        {
                                        var operacion='get-catalogos-pages';
                                        var data="tipo_operacion="+operacion+"&idcatalogo="+$(obj.id).value;
                                        new Ajax.Request("procesos.php",{method:'post',parameters:data,
                                                                onSuccess: function(resultado){var response = resultado.responseText;
                                                                                                  $('cb_pagina'+nposition).update(response);
                                                                                          }});
                        }
                        else
                        { $('cb_pagina'+nposition).update("<option value=''>Seleccione...</option>");}
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
												  $('txt_zipcode').value=result[0].zip_code;
												  $('txt_state').value=result[0].state;
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
	   
	   function bt_excel_click()
	   {
		 /* window.open('<?php echo $file_name; ?>'); */

		var frame=document.getElementById('if_carga'); 
		frame.target='_top';
		
		document.getElementById('frm_edit').target='if_carga';
		//document.getElementById('frm_edit').action = 'catalogo_invoice_det_csv.php';
		document.getElementById('frm_edit').action = 'catalogo_invoice_det_csv_test.php';
		document.getElementById('frm_edit').method = 'POST';
		document.getElementById('frm_edit').submit();	 
	  }

		
       Event.observe(window, 'load', BeginEvents);
    </script>
</head>

<body>

<form id="frm_edit" name="frm_edit">
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
					  $query="select codigo,concat(ifnull(id,''),' - ', nombre) as nombre from sis_clientes where activo=1 order by nombre";
					  $result=$obj->get_array_from(mysqli_query($obj->cn,$query));
					  foreach($result as $row)
					  { echo "<option value='".$row['codigo']."' ".
						($objFields['id_cliente']==$row['codigo']?'selected':'').">".utf8_encode($row['nombre'])."</option>";}
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
                        <td>State</td>
                        <td><input type="text" id="txt_state" name="txt_state" value="<?= $objFields['cliente_state']?>" /></td>
                        <td>Zip Code</td>
                        <td><input type="text" id="txt_zipcode" name="txt_zipcode" value="<?= $objFields['cliente_zipcode']?>" /></td>
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
		    <tr>
                        <td>Mensaje</td>
                        <td>
			       <textarea name="txt_message" id="txt_message" cols="70" rows="2"><?= $p_message?></textarea>
			</td>	
		    </tr>
                    <tr> <td colspan="4">
                          <input type="button" name="bt_add" id="bt_add" value="Agregar Item" />
                          <input type="button" name="bt_add_obs" id="bt_add_obs" value="Agregar Observacion" />
 	                  <input type="button" name="bt_excel" id="bt_excel" value="Excel"
				<?= (isset($_REQUEST['idinvoice'])?'':'style="display:none;"')?>  />
			  <input type="button" name="bt_test" id="bt_test" value="Excel Testing" style='display:none;' />
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
											$lst_columns= array('Catalogo','Pagina','Sufijo','Description','Quantity','Color','Size',
																'Weight','Unit Price','Total Price','');
											foreach($lst_columns as $column)
											{ echo "<th class='nosort'><h3>".$column."</h3></th>"; }  
                                        ?>
                                    </tr>
                                    <tbody>
                                        <?php 
											$nitem=0;
                                            foreach($lst_datos as $row)
											{ 
												$nitem+=1;
												$style='field-style';
												$tipo= $row['tipo']==2?false:true;
												
												?> 
												<tr>
													<input type="hidden" id="nposition" value="<?=$nitem;?>"/>
													<input type="hidden" id="row_type<?=$nitem;?>" value="<?= $tipo==false?'edit':'automatic';?>"/>
												<?
												
											  if($tipo==false){ ?>
											  	    <td colspan=10 style='text-align:center;'>
											  	  			<input type='text' id='cb_producto<?=$nitem;?>' value='<?=$row['idproducto']; ?>' 
											  	  						style='width:430px' <?="class='$style'" ?>  />
											  	    </td>
									  				<td style='text-align:center;'>
									  							<img src="imagenes/ico_remove.png" title="Eliminar" onclick='remove_item(<?=$nitem;?>)' 
																		style="border:0px; cursor:pointer;">
													</td>
											 	<?  }else{	?> 
												<td>
													<select id='cb_catalogo<?=$nitem;?>' class='select-style' onchange='cb_catalogos_changed(this);' style='width:250px;'>
														<option value="">Seleccione...</option>
													<?
														$cmd="select codigo,ucase(catalogo_nombre) as catalogo_nombre
																	from sis_carga_catalogo order by catalogo_nombre asc ;";
														$result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
														foreach($result as $item)
														{ echo "<option value='".$item['codigo']."' ".
																($item['codigo']==$row['idcatalogo']?'selected':'').">".$item['catalogo_nombre']."</option>";}	
													?>
													</select>
												</td>
												<td>
													<select id='cb_pagina<?=$nitem;?>' class='select-style' onchange='cb_pagina_changed(this);' style='width:50px;'>
														<option value="">Seleccione...</option>
														<?
															$p_idcatalogo=$row['idcatalogo'];
															$cmd="select pagina from sis_catalogos 
																  where idcarga='$p_idcatalogo' group by pagina ;";
															$result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
															foreach($result as $item)
															{ echo "<option value='".$item['pagina']."' ".
																($item['pagina']==$row['pagina']?'selected':'').">".$item['pagina']."</option>";}	
														?>
													</select>
												</td>
										<td style='text-align:center;'><input type='text' id='txt_sufijo<?=$nitem;?>'
	                                                		        <?="class='$style'" ?> value="<?=$row['sufijo']; ?>"   />
												</td>

												<td>
													<select id='cb_producto<?=$nitem;?>' class='select-style' 
														onchange='cb_producto_changed(this);'>
														 <option value="">Seleccione...</option>
														<?
															$p_idpagina=$row['pagina'];
															$cmd="select codigo as idproducto,descripcion as nomproducto 
																  from sis_catalogos 
																  where pagina='$p_idpagina' and idcarga='$p_idcatalogo' ";
															$result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
															foreach($result as $item)
															{ echo "<option value='".$item['idproducto']."' ".
																($item['idproducto']==$row['idproducto']?'selected':'').">".utf8_encode($item['nomproducto'])."</option>";}	
														?>
													</select>
												</td>
												<td style='text-align:center;'><input type='text' id='txt_cantidad<?=$nitem;?>' 
													<?="class='$style'" ?> value="<?=$row['cantidad']; ?>" 
													 onkeypress="txt_peticion_atis_keypress()" style='width:50px;'  /></td>
												<td style='text-align:center;'><input type='text' id='txt_color<?=$nitem;?>' 
													<?="class='$style'" ?> value="<?=$row['color']; ?>" style='width:70px;'/></td>
												<td style='text-align:center;'><input type='text' id='txt_longitud<?=$nitem;?>' 
														<?="class='$style'" ?> value="<?=$row['talla']; ?>" style='width:60px;'/></td>
												<td style='text-align:center;'><input type='text' id='txt_peso<?=$nitem;?>' 
													<?="class='$style'" ?> value="<?=$row['peso']; ?>" style='width:50px;'  /></td>
												<td style='text-align:center;'><input type='text' id='txt_punitario<?=$nitem;?>' 
													<?="class='$style'" ?> value="<?=$row['subtotal']; ?>" 
													onkeypress="txt_peticion_atis_keypress()" readonly /></td>
												<td style='text-align:center;'><input type='text' id='txt_total<?=$nitem;?>' 
													<?="class='$style'" ?> value="<?=$row['total']; ?>" 
													onkeypress="txt_peticion_atis_keypress()" readonly /></td>
												<td style='text-align:center;'><img src="imagenes/ico_remove.png" 
														title="Eliminar" onclick='remove_item(<?=$nitem;?>)' style="border:0px; cursor:pointer;"></td>
											<? 
											  }
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
		size:50,
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
                 </div>
      </div>

</body>
</html>
