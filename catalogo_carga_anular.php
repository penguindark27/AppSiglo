<?php 

include_once("connection.php");
$obj=new connection();
$idcatalogo=$_REQUEST['idcarga'];

$cmd="update sis_carga_catalogo set anulado=if(anulado=0,1,0) where codigo='$idcatalogo' ;";
if(mysqli_query($obj->cn,$cmd))
{echo "<script>alert('Catalogo fue anulado satisfactoriamente.'); window.location='catalogo_carga.php';</script>";}
else
{ $p_error=str_replace("'","\'",$p_error);
  echo "<script>alert('Error Trying to anular document:$p_error');</script>";
  die("Error: $p_error");
}

?>

	
