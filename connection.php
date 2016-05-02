<?
session_start();
class connection
{
	var $cn;
	function __construct()
	{
		$this->cn=mysqli_connect("xxxxx","xxxxx","xxxxx","xxxxx");
		if($this->connect_errno)
		{die("Error connection string ".$this->cn->connect_errno);}
	}
	
	 public static function session_out()
         {
                        # if session function has been started or not
                        if(session_id()==''){ header("Location:session.php"); }
                        # idaplicativo : 2  means application CALL_OUT
                        if(empty($_SESSION['nom_user'])) { header("Location:session.php"); }
                        else
                        { if($_SESSION['idaplicativo']!='1'){ header("Location:session.php");} }
                        #  -------------------------------------------------
         }
	
	 /*  functions made by Manuel Lazo*/
        function get_sentence_update($p_table, $list, $where ='')
        {
                $query='update '.$p_table.' set ';
                $values='';
                foreach($list as $column=>$value)
                {
                        if (is_string($value))  $value=utf8_decode(trim(strtoupper($value)));
                        is_null($value) ? $values.= $column."=null," : $values.= $column."='".$value."',";
                }
                $values = substr($values,0, strlen(trim($values))-1);
                $query= $query.$values." ".$where;
                return $query;
        }
	
	function get_correlativo_invoice()
	{
	  $cmd="select lpad((ifnull(max(idinvoice),0)+1),6,'000000') as nfila from sis_invoice;";
	  $result=mysqli_query($this->cn,$cmd);
	  if($result)
	  { if($row=mysqli_fetch_array($result)){ return $row['nfila'];}}
	  return 1;
	}
	function get_sentence_invoice_det($list,$p_idinvoice)
	{
		$cmd_remove="delete from sis_invoice_det where idinvoice='$p_idinvoice' ;";
		mysqli_query($this->cn,$cmd_remove) or die("Error executing remove query.");
		$query="insert into sis_invoice_det(idinvoice,item,cantidad,idcatalogo,pagina,sufijo,idproducto,color,talla,peso,subtotal,total,tipo) values";
		$nitem=0;
		foreach($list as $row)
		{
		  $nitem=$nitem+1;
		  $p_values.="('$p_idinvoice','$nitem','".$row['cantidad']."','".$row['idcatalogo']."','".$row['idpagina']."','".$row['sufijo']."','".
					$row['idproducto']."','".$row['color']."','".$row['tamanio']."','".$row['peso']."','".
					$row['punitario']."','".$row['total']."','".$row['tipo']."'),";
		}
		$p_values=substr(trim($p_values),0,strlen($p_values)-1);
		return ($query.$p_values);
	}
	
	function get_sentence_insert($p_table, $list)
        {
                $query = '';
                $columns ='' ;
                $values ='';
                foreach($list as $column => $value)
                {  $columns.= $column."," ; }
                $columns = substr($columns,0, strlen(trim($columns))-1);

                foreach($list as $row=>$value)
                {  if (is_string($value))  $value=utf8_decode(trim(strtoupper($value)));
                        /*$value = addslashes($value); */
                        is_null($value) ? $values.= "null,": $values.="'".$value."'," ;
                }
                $values = substr($values,0, strlen(trim($values))-1);

                $query= "insert into ".$p_table."(".$columns.") values(".$values.")";
                return $query ;
        }

	 function get_free_results($obj)
        {
                while($obj->next_result())
                {
                        if($result=$obj->store_result())
                        { $result->free();}
                }
        }
        function get_array_from($result)
        {
                $nitem=0;
                $lst=array();
                while($row=$result->fetch_array()) {
                         $lst[$nitem]=$row;
                         $nitem+=1; }
                $result->free();
                return $lst;
        }

	 function get_columns_from($result)
        {
                $lst_columns=array();
                $info_fields=mysqli_fetch_fields($result);
                foreach($info_fields as $field)
                { $lst_columns[$field->name]=$field->name;}
                return $lst_columns;
        }
   
}


?>
