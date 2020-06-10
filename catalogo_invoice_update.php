<?
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("America/Lima");
session_start();

 include_once("connection.php");
 $obj=new connection();
$p_user=$_SESSION['nom_user'];

$lst_estado=array('id'=>'','descripcion'=>'');

$lst_fields=array('txt_fch_ingreso','cb_cliente','txt_direccion','txt_ciudad',
		'txt_state','txt_zipcode','txt_telefono','txt_telefono2');
$fields_names=array('fch_ingreso','id_cliente','cliente_direccion','cliente_ciudad',
		'cliente_state','cliente_zipcode','cliente_telefono','cliente_celular');
$nitem=0;
for($nitem=0;$nitem<=count($lst_fields)-1;$nitem++)
{ 
	$field=$fields_names[$nitem];
    if($field=='id_cliente'){
            if ($_REQUEST[$lst_fields[$nitem]]==''){
                    $item[$field]=null;
            }else{
                    $item[$fields_names[$nitem]]=trim($_REQUEST[$lst_fields[$nitem]]);
            };
    }else{
            $item[$fields_names[$nitem]]=trim($_REQUEST[$lst_fields[$nitem]]);
    };
}		
	
if(strlen($_REQUEST['txt_idinvoice'])>0)
{$item['user_modif']=$_SESSION['user'];  }

$item['observaciones']=trim(preg_replace("/[\n|\r|\n\r]/i",'',$_REQUEST['txt_observaciones']));
$item['message']=trim(preg_replace("/[\n|\r|\n\r]/i",'',$_REQUEST['txt_message']));
$item['nom_user']=$p_user;

$details=json_decode($_REQUEST['lst_details'],true);
$p_idinvoice='';


if($_REQUEST['txt_idinvoice']=="")
{
	
	$p_idinvoice=$obj->get_correlativo_invoice();
    $item['idinvoice']=$p_idinvoice;
	$p_sentence=$obj->get_sentence_insert('sis_invoice',$item);
	if(mysqli_query($obj->cn,$p_sentence))
	{ 
		if (count($details)>0){
			$p_sentence_det=$obj->get_sentence_invoice_det($details,$p_idinvoice);
			mysqli_query($obj->cn,$p_sentence_det) or die(mysqli_error($obj->cn)) ;
		};
		$lst_estado['id']=1; $lst_estado['descripcion']="$p_idinvoice"; 
	}	
	else
	{ 
	   $advice= str_replace("'","\'",mysqli_error($obj->cn));
	   $advice="Error, trying to register row. Contact to software department : $advice";
	   $lst_estado['id']=0; $lst_estado['descripcion']="$advice"; }
	echo json_encode($lst_estado);
}
else
{
	$p_sentence=$obj->get_sentence_update('sis_invoice',$item," where idinvoice='".$_REQUEST['txt_idinvoice']."'") ;
	if(mysqli_query($obj->cn,$p_sentence))
	{
		$p_idinvoice=$_REQUEST['txt_idinvoice'];
		if (count($details)>0){
			$p_sentence_det=$obj->get_sentence_invoice_det($details,$p_idinvoice);
			mysqli_query($obj->cn,$p_sentence_det) or die(mysqli_error($obj->cn)) ;
		};
		$lst_estado['id']=1; $lst_estado['descripcion']="$p_idinvoice";
	}
	else
	{  $advice= str_replace("'","\'",mysqli_error($obj->cn));
	   $advice="Error, trying to register row. Contact to software department : $advice";
	   $lst_estado['id']=0; $lst_estado['descripcion']="$advice"; } 
	echo json_encode($lst_estado);
}



?>
