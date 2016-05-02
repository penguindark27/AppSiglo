<?

include_once("../siac_backauditor/app/modelo/clase_mysqli.inc.php");
session_start();
header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set("America/Lima");
unset($obj);
$obj= new DB_mysqli();

$p_user=$_SESSION['idusuario'];
$p_password=sha1(strtoupper($_REQUEST['txt_password']));

$cmd="update $obj->catalogo.catalogo_usuario set contrasenia=ucase('$p_password') 
	where ucase(idusuario)=ucase('$p_user') and idaplicativo=2 ;";
	
if(mysqli_query($obj,$cmd))
{ echo "<script>alert('Contraseña fue cambiada satisfactoriamente. Se procedera redireccionar a Login de Aplicativo.');
			window.location='/CallOut/session-return.php'; </script>";} 

?>