<?php 
	session_start();
	include ("../conf/validar_usuarios.php");
	validar_usuarios(0,1,2,3);	
	include ("../conf/conectarbase.php");
	$lista_campos=" id,`id_prod`,`descripgral`,`especificacion`";
if ($_POST)
{
//print_r($_POST);
	if ($_POST[action]=="ver_movimiento")
	{
		$totArticulos=0;
		$idmov=$_POST['id'];
		/*consulta*/
		$sqlMOV="SELECT * FROM prodxmov WHERE nummov='$idmov' ORDER BY id";
		$resultado=mysql_db_query($sql_db,$sqlMOV);
		$trows=mysql_num_rows($resultado);
		if($trows==0){
			echo "<center><span class='Estilo51'>No hay productos asociados a este movimiento.</span></center>";
		}else{ 
			$id_movimiento_recibido=$idmov;
			$sql_detalle_movimiento="SELECT mov_almacen.id_mov,mov_almacen.oc,mov_almacen.fecha,mov_almacen.tipo_mov,mov_almacen.almacen,mov_almacen.referencia,mov_almacen.asociado,mov_almacen.observ, tipoalmacen.almacen,tipoalmacen.id_almacen, concepmov.id_concep,concepmov.concepto,concepmov.asociado AS dasociado 
			FROM mov_almacen,tipoalmacen,concepmov 
			WHERE mov_almacen.tipo_mov=concepmov.id_concep AND mov_almacen.almacen=tipoalmacen.id_almacen AND mov_almacen.id_mov=$id_movimiento_recibido";
			if ($result_detalle_movimiento=mysql_db_query($sql_db,$sql_detalle_movimiento))
			{
				while ($row_detalle_movimiento=mysql_fetch_array($result_detalle_movimiento))
				{
					//print_r($row_detalle_movimiento);
					// ========================================================================================================================
					// PROVEEDOR ...
					if ($row_detalle_movimiento["dasociado"]=="Proveedor")
					{
						$sql_proveedor="SELECT id_prov,nr FROM catprovee WHERE id_prov=".$row_detalle_movimiento["asociado"];
						$result_proveedor=mysql_db_query($dbcompras,$sql_proveedor);
						$row_proveedor=mysql_fetch_array($result_proveedor);
						$xasociado=$row_proveedor["id_prov"];	
					}
					// ALMACENES
					if ($row_detalle_movimiento["dasociado"]=="Almacenes")
					{
						$sql_almacen_asociado="SELECT almacen FROM `tipoalmacen` WHERE `id_almacen`=".$row_detalle_movimiento["asociado"];
						$result_almacen_asociado=mysql_db_query($sql_db,$sql_almacen_asociado);	
						while($row_almacen_asociado=mysql_fetch_array($result_almacen_asociado)){	
							$xasociado=$row_almacen_asociado["almacen"];
						}
					}		
?>
<table width="95%" align="center" cellspacing="0" style="border:#333333 1px solid;">
  <tr>
    <td colspan="4" height="20" style=" background-color:#333333;text-align:center; font-weight:bold; color:#FFFFFF;">Movimiento <?=$id_movimiento_recibido?></td>
  </tr>
  <tr bgcolor="#FFFFFF" onMouseOver="this.style.background='#D9FFB3';" onMouseOut="this.style.background='#FFFFFF'">
    <td class="tdx">Id_Movimiento </td>
    <td>&nbsp;<?=$id_movimiento_recibido?></td>
    <td class="tdx">Fecha</td>
    <td>&nbsp;<?=$row_detalle_movimiento["fecha"]?></td>
  </tr>
  <tr bgcolor="#FFFFFF" onMouseOver="this.style.background='#D9FFB3';" onMouseOut="this.style.background='#FFFFFF'">
    <td width="auto" class="tdx">Tipo</td>
    <td width="auto">
		<?php
			if ($row_detalle_movimiento["concepto"]=="Compras"){
				echo "&nbsp;".$row_detalle_movimiento["concepto"]." (OC. #".$row_detalle_movimiento["oc"].")";
			} else {
				echo "&nbsp;".$row_detalle_movimiento["concepto"];
			}	
		?></td>
    <td width="auto" class="tdx">Referencia</td>
    <td width="auto">&nbsp;<?=$row_detalle_movimiento["referencia"]?></td>
  </tr>
  <tr bgcolor="#FFFFFF" onMouseOver="this.style.background='#D9FFB3';" onMouseOut="this.style.background='#FFFFFF'">
    <td class="tdx">Almac&eacute;n</td>
    <td>&nbsp;<?=$row_detalle_movimiento["almacen"]?></td>
    <td class="tdx">Asociado </td>
    <td>&nbsp;<?=$row_detalle_movimiento["dasociado"]." (".$xasociado.")"?></td>
  </tr>
  <tr bgcolor="#FFFFFF" onMouseOver="this.style.background='#D9FFB3';" onMouseOut="this.style.background='#FFFFFF'">
    <td class="tdx">Observaciones</td>
    <td colspan="3">&nbsp;<?=$row_detalle_movimiento["observ"]?></td>
  </tr>  
  
</table>		
		<?php	
		// ========================================================================================================================	
	}
} else {
	echo "<br>Error SQL: ($sql_detalle_movimiento)<br>Los detalles del Movimiento no se pueden mostrar.";
}			
			?>
			<br />
			<table width="95%" align="center" cellpadding="0" cellspacing="0" class="tablax">
            <tr>
                <td colspan="7" bgcolor="#333333" height="20" class="style9"><?=$trows?> Productos registrados en el Movimiento</td>
            </tr>
            <tr style="text-align:center; font-weight:bold;">
                <td width="33" height="20" bgcolor="#CCCCCC" class="style17">ID</td>
                <td width="77" bgcolor="#CCCCCC" class="style17">Clave Producto</td>
                <td width="62" bgcolor="#CCCCCC" class="style17">Cantidad</td>
                <td width="41" bgcolor="#CCCCCC" class="style17">C.U. </td>
                <td width="360" bgcolor="#CCCCCC" class="style17">Descripci&oacute;n</td>
                <td width="228" bgcolor="#CCCCCC" class="style17">Especificaci&oacute;n</td>
				<td width="100" bgcolor="#CCCCCC" class="style17">Ubicaci&oacute;n</td>
            </tr>
            <?
			$color="#D9FFB3";
			while($row=mysql_fetch_array($resultado)){
				$sqlInfoProd="select $lista_campos from catprod where id='".$row['id_prod']."' ORDER BY id";
				$resultado1=mysql_db_query($sql_db,$sqlInfoProd);
				$des_prod=mysql_fetch_array($resultado1);
			?>
            <tr bgcolor="<?=$color?>" onMouseOver="this.style.background='#cccccc';" onMouseOut="this.style.background='<?=$color; ?>'">
                <td align="center" class="td1" height="20"><?=$row['id_prod'];?></td>
                <td class="td1"><?=$row['clave'];?></td>
                <td width="62"  align="right" class="td1" ><?=$row['cantidad'];?>&nbsp;</td>
                <td class="td1" align="right"><?php if($row['cu']!==''||$row['cu']!==' ') echo '$'.$row['cu']; ?>&nbsp;</td>
                <td class="td1"><?=$des_prod['descripgral'];?></td>
                <td class="td1" >&nbsp;<?=$des_prod['especificacion'];?></td>
				<td>&nbsp;<?=$row['ubicacion'];?></td>
            </tr>
			<?
				($color=="#D9FFB3")? $color="#ffffff" : $color="#D9FFB3";
				$totArticulos=$totArticulos+$row['cantidad'];	
			}
			?>
			<tr>
				<td colspan="7"><hr color="#000000" /></td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="#CCCCCC" class=""><div class="style10">Total de Articulos </div></td>
				<td style="text-align:right; padding-right:px;"><b><?=$totArticulos;?></b>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			</table>
		<?	
		}		
		exit();
	}
}


// ------------ Paginacion -----------------------------
$actual=$_SERVER['PHP_SELF'];

	if (isset($_GET['orden'])) 
		$orden=$_GET["orden"];
	else 
		$orden='mov_almacen.id_mov DESC';
	if (isset($_GET['cri']))
	{
		$cri=$_GET['cri'];
		$orden=$_GET['orden'];
	} else { $cri=''; }
	
	// ... Reviso # de resultados con el criterio introducido ............. 
$sql_criterio="SELECT mov_almacen.*,concepmov.*,tipoalmacen.* FROM mov_almacen,concepmov,tipoalmacen 
WHERE 
mov_almacen.almacen=tipoalmacen.id_almacen AND mov_almacen.tipo_mov=concepmov.id_concep AND mov_almacen.referencia like '%" . $cri . "%' " ; 

	//$sql_criterio="SELECT count(id_mov) as total_registros FROM mov_almacen,concepmov where concepmov.concepto like '%" . $cri . "%' ";
	$result0=mysql_db_query($sql_db,$sql_criterio);
	//$row0=mysql_fetch_array($result0);
	$numeroRegistros=mysql_num_rows($result0);

	$tamPag=25; 
    //pagina actual si no esta definida y limites 
    	if(!isset($_GET["pagina"])) 
    	{ 
       		$pagina=1; 
       		$inicio=1; 
       		$final=$tamPag; 
    	} else { 	
			if (is_numeric($_GET["pagina"]))
				{ $pagina = $_GET["pagina"];  } else { $pagina=1; }
		} 
	
    //calculo del limite inferior 
    $limitInf=($pagina-1)*$tamPag; 
    //calculo del numero de paginas 
    $numPags=ceil($numeroRegistros/$tamPag); 
    
		if(!isset($pagina)) 
    	{ 
       		$pagina=1; 
       		$inicio=1; 
       		$final=$tamPag; 
    	}else{ 
       		$seccionActual=intval(($pagina-1)/$tamPag); 
       		$inicio=($seccionActual*$tamPag)+1; 
			if($pagina<$numPags) 
       			$final=$inicio+$tamPag-1; 
       		else 
          		$final=$numPags; 
       		
			if ($final>$numPags)
				$final=$numPags; 
	    } 
// ------------ Paginacion -----------------------------		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
.td1{ border-right:#CCCCCC 1px solid; padding:1px; }
.tablax{ border:#333333 1px solid; }
#detalle{ position:absolute; display:none; border:#333333 3px solid; background-color:#ffffff; 
width:800px; height:500px; left:50%; top:50%; margin-left:-400px; margin-top:-250px; z-index:3;}
#d_tit{width:710px; height:20px; float:left; background-color:#333333; color:#FFFFFF;}
#d_cer{width:90px; height:20px; float:right; text-align:right; background-color:#333333;}
#d_con{ clear:both; margin:2px; margin-top:3px; padding:2px; height:470px; /*border:#333333 1px solid;*/ overflow:auto;}

.tdx{ background-color:#CCCCCC; font-weight:bold; text-align:left; padding-left:2px;}


/*==========================================================================================*/
 .paginador1:link{ border:#CCCCCC 1px solid; background-color:#efefef; color:#000000; text-align:center; 
 width:20px; height:30px; padding:2px; font-size:10px; margin:1px;}
 .paginador1:visited{ border:#CCCCCC 1px solid; background-color:#efefef; color:#000000; text-align:center; 
 width:20px; height:30px; padding:2px; font-size:10px; margin:1px;}
 .paginador1:hover{ background-color:#D9FFB3; color:#333333; text-align:center; 
 width:20px; height:30px; padding:2px; margin:1px; font-weight:bold; }
 .pagact:link{ border:#CCCCCC 1px solid; border-bottom:#CCCCCC 2px solid; border-right:#CCCCCC 2px solid; background-color:#efefef; color:#333333; font-weight:normal; text-align:center; 
 width:20px; height:30px; padding:2px; font-size:15px; margin:1px; margin-right:2px;}
 .pagact:visited{ border:#CCCCCC 1px solid; background-color:#efefef; color:#333333; font-weight:bold; text-align:center; 
 width:20px; height:30px; padding:2px; font-size:15px; margin:1px; margin-right:2px;}
/*==========================================================================================*/
.buscadorX{ text-align:right; padding-right:10px;}
.buscador{ text-align:center; }
.paginas { margin-top:5px; font-size:small; text-align:center; font-weight:normal; color:#666666;  }

</style>
<script language="javascript" src="../../clases/jquery-1.3.2.min.js"></script>
<script language="javascript">
<!-- 
function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=400');");
	}
function verificaOS(form){
	var numorder=form.numorder.value;
	if(numorder==""){
		alert("No hay un numero de orden a para ver");
		return false;
	}
	else{
		alert(numorder);
	}
}
function validar(form)
	{
		var numorder=form.numorder.value;
    	var fecha=form.fecha.value;
    	if (numorder=="" ){				//Fecha
			alert("falta Numero de Orden");
			form.numorder.focus();
			return false;}
		if (fecha==""){				//fecha
			alert("Falta fecha de Orden de Servicio");
			return false;}
		//form.submit();
	}
function printMov(n){
    win1= window.open("../reportes/print_mov.php?mov="+n,"Impresion","width=750,height=700,scrollbars=yes,top=50,left=600,menubar=1") 
    win1.focus()
}
//====================================================================================
function ver_movimiento(m)
{
//alert(m);
$("#all").hide();
$("#detalle").show();
$("#d_tit").html('&nbsp;&nbsp;&nbsp;Detalles del Movimiento.');

	$.ajax({
    async:true,
    type: "POST",
    dataType: "html",
    contentType: "application/x-www-form-urlencoded",
    url:"<?=$actual?>",
    data:"action=ver_movimiento&id="+m,
    beforeSend:inicio0,
    success:resultado0,
    timeout:10000,
    error:problemas0
  	});
}

function inicio0()
{
  	$("#d_con").show().html('&nbsp;<center><img src="../../img/barra6.gif"></center>');
}
function resultado0(datos)
{
	$("#d_con").show().html("&nbsp;"+datos);
}
function problemas0()
{
	$("#d_con").show().html('Error: El servidor no responde.');
}
// ----------------------------------------------------------------------
function cerrarv()
{
	$("#detalle").hide();	
	$("#all").show();
}
// ======================================================================	
function cerrar(elEvento) {
var evento = elEvento || window.event;
var codigo = evento.charCode || evento.keyCode;
var caracter = String.fromCharCode(codigo);
//alert("Evento: "+evento+" Codigo: "+codigo+" Caracter: "+caracter);
if (codigo==27||codigo==13)
 	cerrarv();
}
document.onkeypress = cerrar;
// -->
</script>

</head>
<body topmargin="0">
<div id="all">
<?php include("../menu/menuX.php"); ?>
<div class="buscadorX">
	<div class="form_buscador">
		<form name="frm_buscador" action="<?=$_SERVER['PHP_SELF'];?>" method="get" style="margin:0px; padding:0px;">
		<input type="hidden" name="orden" value="<?=$orden;?>" />
		<input type="text" name="cri" id="txt_buscar" value="<?php if ($cri=="") echo "Referencia"; else echo $cri; ?>" style="text-align:center; color:#666666;" />&nbsp;
		<input type="submit" value="Buscar" />
		</form>
	</div>


	<?php 
	/**/	


	
	$result=mysql_db_query($sql_db,$sql);
?>	
    </div>

</div>
<div align="center" style="clear:both;">
<h3 align="center">Movimientos del Sistema </h3>
<table width="98%" border="0" align="center" cellspacing="0" cellpadding="2" class="tabla1">
  <tr>
    <th><a href="<?=$_SERVER["PHP_SELF"]?>?pagina=<?=$pagina?>&orden=mov_almacen.id_mov,mov_almacen.fecha&cri=<?=$cri?>" title="Ordenar por Id">id</a></td>
    <th><a href="<?=$_SERVER["PHP_SELF"]?>?pagina=<?=$pagina?>&orden=concepmov.concepto,mov_almacen.fecha&cri=<?=$cri?>" title="Ordenar por Tipo">tipo</a></th>
    <th><a href="<?=$_SERVER["PHP_SELF"]?>?pagina=<?=$pagina?>&orden=mov_almacen.almacen,mov_almacen.fecha&cri=<?=$cri?>" title="Ordenar por almac&eacute;n">almac&eacute;n</a></th>
    <th><a href="<?=$_SERVER["PHP_SELF"]?>?pagina=<?=$pagina?>&orden=mov_almacen.asociado,mov_almacen.fecha&cri=<?=$cri?>" title="Ordenar por asociado">asociado</a></th>
    <th><a href="<?=$_SERVER["PHP_SELF"]?>?pagina=<?=$pagina?>&orden=mov_almacen.referencia&cri=<?=$cri?>" title="Ordenar por Referencia">referencia</a></th>
    <th><a href="<?=$_SERVER["PHP_SELF"]?>?pagina=<?=$pagina?>&orden=concepmov.concepto,mov_almacen.fecha&cri=<?=$cri?>" title="Ordenar por Fecha">fecha</a></th>
    <th title="<?=$numeroRegistros?> resultados">&nbsp;</th>
  </tr>
<?php
// sentencia SQL ...
$sql7="SELECT mov_almacen.*,concepmov.*,tipoalmacen.* FROM mov_almacen,concepmov,tipoalmacen 
WHERE 
mov_almacen.almacen=tipoalmacen.id_almacen AND mov_almacen.tipo_mov=concepmov.id_concep AND mov_almacen.referencia like '%" . $cri . "%' order by ".$orden." LIMIT ".$limitInf.",".$tamPag; 

		//echo "<hr>SQL [$sql7]";
		$result=mysql_db_query($sql_db,$sql7);  	
		$color=="#D9FFB3";
		
		while($row=mysql_fetch_array($result)){	
$id_mov=$row[0];
$tip_mov_id=$row["id_concep"];
$tip_mov=$row["concepto"];
$asoc=$row["asociado"];
$aso2='';
	
	$sql_aso="SELECT asociado FROM mov_almacen WHERE id_mov='$id_mov'";
	$result_aso=mysql_db_query($sql_db,$sql_aso);	
	while($row_aso=mysql_fetch_array($result_aso)){	
		$id_aso=$row_aso["asociado"];
	}

	if ($asoc=='Almacenes')
	{
			$sql_aso2="SELECT almacen FROM `tipoalmacen` WHERE `id_almacen`='$id_aso'";
			$result_aso2=mysql_db_query($sql_db,$sql_aso2);	
			while($row_aso2=mysql_fetch_array($result_aso2)){	
				$aso2=$row_aso2["almacen"];
			}
	}
	if ($asoc=='Proveedor'){
		$sql3="SELECT id_prov,nr FROM catprovee WHERE id_prov='$id_aso' ";
		$result3=mysql_db_query($dbcompras,$sql3);
		$row3=mysql_fetch_array($result3);
		$aso2=$asoc." ".$row3["id_prov"];	
	}		
?>
  <tr onMouseOver="this.style.background='#D9FFB3';" onMouseOut="this.style.background='#ffffff'">
    <td align="center" ><?=$id_mov?></td>
    <td><?=$tip_mov?></td>
    <td><?=$row["almacen"]?></td>
    <td>
		<?php
		if ($aso2==''){
		echo $row["asociado"];
		//echo ' - '.$id_aso;
		} else {
			echo substr($aso2,0,25).'...'; 
		}	
		?>    </td>
    <td><?=$row["referencia"]?></td>
    <td align="center"><?= $row["fecha"]; ?></td>
    <td colspan="2" align="center">
		<?php if ($_SESSION['usuario_nivel']<=3) {?>
		<a href="javascript:ver_movimiento('<?=$row[0];?>');" style="font-size:10px;" title="VER EL MOVIMIENTO">Ver</a>
		<?php  } if ($_SESSION['usuario_nivel']<=2) {?>
		 | <a href="javascript:printMov('<?=$row[0];?>')" style="font-size:10px;" title="VISTA DE IMPRESION">Impr</a>
		 | <a href="../reportes/xls_movimiento.php?idmov=<?=$row[0];?>" style="font-size:10px;" title="EXPORTAR MOVIMIENTO A EXCEL">Excel</a>
		<?php } ?>	</td>
  </tr>
  <?
  	//($color=="#D9FFB3")? $color="#ffffff" : $color="#D9FFB3";
	} 
  ?>
</table>
</div>
<br>

<div class="buscadorX">
	<?php if ($numeroRegistros>$tamPag) {?>
	<div class="paginador"> 
<?php 
	// ...... Codigo de la Paginacion ..................................... 
	if($pagina>1) 
	{ 
    	echo "<a alt='Anterior' class='paginador1' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina-1)."&orden=".$orden."&cri=".$cri."'> "; 
       	echo "<strong> << </strong></a> "; 
    } 

    for($i=$inicio;$i<=$final;$i++) 
    { 
		if ($i<10) $i2='0'.$i; else $i2=$i;
		if($i==$pagina) 
       	{ 
       		//echo "<strong><font color='#ff0000'> [".$i2."] </font></strong>";
			echo "<a href='#'  class='pagact'>".$i2."</a>";  
       	} else { 
        	echo "<a class='paginador1' href='".$_SERVER["PHP_SELF"]."?pagina=".$i."&orden=".$orden."&cri=".$cri."'>"; 
          	echo $i2."&nbsp;</a> "; 
	   	} 
    } 
    if($pagina<$numPags) 
  	{
		echo "<a  class='paginador1' alt='Siguiente' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina+1)."&orden=".$orden."&cri=".$cri."'>";   
		echo "<strong> >> </strong></a>"; 
	}	
	$result=mysql_db_query($sql_db,$sql);
?>	
    </div>
	<div class="paginas">p&aacute;ginas (<?=$pagina."/".$numPags;?>)</div>
	<?php }?>
</div>
<?
	include("../f.php");
?>
</div>
<div id="detalle">
	<div style="background-color:#333333;">
		<div id="d_tit">Movimiento X</div>
		<div id="d_cer"><a href="javascript:cerrarv();"><img src="../../img/cerrar_2.png" border="0" alt="Cerrar" title="Cerrar esta ventana" /></a></div>
	</div>
	<div id="d_con">...</div>
</div>
</body>
</html>