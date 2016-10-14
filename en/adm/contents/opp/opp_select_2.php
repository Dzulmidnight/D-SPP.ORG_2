<?php 
require_once('../Connections/dspp.php'); 

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

if(isset($_POST['opp_delete'])){
  $query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 40;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);

if(isset($_GET['query'])){
  $query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE idoc = $_GET[query] AND (opp.estado IS NULL OR opp.estado != 'ARCHIVADO') ORDER BY opp.idopp ASC";

  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE idoc = $_GET[query] AND (opp.estado IS NULL OR opp.estado != 'ARCHIVADO') ORDER BY opp.idopp ASC";



}else if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1"){
  $palabraClave = $_POST['palabraClave'];

  $query_opp = "SELECT *, opp.idopp AS 'idOPP' ,opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP' , opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (opp.estado != 'ARCHIVADO' OR opp.estado IS NULL) AND ((idf LIKE '%$palabraClave%') OR (opp.nombre LIKE '%$palabraClave%') OR (opp.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%')) ORDER BY opp.idopp ASC";

  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE (opp.estado != 'ARCHIVADO' OR opp.estado IS NULL) AND ((opp.idf LIKE '%$palabraClave%') OR (opp.nombre LIKE '%$palabraClave%') OR (opp.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%')) ORDER BY opp.idopp ASC";



}else if(isset($_POST['busquedaPais']) && $_POST['busquedaPais'] == 1){
  $pais = $_POST['nombrePais'];

  $query_opp = "SELECT *, opp.idopp AS 'idOPP' ,opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP' , opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais' ORDER BY opp.idopp ASC";

  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.pais = '$pais' ORDER BY opp.idopp ASC";

}else if(isset($_POST['busquedaOC']) && $_POST['busquedaOC'] == 1){
  $idoc = $_POST['idoc'];
  if($idoc == "sinOC"){
    $query_opp = "SELECT *, opp.idopp AS 'idOPP' ,opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP' , opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc IS NULL || opp.idoc = '' ORDER BY opp.idopp ASC";

    $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.idoc IS NULL || opp.idoc = '' ORDER BY opp.idopp ASC";

  }else{
    $query_opp = "SELECT *, opp.idopp AS 'idOPP' ,opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP' , opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = '$idoc' ORDER BY opp.idopp ASC";

    $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.idoc = '$idoc' ORDER BY opp.idopp ASC";
  }

}else if(isset($_POST['busquedaEstatus']) && $_POST['busquedaEstatus'] == 1){
  $estatus = $_POST['estatus'];

  $query_opp = "SELECT *, opp.idopp AS 'idOPP' ,opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP' , opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado = '$estatus' ORDER BY opp.idopp ASC";

  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.estado = '$estatus' ORDER BY opp.idopp ASC";

}else{
  $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion, opp.pais, opp.estatus_publico, opp.estatus_dspp, estatus_publico.nombre AS 'nombre_publico', estatus_dspp.nombre AS 'nombre_dspp' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp";


  //$query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', opp.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN status_pagina ON opp.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON opp.estatusPublico = status_publico.idstatus_publico LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (opp.estado IS NULL) OR (opp.estado != 'ARCHIVADO') ORDER BY opp.idopp ASC";
  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE (opp.estado IS NULL) OR (opp.estado != 'ARCHIVADO') ORDER BY opp.idopp ASC";

}

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());



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


$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);


$timeActual = time();

  if(isset($_POST['archivar']) && $_POST['archivar'] == 1){

    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {
        $query = "UPDATE opp SET estado = 'ARCHIVADO' WHERE idopp = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
  
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?OPP&select";</script>';
  }
  if(isset($_POST['eliminar']) && $_POST['eliminar'] == 2){
    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {
        $query = "DELETE FROM opp WHERE idopp = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?OPP&select";</script>';

  }

  if(isset($_POST['actualizacionOPP']) && $_POST['actualizacionOPP'] == 1){/* INICIA BOTON ACTUALIZAR LISTA OPP*/

    $row_opp = mysql_query("SELECT * FROM opp",$dspp) or die(mysql_error());
    $cont = 1;
    $fecha = time();

    while($datosOPP = mysql_fetch_assoc($row_opp)){
      //$nombre = "estatusPagina"+$datosOPP['idopp']+"";

      if(isset($_POST['estatusPagina'.$datosOPP['idopp']])){/*********************************** INICIA ESTATUS PAGINA DEL OPP ******************/
        $estatusPagina = $_POST['estatusPagina'.$datosOPP['idopp']];

        if(!empty($estatusPagina)){
          $query = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datosOPP[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

          //echo "cont: $cont | id($datosOPP[idopp]): $estatusPagina<br>";
        }      
      }/*********************************** TERMINA ESTATUS PAGINA DEL OPP ****************************************************/


      if(isset($_POST['estatusInterno'.$datosOPP['idopp']])){/*********************************** INICIA ESTATUS INTERNO DEL OPP ******************/
        $estatusInterno = $_POST['estatusInterno'.$datosOPP['idopp']];

        if(!empty($estatusInterno)){
          /*
          ESTATUS PAGINA = 
          1.- EN REVISION
          2.- CERTIFICADA
          3.- REGISTRADA
          4.- CANCELADA
          */
          $estatusPagina = "";
          if($estatusInterno == 10){ //ESTATUS PAGINA = CERTIFICADO(REGISTRADO)
            $estatusPagina = 2;
          }else if($estatusInterno == 14 || $estatusInterno == 24){ // ESTATUS PAGINA = CANCELADO
            $estatusPagina = 4;
          }else{ // ESTATUS PAGINA = EN REVISION
            $estatusPagina = 1;
          }

          $query = "UPDATE opp SET estatusInterno = $estatusInterno, estatusPagina = $estatusPagina WHERE idopp = $datosOPP[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datosOPP[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datosOPP[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS INTERNO DEL OPP ****************************************************/



      if(isset($_POST['estatusPublico'.$datosOPP['idopp']])){/*********************************** INICIA ESTATUS PUBLICO DEL OPP ******************/
        $estatusPublico = $_POST['estatusPublico'.$datosOPP['idopp']];

        if(!empty($estatusPublico)){

          $query = "UPDATE opp SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idopp = $datosOPP[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datosOPP[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datosOPP[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS PUBLICO DEL OPP ****************************************************/



      if(isset($_POST['numero_socios'.$datosOPP['idopp']])){/*********************************** INICIA NUMERO DE SOCIOS DEL OPP ******************/
        $numero_socios = $_POST['numero_socios'.$datosOPP['idopp']];

        if(!empty($numero_socios)){
          $consultar = mysql_query("SELECT idopp,socios, fecha_captura FROM numero_socios WHERE idopp = $datosOPP[idopp] ORDER BY fecha_captura DESC LIMIT 1",$dspp) or die(mysql_error());
          $consultaNumeroSocios = mysql_fetch_assoc($consultar);

          if($consultaNumeroSocios['socios'] != $numero_socios){

            $query = "INSERT INTO numero_socios(idopp,socios,fecha_captura) VALUES($datosOPP[idopp], $numero_socios, $fecha)";
            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          }

        }      
      }/*********************************** TERMINA NUMERO DE SOCIOS DEL OPP ****************************************************/


      if(isset($_POST['idf'.$datosOPP['idopp']])){/*********************************** INICIA NUMERO #SPP DEL OPP ******************/
        $idf = $_POST['idf'.$datosOPP['idopp']];

        if(!empty($idf)){
          $query = "UPDATE opp SET idf = '$idf' WHERE idopp = $datosOPP[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        }      
      }/*********************************** TERMINA NUMERO #SPP DEL OPP ****************************************************/




      if(isset($_POST['finCertificado'.$datosOPP['idopp']])){ /****************** INICIA VIGENCIA FIN DEL CERTIFICADO ******************/
        $finCertificado = $_POST['finCertificado'.$datosOPP['idopp']];
        $timeActual = time();

        $timeVencimiento = strtotime($finCertificado);
        $timeRestante = ($timeVencimiento - $timeActual);
        $estatusCertificado = "";
        $plazo = 60 *(24*60*60);
        $plazoDespues = ($timeVencimiento + $plazo);
        $prorroga = ($timeVencimiento + $plazo);
            // Calculamos el número de segundos que tienen 60 días

        if(!empty($finCertificado)){ // NO SE INGRESO NINGUNA FECHA

          $row_certificado = mysql_query("SELECT * FROM certificado WHERE idopp = '$datosOPP[idopp]'", $dspp) or die(mysql_error()); // CONSULTO SI EL OPP CUENTA CON ALGUN REGISTRO DE CERTIFICADO
          $totalCertificado = mysql_num_rows($row_certificado);
          
          if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
            //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idopp = $datosOPP[idopp]";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatusCertificado = 16; // AVISO DE RENOVACIÓN
              }else{
                $estatusCertificado = 10; // CERTIFICADO ACTIVO
              }
            }else{
              if($prorroga >= $timeActual){
                $estatusCertificado = 12; // CERTIFICADO POR EXPIRAR
              }else{
                $estatusCertificado = 11; // CERTIFICADO EXPIRADO
              }
            }

              $actualizar = "UPDATE opp SET estado = '$estatusCertificado' WHERE idopp = '$datosOPP[idopp]'";
              $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

              $query = "UPDATE certificado SET status = '$estatusCertificado', vigenciafin = '$finCertificado' WHERE idopp = '$datosOPP[idopp]'";
              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datosOPP[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

          }else{ // SI NO CUENTA CON REGISTRO PREVIO, ENTONCES INSERTO UN NUEVO REGISTRO
            //$query = "INSERT INTO certificado(vigenciafin,idopp) VALUES('$vigenciafin',$datosOPP[idopp])";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/
            /*if($timeVencimiento > $timeActual){
              if($timeRestante <= $plazo){
                $estatusCertificado = 16; // AVISO DE RENOVACIÓN DEL CERTIFICADO, 1º VEZ
              }else{
                $estatusCertificado = 10; // CERTIFICADA, 1º VEZ
              }
            }else{
              $estatusCertificado = 28;
            }*/
            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatusCertificado = 16; // AVISO DE RENOVACIÓN
              }else{
                $estatusCertificado = 10; // CERTIFICADO ACTIVO
              }
            }else{
              if($prorroga >= $timeActual){
                $estatusCertificado = 12; // CERTIFICADO POR EXPIRAR
              }else{
                $estatusCertificado = 11; // CERTIFICADO EXPIRADO
              }
            }

              $actualizar = "UPDATE opp SET estado = '$estatusCertificado' WHERE idopp = '$datosOPP[idopp]'";
              $ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());

              $query = "INSERT INTO certificado(status, vigenciafin, idopp) VALUES('$estatusCertificado', '$finCertificado', '$datosOPP[idopp]')";
              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datosOPP[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


          }

          //echo "cont: $cont | VIGENCIA FIN($datosOPP[idopp]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
        }      
      }/************************************ TERMINA VIGENCIA FIN DEL CERTIFICADO ***********************************/


      if(isset($_POST['ocAsignado'.$datosOPP['idopp']])){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/
        $ocAsignado = $_POST['ocAsignado'.$datosOPP['idopp']];
        if(!empty($ocAsignado)){
          $update = "UPDATE opp SET idoc = '$ocAsignado' WHERE idopp = '$datosOPP[idopp]'";
          $ejecutar = mysql_query($update,$dspp) or die(mysql_error());
        }
      } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/

      $cont++;
    }
    
    echo '<script>location.href="?OPP&select";</script>';
  } /* TERMINA BOTON ACTUALIZAR LISTA OPP*/

  $rowOPP = mysql_query("SELECT * FROM opp",$dspp) or die(mysql_error());
    $estatus_publico = "";


  while ($actualizarOPP = mysql_fetch_assoc($rowOPP)) {

    if($actualizarOPP['estatus_interno'] == 10){ //ESTATUS PAGINA = CERTIFICADO(REGISTRADO)
      $estatus_publico = 2;
    }else if($actualizarOPP['estatus_interno'] == 14 || $actualizarOPP['estatus_interno'] == 24){ // ESTATUS PAGINA = CANCELADO
      $estatus_publico = 3;
    }else{ // ESTATUS PAGINA = EN REVISION
      $estatus_publico = 1;
    }
      
    $query = "UPDATE opp SET estatus_publico = $estatus_publico WHERE idopp = $actualizarOPP[idopp]";
    $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

  }

 ?>

  <hr>
  <div class="row">
    <div class="col-xs-6">
      <p class="alert alert-info" >Busqueda extendida(idf, nombre, abreviacion, sitio web, email, país, etc...). Sensible a acentos.</p>

      <form method="post" name="filtro" action="" enctype="application/x-www-form-urlencoded">
        <div class="input-group">
          <input type="text" class="form-control" name="palabraClave" placeholder="Palabra clave...">
          <span class="input-group-btn">
            <input type="hidden" name="filtroPalabra" value="1">
            <button class="btn btn-default" type="submit">Buscar !</button>
          </span>
        </div><!-- /input-group -->        
      </form>
    </div><!-- /.col-lg-6 -->
    <div class="col-xs-6">
      <div class="alert alert-warning col-xs-12">
        Consulta OPP  | 
        <span> 
          <?php 
            $ejecutar = mysql_query($query_opp,$dspp) or die(mysql_error());
            $totalOPP = mysql_num_rows($ejecutar);
          ?>
            Total OPP(s): <strong style="color:red"><?php echo $totalOPP; ?></strong>

        </span>
      </div>
      <div class="col-lg-6 col-xs-12">
        <form action="" name="formularioPais" method="POST" enctype="application/x-www-form-urlencoded">
          <?php 
          $row_paises = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
           ?>
          <select name="nombrePais" id="" onchange="document.formularioPais.submit()">
            <option value="">Buscar País</option>
            <?php 
            while($datosPais = mysql_fetch_assoc($row_paises)){
            ?>
            <option class="form-control" value="<?php echo utf8_encode($datosPais['nombre']);?>" ><?php echo utf8_encode($datosPais['nombre']);?></option>
            <?php
            }
             ?>
          </select>
          <input type="hidden" name="busquedaPais" value="1">
        </form>
      </div>

      <div class="col-lg-6 col-md-12 col-xs-6">
        <form action="" name="formularioOC" method="POST" enctype="application/x-www-form-urlencoded">
          <?php 
          $row_oc = mysql_query("SELECT * FROM oc",$dspp) or die(mysql_error());
           ?>
          <select name="idoc" id="" onchange="document.formularioOC.submit()">
            <option value="">Buscar OC</option>
            <option value="sinOC">SIN OC</option>
            <?php 
            while($datosOC = mysql_fetch_assoc($row_oc)){
            ?>
            <option class="form-control" value="<?php echo $datosOC['idoc'];?>" ><?php echo $datosOC['abreviacion'];?></option>
            <?php
            }
             ?>
          </select>
          <input type="hidden" name="busquedaOC" value="1">
        </form>
      </div>

      <div class="col-lg-6 col-md-12 col-xs-6">
        <form action="" name="formularioEstatus" method="POST" enctype="application/x-www-form-urlencoded">
          <select name="estatus" id="" onchange="document.formularioEstatus.submit()">
            <option value="">ESTATUS CERTIFICADO</option>
            <option value="10">CERTIFICADA</option>
            <option value="16">AVISO DE RENOVACIÓN</option>
            <option value="12">CERTIFICADO POR EXPIRAR</option>
            <option value="11">CERTIFICADO EXPIRADO</option>
          </select>
          <input type="hidden" name="busquedaEstatus" value="1">
        </form>
      </div>

    </div>


  </div>


  <hr>

  <a class="btn btn-sm btn-warning" href="?OPP&filed">OPP(s) Archivado(s)</a>


<!--<div class="panel panel-default fluid">-->
  <!--<div class="panel-heading">-->
    <div style="display:inline;margin-right:10em;">
      <button class="btn btn-sm btn-success" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
    </div>
    <div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>
    </div>
 
  <!--</div>-->

  <form name="formulario1" method="POST" action="../../reporte.php">
    <input type="hidden" name="contactoPDF" value="1">
    <input type="hidden" name="queryPDF" value="<?php echo $queryExportar; ?>">
  </form>
  <form name="formulario2" method="POST" action="../../reporte.php">
    <input type="hidden" name="contactoExcel" value="2">
    <input type="hidden" name="queryExcel" value="<?php echo $queryExportar; ?>">
  </form>
  
  <!--<div class="panel-body">-->
  <table class="table table-condensed table-bordered table-hover">
    <thead style="font-size:10px;">
      <tr>
        <th class="text-center">#SPP</th>
        <th class="text-center">Estatus Pagina</th>
        <th class="text-center">Estatus Publico</th>
        <th class="text-center">Estatus Interno<br><span style="color:#e74c3c">(Define el Estatus Pagina)</span></th>
        <th class="text-center">Estatus Certificado</th>
        <th class="text-center" style="width:90px;">Vigencia Fin</th>
        <th class="text-center" style="width:200px;">Nombre</th>
        <th class="text-center">Abreviación</th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <!--<th class="text-center">Email OPP</th>-->
        <th class="text-center">País</th>
        <th class="text-center">Nº Socios</th>
        <th class="text-center">OC</th>
        <!--<th class="text-center">Razón social</th>
        <th class="text-center">Dirección fiscal</th>
        <th class="text-center">RFC</th>-->
        <!--<th class="text-center">Eliminar</th>-->
        <!--<th class="text-center">Acciones</th>-->
        <th style="width:60px;">
          <form  style="margin: 0;padding: 0;" action="" method="POST" >            
              <button class="btn btn-xs btn-danger" type="subtmit" value="2"  name="eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" >
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
              </button>        
              <button class="btn btn-xs btn-info" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
                <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
              </button> 
          </form>
        </th>
      </tr>      
    </thead>
    <form name="formularioActualizar" id="formularioActualizar" action="" method="POST"><!-- INICIA FORM -->
      <input type="hidden" name="actualizacionOPP" value="1">
      <tbody style="font-size:10px">
        <?php 
          $contador=0; 

          while ($row_opp = mysql_fetch_assoc($opp)) {
          $contador++; 

          $rowEstatusPagina = mysql_query("SELECT * FROM status_pagina",$dspp) or die(mysql_error());
        
        ?>

          <tr>
            
              <td>
                <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $row_opp['idOPP']; ?>&contact">Consultar<br>
                  <!--<?php echo "<br>IDOPP: ".$row_opp['idOPP']; ?>-->
                </a>
                <input type="text" name="idf<?echo $row_opp['idOPP']?>" value="<?php echo $row_opp['idf']; ?>" placeholder="#SPP">
              </td>
              <td>
                <?php 
                if(!empty($row_opp['nombreEstatusPagina'])){
                  if($row_opp['estatusPagina'] == 4){
                    echo "<p class='text-center alert alert-danger' style='padding:7px;'>".$row_opp['nombreEstatusPagina']."</p>";
                  }else if($row_opp['estatusPagina'] == 2){
                    echo "<p class='text-center alert alert-success' style='padding:7px;'>".$row_opp['nombreEstatusPagina']."</p>";
                  }else{
                    echo "<p class='text-center alert alert-warning' style='padding:7px;'>".$row_opp['nombreEstatusPagina']."</p>";
                  }
                  
                }
                 ?>            
              </td>
              <td>
                <?php 
                if(empty($row_opp['estatusPublico'])){
                ?>
                  <select name="estatusPublico<?echo $row_opp['idOPP']?>" id="estatusPublico">
                    <option value="">---</option>
                    <?php include("../option_estadoPublico.php"); ?>
                  </select>
                <?php
                }else{
                  echo "<p class='alert alert-info' style='padding:7px;'>".$row_opp['nombreEstatusPublico']."</p>";
                }
                 ?>
              </td>
              <td>
                <?php 
                  $estatusInterno = mysql_query("SELECT opp.idopp, opp.estatusInterno, status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estatusInterno = status.idstatus  WHERE idopp = $row_opp[idOPP]",$dspp) or die(mysql_error());
                  $row_estatus = mysql_fetch_assoc($estatusInterno);
                  if(!empty($row_estatus['estatusInterno'])){
                  ?>
                    <select name="estatusInterno<?echo $row_opp['idOPP']?>" id="estatusInterno">
                      <option value="">....</option>
                      <option value="21">1ra Evaluación</option>
                      <option value="23">Completar Información</option>
                      <option value="22">2ª Revisión</option>
                      <option value="4">Proceso Interrumpido</option>
                      <option value="5">Evaluación In Situ</option>
                      <option value="6">Informe de Evaluación</option>
                      <option value="7">Acciones Correctivas</option>
                      <option value="8">Dictamen Positivo</option>
                      <option value="9">Dictamen Negativo</option>
                      <option value="14">Cancelada</option>
                    </select>
                  <?php
                    echo "<p class='alert alert-info text-center' style='padding:7px;'>".$row_estatus['nombreStatus']."</p>";
                  }else{
                  ?>
                  <select name="estatusInterno<?echo $row_opp['idOPP']?>" id="estatusInterno">
                    <option value="">---</option>
                    <?php include('../option_estados_adm.php'); ?>
                  </select>
                  <?php
                  }
                  
                ?>          
              </td>
              <td style="width:150px;">
                <?php 
                  if(isset($row_opp['nombreStatus'])){
                    if($row_opp['estado'] == 10){
                      echo "<input type='text' class='informacion text-center alert alert-success' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // CERTIFICADO ACTIVO
                    }
                    if($row_opp['estado'] == 11){
                      echo "<input type='text' class='informacion text-center alert alert-danger' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // CERTIFICADO EXPIRADO
                    }
                    if($row_opp['estado'] == 12){
                      echo "<input type='text' class='informacion text-center alert alert-warning' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // CERTIFICADO POR EXPIRAR
                    }
                    if($row_opp['estado'] == 16){
                      echo "<input type='text' class='informacion text-center alert alert-info' style='padding:7px;' value='$row_opp[nombreStatus]'>"; // AVISO DE RENOVACIÓN
                    }
                  }
                 ?>
              </td>
              <td>
                <?php 
                  $vigenciafin = date('d-m-Y', strtotime($row_opp['vigenciafin']));
                  $timeVencimiento = strtotime($row_opp['vigenciafin']);
                  $timeRestante = ($timeVencimiento - $timeActual);

                  if(isset($row_opp['vigenciafin'])){
                    if($timeVencimiento < $timeActual){
                      $alerta = "alert alert-danger";
                    }else{
                      $alerta = "alert alert-success";
                    }
                    echo "<input type='date' name='finCertificado".$row_opp['idOPP']."' value='$row_opp[vigenciafin]' class='text-center'>";
                    echo "<p style='padding:7px;width:80px;' class='text-center $alerta'></p>";

                  }else{
                    echo "<input type='date' name='finCertificado".$row_opp['idOPP']."' value='$row_opp[vigenciafin]' class='text-center'>";
                  }
                ?>
              </td>
              <td>
                <?php 
                  if(isset($row_opp['nombreOPP'])){
                    echo "<p class='text-center'>".$row_opp['nombreOPP']."</p>";
                  }else{
                    echo "<p class='alert alert-danger'>No Disponible</p>";
                  } 
                ?>
              </td>
              <td>
                <?php 
                  if(isset($row_opp['abreviacion'])){
                    echo "<p class='text-center'>".$row_opp['abreviacion']."</p>";
                  }else{
                    echo "<p class='alert alert-danger'>No Disponible</p>";
                  } 
                ?>
              </td>      
              <td>
                <?php echo $row_opp['pais']; ?>
              </td>
              <td>
                <?php 
                $numero_socios = mysql_query("SELECT idopp,socios FROM numero_socios WHERE idopp = $row_opp[idOPP] ORDER BY idnumero_socios DESC",$dspp) or die(mysql_error());
                $row_socios = mysql_fetch_assoc($numero_socios);
                if(empty($row_socios['socios'])){
                ?>
                 <input type="text" name="numero_socios<?echo $row_opp['idOPP']?>" class="" style="width:100px;">
                <? }else{ ?>
                <input type="text" name="numero_socios<?echo $row_opp['idOPP']?>" value="<?php echo $row_socios['socios']?>" class="" style="width:100px;">
                <?
                }
                 ?>
              </td>

              <td><!-- INICIA OC AL QUE PERTENECE EL OPP-->
                <?
                  $query_topp = "SELECT abreviacion FROM oc where idoc='".$row_opp['idoc']."'";
                  $topp = mysql_query($query_topp, $dspp) or die(mysql_error());
                  $row_topp = mysql_fetch_assoc($topp);
                ?>
                <?php if(isset($row_topp['abreviacion'])){ ?>
                  <a style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_opp['idoc']; ?>&contact">
                    <?php  echo "<p class='alert alert-success' style='padding:7px;'>".$row_topp['abreviacion']."</p>"; ?>
                  </a>
                <?php }else{ 
                  $row_oc = mysql_query("SELECT idoc,nombre,abreviacion FROM oc",$dspp) or die(mysql_error());
                  ?>   
                    <select name="ocAsignado<?echo $row_opp['idOPP']?>" id="">
                      <option value="">SELECCIONA UNA OC</option>
                    <?php 
                    while($listaOC = mysql_fetch_assoc($row_oc)){
                      echo "<option value='$listaOC[idoc]'>$listaOC[abreviacion]</option>";
                    }
                    ?>
                    </select>
                <?php } ?>
              </td><!-- TERMINA OC AL QUE PERTENECE EL OPP-->
          
             
            <!--02/06</td>-->
            <td class="text-center">

              <div name="formulario">
                <input type="checkbox" name="idoppCheckbox" id="<?php echo "idopp".$contador; ?>" value="<?php echo $row_opp['idOPP'] ?>" onclick="addCheckbox()">
              </div>
            </td>

          </tr>
          <?php }  ?>
          <? if($contador==0){?>
          <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
          <? }?>
      </tbody>
    </form><!-- TERMINA FORM -->
    
  </table>


  <!--</div>-->
<!--</div>-->




  <input type="hidden" name="prueba2" value="2">
  <table>
    <tr>
    <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
    <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not first page ?></td>
    <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>" >
    <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not first page ?></td>
    <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
    <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
    <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not last page ?></td>
    <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
    <a  href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>" >
    <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not last page ?></td>
    </tr>
  </table>

<?php
mysql_free_result($opp);
?>


<script language="JavaScript"> 

var contadorPHP = 'qwerty';
var miVariable = [];
var idopp = '';


function addCheckbox(){
  var cont = 0;
  var checkboxIdopp = document.getElementsByName("idoppCheckbox");
//var precio=document.getElementById('precio').value;

  for (var i=0; i<checkboxIdopp.length; i++) {
    if (checkboxIdopp[i].checked == 1) { 
      //alert("EL VALOR ES: "+checkboxIdopp[i].value); 
      //cont = cont + 1; 
      idopp = checkboxIdopp[i].value; 
      sessionStorage[idopp] = idopp; 

    }

  }

  for(var i=0;i<sessionStorage.length;i++){
    var idopp=sessionStorage.key(i);
    miVariable[i] = idopp;
    document.cookie = 'variable='+miVariable;
  }
}



function mostrarDatos(){
  var datosDisponibles=document.getElementById('datosDisponibles');
  datosDisponibles.innerHTML='';
  for(var i=0;i<sessionStorage.length;i++){
    var idopp=sessionStorage.key(i);
    var variablePHP = "<?php $otraVariable = 6; ?>";
    datosDisponibles.innerHTML += '<div>'+idopp+'</div>';
  }
 
}

function limpiarVista() {
var datosDisponibles=document.getElementById('datosDisponibles');
datosDisponibles.innerHTML='Limpiada vista. Los datos permanecen.';
}
 
function borrarTodo() {
  var cookies = document.cookie.split(";");

  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i];
    var eqPos = cookie.indexOf("=");
    var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
  }
  sessionStorage.clear();  

}


function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?, los datos se eliminaran permanentemen')){ 
       return false; } 
} 

function guardarDatos(){
  document.getElementById("formularioActualizar").submit();
}


</script>