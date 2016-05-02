<?
session_start();
include_once("connection.php");
$obj=new connection();

ini_set('include_path', ini_get('include_path').';../Classes/');
include 'librerias/PhpExcel/Classes/PHPExcel.php';
include 'librerias/PhpExcel/Classes/PhpExcel/Writer/Excel2007.php';

$user=$_SESSION['nom_user'];
$directory_download="download_xls";
$file_name= $directory_download.'/xls_schema.xls';
$file_download= $directory_download.'/xls_'.date('YmdHis').'.xls';
$objFileType='Excel5';
$objReader= PHPExcel_IOFactory::createReader($objFileType);
$objPHPExcel = $objReader->load($file_name);
#$objPHPExcel = new PHPExcel();

PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

$p_idinvoice=$_REQUEST['txt_idinvoice'];

# Get Query Master

$p_query="select s.*,c.id,c.nombre as nomcliente,s.cliente_direccion, s.cliente_ciudad,
                                 s.cliente_telefono, s.cliente_celular as cliente_telefono2,
				sum(d.subtotal) as tot_punitario,sum(d.total) as tot_total,s.cliente_state,
				date_format(c.birthday,'%d/%m/%Y') as birthday,
				(select nombres from sis_usuarios where nom_user='$user') as user_name
                 from sis_invoice as s
				inner join sis_invoice_det as d on d.idinvoice=s.idinvoice 
                                 left join sis_clientes as c on c.codigo=s.id_cliente and c.activo=1
                 where s.idinvoice='$p_idinvoice';";
#echo $p_query;
$lst=mysqli_query($obj->cn,$p_query) or die("Error ".mysqli_error($obj->cn).", trying to execute query: $p_query");
if($lst){$objFields=mysqli_fetch_array($lst);}
$obj->get_free_results($obj->cn);

$cmd_det="call sp_sis_rpt_invoice_det('$p_idinvoice');";
$result=mysqli_query($obj->cn,$cmd_det) or die("Error executing process: ".mysqli_error($obj->cn));
$lst_details=$obj->get_array_from($result);
$obj->get_free_results($obj->cn);

# Area from rpt Estados
        $objPHPExcel->getActiveSheet()->setTitle("Invoice $p_idinvoice");
        $ncolumn=1;
        $p_maxcolumn='';

/*
# Area to add image
	$img = imagecreatefromjpeg('imagenes/img_siglo.jpg');
	$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
	$objDrawing->setName('Img Siglo');$objDrawing->setDescription('Img Siglo');
	$objDrawing->setImageResource($img);
	$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
	$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
	#$objDrawing->setWidthAndHeight(190,300);
	$objDrawing->setWidth(150);
	$objDrawing->setResizeProportional(true);
	$objDrawing->setCoordinates('C3');
	$objDrawing->setWorkSheet($objPHPExcel->getActiveSheet());
# --------------------------------------
*/
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,20,"Order #:"); 
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,20,$objFields['idinvoice']);
	# $objPHPExcel->getActiveSheet()->mergeCells('G18:H18');
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,20,"Order Date :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,20,date("m/d/Y",strtotime($objFields['fch_ingreso'])));
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,21,"Customer #:");
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,21,$objFields['id']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,22,"Name :");
	 
	 $p_nom_client=utf8_encode($objFields['nomcliente']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,22,$p_nom_client);
	 $objPHPExcel->getActiveSheet()->mergeCells('G20:H20');
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,22,"Phone :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,22,$objFields['cliente_telefono']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,23,"Address :");
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,23,$objFields['cliente_direccion']);
         $objPHPExcel->getActiveSheet()->mergeCells('G21:H21');
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,23,"Celular :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,23,$objFields['cliente_telefono2']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,24,"City :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,24,$objFields['cliente_ciudad']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,25,"State :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,25,$objFields['cliente_state']);
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,26,"Zip Code :");
	## $objPHPExcel->getActiveSheet()->getStyle("D26:D26")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	 $objPHPExcel->getActiveSheet()->setCellValueExplicit("D26",$objFields['cliente_zipcode'],PHPExcel_Cell_DataType::TYPE_STRING);
         ##$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,26,$objFields['cliente_zipcode']);
	 $objPHPExcel->getActiveSheet()->mergeCells('G22:H22');
	 $objPHPExcel->getActiveSheet()->mergeCells('G23:H23');
	 $objPHPExcel->getActiveSheet()->mergeCells('G24:H24');
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,24,"Birhday :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,24,$objFields['birthday']);

	 $objPHPExcel->getActiveSheet()->getStyle("D20:D26")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	 

	$LabelStyle=array('font'=>array('bold'=>true,'color'=>array('argb'=>'000000'),'size'=>10,'name'=>'Arial'));
	$ValuesStyle=array('font'=>array('bold'=>false,'color'=>array('argb'=>'000000'),'size'=>10,'name'=>'Arial'));
	$objPHPExcel->getActiveSheet()->getStyle("C20:C26")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle("C20:C26")->applyFromArray($LabelStyle);
	$objPHPExcel->getActiveSheet()->getStyle("D20:D26")->applyFromArray($ValuesStyle);
	
	$objPHPExcel->getActiveSheet()->getStyle("G20:H26")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle("G20:H26")->applyFromArray($LabelStyle);
        $objPHPExcel->getActiveSheet()->getStyle("I20:I26")->applyFromArray($ValuesStyle);
	
	$BorderStyle=array('borders'=>array('outline'=> array('style'=>PHPExcel_Style_Border::BORDER_THIN,
							      'color'=>array('argb'=>'000000'))));
	$objPHPExcel->getActiveSheet()->getStyle("B20:D26")->applyFromArray($BorderStyle);
        $objPHPExcel->getActiveSheet()->getStyle("G20:I26")->applyFromArray($BorderStyle);
	$objPHPExcel->getActiveSheet()->mergeCells('D28:I28');
	$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,28,$objFields['observaciones']);
	$objPHPExcel->getActiveSheet()->getStyle('D28:I28')->getAlignment()->setWrapText(true);
	
	$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,29,"REPRESENTANTE: ".$objFields['user_name']);
	$objPHPExcel->getActiveSheet()->getStyle("D29:D29")->getFont()->setBold(true);


        # drawing headers
	$nheader=31;
	$lst_headers=array('Qty','Nro.Page','Description','Color','Size','Weight','Unit Price','Total Price');
        foreach($lst_headers as $column)
        {
                $from= PHPExcel_Cell::stringFromColumnIndex($ncolumn);
                $objPHPExcel->getActiveSheet()->getStyle($from."$nheader")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle($from."$nheader")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow($ncolumn,$nheader,$column);
                $ncolumn+=1;
        }
	$letter= PHPExcel_Cell::stringFromColumnIndex($ncolumn-1);	
	$objPHPExcel->getActiveSheet()->getStyle("B$nheader:".$letter.$nheader)->
        getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
	$objPHPExcel->getActiveSheet()->getStyle("B$nheader:".$letter.$nheader)->applyFromArray($LabelStyle);
	
	$nrow=($nheader);
	foreach($lst_details as $row)
	{
		$nrow+=1;
		$p_nom_producto= utf8_encode($row['nomproducto']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(1,$nrow,$row['cantidad']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,$nrow,$row['pagina']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,$nrow,$p_nom_producto);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(4,$nrow,$row['color']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(5,$nrow,$row['talla']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,$nrow,$row['peso']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(7,$nrow,$row['subtotal']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,$nrow,$row['total']);
	}	
	
	$letter= PHPExcel_Cell::stringFromColumnIndex($ncolumn-1);
	$ndetails=($nheader+1);
        $objPHPExcel->getActiveSheet()->getStyle("B$ndetails:".$letter.$nrow)->
        getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);	
	
	$ntotal=$nrow+2;
	 $objPHPExcel->getActiveSheet()->mergeCells('G'.$ntotal.':H'.$ntotal); 
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,$ntotal,"Total Sale");
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,$ntotal,$objFields['tot_punitario']);
        $ntotal+=1;
	 $objPHPExcel->getActiveSheet()->mergeCells('G'.$ntotal.':H'.$ntotal); 
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,$ntotal,"Shipping");
	$ntotal+=1;
	 $objPHPExcel->getActiveSheet()->mergeCells('G'.$ntotal.':H'.$ntotal); 
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,$ntotal,"Total Order");
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,$ntotal,$objFields['tot_total']);

	$nmessage=($ntotal+2);
        $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(1,$nmessage,$objFields['message']);
	$StyleMessage=array('font'=>array('bold'=>true,'color'=>array('argb'=>'000000'),'size'=>10,'name'=>'Arial'));
        $objPHPExcel->getActiveSheet()->getStyle("B".$nmessage.":D".$nmessage)->applyFromArray($StyleMessage);
	
	
	
	#apply Font style to rows from the details and the end

         $objPHPExcel->getActiveSheet()->getStyle('B'.($nheader+1).':I'.$ntotal)->applyFromArray($ValuesStyle);
	
	# --------------------------------------------------------

	$letter= PHPExcel_Cell::stringFromColumnIndex(6);
	$letter_to= PHPExcel_Cell::stringFromColumnIndex(8);
	$objPHPExcel->getActiveSheet()->getStyle($letter.($nrow+2).":".$letter_to.$ntotal)->
	getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
	 #apply AutoSizeMode to all cells of each sheet

	
         PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
	 $objPHPExcel->setActiveSheetIndex(0);
	 $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	 $ValuesStyleHeader=array('font'=>array('bold'=>false,'color'=>array('argb'=>'000000'),'size'=>8,'name'=>'Arial'));
         $objPHPExcel->getActiveSheet()->getStyle("C17:C17")->applyFromArray($ValuesStyleHeader);
	
	/*
         foreach(range('A','H') as $column_id)
         {  $objPHPExcel->getActiveSheet()->getColumnDimension($column_id)->setAutoSize(true);}
	*/
        # -----------------------------------------------------------------
	
	
	## Lines to add another sheet and set Font ---------------
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(1) ;
	$styleArray= array('font'=>array('bold'=>true,'color'=>array('rgb'=>'0F0E0E'),'size'=>10,'name'=>'Arial'));
        foreach(range('A','Z') as $column_id)
         {  $objPHPExcel->getActiveSheet()->getStyle($column_id)->applyFromArray($styleArray); }

        # -------------------------------------------------------

	$objPHPExcel->setActiveSheetIndex(0) ;
        $objWriter=new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save($file_download);
     ##   header('content-type:applicacion/octet-stream');
        header('content-type:application/vnd.ms-excel');
        header('content-disposition: attachment; filename="'.basename($file_download).'"');
	readfile($file_download);
	
?>
