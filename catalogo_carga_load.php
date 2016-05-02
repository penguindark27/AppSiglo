<?php 
	session_start();
	set_time_limit(0); // NO HAY INTERVALO MINIMO DE TIEMPO duracion .php.	
	
	include_once("connection.php");
	$obj=new connection();
	
	$user= $_SESSION['nom_user'];	
	move_uploaded_file($_FILES['file_csv']['tmp_name'],'shotdev/'.$_FILES['file_csv']['name']);
	
	$objCsv= fopen('shotdev/'.$_FILES['file_csv']['name'],'r');
	$p_fhregistro=date('Y-m-d H:i:s');
	$p_fproceso= date('Y-m-d');

	$p_proveedor=$_REQUEST['cb_proveedor'];
	$p_catalogo=$_REQUEST['txt_catalogo'];
	$p_archivo=$_FILES['file_csv']['name'];
	$p_peso=$_FILES['file_csv']['size'];
	$p_tipo=$_FILES['file_csv']['type'];
	
	# to register process ------------
	$cmd_proceso="insert into sis_carga_catalogo(idproveedor,catalogo_nombre,archivo_nombre,archivo_peso,archivo_tipo) 
			values('$p_proveedor','$p_catalogo','$p_archivo','$p_peso','$p_tipo') ;";
	if(!mysqli_query($obj->cn,$cmd_proceso))
	{ die("Error,".mysqli_error($obj->cn)."  query:'".$cmd_proceso."'");}
	$p_codigo= $obj->cn->insert_id;
	
	# ----------------------------------------
					
	$nfila=0;
	$nupdate=0;
	$ninsert=0;
	$noprocesado=0;
	
while(($objRead=fgetcsv($objCsv,1000000,','))!==false)
{
	$pagina=$objRead[0];
        $descripcion=$objRead[1];
        $precio=$objRead[2];
        $nfila=$nfila+1;
        if($nfila!=1)
        {
	  $cmd="insert into sis_catalogos(pagina,descripcion,precio,idcarga) 
		values('$pagina','$descripcion','$precio','$p_codigo') ;"; 
	  if(!mysqli_query($obj->cn,$cmd))
	  { die("Error trying to insert item : ".mysqli_error($obj->cn));}
        }
}

$cmd_update="update sis_carga_catalogo set nfilas='$nfila' where codigo='$p_codigo' ;";

if(!mysqli_query($obj->cn,$cmd_update))
{echo "<script> alert('Error, ".mysql_error($obj->cn)."');</script>";}	
else
{ echo "<script>alert('Proceso fue completado Satisfactoriamente.'); parent.middleside.location='catalogo_carga.php';</script>";}   

?>
