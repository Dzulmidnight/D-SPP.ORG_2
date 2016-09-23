<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 


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

  $pais = $_POST['pais'];

  $query = "SELECT * FROM com WHERE pais = '$pais'";
  $ejecutar = mysql_query($query) or die(mysql_error());
  $datos_com = mysql_fetch_assoc($ejecutar);
  $fecha = $_POST['fecha_inclusion'];

  setlocale(LC_ALL, 'en_US.UTF8');

  if(!empty($_POST['idf'])){
    $idfcom = $_POST['idf'];
  }else{
    $charset='utf-8'; // o 'UTF-8'
    $str = iconv($charset, 'ASCII//TRANSLIT', $pais);
    $pais = preg_replace("/[^a-zA-Z0-9]/", '', $str);

    $paisDigitos = strtoupper(substr($pais, 0, 3));
    $formatoFecha = date("d/m/Y", $fecha);
    $fechaDigitos = substr($formatoFecha, -2);
    $contador = 1;
    $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
    //$numero =  strlen($contador);

    $idfcom = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;

    while ($datos_com = mysql_fetch_assoc($ejecutar)) {
      if($datos_com['idf'] == $idfcom){
        //echo "<b style='color:red'>es igual el COM con id: $datos_com[idf]</b><br>";
        $contador++;
        $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
        $idfcom = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;
      }/*else{
        echo "el id encontrado es: $datos_com[idf]<br>";
      }*/
      
    }
    //echo "se ha creado un nuevo idf de COM el cual es: <b>$idfcom</b>";
  }

  $logitud = 8;
  $psswd = substr( md5(microtime()), 1, $logitud);

/*  $idfoc = $_POST['idfoc'];
  $query = "SELECT idoc,idf FROM oc WHERE idf = '$idfoc'";
  $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
  $oc = mysql_fetch_assoc($ejecutar);*/


  $insertSQL = sprintf("INSERT INTO com (idf, nombre, password, abreviacion, sitio_web, email, telefono, pais, direccion, fecha_inclusion, idoc, direccion_fiscal, rfc, ruc, ciudad) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($idfcom, "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($psswd, "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['direccion_oficinas'], "text"),
                       GetSQLValueString($_POST['fecha_inclusion'], "text"),
                       GetSQLValueString($_POST['idoc'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"));


  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());


        $destinatario = $_POST['email'];
        $asunto = "D-SPP Datos de Usuario"; 


    $mensaje = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
                <tr>
                  <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><strong style="color:#27ae60;">Nuevo Registro / New Register</strong></th>
                </tr>
                <tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder inicia sesión</b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información COM, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
                </tr>
                <tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/?COM" target="_new">www.d-spp.org/?COM</a></i>, once you have logged you are advised to change your password on the Information COM section, in that section are data which can be modified if be necessary.</td>
                </tr>
            <tr>
              <td align="left"><br><b>Nombre de la Empresa / Company Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span></td>
            </tr>
            <tr>
              <td align="left"><br><b>#SPP:</b> <span style="color:#27ae60;">'.$idfcom.'</span></td>
            </tr>
            <tr>
              <td align="left"><b>Contraseña / Password:</b> <span style="color:#27ae60;">'.$psswd.'</span></td>
            </tr>
            <tr>
              <td>Cualquier duda escribir a / Any questions write to : <u style="color:#27ae60;">cert@spp.coop</u></td>
            </tr>
          </tbody>
        </table>

      </body>
      </html>
    ';

        $mail->AddAddress($destinatario);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($mensaje);
        $mail->MsgHTML(utf8_decode($mensaje));
        if($mail->Send()){
          echo "<script>location.href='main_menu.php?COM&add&mensaje=COM agregado correctamente';</script>";
        }else{
          echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
        }

      $mail->ClearAddresses();


  /*$insertGoTo = "main_menu.php?COM&add&mensaje=COM agregado correctamente";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));*/
}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc where idoc = $_SESSION[idoc] ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);


?>
<br>
<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table class="table col-xs-8">
    <tr valign="baseline">
      <th colspan="2" class="alert alert-warning">El #SPP y la contraseña son proporcionados por D-SPP, dichos datos son enviados por email al COM</th>
    </tr>
    <!--<tr valign="baseline">
      <th nowrap align="left">Password</th>
      <td><input required="required" class="form-control" type="text" name="password" value="" size="32"></td>
    </tr>-->
    <tr valign="baseline">
      <th nowrap align="left">#SPP <br>(En caso de contar con uno)</th>
      <td><input class="form-control" type="text" id="idf" name="idf" value="" size="32"></td>
    </tr>

    <tr valign="baseline">
      <th nowrap align="left">Nombre</th>
      <td><input required="required" class="form-control" type="text" name="nombre" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Abreviacion</th>
      <td><input class="form-control" type="text" name="abreviacion" value="" size="32" required></td>
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
      <td><input class="form-control" type="email" name="email" value="" size="32" required></td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Pais</th>
      <td>
        <select required class="form-control" name="pais">
        <option value="">Selecciona</option>
        <?php 
          do {  
          ?>
          <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>">
            <?php echo utf8_encode($row_pais['nombre']);?>
          </option>
          <?php
          } while ($row_pais = mysql_fetch_assoc($pais));
        ?>
        </select>
      </td>
    </tr>
    <tr valign="baseline">
      <th nowrap align="left">Ciudad</th>
      <td>
        <input class="form-control" type="text" name="ciudad" value="" size="32">
    </tr>    
    <tr valign="baseline">
      <th nowrap align="left">IDF OC</th>
      <td>
        <input required class="form-control" type="text" name="idf_oc" value="<?php echo $row_oc['abreviacion']?>" size="32" disabled>
    </tr>
  
    <tr valign="baseline">
      <th nowrap align="left">Direccion_Oficinas</th>
      <td><input class="form-control" type="text" name="direccion_oficinas" value="" size="32"></td>
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
      <th nowrap align="left">RUC</th>
      <td><input class="form-control" type="text" name="ruc" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input class="btn btn-primary" type="submit" value="Agregar Empresa"></td>
    </tr>
  </table>
  <input type="hidden" name="fecha_inclusion" value="<?php echo time();?>">
  <input type="hidden" name="idoc" value="<?php echo $_SESSION['idoc'];?>">
  <input type="hidden" name="MM_insert" value="form1">
</form>

<?
mysql_free_result($pais);

mysql_free_result($oc);
?>