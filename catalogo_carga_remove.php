<?php

include_once("connection.php");
$obj= new connection();
$idcatalogo=$_REQUEST['idcarga'];
$cmd="delete from sis_carga_catalogo where codigo='$idcatalogo';
      delete from sis_carga_catalogos where idcarga='$idcatalogo'; ";

#echo "<script>alert('Catalogo fue eliminado satisfactoriamente.');
#	    window.location='catalogo_carga.php';</script>";


if(mysqli_multi_query($obj->cn,$cmd))
{ echo "<script>alert('Catalogo fue eliminado satisfactoriamente.');</script>";}
else
{ 
  $p_error=str_replace("'","\'",mysqli_error($obj->cn));
  echo "<script>alert('Error trying to remove catalogo and details: $p_error .'); </script>";
  die($p_error);}


?>
