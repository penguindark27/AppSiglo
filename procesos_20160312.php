<?
# In PHP 5.2 or higher we don't need to bring this in
# it's to use json_encode and json_decode

	require "librerias/jsonwrapper/jsonwrapper.php";
	
	session_start();
	include_once("connection.php");
	$obj=new connection();
	$user=$_SESSION['nom_user'];
	##mysqli_query($obj->cn,'set charset utf8; ');
	$tipo_operacion=$_REQUEST['tipo_operacion'];
	switch($tipo_operacion)
	{
		case 'add-item':			
			$nitem=$_REQUEST['nitem']+1;
			$style='field-style';
			?> 
				<input type="hidden" id="nposition" value="<?=$nitem;?>" />
				<td>
					<select id='cb_catalogo<?=$nitem;?>' class='select-style' onchange='cb_catalogos_changed(this);'
						style='width:250px;'>
					  <?
			                        $cmd="select codigo,ucase(catalogo_nombre) as catalogo_nombre
		                                        from sis_carga_catalogo order by catalogo_nombre asc ;";
		                                $result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
                                		echo "<option value=''>Seleccione...</option>";
                		                foreach($result as $row)
		                                { echo "<option value='".$row['codigo']."'>".$row['catalogo_nombre']."</option>";}	
					  ?>
					</select>
				</td>
				<td><select id='cb_pagina<?=$nitem;?>' class='select-style' onchange='cb_pagina_changed(this);'
					style='width:50px;'>
					<option value="">Seleccione...</option>
				</select></td>
				<td style='text-align:center;'><input type='text' id='txt_sufijo<?=$nitem;?>' <?="class='$style'" ?>  /></td>
				<td><select id='cb_producto<?=$nitem;?>' class='select-style' onchange='cb_producto_changed(this);'>
					<option value="">Seleccione...</option>
				</select></td>
				<td style='text-align:center;'><input type='text' id='txt_cantidad<?=$nitem;?>' <?="class='$style'" ?> 
						onkeypress="txt_peticion_atis_keypress()" style='width:50px;'/></td>
				<td style='text-align:center;'><input type='text' id='txt_color<?=$nitem;?>' <?="class='$style'" ?> style='width:70px;' /></td>
				<td style='text-align:center;'><input type='text' id='txt_longitud<?=$nitem;?>' <?="class='$style'" ?>  style='width:60px;' /></td>
				<td style='text-align:center;'><input type='text' id='txt_peso<?=$nitem;?>' <?="class='$style'" ?>  style='width:50px;'  /></td>
				<td style='text-align:center;'><input type='text' id='txt_punitario<?=$nitem;?>' <?="class='$style'" ?> 
						onkeypress="txt_peticion_atis_keypress()"  readonly /></td>
				<td style='text-align:center;'><input type='text' id='txt_total<?=$nitem;?>' <?="class='$style'" ?> 
							onkeypress="txt_peticion_atis_keypress()" readonly /></td>
				<td style='text-align:center;'><img src="imagenes/ico_remove.png" title="Eliminar" onclick='remove_item(<?=$nitem;?>)' 
					style="border:0px; cursor:pointer;"></td>
			<?
			break;
		case 'get-catalogos':
				$cmd="select codigo,ucase(catalogo_nombre) as catalogo_nombre 
					from sis_carga_catalogo
					where anulado=0 order by catalogo_nombre asc ;";
                                $result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
				echo "<option value=''>Seleccione...</option>";
                                foreach($result as $row)
                                { echo "<option value='".$row['codigo']."'>".$row['catalogo_nombre']."</option>";}
                        break;
		case 'get-catalogos-pages':
				$p_idcatalogo=$_REQUEST['idcatalogo'];
				$cmd="select pagina from sis_catalogos 
					where idcarga='$p_idcatalogo' group by pagina order by pagina ;";
				$result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
				echo "<option value=''>Seleccione...</option>";
			        foreach($result as $row)
		                { echo "<option value='".$row['pagina']."'>".$row['pagina']."</option>";}	
			break;
		case 'get-catalogos-products':
				$p_idcatalogo=$_REQUEST['idcatalogo'];
				$p_pagina=$_REQUEST['idpagina'];
				$cmd="select codigo,convert(descripcion,binary) as descripcion, precio from sis_catalogos 
					where pagina='$p_pagina' and activo=1 and idcarga='$p_idcatalogo' ;";
                                $result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
				echo "<option value=''>Seleccione...</option>";
                                foreach($result as $row)
                                { echo "<option value='".$row['codigo']."'>".utf8_encode($row['descripcion'])."</option>";}
                        break;
		case 'get-product-value':
				$p_idcatalogo=$_REQUEST['idcatalogo'];
                                $p_pagina=$_REQUEST['idpagina'];
				$p_idproducto=$_REQUEST['idproducto'];
				$cmd="select codigo,pagina,descripcion,precio from sis_catalogos 
					where pagina='$p_pagina' and idcarga='$p_idcatalogo' and codigo='$p_idproducto' ;" ;
				$result=mysqli_query($obj->cn,$cmd);
				if($result)
				{ if($row=mysqli_fetch_array($result)){$p_precio=$row['precio'];}}
				echo $p_precio;
			break;
		case 'get_customer_values':
		case 'get_values':
					$p_idcliente=$_REQUEST['idcliente'];
					$cmd="select * from sis_clientes where codigo='$p_idcliente'";
					$result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
					echo json_encode($result); 
			break;
		case 'save_customer':
				#echo "das click en guardar";
				$lst_values=array('txt_nombre','txt_direccion','txt_ciudad','txt_telefono','txt_telefono2','txt_email',
								'txt_contacto','cb_estado','cb_pais','txt_id','txt_zipcode','txt_state',
								'txt_birthday');
				$lst_fields=array('nombre','direccion','ciudad','telefono','telefono2','email','contacto','activo',
						'pais','id','zip_code','state','birthday');
				for($nitem=0;$nitem<=count($lst_fields)-1;$nitem++)
				{ $items[$lst_fields[$nitem]]=$_REQUEST[$lst_values[$nitem]] ;}
				 if($_REQUEST['txt_fch_ingreso']==''){$items['fch_ingreso']=date("Y-m-d H:i:s");}

				if($_REQUEST['txt_idcliente']=='')
				{
				    $items['nom_user']=$user;
				    $cmd=$obj->get_sentence_insert('sis_clientes',$items);}
				else
				{ 
					$items['codigo']=$_REQUEST['txt_idcliente'];
					$items['nom_user_modif']=$user;
					$where=" where codigo='".$items['codigo']."' ;";
					$cmd=$obj->get_sentence_update('sis_clientes',$items,$where);}
				#echo $cmd;	
				
				$lst=array(id=>'',descripcion=>'');
				if(!mysqli_query($obj->cn,$cmd))
				{ $error= "Error, ".mysqli_error($obj->cn);
					$lst['id']=0; $lst['descripcion']=str_replace("'","\'",$error);}
				else
				{ $lst['id']=1; $lst['descripcion']='Datos fueron grabados satisfactoriamente.';}
				echo json_encode($lst); 
			break;
			
		case 'get_supplier_values':
					$p_idproveedor=$_REQUEST['idproveedor'];
					$cmd="select * from sis_proveedores where codigo='$p_idproveedor'";
					
					$result=mysqli_query($obj->cn,$cmd);
					$lst_columns=$obj->get_columns_from($result);
					$list=array();					
					while($row=mysqli_fetch_array($result)){
						$item=array(count($lst_columns)-1);	
						foreach($lst_columns as $column){$item[$column]=utf8_encode($row[$column]);}
						$list[]=$item;
					}
					
					#$result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
					echo json_encode($list); 
					#echo json_encode($result); 
			break;
		case 'save_supplier':
				#echo "das click en guardar";
				$lst_values=array('txt_nombre','txt_direccion','txt_ciudad','txt_telefono','txt_telefono2','txt_email',
								'txt_contacto','cb_estado','cb_pais');
				$lst_fields=array('nombre','direccion','ciudad','telefono','telefono2','email','contacto','activo','pais');
				for($nitem=0;$nitem<=count($lst_fields)-1;$nitem++)
				{ $items[$lst_fields[$nitem]]=$_REQUEST[$lst_values[$nitem]] ;}

				if($_REQUEST['txt_fch_ingreso']==''){$items['fch_ingreso']=date("Y-m-d H:i:s");}

				if($_REQUEST['txt_idproveedor']=='')
				{
                                   $items['nom_user']=$user;
				   $cmd=$obj->get_sentence_insert('sis_proveedores',$items);}
				else
				{ 
					$items['codigo']=$_REQUEST['txt_idproveedor'];
					$items['nom_user_modif']=$user;
					$where=" where codigo='".$items['codigo']."' ;";
					$cmd=$obj->get_sentence_update('sis_proveedores',$items,$where);}
				#echo $cmd;	
			
				$lst=array(id=>'',descripcion=>'');
				if(!mysqli_query($obj->cn,$cmd))
				{ $error= "Error, ".mysqli_error($obj->cn);
					$lst['id']=0; $lst['descripcion']=str_replace("'","\'",$error);}
				else
				{ $lst['id']=1; $lst['descripcion']='Datos fueron grabados satisfactoriamente.';}
				echo json_encode($lst); 
			break;
		case 'get_product_values':
				#echo "prueba";
	
				$p_idproducto=$_REQUEST['idproducto'];
				$cmd="select * from sis_catalogos where codigo='$p_idproducto'; ";
				$result=$obj->get_array_from(mysqli_query($obj->cn,$cmd));
				echo json_encode($result);
			break;
		case 'save_product':
				$p_idproducto=$_REQUEST['txt_idproducto'];
				$p_pagina=$_REQUEST['txt_pagina'];
				$p_descripcion=$_REQUEST['txt_descripcion'];
				$p_precio=$_REQUEST['txt_precio'];
				$cmd="update sis_catalogos set descripcion='$p_descripcion', precio='$p_precio', pagina='$p_pagina' where codigo='$p_idproducto' ;";
				$lst=array(id=>'',descripcion=>'');
                                if(!mysqli_query($obj->cn,$cmd))
                                { $error= "Error, ".mysqli_error($obj->cn);
                                        $lst['id']=0; $lst['descripcion']=str_replace("'","\'",$error);}
                                else
                                { $lst['id']=1; $lst['descripcion']='Datos fueron grabados satisfactoriamente.';}
                                echo json_encode($lst);
			break;
		case 'anular_catalogo':
				$idcatalogo=$_REQUEST['catalogo'];
				$cmd="update sis_carga_catalogo set anulado=if(anulado=0,'1','0') where codigo='$idcatalogo';";
				$lst=array(id=>'',descripcion=>'');
				if(mysqli_query($obj->cn,$cmd))
				{ $lst['id']=1; $lst['descripcion']='Catalogo fue anulado satisfactoriamente.';}
				else
				{$error= "Error, ".mysqli_error($obj->cn);
                                        $lst['id']=0; $lst['descripcion']=str_replace("'","\'",$error);}
				 echo json_encode($lst);
			break;
		
	}
?>
