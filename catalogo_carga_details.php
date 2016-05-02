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
	$p_idcarga=$_REQUEST['idcarga'];
	
	$p_query="call sp_sis_get_carga_catalogo($p_idcarga) ;";
	$result=mysqli_query($obj->cn,$p_query) or die("Error ".mysqli_error($obj).", trying to execute query: $p_query");
	$lst_fields=$obj->get_columns_from($result);
	$lst_datos= $obj->get_array_from($result);
	$obj->get_free_results($obj->cn);
	
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Detalle de Carga Catalogo <?= $p_catalogo; ?></title>
  <link rel="stylesheet" href="librerias/tinytablev3.0/style.css" />
 <script type="text/javascript"  src="librerias/prototype_1.7/prototype.js"></script>
  <script type="text/javascript"  src="librerias/prototype_1.7/src/scriptaculous.js?load=effects"></script>
    <style type="text/css">
			 .big{  height:100%; width:100%; position:absolute; top:0px; left:0px;
        		                background-color:#999; opacity:0.4; filter: alpha(opacity=100);}
	                .hidden{ height:100%; width:100%; position:absolute; top:0px; left:0px; }

			#div_header{ width:auto; height:32px; background-color:#FFF; 
							padding-top:10px; margin-bottom:6px; border:1px solid #c6d5e1;
							padding-left:4px}
			#div_header label{ font: 14px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
							font-weight:bold;  text-transform:uppercase }
										
			#div_report{ width:auto; height:auto; background-color:#FFF; 
						padding:10px 0px 10px 4px; margin-top:3px; border:1px solid #c6d5e1; }

			#layer{ position: absolute; top:20%; left:0; right:0; height:auto; width:auto; 
                                background-color:#FFF; 
                                padding:6px; background-color:#FFF; margin:0px 230px 0px 230px ; border:solid 1px #}
	                #layer div#div_layer{ width:auto; height:auto;
                                                padding:20px 0px 10px 10px;  border:1px solid #c6d5e1; background-color:#F8F8F9 }
        	        #layer div#div_layer table#tbdatos{ width:auto; }
	                #layer div#div_layer table#tbdatos tr td { padding:3px 0px 3px 15px; font-size:13px;}
        	        #layer div#div_layer table#tbdatos tr td input[type="text"],
	                #layer div#div_layer table#tbdatos tr td select { padding:4px; font-size:12px; text-transform:uppercase;
	                                                                 font-family: 'Lucida Grande', Tahoma, Verdana, sans-serif;}
        	        #layer div#div_layer table#tbdatos tr td select{ width:215px}
	                #layer div#div_layer table#tbdatos tr td input[type="button"]{ width:100px; padding:4px;cursor:pointer;
                                        font:bold 12px Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif}
			#layer div#div_layer table#tbdatos tr td input[name="txt_descripcion"]{ width:300px;}
	
    </style>
    <script type="text/javascript">
		function BeginEvents(){ 
		  	 Event.observe('bt_cancelar','click',BtCancel_Click);
			Event.observe('bt_guardar','click',BtGuardar_Click);
			Event.observe('txt_precio','keypress',txt_peticion_atis_keypress);
	   	}
		
         Event.observe(window, 'load', BeginEvents);

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
	 function edit_product(nfila)
        {
                var fondo= document.getElementById('big');
                fondo.className="big";
                Effect.toggle('big','appear');

                var operacion='get_product_values';
                var data="tipo_operacion="+operacion+"&idproducto="+ nfila;

                new Ajax.Request("procesos.php",{method:'post',parameters:data,
                            onSuccess: function(resultado){var response = resultado.responseText;
                                                           var result=JSON.parse(response);
                                                           if(result[0].codigo!=null)
                                                           {
                                                               $('txt_idproducto').value=result[0].codigo;
                                                               $('txt_pagina').value=result[0].pagina;
                	                                       $('txt_descripcion').value=result[0].descripcion;
                                                               $('txt_precio').value=result[0].precio;
							   }
                                                           Effect.toggle('layer','appear');
                                                  }});

        }
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
               if(confirm('Desea proceder a guardar los datos?'))
               {
                       var operacion='save_product';
                       var data="tipo_operacion="+operacion+"&"+Form.serialize('frm_edit');
                       new Ajax.Request("procesos.php",{method:'post',parameters:data,
                                         onSuccess: function(resultado){var response = resultado.responseText;
                       		                 var obj=JSON.parse(response);
                                	        if(obj.id==1)
	                                	{ alert(obj.descripcion);
	        				  parent.middleside.location='catalogo_carga_details.php?idcarga='+$('txt_idcarga').value;}
	                                         else
        	                                 { alert(obj.descripcion); }
                	                  }});
               }
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

    </script>
</head>

<body>
	<input type="hidden" id="txt_idcarga" name="txt_idcarga" value="<?=$p_idcarga ?>" />
	<div id="big" class="big" style="display:none;">
                <div class="child"></div>
        </div>

        <div id="div_header" >
       			 <label>Detalle de Carga Catalogo <?= $p_catalogo; ?></label>
        </div>
       <div id="tablewrapper"  style="margin-bottom:30px">
                      <div id="tableheader" style="display:">
                                    <div class="search">
                                            <select id="columns" onchange="sorter.search('query')" style="font-size:12px"></select>
                                            <input type="text" id="query" onkeyup="sorter.search('query')" style="font-size:12px" />
				          <input type="button" id="bt_return" value="Regresar" 
					style="cursor:pointer; padding:4px;" onclick="parent.middleside.location='catalogo_carga.php';" />
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
                                                echo "<tr>";
							foreach($lst_fields as $column)
							{ echo " <td ".($column==''?'style="text-align:center;"':'').">".utf8_encode($row[$column])."</td>";}
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
	<div id="layer" style="display:none; ">
                <form id="frm_edit" name="frm_edit">
	                <div id="div_layer" >
                                <table id="tbdatos">
                                        <tr>
                                                <td id="label"></td>
                                                <td id="value"></td>
                                        </tr>
					 <tr>
                                                <td>ID Producto</td>
                                                <td colspan=3><input type="text" name="txt_idproducto"  readonly="true" id="txt_idproducto"></td>
                                        </tr>
                                        <tr>
                                                <td>Pagina</td>
                                                <td colspan=3><input type="text" name="txt_pagina"   id="txt_pagina"></td>
                                        </tr>
					 <tr>
                                                <td>Descripcion</td>
                                                <td colspan=3><input type="text" name="txt_descripcion"  id="txt_descripcion"></td>
                                        </tr>
					 <tr>
                                                <td>Precio</td>
                                                <td colspan=3><input type="text" name="txt_precio"  id="txt_precio"></td>
                                        </tr>
					<tr>
                                                <td>
                                                        <input type="button" id="bt_guardar" value="Guardar">
                                                        <input type="button" id="bt_cancelar" value="Cancelar">
                                                </td>
                                        </tr>
				</table>
			</div>
		</form>
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
