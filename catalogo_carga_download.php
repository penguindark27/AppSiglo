<?php

include_once("connection.php");

$tmp_name='xls_'.date('YmdHis');
$file_name= $tmp_name.'.csv';

$obj= new connection();
$idcatalogo=$_REQUEST['idcarga'];
$cmd="select pagina,descripcion,precio from sis_catalogos 
      where idcarga='$idcatalogo' order by pagina ;";
$result=mysqli_query($obj->cn,$cmd);
$lst_fields=$obj->get_columns_from($result);
$lst_result=$obj->get_array_from($result);
$obj->get_free_results($obj->cn);
$p_list='';

foreach($lst_fields as $column){$p_list.='"'.strtoupper($column).'",'; }
$p_list=substr($p_list,0,strlen(trim($p_list))-1)." \n";



foreach($lst_result as $row)
{
  foreach($lst_fields as $column)
  {
     if(is_string($row[$column]))
     { $p_value=preg_replace('/[\n|\r|\n\r|#|,|"]/i',' ',$row[$column]);
       $p_list.='"'.$p_value.'",';}
     else
     {$p_list.='"'.$row[$column].'"';}
  }
  $p_list=substr($p_list,0,strlen(trim($p_list))-1)." \n";
}

header('content-type:applicacion/octet-stream');
#header('cache-control: no-cache,no-store,must-revalidate');
header("Cache-Control: post-check=0, pre-check=0", false);
header('content-disposition: attachment; filename="'.basename($file_name).'"');
header('pragma:no-cache');
echo $p_list;
exit()



?>
