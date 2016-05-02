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
	$obj->session_out();	
	$user=$_SESSION['nom_user'];
	$p_query="call sp_sis_get_catalogos();";
	$result=mysqli_query($obj->cn,$p_query) or die("Error ".mysqli_error($obj->cn).", trying to execute query: $p_query");
	$lst_fields=$obj->get_columns_from($result);
	$lst_datos= $obj->get_array_from($result);
	$obj->get_free_results($obj->cn);
	
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Actualizacion Masiva de Sub Productos</title>
  <link rel="stylesheet" href="librerias/tinytablev3.0/style.css" />
 <script type="text/javascript"  src="librerias/scriptaculous/prototype.js"></script>
    <style type="text/css">
		#div_header{ width:auto; height:32px; background-color:#FFF; 
							padding-top:10px; margin-bottom:6px; border:1px solid #c6d5e1;
							padding-left:4px}
			#div_header label{ font: 14px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
							font-weight:bold;  text-transform:uppercase }
							
			#div_search{ width:auto; height:auto; background-color:#FFF; 
						padding:10px 0px 10px 4px; margin:3px 0px 6px 0px; border:1px solid #c6d5e1;}
						
			#div_search table#tb{ width:auto}
			#div_search table#tb tr td{ padding:3px 3px 3px 5px; font:13px bold Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif}
			#div_search table#tb tr td input[type="button"]:hover{ cursor: pointer; }
			#div_search table#tb tr td input[type="button"]{ padding:4px; font: 13px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
							font-weight:bold }
			#div_search table#tb tr td input[type="button"]#bt_procesar, 
			#div_search table#tb tr td input[type="button"]#bt_excel{  width:100px;float:left; margin-right:6px}
			#div_search table#tb tr td iframe#layer_rpt{ height:27px; padding:3px; width:300px; float:left; overflow:none;}
			#div_search table#tb tr td input[type="text"],#div_search table#tb tr td select{ width:300px;text-transform:uppercase; padding:3px}
			
			
			#div_report{ width:auto; height:auto; background-color:#FFF; 
						padding:10px 0px 10px 4px; margin-top:3px; border:1px solid #c6d5e1; }
						
			input[type="text"]:focus,select:focus, textarea:focus{  background-color:#EFF5FF; border:solid 1px #73A6FF; }
	                input[type="text"],select, textarea{ border:solid 1px #C6D5E1; padding:3px;}	
    </style>
    <script type="text/javascript">
		function BeginEvents(){ 
	  	
			Event.observe('bt_excel','click',ButtonExcel);
	  		Event.observe('bt_procesar','click',ButtonProcess);
	  		
	   	}
	  
	 
	  function remove_catalogo(ncatalogo)
	  {
	    var result=confirm('Esta seguro de proceder a eliminar catalogo?');
	    if(result){ window.location='catalogo_carga_remove.php?idcarga='+ncatalogo;}
	  }
	  function anular_catalogo(ncatalogo)
	  {
	     var result=confirm('Esta seguro de proceder a anular catalogo?');
            if(result){ window.location='catalogo_carga_anular.php?idcarga='+ncatalogo;}
	  }

	  function ButtonProcess()
	  {
		var file=document.getElementById('file_csv').value;
		var extension=file.substring(file.length-3, file.length);
		if(file.length>0)
		{
			if(extension=='csv')
			{
				var est=confirm('Se procedera a carga registros, desea continuar?');
				if(est)
				{
					document.getElementById('frm_edit').action='catalogo_carga_load.php';
					document.getElementById('frm_edit').method='post';
					document.getElementById('frm_edit').submit();
				}
			}
			else
			{alert("ERROR, DEBE SELECCIONAR ARCHIVO CON FORMATO '.csv'.");}
		}
		else
		{
			alert('ERROR, SELECCIONE ARCHIVO A CARGAR.');			
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

        <div id="div_header" >
       			 <label>Carga de Catalogos</label>
        </div>
        <div id="div_search" >
            <form id="frm_edit" name="frm_edit" enctype="multipart/form-data">
                <table cellpadding="0" cellspacing="1" width="auto" id="tb">
                     <tr>
                        <td>Proveedor</td>
                        <td>
				<select id="cb_proveedor" name="cb_proveedor">
					<option value="">Seleccione... </option>
				 <?
				   $cmd="select codigo, nombre from sis_proveedores where activo=1";
				   $result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
				   foreach($result as $row)
				   { echo "<option value='".$row['codigo']."'>".$row['nombre']."</option>";}	
				 ?>
				</select>
			</td>
                    </tr>
		    <tr>
                        <td>Nombre Catalogo</td>
                        <td><input type="text" id="txt_catalogo" name="txt_catalogo" /></td>
		    </tr>
		    <tr>
                        <td>Archivo de Carga</td>
                        <td><input type="file" id="file_csv" name="file_csv"></td>
                        <td colspan="2"></td>
                    </tr> 
                    <tr> <td colspan="4">
                        <input type="button" name="bt_procesar" id="bt_procesar" value="Procesar">
                            <input type="button" name="bt_excel" id="bt_excel" value="Excel"  style="display:none;"/>
                    </td></tr>
                </table>
            </form>
       </div>
       <div id="tablewrapper"  style="margin-bottom:30px">
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
                                          { 
					    $p_column=(strpos($column,'ico_')!==false?'':$column);
					    echo "<th class='nosort'><h3>".$p_column."</h3></th>"; 
					  } 
                                        ?>
                                    </tr>
                                    <tbody>
                                        <?php 
                                            foreach($lst_datos as $row)
                                            {
                                                echo "<tr>";
							foreach($lst_fields as $column)
							{  echo " <td ".($column==''?'style="text-align:center;"':'').">".$row[$column]."</td>";}
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
  
  <iframe id="if_proceso" name="if_proceso" style="display:; border:none; width:200px; height:0px;" >
  </iframe> 
</body>
</html>
