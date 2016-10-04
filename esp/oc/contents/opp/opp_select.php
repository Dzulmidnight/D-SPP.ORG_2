<?php 
require_once('../../Connections/dspp.php'); 

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


$fecha = time();
$idoc = $_SESSION['idoc'];

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_opp = "SELECT *, opp.idopp AS 'idopp', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus  LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp where idoc='".$_SESSION['idoc']."' ORDER BY opp.nombre ASC";
}else{
  $query_opp = "SELECT opp.*, estatus_interno.idestatus_interno, estatus_interno.nombre AS 'nombre_interno', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.estatus_certificado, estatus_publico.idestatus_publico, estatus_publico.nombre AS 'nombre_publico', num_socios.idnum_socios, num_socios.numero FROM opp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp WHERE opp.idoc = $_SESSION[idoc] ORDER BY opp.nombre ASC";
}



$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);

if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = ceil($totalRows_opp/$maxRows_opp)-1;

$queryString_opp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_opp") == false && 
        stristr($param, "totalRows_opp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_opp = "&" . htmlentities(implode("&", $newParams));
  }
}


if(isset($_POST['actualizacion_opp']) && $_POST['actualizacion_opp'] == 'actualizar_datos'){

    $row_opp = mysql_query("SELECT * FROM opp",$dspp) or die(mysql_error());
    $cont = 1;
    $fecha = time();

    while($datos_opp = mysql_fetch_assoc($row_opp)){
      //$nombre = "estatusPagina"+$datos_opp['idopp']+"";

      if(isset($_POST['estatus_interno'.$datos_opp['idopp']])){/*********************************** INICIA ESTATUS INTERNO DEL OPP ******************/
        $estatus_interno = $_POST['estatus_interno'.$datos_opp['idopp']];

        if(!empty($estatus_interno)){
          /*
          ESTATUS PAGINA = 
          1.- EN REVISION
          2.- CERTIFICADA
          3.- REGISTRADA
          4.- CANCELADA
          */
          $estatus_publico = "";
          if($estatus_interno == 10){ // CANCELADO
            $estatus_publico = 3; //cancelado
          }else{ // ESTATUS PAGINA = EN REVISION
            $estatus_publico = 1; //en revision
          }
          $updateSQL = sprintf("UPDATE opp SET estatus_interno = %s, estatus_publico = %s WHERE idopp = %s",
            GetSQLValueString($estatus_interno, "int"),
            GetSQLValueString($estatus_publico, "int"),
            GetSQLValueString($datos_opp['idopp'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datos_opp[idopp]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_opp[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS INTERNO DEL OPP ****************************************************/


      if(isset($_POST['estatus_publico'.$datos_opp['idopp']])){/*********************************** INICIA ESTATUS PUBLICO DEL OPP ******************/
        $estatus_publico = $_POST['estatusPublico'.$datos_opp['idopp']];

        if(!empty($estatusPublico)){

          $query = "UPDATE opp SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idopp = $datos_opp[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datos_opp[idopp]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_opp[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS PUBLICO DEL OPP ****************************************************/



      


      if(isset($_POST['num_socios'.$datos_opp['idopp']])){/*********************************** INICIA NUMERO DE SOCIOS DEL OPP ******************/
        $num_socios = $_POST['num_socios'.$datos_opp['idopp']];


        if(!empty($num_socios)){
          $row_socios = mysql_query("SELECT idopp, numero FROM num_socios WHERE idopp = ".$datos_opp['idopp']."", $dspp) or die(mysql_error());
          $total = mysql_num_rows($row_socios);

          if($total == 0){
            $insertSQL = sprintf("INSERT INTO num_socios(idopp, numero, fecha_registro) VALUES (%s, %s, %s)",
              GetSQLValueString($datos_opp['idopp'], "int"),
              GetSQLValueString($num_socios, "int"),
              GetSQLValueString($fecha, "int"));
            $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

          }else{
            $updateSQL = sprintf("UPDATE num_socios SET numero = %s, fecha_registro = %s WHERE idopp = %s",
              GetSQLValueString($num_socios, "int"),
              GetSQLValueString($fecha, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $insertar = mysql_query($updateSQL, $dspp) or die(mysql_error());
          }
        }      
      }/*********************************** TERMINA NUMERO DE SOCIOS DEL OPP ****************************************************/


      if(isset($_POST['idf'.$datos_opp['idopp']])){/*********************************** INICIA NUMERO #SPP DEL OPP ******************/
        $idf = $_POST['idf'.$datos_opp['idopp']];

        if(!empty($idf)){
          $query = "UPDATE opp SET idf = '$idf' WHERE idopp = $datos_opp[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        }      
      }/*********************************** TERMINA NUMERO #SPP DEL OPP ****************************************************/




      if(isset($_POST['vigencia_fin'.$datos_opp['idopp']])){ /****************** INICIA VIGENCIA FIN DEL CERTIFICADO ******************/
        $vigencia_fin = $_POST['vigencia_fin'.$datos_opp['idopp']];
        $timeActual = time();

        $timeVencimiento = strtotime($vigencia_fin);
        $timeRestante = ($timeVencimiento - $timeActual);
        $estatus_certificado = "";
        $plazo = 60 *(24*60*60);
        $plazoDespues = ($timeVencimiento - $plazo);
        $prorroga = ($timeVencimiento + $plazo);
            // Calculamos el número de segundos que tienen 60 días

        if(!empty($vigencia_fin)){ // NO SE INGRESO NINGUNA FECHA

          $row_certificado = mysql_query("SELECT * FROM certificado WHERE idopp = '$datos_opp[idopp]'", $dspp) or die(mysql_error()); // CONSULTO SI EL OPP CUENTA CON ALGUN REGISTRO DE CERTIFICADO
          $totalCertificado = mysql_num_rows($row_certificado);
          
          if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
            //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idopp = $datos_opp[idopp]";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatus_certificado = 14; //estatus_dspp AVISO DE RENOVACIÓN
              }else{
                $estatus_certificado = 13; //estatus_dspp CERTIFICADO ACTIVO
              }
            }else{
              if($prorroga >= $timeActual){
                $estatus_certificado = 15; //estatus_dspp CERTIFICADO POR EXPIRAR
              }else{
                $estatus_certificado = 16; //estatus_dspp CERTIFICADO EXPIRADO
              }
            }

            $updateSQL = sprintf("UPDATE opp SET estatus_opp = %s WHERE idopp = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

            $updateSQL = sprintf("UPDATE certificado SET estatus_certificado = %s, vigencia_fin = %s, entidad = %s WHERE idopp = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($vigencia_fin, "text"),
              GetSQLValueString($idoc, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_opp[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

          }else{ // SI NO CUENTA CON REGISTRO PREVIO, ENTONCES INSERTO UN NUEVO REGISTRO
            //$query = "INSERT INTO certificado(vigenciafin,idopp) VALUES('$vigenciafin',$datos_opp[idopp])";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatus_certificado = 14; // AVISO DE RENOVACIÓN
              }else{
                $estatus_certificado = 13; // CERTIFICADO ACTIVO
              }
            }else{
              if($prorroga >= $timeActual){
                $estatus_certificado = 15; // CERTIFICADO POR EXPIRAR
              }else{
                $estatus_certificado = 16; // CERTIFICADO EXPIRADO
              }
            }

              $updateSQL = sprintf("UPDATE opp SET estatus_opp = %s WHERE idopp = %s",
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($datos_opp['idopp'], "int"));
              $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

              $insertSQL = sprintf("INSERT INTO certificado (idopp, entidad, estatus_certificado, vigencia_fin) VALUES (%s, %s, %s, %s)",
                GetSQLValueString($datos_opp['idopp'], "int"),
                GetSQLValueString($idoc, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($vigencia_fin, "text"));
              $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_opp[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


          }

          //echo "cont: $cont | VIGENCIA FIN($datos_opp[idopp]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
        }      
      }/************************************ TERMINA VIGENCIA FIN DEL CERTIFICADO ***********************************/


      if(isset($_POST['ocAsignado'.$datos_opp['idopp']])){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/
        $ocAsignado = $_POST['ocAsignado'.$datos_opp['idopp']];
        if(!empty($ocAsignado)){
          $update = "UPDATE opp SET idoc = '$ocAsignado' WHERE idopp = '$datos_opp[idopp]'";
          $ejecutar = mysql_query($update,$dspp) or die(mysql_error());
        }
      } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/

      $cont++;
    }
    
    echo '<script>location.href="?OPP&select";</script>';


}


$detalle_opp = mysql_query($query_opp,$dspp) or die(mysql_error());
$totalOPP = mysql_num_rows($detalle_opp);

$row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());

$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);
?>
<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
       return false; } 
} 
</script>
  <hr>
    <div style="display:inline;margin-right:10em;">
      <button class="btn btn-sm btn-primary" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
      | <span class="alert alert-warning" style="padding:7px;">Total OPP: <?php echo $totalOPP; ?></span>

    </div>

    <!--<div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>
    </div>-->


    <table class="table table-bordered table-condensed table-hover" style="font-size:11px;">
      <thead>
        <tr>
          <th class="text-center" style="width:100px;">#SPP</th>
          <th class="text-center" style="width:100px;">Nombre</th>
          <th class="text-center">Abreviación</th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Proceso de Certificación en el que se encuentra la OPP"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Proceso certificación</a></th>
          <th class="text-center">
            <a href="#" data-toggle="tooltip" title="Fecha en la que expira el Certificado"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Fecha Final<br>(Certificado)</a>
          </th>
          <th class="text-center"><a href="#" data-toggle="tooltip" title="Estatus del Certificado definido por la fecha de vigencia final">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Estatus Certificado</a>
          </th>

          <!--<th class="text-center">Abreviación</th>-->
          <th >Productos</th>
          <th class="text-center">Nº de Socios</th>
          <!--<th class="text-center">Email</th>
          <th class="text-center">Teléfono Oficinas</th>
          <th class="text-center">País</th>-->
          <!--<th class="text-center">OC</th>-->
          <!--<th class="text-center">Razón social</th>-->

          <!--<th class="text-center">Dirección fiscal</th>-->
          <!--<th class="text-center">RFC</th>-->
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <form name="formularioActualizar" id="formularioActualizar" action="" method="POST">
        <input type="hidden" name="actualizacion_opp" value="actualizar_datos">
        <tbody>
          <?php 
          if($totalOPP == 0){
            echo "<tr><td class='alert alert-info text-center' colspan='10'>No se encontraron registros</td></tr>";
          }else{
            while($opp = mysql_fetch_assoc($detalle_opp)){
            ?>
              <tr>
                <td>
                  <input type="text" name="spp<?php echo $opp['idopp']; ?>" value="<?php echo $opp['spp']; ?>">
                  <?php echo $opp['spp']; ?>
                </td>
                <td>
                  <?php echo $opp['nombre']; ?>
                </td>
                <td>
                  <?php echo $opp['abreviacion']; ?>
                </td>
                <td>
                  <select name="estatus_interno<?php echo $opp['idopp']; ?>">
                    <option>...</option>
                    <?php 
                    $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
                    while($estatus_interno = mysql_fetch_assoc($row_interno)){
                    ?>
                      <option value="<?php echo $estatus_interno['idestatus_interno'] ?>" <?php if($estatus_interno['idestatus_interno'] == $opp['estatus_interno']){echo "selected";} ?>><?php echo $estatus_interno['nombre']; ?></option>
                    <?php
                    }
                     ?>
                  </select>
                  <?php echo "<p class='alert alert-info' style='padding:7px;'>$opp[nombre_interno]</p>"; ?>
                </td>
                <td>
                  <?php 
                    $vigenciafin = date('d-m-Y', strtotime($opp['vigencia_fin']));
                    $timeVencimiento = strtotime($opp['vigencia_fin']);
                  
                   ?>
                  <input type="date" name="vigencia_fin<?php echo $opp['idopp']; ?>" value="<?php echo $opp['vigencia_fin']; ?>">
                </td>
                <td>
                  <?php echo $opp['estatus_certificado']; ?>
                </td>
                <td>
                  productos
                </td>
                <td>
                  <input type="number" name="num_socios<?php echo $opp['idopp']; ?>" value="<?php echo $opp['numero']; ?>">
                  <?php echo $opp['numero']; ?>
                </td>
                <td>
                  <button class="btn btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                </td>
              </tr>
            <?php
            }
          }
           ?>
        </tbody>
      </form>
    </table>



<table>
<tr>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>


<script>
function guardarDatos(){
  document.getElementById("formularioActualizar").submit();
}

</script>
