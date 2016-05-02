<?

header("Content-Type:text/html; charset=utf-8");
include_once("connection.php");
$obj=new connection();

ini_set('include_path', ini_get('include_path').';../Classes/');
include 'librerias/PhpExcel/Classes/PHPExcel.php';
include 'librerias/PhpExcel/Classes/PhpExcel/Writer/Excel2007.php';

$objPHPExcel = new PHPExcel();
$directory_download="download_xls";
$file_name= $directory_download.'/xls_'.date('YmdHis').'.xls';
PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

$p_idinvoice=$_REQUEST['txt_idinvoice'];

# Get Query Master

mysqli_query($obj->cn,"set names utf8 ;");
$p_query="select s.*,c.id,c.nombre as nomcliente,s.cliente_direccion, s.cliente_ciudad,
                                 s.cliente_telefono, s.cliente_celular as cliente_telefono2,
				sum(d.subtotal) as tot_punitario,sum(d.total) as tot_total,s.cliente_state,
				s.cliente_zipcode,date_format(c.birthday,'%d/%m/%Y') as birthday
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

	 $ValuesStyleHeader=array('font'=>array('bold'=>false,'color'=>array('argb'=>'000000'),'size'=>18,'name'=>'Calibri'));	
         $objPHPExcel->getActiveSheet()->getStyle("D2:D2")->applyFromArray($ValuesStyleHeader);	

	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,5,"Order #:"); 
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,5,$objFields['idinvoice']);
	 $objPHPExcel->getActiveSheet()->mergeCells('G3:H3');
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,5,"Order Date :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,5,date("m/d/Y",strtotime($objFields['fch_ingreso'])));
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,6,"Customer #:");
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,6,$objFields['id']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,7,"Name :");
	$dato=utf8_encode($objFields['nomcliente']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,7,$dato);
	 $objPHPExcel->getActiveSheet()->mergeCells('G5:H5');
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,7,"Phone :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,7,$objFields['cliente_telefono']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,8,"Address :");
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,8,$objFields['cliente_direccion']);
         $objPHPExcel->getActiveSheet()->mergeCells('G6:H6');
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,8,"Celular :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,8,$objFields['cliente_telefono2']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,9,"City :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,9,$objFields['cliente_ciudad']);
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,10,"State :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,10,$objFields['cliente_state']);
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,11,"Zip Code :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,11,$objFields['cliente_zipcode']);
	 $objPHPExcel->getActiveSheet()->mergeCells('G7:H7');
	 $objPHPExcel->getActiveSheet()->mergeCells('G8:H8');
	 $objPHPExcel->getActiveSheet()->mergeCells('G9:H9');
	 $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(6,9,"Birhday :");
         $objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(8,9,$objFields['birthday']);

	 $objPHPExcel->getActiveSheet()->getStyle("D5:D11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

	 

	$LabelStyle=array('font'=>array('bold'=>true,'color'=>array('argb'=>'000000'),'size'=>10,'name'=>'Arial'));
	$ValuesStyle=array('font'=>array('bold'=>false,'color'=>array('argb'=>'000000'),'size'=>10,'name'=>'Arial'));
	$objPHPExcel->getActiveSheet()->getStyle("C5:C11")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle("C5:C11")->applyFromArray($LabelStyle);
	$objPHPExcel->getActiveSheet()->getStyle("D5:D11")->applyFromArray($ValuesStyle);
	
	$objPHPExcel->getActiveSheet()->getStyle("G5:H11")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle("G5:H11")->applyFromArray($LabelStyle);
        $objPHPExcel->getActiveSheet()->getStyle("I5:I11")->applyFromArray($ValuesStyle);
	
	$BorderStyle=array('borders'=>array('outline'=> array('style'=>PHPExcel_Style_Border::BORDER_THIN,
							      'color'=>array('argb'=>'000000'))));
	$objPHPExcel->getActiveSheet()->getStyle("B5:D11")->applyFromArray($BorderStyle);
        $objPHPExcel->getActiveSheet()->getStyle("G5:I11")->applyFromArray($BorderStyle);
	$objPHPExcel->getActiveSheet()->mergeCells('D13:I13');
	$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,13,$objFields['observaciones']);
	$objPHPExcel->getActiveSheet()->getStyle('D13:I13')->getAlignment()->setWrapText(true);

        # drawing headers
	$nheader=15;
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
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(1,$nrow,$row['cantidad']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(2,$nrow,$row['pagina']);
		$objPHPExcel->getActiveSheet()->SetCellValueByColumnAndRow(3,$nrow,utf8_encode($row['nomproducto']));
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
         foreach(range('A','H') as $column_id)
         {  $objPHPExcel->getActiveSheet()->getColumnDimension($column_id)->setAutoSize(true);}

        # -----------------------------------------------------------------

	$objPHPExcel->setActiveSheetIndex(0) ;
        $objWriter=new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save($file_name);
      ##  header('content-type:applicacion/octet-stream');
        header('content-type:applicacion/vnd.ms-excel');
        header('content-disposition: attachment; filename="'.basename($file_name).'"');
	readfile($file_name);
	
?>
