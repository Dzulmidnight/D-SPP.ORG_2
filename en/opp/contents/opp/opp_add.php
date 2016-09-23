<?php require_once('../Connections/dspp.php'); ?>
<?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO opp (idf, password, nombre, abreviacion, sitio_web, telefono, email, pais, idoc, razon_social, direccion_fiscal, rfc, fecha_inclusion) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['fecha_inclusion'], "int"),
                       GetSQLValueString($_POST['rfc'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $insertGoTo = "main_menu.php?OPP&add&mensaje=OPP agregado correctamente";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);


?>
<br>
<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table class="table col-xs-8">
    <tr valign="baseline">
      <th nowrap align="left" width="1">IDF</th>
      <td><input autofocus="autofocus" required class="form-control" type="text" name="idf" value="OPP-" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Password</th>
      <td><input class="form-control" type="text" name="password" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Nombre</th>
      <td><input required="required" class="form-control" type="text" name="nombre" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Abreviacion</th>
      <td><input class="form-control" type="text" name="abreviacion" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Sitio_web</th>
      <td><input class="form-control" type="text" name="sitio_web" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Teléfono Oficinas</th>
      <td><input class="form-control" type="text" name="telefono" value="" size="32"></td>
    </tr>    
    <tr valign="baseline">
      <th nowrap align="left">Email</th>
      <td><input class="form-control" type="email" name="email" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Pais</th>
      <td><select required class="form-control" name="pais">
      <option value="">Selecciona</option>
<?php 
do {  
?>
<option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
<?php
} while ($row_pais = mysql_fetch_assoc($pais));
?>
      </select></td>
    <tr>
    <tr valign="baseline">
      <th nowrap align="left">IDF OC</th>
      <td><select class="form-control" name="idoc">
        <?php 
do {  
?>
        <option class="form-control" value="<?php echo $row_oc['idoc']?>" ><?php echo $row_oc['abreviacion']?></option>
        <?php
} while ($row_oc = mysql_fetch_assoc($oc));
?>
      </select></td>
    <tr>
    <tr valign="baseline">
      <th nowrap align="left">Razon_social</th>
      <td><input class="form-control" type="text" name="razon_social" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Direccion_fiscal</th>
      <td><input class="form-control" type="text" name="direccion_fiscal" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">RFC</th>
      <td><input class="form-control" type="text" name="rfc" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input class="btn btn-primary" type="submit" value="Agregar OPP"></td>
    </tr>
  </table>
  <input type="text" name="fecha_inclusion" value="<?php echo date();?>">
  <input type="hidden" name="MM_insert" value="form1">
</form>

<?
mysql_free_result($pais);

mysql_free_result($oc);
?>