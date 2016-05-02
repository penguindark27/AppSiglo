<?
session_start();
include_once("connection.php");

header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set("America/Lima");

$user=$_SESSION['nom_user'];
unset($obj);
$obj= new connection();

$cmd=" select z.* from 
	  (select c.codigo,c.object_id, c.object_name, c.object_type, 
	  		c.object_status, c.object_path 
			from sis_menu as c
			where c.object_status in(1) and c.object_type in(1)  
			union all
			select concat((select codigo from sis_menu 
							where object_id=c.object_parent),',',c.codigo) as codigo, 
						c.object_id,c.object_name, c.object_type, c.object_status, c.object_path  
			from sis_menu as c
		  where c.object_type=2) as z 
    inner join sis_menu_accesos as ma on ma.object_id=z.object_id
	where object_status=1 and ma.nom_user='$user' 
	order by codigo ;";
#echo $cmd;
$result=mysqli_query($obj->cn,$cmd);
$list_parents=$obj->get_array_from($result);

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<script type="text/javascript"  src="librerias/prototype_1.7/prototype.js"></script>
	<script type="text/javascript"  src="librerias/prototype_1.7/src/scriptaculous.js?load=effects"></script>
	<style type="text/css">
		body{background-color:#EDF2FB;}
		
		#menu_navigator{ border:5px solid #002C75; width:80%; height:auto; padding:5px; display:block;}
		#menu_navigator div{ border:1px solid #FFF; display:block}
		#menu_navigator a{padding:7px; display:block;  color:#FFF; text-decoration:none;
								font:bold 12px Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;}
		
		.item_parent#header{background-color:#002C75; cursor:pointer; }
		.item_parent_select{ background-color:#002C75; cursor:pointer; }
		.item_parent{ background-color:#538AC0; cursor:pointer; }
		.item_parent:hover, .item_parent_select:hover{  background-color:#F68A43; cursor:pointer; }
	
		.item_child{ background-color:#70BDFF; }
		.item_child:hover{background-color:#54A6ED}
	
	</style> 
	<script type="text/javascript">
	
		function EventBeggin()
		{
			add_handler_divs();	
		}
		
		function load_page(url)
		{  if(url.length==0) return false;
			top.frames['middleside'].location=url;}
		function get_div_index(div)
		{ return div.id.substring(div.id.length-1);}
		function add_handler_divs()
		{
			var divs=document.getElementById('menu_navigator').getElementsByTagName("div");
			for(n=0;n<=divs.length-1;n++)
			{
				var div=divs[n]; 
				if(div.id.indexOf('child-content')>-1){div.style.display="none"; }
				if(div.id.indexOf('parent')==-1){ continue ;}
				div.onclick=function(){ var elements=document.getElementById('menu_navigator').getElementsByTagName("div");
										for(x=0;x<=elements.length-1;x++)
										{ var child=elements[x].childNodes[0] ;
										  if(elements[x].id.indexOf('parent')>-1)
										  {  if(child.className!="item_parent"){child.className="item_parent";} } 
										}
										this.childNodes[0].className="item_parent_select";  
										var nindex= get_div_index(this);
										if(document.getElementById('child-content'+nindex))
										{ 
											var divs=document.getElementsByTagName("div");
											var obj=document.getElementById('child-content'+nindex);
											Effect.toggle(obj,'blind',{duration:1.0})
										}
									}
									
			}
		}
		
		Event.observe(window,'load',EventBeggin);
	</script>
</head>
<body>
		<div id="menu_navigator">
			<div><a id="header" class="item_parent" >ADMINISTRACION</a></div>
            <?
				$nparent=1;
				$nchild=1;
				$nitem=0;
				foreach($list_parents as $item)
				{
					switch($item['object_type'])
					{
						case 'PARENT':
								if($nparent!=1 && $nchild!=1){ echo "</div>"; $nchild=1;}
								if(strlen($item['object_path'])>0)
								{$p_page=$item['object_path'];}
								else{$p_page='#';}
								echo "<div id='parent-item$nparent'><a href='$p_page' class='item_parent'>".$item['object_name']."</a></div>";
								$nparent+=1;
							break ;
						case 'CHILD':
								if($nchild==1) { echo "<div id='child-content".($nparent-1)."'>";}
								echo "<div id='child-item$nchild'><a href='#' class='item_child' 
										onclick='load_page(\"".$item['object_path']."\")'>".$item['object_name']."</a></div>";
								$nchild+=1;
								if($nitem==count($list_parent)){echo "</div>";}			
							break;
					}
					$nitem+=1;
				 } 
			?>
		</div>
</body>
<html>
