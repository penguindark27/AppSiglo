<?

include_once("connection.php");

header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set("America/Lima");
unset($obj);
$obj= new connection();
$obj->session_out();

?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Portal de Administracion</title>
</head>
<frameset cols="13%, 87%">
	<frame src="leftside.php" id="leftside" name="leftside" noresize></frame>
	<frame src="middleside.php" id="middleside" name="middleside" noresize></frame>			
</frameset>
<html>
