<?php
	# In PHP 5.2 or higher we don't need to bring this in
	# it's to use json_encode and json_decode

	require "librerias/jsonwrapper/jsonwrapper.php";
	 
	session_start();
	include_once("connection.php");
	$obj=new connection();
	$obj->session_out();
        $user= $_SESSION['nom_user'];
	$p_fch_desde=(isset($_REQUEST['txt_fch_desde'])?$_REQUEST['txt_fch_desde']:date("Y-m-d"));
	$p_fch_hasta=(isset($_REQUEST['txt_fch_hasta'])?$_REQUEST['txt_fch_hasta']:date("Y-m-d"));
	$p_idcliente=(isset($_REQUEST['cb_cliente'])?$_REQUEST['cb_cliente']:"");
	$p_idinvoice=(isset($_REQUEST['txt_invoice'])?$_REQUEST['txt_invoice']:"");	
	
	if(isset($_REQUEST['txt_fch_desde']))
	{
		$p_query="call sp_sis_get_invoices('$p_fch_desde','$p_fch_hasta','$p_idcliente','$p_idinvoice','$user') ;";
		#echo $p_query;
		$result=mysqli_query($obj->cn,$p_query) or die("Error ".mysqli_error($obj).", trying to execute query: $p_query");
		$lst_fields=$obj->get_columns_from($result);
		$lst_datos= $obj->get_array_from($result);
		$obj->get_free_results($obj->cn);
	}	
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
			#div_header{ width:auto; height:32px; background-color:#FFF; 
							padding-top:10px; margin-bottom:6px; border:1px solid #c6d5e1;
							padding-left:4px}
			#div_header label{ font: 14px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
							font-weight:bold;  text-transform:uppercase }
							
			#div_search{ width:auto; height:auto; background-color:#FFF; 
						padding:10px 0px 10px 4px; margin:3px 0px 6px 0px; border:1px solid #c6d5e1;}
						
			input[type="text"]:focus,select:focus, textarea:focus{  background-color:#EFF5FF; border:solid 1px #73A6FF; }
			input[type="text"],select, textarea{ border:solid 1px #C6D5E1; padding:3px;}
			#div_search table#tb{ width:auto}
			#div_search table#tb tr td{ padding:3px 3px 3px 5px; font:13px bold Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif}
			#div_search table#tb tr td input[type="button"]:hover{ cursor: pointer; }
			#div_search table#tb tr td input[type="button"]{ padding:2px; font: 13px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
							font-weight:bold ; }
			#div_search table#tb tr td input[type="button"]#bt_procesar, 
			#div_search table#tb tr td input[type="button"]#bt_nuevo,
			#div_search table#tb tr td input[type="button"]#bt_excel{  width:120px;float:left; margin-right:6px}
			#div_search table#tb tr td iframe#layer_rpt{ height:27px; padding:3px; width:300px; float:left; overflow:none;}
			#div_search table#tb tr td input[type="text"]{width:100px; }
			#div_search table#tb tr td select{ width:300px;text-transform:uppercase; padding:1px}
			
			
			#div_report{ width:auto; height:auto; background-color:#FFF; 
						padding:10px 0px 10px 4px; margin-top:3px; border:1px solid #c6d5e1; }
				
			
    </style>
    <script type="text/javascript">
		function BeginEvents(){ 
	  		
			Event.observe('bt_excel','click',ButtonExcel);
	  		Event.observe('bt_procesar','click',ButtonProcess);
			Event.observe('bt_nuevo','click',ButtonNew);
			setupCalendars();  		
	   	}
	 function edit_invoice(id){ parent.middleside.location="catalogo_invoice_det.php?idinvoice="+id;}
	 function get_new_id(){
	 		var p_operacion='get-new-id';
	 		var data="tipo_operacion="+p_operacion;
		 	new Ajax.Request("procesos.php",{method:'post',parameters:data,
										  onSuccess: function(resultado){var response = resultado.responseText;
										  					if(response.length>0){ 
										  							var p_id=response ;
																	process_new_id(p_id);			  						
										  					}
													}});
	 }
	 function process_new_id(p_id){
			if(confirm('Desea registrar un nuevo invoice?')){
			var result= confirm('Se procedera a crear el Invoice Nro. '+p_id+', desea continuar?');
			if(result){
					var data="tipo_operacion=process-new-id&id="+p_id;
				 	new Ajax.Request("procesos.php",{method:'post',parameters:data,
												  onSuccess: function(resultado){
												  				var result= JSON.parse(resultado.responseText);
												  				if(result.idestado=='0'){
												  					alert(result.descripcion); return false;
												  				}else{
												  					alert(result.descripcion); 
												  				}
												  				parent.middleside.location="catalogo_invoice_det.php?idinvoice="+p_id;
															}});	
				}
			}
	 }
	 function ButtonNew(p_id){ get_new_id() ;}
	 function setupCalendars() {	
					// Embedded Calendar
					// Popup Calendar
					   Calendar.setup(
								{
								   dateField: 'txt_fch_desde',
								   triggerElement: 'bt_fch_desde'
								}
							)
							   Calendar.setup(
									  {
										 dateField: 'txt_fch_hasta',
										 triggerElement: 'bt_fch_hasta'
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
		var estado=confirm('Desea proceder con el exportado de Datos?');
		if(estado){
			document.getElementById('frm_edit').action = 'catalogo_invoice_csv.php';
			document.getElementById('frm_edit').method = 'POST';
			document.getElementById('frm_edit').submit();	 
		}
	  }
		
       Event.observe(window, 'load', BeginEvents);
    </script>
</head>

<body>

        <div id="div_header" >
       			 <label>Catalogo de Invoices</label>
        </div>
        <div id="div_search" >
            <form id="frm_edit" name="frm_edit" enctype="multipart/form-data">
                <table cellpadding="0" cellspacing="1" width="auto" id="tb">
                     <tr>
                        <td>Fch.Desde</td>
                        <td>
				<input type="text" id="txt_fch_desde" name="txt_fch_desde" value="<?= $p_fch_desde ?>" />
				<input type="button" id="bt_fch_desde" name="bt_fch_desde" value="..." />
			</td>
			<td>Fch.Hasta</td>
                        <td>
                                <input type="text" id="txt_fch_hasta" name="txt_fch_hasta" value="<?= $p_fch_hasta ?>" />
                                <input type="button" id="bt_fch_hasta" name="bt_fch_hasta" value="..." />
                        </td>
			<td>
                                 <input type="button" id="bt_nuevo" name="bt_nuevo" value="Nuevo Invoice" />
                         </td>
                    </tr>
		    <tr>
                        <td>Cliente</td>
                        <td>
				<select id="cb_cliente" name="cb_cliente" >
					<option value="">Seleccione...</option>
					<?
					  $query="select codigo, nombre from sis_clientes where activo=1 order by nombre";
					  $result=$obj->get_array_from(mysqli_query($obj->cn,$query));
					  foreach($result as $row)
					  { echo "<option value='".$row['codigo']."' ".
							($p_idcliente==$row['codigo']?'selected':'').">".$row['nombre']."</option>";}
					?>
				</select>
			</td>
		    </tr>
		    <tr>
                        <td>Nro. Invoice</td>
			<td><input type="text" id="txt_invoice" name="txt_invoice" /></td>
                        <td colspan="2"></td>
                    </tr> 
                    <tr> <td colspan="4">
                        <input type="button" name="bt_procesar" id="bt_procesar" value="Buscar">
                            <input type="button" name="bt_excel" id="bt_excel" value="Exportar"  
                            <?php /* if(!strlen(trim($p_fch_desde))>0){echo "style=' display:none;'";}else{echo "";} */ ?>>                       
                    </td></tr>
                </table>
            </form>
       </div>
       <div id="tablewrapper"  <?= (isset($_REQUEST['txt_fch_desde'])?'style="margin-bottom:30px;"':'style="display:none;"');?>>
                      <div id="tableheader" style="display:">
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
                                          foreach($lst_fields as $column)
                                          { echo "<th class='nosort'><h3>".$column."</h3></th>"; }  
                                        ?>
                                    </tr>
                                    <tbody>
                                        <?php 
                                            foreach($lst_datos as $row)
                                            {
                                                echo "<tr ondblclick=\"edit_invoice('".$row['ID Invoice']."')\">";
												foreach($lst_fields as $column)
												{ echo " <td ".($column==''?'style="text-align:center;"':'').">".$row[$column]."</td>";}
												echo "</tr>";											
                                            } 
											 
                                        ?>
                                    </tbody>
                               </thead>
                            </table> 
                 </div>     
                 <div id="tablefooter" style="">
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
		sortcolumn:0,
		sortdir:1,
	//		sum:[3,4],
	//		avg:[6,7,8,9],
		//columns:[{index:7, format:'%', decimals:1},{index:8, format:'$', decimals:0}],
		init:true
	}); 
</script>

 <?php 
	/* $ncolumn=0;
	$p_maxcolumn='';
	# drawing headers
	foreach($lst_fields as $column)
	{
		$from= PHPExcel_Cell::stringFromColumnIndex($ncolumn);
		$objPHPExcel->getActiveSheet()->getStyle($from."1")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle($from."1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($ncolumn,1,strtoupper($column)); 
		if(substr($column,0,2)=='id' || $column=='nomprograma')
		{ $objPHPExcel->getActiveSheet()->getStyle($from."1")->
			applyFromArray(array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color'=>array('rgb'=>'FF0')))); }
		else
		{ $objPHPExcel->getActiveSheet()->getStyle($from."1")->
			applyFromArray(array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color'=>array('rgb'=>'D7FFFF'))));}
			
		$ncolumn+=1;
	} */

	/*$p_maxcolumn=PHPExcel_Cell::stringFromColumnIndex($ncolumn-1);
	$objPHPExcel->getActiveSheet()->getStyle("A1:".$p_maxcolumn."1")->
	applyFromArray(array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color'=>array('rgb'=>'FFFFB3')))); */

/*
	# drawing details
	$nrow=2;
	foreach($lst_datos as $row)
	{
		$ncolumn=0;
		foreach($lst_fields as $column)
		{
			$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($ncolumn,$nrow,$row[$column]); 
			$ncolumn+=1;
		}
		$nrow+=1;
	}	
*/		
	#apply background-color to last row 
	/*
	$range_total="A".($nrow-1).":".$p_maxcolumn.($nrow-1);
	$objPHPExcel->getActiveSheet()->getStyle($range_total)->
	applyFromArray(array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color'=>array('rgb'=>'FFFFB3')))); 
	*/
	
	#apply borders to all cells of sheet
	/*
	$objPHPExcel->getActiveSheet()->getStyle("A1:".$p_maxcolumn.($nrow-1))->
	getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle("A1:".$p_maxcolumn.($nrow-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	*/

/*	
	#apply AutoSizeMode to all cells of each sheet
	PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);	
	foreach(range('A',$p_maxcolumn) as $column_id)
	{  $objPHPExcel->getActiveSheet()->getColumnDimension($column_id)->setAutoSize(true);}
		
	if(!file_exists($directory_download)) { mkdir($directory_download,0777);}
	#$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);  
	$objWriter=new PHPExcel_Writer_Excel5($objPHPExcel);  
	$objWriter->save($file_name);
*/
  ?>
  
  <iframe id="if_carga" name="if_carga" style="display:; border:none; width:200px; height:0px;" >
  </iframe> 
</body>
</html>
