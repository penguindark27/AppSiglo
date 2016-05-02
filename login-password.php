<?
session_start();
include_once('../siac_backauditor/app/vista/login/Auth.class.php');
include_once("../siac_backauditor/app/modelo/clase_mysqli.inc.php");

header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set("America/Lima");
unset($obj);
$obj= new DB_mysqli();
Auth::session_out();

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Cambio de Clave</title>
	<script type="text/javascript" src="../siac_backauditor/librerias/prototype_1.7/prototype.js"></script>
	<style type="text/css" >
		body{background-color:#F7F7F7; margin:10px 40px 10px 40px}
		.div-header{ display:block;  background-color:#FFF; 
					border:2px solid #CFCFCF; width:100%; overflow:hidden;}
		.div-header div{ width:50%; display:block; height:auto; margin:0px; float:left; }
		.div-header div img{ margin:16px} 
		
		.div-advice{ display:block; height:auto;  background-color:#FFF; 
					border:2px solid #CFCFCF; margin:10px 0px 10px 0px;
				font-family: 'Lucida Grande', Tahoma, Verdana, sans-serif; 
				overflow:hidden}
		.div-advice div { width:100%; display:block; margin-bottom:10px;}
	    .div-advice div h4{ font-size:14px; margin-left:20px; color:#008FBF}
		
		.fields{ margin-left:10px;}
		.fields tr td{ padding:5px; font:normal 14px 'Lucida Grande', Tahoma, Verdana, sans-serif;}
		.fields input[type="password"]{ padding:5px; font-size:16px; }
		.fields input[type="button"]{ padding:5px; font: bold 12px 'Lucida Grande', Tahoma, Verdana, sans-serif; }
		.fields input[type="button"]:hover{ cursor:pointer;}
		
		.field_style{border:solid 1px #DFDFE6;  background-color:#F4F7F8;}
		.field_style:focus{ border:solid 1px #73A6FF}
		.field_mandatory{ border:solid 1px #F00; background-color:#FBDADC}
				
		.rights{ color:#767A86;
				 font-family:"Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "DejaVu Sans", Verdana, sans-serif;
				 font-size:12px; text-align:center;}
	</style>
    <script>
			function BeginEvents()
			{
				Event.observe('bt_procesar','click',BtProcess_click);
			}
			function set_default_style()
			{
				var field=document.getElementsByClassName('fields')[0].getElementsByTagName("input");
				for(nitem=0;nitem<=field.length-1;nitem++)
				{ var input=field[nitem];
				  if(input.type=="password"){
					   if(input.className=='field_mandatory'){input.removeAttribute("class");}
					   input.className="field_style";
					}
				}
			}
			function BtProcess_click()
			{
				set_default_style();
				if($('txt_password').value=='')
				{ 	if($('txt_password').hasAttribute("class")){ $('txt_password').removeAttribute("class");}
					$('txt_password').className="field_mandatory";
					 alert('Debe ingresar nueva clave a ser procesada.'.toUpperCase()); 
					 return false; }
				if($('txt_conformidad').value=='')
				{  if($('txt_conformidad').hasAttribute("class")){ $('txt_conformidad').removeAttribute("class");}
				   $('txt_conformidad').className="field_mandatory";
				   alert('Debe ingresar confirmacion de clave a ser procesada.'.toUpperCase()); 
				   return false;  }
				
				if($('txt_conformidad').value!=$('txt_password').value) 
				{ alert('Clave y confirmacion deben ser identicas, volver a ingresar valores'.toUpperCase()); return false;}
				
				if(confirm("Desea proceder con la actualizacion de clave?".toUpperCase()))
				{
					$('frm_values').action="login-password-change.php";
					$('frm_values').method="post";
					$('frm_values').submit();								
				}
			}
			Event.observe(window,'load',BeginEvents);
	</script>
</head>
<body>
	<div class="div-header">
    	<div>
  	   	  <img src="img_tgestiona.png">  
        </div>
        <div> </div>
    </div>
    <div class="div-advice">
    	<form id="frm_values" name="frm_values" >
            <div>
                 <h4>Cambio de Clave</h4>
                 <table class="fields">
                    <tr>
                        <td>Ingrese Nueva Clave</td>
                        <td><input type="password" name="txt_password" id="txt_password" class="field_style"/></td>
                    </tr>
                    <tr>
                        <td>Validar Nueva Clave</td>
                        <td><input type="password" name="txt_conformidad" id="txt_conformidad" class="field_style" /></td>
                    </tr>
                    <tr>
                        <td><input type="button" id="bt_procesar" name="bt_procesar" value="Cambiar Clave"/></td>
                    </tr>
                 </table>
            </div>
        </form>
    </div>
   <h4 class="rights">Copyright © 2015 TGestiona . All rights reserved.</h4>
</body>
</html>