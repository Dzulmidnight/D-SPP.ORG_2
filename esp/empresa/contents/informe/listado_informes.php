<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?EMPRESA";

	if(!$_SESSION["autentificado"]){
		header("Location:".$redireccion);
	}
}

if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  if (PHP_VERSION < 6) {
	    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	  }

	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
$idempresa = $_SESSION['idempresa'];
$ano_actual = date('Y', time());

$row_informes = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4 FROM informe_general INNER JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 INNER JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 INNER JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 INNER JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idempresa = $idempresa", $dspp) or die(mysql_error());
$numero_informes = mysql_num_rows($row_informes);

echo "<h4>Numero de informes actuales: $numero_informes</h4>";
?>
<table class="table table-bordered" style="font-size:12px;">
	<thead>
		<tr>
			<th>Id informe general</th>
			<th>Año informe general</th>
			<th>Trimestre 1</th>
			<th>Trimestre 2</th>
			<th>Trimestre 3</th>
			<th>Trimestre 4</th>
			<th>Total</th>
			<th>Estatus</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		while($listado = mysql_fetch_assoc($row_informes)){
			$total = $listado['total_trim1'] + $listado['total_trim2'] + $listado['total_trim3'] + $listado['total_trim4'];
		?>
		<tr>
			<td><?php echo $listado['idinforme_general']; ?></td>
			<td><?php echo date('Y',$listado['ano']); ?></td>
			<td><?php echo $listado['total_trim1']; ?></td>
			<td><?php echo $listado['total_trim2']; ?></td>
			<td><?php echo $listado['total_trim3']; ?></td>
			<td><?php echo $listado['total_trim4']; ?></td>
			<td><?php echo $total; ?></td>
			<td><?php echo $listado['estado_informe']; ?></td>
		</tr>
		<?php
		}
		 ?>
	</tbody>
</table>