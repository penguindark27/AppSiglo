<?

include_once("connection.php");
header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set("America/Lima");
unset($obj);
$obj= new connection();

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Portal de Administracion</title>
    <script type="text/javascript"  src="librerias/prototype_1.7/prototype.js"></script>
    <script type="text/javascript"  src="librerias/prototype_1.7/src/scriptaculous.js?load=effects"></script>
    <style type="text/css">
		body{ background-color:#F0F0F0}
		.div-container{ margin:30px auto; width:640px; }
		.div-login{ padding:20px 20px 20px; margin:0px auto; width:310px ;
					 font:bold 12px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
					 color:white; background:url(bg.png); position:relative}
		.div-login:before{  content: ''; position:absolute	;
							top:-5px; left:-5px; right:-5px; bottom:-5px ;
							background-color:#4885B9; 
							z-index:-1; border-radius:4px;}
						
		.div-login input{ font-family: 'Lucida Grande', Tahoma, Verdana, sans-serif;  font-size: 14px;}
		.div-login input[type="text"], .div-login input[type="password"]{ padding:5px ; width:200px;
					color: #404040 ;   background: #F8F8F8; border:solid 1px #BCBCB5;
					 outline: 4px solid #F0F0F0 ;   height:25px; border-radius:2px;}
		.div-login input[type="text"]:focus, 
		.div-login input[type="password"]:focus{ border-color: #0F66BE; outline-color:#B5DBFF; outline-offset:0;}
					 
		.div-login input[type="submit"], .div-login input[type="button"]{ padding:5px; font-weight:bold; }
		.div-login input[type="submit"]:hover, .div-login input[type="button"]:hover{ cursor:pointer;}
		
		.div-login h1{ font-size:15px; border-bottom:solid 1px #B5DBFF;
					font-weight:bold; line-height:40px; text-align:center;
					color:white; background-color:#4481B7; margin:-20px -20px 10px -20px;}
		
		.div-login-help{ margin:20px 0px 20px; font-size:12px; text-align:center; color:black;
						/*text-shadow: 0 1px #2a85a1 */}
		.div-login-help a{ text-decoration:none;}
		.div-login-help a:hover{ text-decoration:underline;}
		
		.tb{ border-collapse:collapse; }
		.tb td{ padding:10px;}
	</style>
    <script type="text/javascript">
		function validating()
		{
			if($('txt_usuario').value=='')
			{ alert('DEBE INGRESAR USUARIO A LOGEARSE.'); return false;}
			if($('txt_clave').value=='')
			{ alert('DEBE INGRESAR CLAVE A LOGEARSE.'); return false;}
			
			$('frm_datos').action='login-validate.php';			
			$('frm_datos').method='post';
			$('frm_datos').submit();
		}
		function BeginEvents()
		{ Event.observe('bt_login','click',validating);}
		
		Event.observe(window,'load',BeginEvents);
	</script>
</head>

<body>
    <div class="div-container">
		<div class="div-login">
        	<h1>Login</h1>
          	<form id="frm_datos" name="frm_datos" onSubmit="return false;">
            	<table class="tb">
                	<tr>
                    	<td>Usuario</td>
                        <td><input type="text" id="txt_usuario" name="txt_usuario" /></td>
                    </tr>
					<tr>
                    	<td>Password</td>
                        <td><input type="password" id="txt_clave" name="txt_clave" /></td>
                    </tr>
                    <tr>
                    	<td colspan=2 style=" text-align:center">
                        	<input type="button" id="bt_login" name="bt_login" value="Login" /></td>
                    </tr>
                </table>
            </form>
        </div>    	
      <!--  <div class="div-login-help">
        	<p>No recuerda su clave? <a href="#"> Click aqui para resetear.</a></p>
        </div> -->
    </div>
</body>
</html>
