<?php 
	ini_set('memory_limit','2048M');
	set_time_limit(0);

	$tmp_name='xls_'.date('YmdHis');
	$file_name= $tmp_name.'.csv';	
	$file_replace= $tmp_name.'_2.csv';	
	
	header('Content-Type: text/html; charset=utf-8');
	date_default_timezone_set("America/Lima");
	session_start();
        include_once("connection.php");
	unset($obj);
	$obj=new connection();
	$p_user= $_SESSION['nom_user'];
	
	$p_fch_desde=(isset($_REQUEST['txt_fch_desde'])?$_REQUEST['txt_fch_desde']:date("Y-m-d"));
        $p_fch_hasta=(isset($_REQUEST['txt_fch_hasta'])?$_REQUEST['txt_fch_hasta']:date("Y-m-d"));
        $p_idcliente=(isset($_REQUEST['cb_cliente'])?$_REQUEST['cb_cliente']:"");
        $p_idinvoice=(isset($_REQUEST['txt_invoice'])?$_REQUEST['txt_invoice']:"");
	
	# Getting difference of days
	#if((strtotime($p_fch_hasta)-strtotime($p_fch_desde))==0){ $status=false;} else {$status=true;}
	
	# if difference is more than 0 or not follow the next process.


		$cmd="call sp_sis_get_invoices('$p_fch_desde','$p_fch_hasta','$p_idcliente','$p_idinvoice','$p_user');";
		##echo $cmd;
		$result=mysqli_query($obj->cn,$cmd) or die("Error executing process: ".mysqli_error($obj->cn));
		$lst_columns=$obj->get_columns_from($result);
		$lst_datos=$obj->get_array_from($result);
		$obj->get_free_results($obj->cn);
		
		$line="\r\n";
		$ncolumn=0;
		$content_file="";
		
	
		foreach($lst_columns as $column)
		{$content_file.='"'.$column.'",';}
		$content_file=substr($content_file,0,strlen(trim($content_file))-1);
		$content_file=$content_file.$line;

		foreach($lst_datos as $row)
		{
			foreach($lst_columns as $column)
			{ 
				if(is_string($row[$column]))
				{ $p_value=preg_replace('/[\n|\r|\n\r|#|,|"]/i',' ',$row[$column]);
				  $content_file.='"'.$p_value.'",'; }
				else
				{$content_file.='"'.$row[$column].'",';}
			}
			$content_file=substr($content_file,0,strlen(trim($content_file))-1);
			$content_file=$content_file.$line;
		}
		header('content-type:applicacion/octet-stream');
		header('content-disposition: attachment; filename="'.basename($file_name).'"');
		echo $content_file;
		exit();
?>
