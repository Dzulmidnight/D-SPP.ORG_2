<?php 
require_once('Connections/dspp.php'); 
mysql_select_db($database_dspp, $dspp);
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$charset='utf-8';
$nombre_empresa = "";
$consultaBusqueda = $_POST['valorBusqueda'];

if (isset($consultaBusqueda)) {
	$consulta = mysql_query("SELECT nombre FROM empresa WHERE spp LIKE '%$consultaBusqueda%'", $dspp) or die(mysql_error());
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysql_num_rows($consulta);
	if ($filas === 0) {
		$nombre_empresa = "No se encontro resultado";
	} else {
		$resultados = mysql_fetch_assoc($consulta);
		$nombre_empresa = $resultados['nombre'];
	} //Fin else $filas
}
echo $nombre_empresa;
?>