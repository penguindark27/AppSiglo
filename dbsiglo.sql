
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `sis_carga_catalogo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_carga_catalogo` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idproveedor` int(10) unsigned DEFAULT NULL,
  `catalogo_nombre` varchar(200) DEFAULT NULL,
  `archivo_nombre` varchar(200) DEFAULT NULL,
  `archivo_peso` varchar(200) DEFAULT NULL,
  `archivo_tipo` varchar(200) DEFAULT NULL,
  `fch_proceso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nom_user` varchar(200) DEFAULT NULL,
  `nfilas` int(10) unsigned DEFAULT NULL,
  `anulado` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`codigo`),
  KEY `nom_user` (`nom_user`),
  KEY `fch_proceso` (`fch_proceso`)
) ENGINE=MyISAM AUTO_INCREMENT=206 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_catalogos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_catalogos` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pagina` int(10) unsigned DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `precio` decimal(12,2) DEFAULT '0.00',
  `color` varchar(100) DEFAULT NULL,
  `talla` varchar(10) DEFAULT NULL,
  `peso` decimal(12,2) DEFAULT NULL,
  `activo` int(10) unsigned DEFAULT '1',
  `idcarga` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `idcarga` (`idcarga`),
  KEY `pagina` (`pagina`),
  KEY `descripcion` (`descripcion`)
) ENGINE=MyISAM AUTO_INCREMENT=87245868 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_clientes` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(10) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `ciudad` varchar(200) DEFAULT NULL,
  `pais` varchar(200) DEFAULT NULL,
  `zip_code` varchar(100) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `telefono2` varchar(25) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `contacto` varchar(200) DEFAULT NULL,
  `web` varchar(200) DEFAULT NULL,
  `activo` int(10) unsigned DEFAULT '1',
  `fch_ingreso` datetime DEFAULT NULL,
  `fch_modif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nom_user` varchar(50) DEFAULT NULL,
  `nom_user_modif` varchar(50) DEFAULT NULL,
  `birthday` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_config_parameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_config_parameters` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` longtext,
  `tipo` enum('invoice_message') DEFAULT NULL,
  `activo` int(10) unsigned DEFAULT '1',
  `fhregistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`codigo`),
  KEY `tipo` (`tipo`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_invoice` (
  `idinvoice` varchar(10) NOT NULL,
  `moneda` varchar(1) DEFAULT NULL,
  `tcambio` decimal(12,4) DEFAULT '0.0000',
  `id_cliente` int(10) unsigned DEFAULT NULL,
  `fch_ingreso` datetime DEFAULT NULL,
  `nom_user` varchar(50) DEFAULT NULL,
  `fch_modif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_modif` varchar(50) DEFAULT NULL,
  `observaciones` longtext,
  `subtotal` decimal(12,2) DEFAULT '0.00',
  `igv` decimal(12,2) DEFAULT '0.00',
  `total` decimal(12,2) DEFAULT '0.00',
  `cliente_direccion` varchar(200) DEFAULT NULL,
  `cliente_ciudad` varchar(200) DEFAULT NULL,
  `cliente_state` varchar(200) DEFAULT NULL,
  `cliente_zipcode` varchar(200) DEFAULT NULL,
  `cliente_telefono` varchar(50) DEFAULT NULL,
  `cliente_celular` varchar(50) DEFAULT NULL,
  `message` longtext,
  `fhproceso` datetime DEFAULT NULL,
  PRIMARY KEY (`idinvoice`),
  KEY `fhproceso` (`fhproceso`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_invoice_det`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_invoice_det` (
  `idinvoice` varchar(10) NOT NULL,
  `item` int(10) unsigned NOT NULL,
  `idcatalogo` int(10) unsigned DEFAULT NULL,
  `pagina` int(10) unsigned DEFAULT NULL,
  `sufijo` varchar(20) DEFAULT NULL,
  `idproducto` varchar(200) DEFAULT NULL,
  `producto` varchar(200) DEFAULT NULL,
  `cantidad` decimal(12,2) DEFAULT '0.00',
  `codigo_color` varchar(10) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `talla` varchar(20) DEFAULT NULL,
  `peso` varchar(20) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT '0.00',
  `igv` decimal(12,2) DEFAULT '0.00',
  `total` decimal(12,2) DEFAULT '0.00',
  `tipo` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`idinvoice`,`item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_invoice_det_20160312`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_invoice_det_20160312` (
  `idinvoice` varchar(10) NOT NULL,
  `item` int(10) unsigned NOT NULL,
  `idcatalogo` int(10) unsigned DEFAULT NULL,
  `pagina` int(10) unsigned DEFAULT NULL,
  `sufijo` varchar(20) DEFAULT NULL,
  `idproducto` varchar(10) DEFAULT NULL,
  `producto` varchar(200) DEFAULT NULL,
  `cantidad` decimal(12,2) DEFAULT '0.00',
  `codigo_color` varchar(10) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `talla` varchar(20) DEFAULT NULL,
  `peso` varchar(20) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT '0.00',
  `igv` decimal(12,2) DEFAULT '0.00',
  `total` decimal(12,2) DEFAULT '0.00',
  `tipo` int(10) unsigned DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_menu` (
  `CODIGO` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `OBJECT_ID` varchar(20) DEFAULT NULL,
  `OBJECT_NAME` varchar(200) DEFAULT NULL,
  `OBJECT_PARENT` varchar(20) DEFAULT NULL,
  `OBJECT_PATH` longtext,
  `OBJECT_STATUS` int(10) unsigned DEFAULT '1',
  `OBJECT_TYPE` enum('PARENT','CHILD') DEFAULT NULL,
  `FHMODIF` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`CODIGO`),
  KEY `OBJECT_ID` (`OBJECT_ID`),
  KEY `OBJECT_STATUS` (`OBJECT_STATUS`),
  KEY `FHMODIF` (`FHMODIF`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_menu_accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_menu_accesos` (
  `OBJECT_ID` varchar(50) NOT NULL,
  `NOM_USER` varchar(50) NOT NULL,
  `STATUS` int(10) unsigned DEFAULT '1',
  `FCH_MODIF` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`OBJECT_ID`,`NOM_USER`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_paises` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pais` varchar(200) DEFAULT NULL,
  `region` varchar(200) DEFAULT NULL,
  `activo` int(10) unsigned DEFAULT '1',
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_proveedores` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `ciudad` varchar(200) DEFAULT NULL,
  `pais` varchar(200) DEFAULT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `telefono2` varchar(25) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `contacto` varchar(200) DEFAULT NULL,
  `web` varchar(200) DEFAULT NULL,
  `activo` int(10) unsigned DEFAULT '1',
  `precio` decimal(12,2) DEFAULT '0.00',
  `coleccion` varchar(100) DEFAULT NULL,
  `fch_ingreso` datetime DEFAULT NULL,
  `fch_modif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nom_user` varchar(50) DEFAULT NULL,
  `nom_user_modif` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sis_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sis_usuarios` (
  `nom_user` varchar(200) NOT NULL,
  `password` varchar(200) DEFAULT NULL,
  `fhingreso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nombres` varchar(250) DEFAULT NULL,
  `apellidos` varchar(250) DEFAULT NULL,
  `dni` varchar(22) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `idaplicativo` int(10) unsigned DEFAULT '1',
  `fhmodif` datetime DEFAULT NULL,
  `user_modif` varchar(200) DEFAULT NULL,
  `activo` int(10) unsigned DEFAULT '1',
  `invoice_filter` varchar(10) DEFAULT NULL,
  `customer_filter` varchar(10) DEFAULT NULL COMMENT '1:show all,2:allowed to remove',
  PRIMARY KEY (`nom_user`),
  KEY `fhingreso` (`fhingreso`),
  KEY `fhmodif` (`fhmodif`),
  KEY `user_modif` (`user_modif`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sis_get_carga_catalogo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sis_get_carga_catalogo`(p_idcarga int unsigned)
begin
		select codigo as 'Nro.',pagina as 'Pagina',	
					convert(descripcion using binary) as 'Descripcion', precio as 'Precio',
					concat("<a href='#' onclick='edit_product(",codigo,")' title='Editar Producto' ",
						" style='cursor:pointer;'><img src='imagenes/ico_edit2.png'/ style='border:0px'></a>") as ''
		from sis_catalogos 
		where idcarga=p_idcarga order by codigo asc;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sis_get_catalogos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sis_get_catalogos`()
begin
		select  c.codigo as 'Nro.', p.nombre as 'Proveedor', c.catalogo_nombre as 'Catalogo',
					c.archivo_nombre as 'Archivo', c.archivo_peso as 'Peso', 
					c.archivo_tipo as 'Tipo',c.nfilas as 'Filas',
					c.nom_user as 'Usuario', date(c.fch_proceso) as 'Fecha Proceso' ,
					concat("<a href='catalogo_carga_details.php?idcarga=",c.codigo,"' style='cursor:pointer;text-decoration:none;' title='Detalle de Carga'>
								<img src='imagenes/ico_details.png' style='border:0px;width:21px;padding:0px 3px 0px 3px;'></a>") as 'ico_details',
					concat("<a href='catalogo_carga_download.php?idcarga=",c.codigo,"' style='cursor:pointer;text-decoration:none;' title='Descargar Catalogo'>
								<img src='imagenes/ico_download.png' style='border:0px;width:21px;padding:0px 3px 0px 3px;'></a>") as 'ico_download',
					concat("<a href='javascript:remove_catalogo(",c.codigo,");' style='cursor:pointer;text-decoration:none;' title='Eliminar Catalogo'>
								<img src='imagenes/ico_cancel.png' style='border:0px;width:21px;padding:0px 3px 0px 3px;'></a>") as 'ico_cancel' 
					#concat("<a href='javascript:anular_catalogo(",c.codigo,");' style='cursor:pointer;text-decoration:none;' title='Anular Catalogo'>
					#			<img src='imagenes/ico_cancel2.png' style='border:0px;width:21px;padding:0px 3px 0px 3px;'></a>") as 'ico_cancel2'
		from sis_carga_catalogo as c
				left join sis_proveedores as p on p.codigo=c.idproveedor and p.activo=1 
		order by c.codigo desc;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sis_get_clientes` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sis_get_clientes`(p_user varchar(20))
begin
		declare p_status int unsigned default 0;
		declare p_remove int unsigned default 0;
		declare sentence_remove longtext default '';
		declare sentence_filter longtext default '';
		#select customer_filter from sis_usuarios where nom_user=p_user into p_status;
		
		# 1: show all,2:allowd to remove
		
		select if(count(customer_filter)>0,1,0) from sis_usuarios 
		where nom_user=p_user and customer_filter like '%1%' into p_status;
		select if(count(customer_filter)>0,1,0) from sis_usuarios 
		where nom_user=p_user and customer_filter like '%2%' into p_remove;
		
		if(p_remove=1) then
					set sentence_remove=concat(", concat(\"<a href='#' onclick='remove_customer(\",c.codigo,\")' title='Eliminar Cliente' ",
																" style='cursor:pointer;'><img src='imagenes/ico_remove.png' style='border:0px'></a>\") as remove ");
		end if ;
		if(p_status=0) then
					set sentence_filter=concat(" and c.nom_user='",p_user,"' ");
		end if ;
		
		set @command='' ;
		set @command=concat("select c.codigo as 'ID', c.nombre as 'Nombre', c.direccion as 'Direccion',
														c.ciudad as 'Ciudad', ucase(if(c.activo=1,'Activo','No Activo')) as 'Activo',
														concat(\"<a href='#' onclick='edit_customer(\",c.codigo,\")' title='Editar Cliente' \",
														\" style='cursor:pointer;'><img src='imagenes/ico_edit2.png' style='border:0px'></a>\") as 'modif' ",
															sentence_remove,"
												from sis_clientes as c 
												where c.codigo is not null ",sentence_filter," order by c.codigo desc;") ;
		
		prepare cmd from @command;
		execute cmd;
		deallocate prepare cmd;		
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sis_get_invoices` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sis_get_invoices`(p_fch_desde date,p_fch_hasta date,p_idcliente varchar(10), 
													p_idinvoice varchar(10),p_user varchar(25))
begin
		declare nresult int unsigned default 0;
		select ifnull(count(nom_user),0) from sis_usuarios where nom_user=p_user and invoice_filter=1 into nresult ;

		select s.idinvoice as 'ID Invoice', date(s.fch_ingreso) as 'Fch. Ingreso', 
					c.nombre as 'Cliente', s.observaciones as 'Obs.', ifnull(sum(d.total),0) as 'Total',
					s.nom_user as 'Usuario'
		from sis_invoice as s 
				left join sis_invoice_det as d on d.idinvoice=s.idinvoice 							
				left join sis_clientes as c on c.codigo=s.id_cliente and c.activo=1 
		where (date(s.fch_ingreso) between p_fch_desde and p_fch_hasta)	
				and if(length(ifnull(p_idcliente,''))>0,s.id_cliente=p_idcliente, s.idinvoice is not null) 
				and if(length(ifnull(p_idinvoice,''))>0,s.id_cliente=p_idcliente, s.idinvoice is not null) 
				and if(nresult=0,s.nom_user=p_user, s.idinvoice is not null) 
		group by s.idinvoice;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sis_get_proveedores` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sis_get_proveedores`()
begin
		select p.codigo  as 'ID', p.nombre as 'Nombre', p.direccion as 'Direccion',
				p.ciudad as 'Ciudad', ucase(if(p.activo=1,'Activo','No Activo')) as 'Activo',
				concat("<a href='#' onclick='edit_supplier(",p.codigo,")' title='Editar Proveedor' ",
							" style='cursor:pointer;'><img src='imagenes/ico_edit2.png' style='border:0px'/></a>") as 'modif'
		from sis_proveedores as p 
		order by p.codigo desc;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sis_rpt_invoice_det` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sis_rpt_invoice_det`(p_idinvoice varchar(20))
begin
			select * from 
			(
					select concat(ifnull(cc.idproveedor,''),'-',d.item) as nindex, d.item,
								if(tipo=1,d.cantidad,null) as cantidad, d.idproducto, 
								if(tipo=1,c.descripcion,ucase(d.idproducto)) as nomproducto,
								ucase(d.color) as color, ucase(d.talla) as talla,
								d.peso,d.subtotal,d.total,if(tipo=1,concat('P',d.pagina,ucase(d.sufijo)),null) as pagina
					from  sis_invoice_det as d 
							left join sis_catalogos as c on c.idcarga=d.idcatalogo and c.pagina=d.pagina and c.codigo=d.idproducto 
							left join sis_carga_catalogo as cc on cc.codigo=d.idcatalogo 
					where d.idinvoice=p_idinvoice 
					union all 
					select concat(cc.idproveedor,'-0') as nindex,d.item,'','',prv.nombre as nomproducto,'','','','','',''
					from sis_invoice_det as d 
							inner join sis_invoice as s on s.idinvoice=d.idinvoice 
							inner join sis_carga_catalogo as cc on cc.codigo=d.idcatalogo  
							inner join sis_proveedores as prv on prv.codigo=cc.idproveedor and prv.activo=1 
					where d.idinvoice=p_idinvoice
					union all select '9999-1','9998','','','','','','','','',''
					union all select '9999-2','9999','','','','','','','','',''
			) as w 
			#where nindex is not null and nomproducto is not null 
			group by nindex 
			order by item,nindex;
end ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

