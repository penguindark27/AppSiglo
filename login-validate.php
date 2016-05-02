<?
include_once("connection.php");

header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set("America/Lima");
unset($obj);
$obj= new connection();

$p_user=trim(strtoupper($_REQUEST['txt_usuario']));
$p_password=strtoupper($_REQUEST['txt_clave']);

$cmd="select (u.idaplicativo+0) as idaplicativo, u.nom_user, 
	     concat(u.apellidos,', ',u.nombres) as nomusuario, u.dni 
	from sis_usuarios as u 
	where ucase(u.nom_user)='$p_user' and  ucase(u.password)=ucase('$p_password') 
	     and u.activo=1 and u.idaplicativo=1 ;";
#echo $cmd;


$result=mysqli_query($obj->cn,$cmd);

if(mysqli_num_rows($result)>0) 
{ 
	if(session_id()==''){session_start(); echo "session activated.";}
	$lst_columns=$obj->get_columns_from($result); 
	while($row=mysqli_fetch_array($result))
	{  foreach($lst_columns as $column) {  $_SESSION[$column]=$row[$column];} }  
	header("Location:/AppSiglo/index.php");
}
else
{ echo "<script>alert('Usuario y clave no pasaron validacion, vuelva a intentarlo.'.toUpperCase()) ;
		window.location='login.php';</script>";}

?>
