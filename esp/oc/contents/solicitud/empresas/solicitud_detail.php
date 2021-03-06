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

$fecha = time();
$idsolicitud_registro = $_GET['IDsolicitud_empresa'];
$charset='utf-8'; 
$ruta_croquis = "../../archivos/empresaArchivos/croquis/";
$spp_global = "cert@spp.coop";
$administrador = "yasser.midnight@gmail.com";

if(isset($_POST['actualizar_solicitud']) && $_POST['actualizar_solicitud'] == 1){

  /*
  SE ACTUALIZA LA SOLICITUD
  LA INFORMACION DE empresa
  NUMERO DE SOCIOS
  CONTACTOS
  PRODUCTOS
  CERTIFICACIONES
  */


  if(isset($_POST['op_preg12'])){
    $op_preg12 = $_POST['op_preg12'];
  }else{
    $op_preg12 = "";
  }

///CAPTURAMOS SI HUBO VENTAS ////
  if(isset($_POST['preg6'])){
    $preg6 = $_POST['preg6'];
  }else{
    $preg6 = "";
  }
  if(isset($_POST['preg13'])){
    $preg13 = $_POST['preg13'];
  }else{
    $preg13 = "";
  }
  if(isset($_POST['preg14'])){
    $preg14 = $_POST['preg14'];
  }else{
    $preg14 = "";
  }


  /*if(!empty($_FILES['op_preg15']['name'])){
      $_FILES["op_preg15"]["name"];
        move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
        $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
  }else{
    $croquis = NULL;
  }*/
  if(!empty($_POST['comprador'])){
    $comprador = $_POST['comprador'];
  }else{
    $comprador = '';
  }
  if(!empty($_POST['intermediario'])){
    $intermediario = $_POST['intermediario'];
  }else{
    $intermediario = '';
  }
  if(!empty($_POST['maquilador'])){
    $maquilador = $_POST['maquilador'];
  }else{
    $maquilador = '';
  }


  if(!empty($_POST['produccion'])){
    $produccion = $_POST['produccion'];
  }else{
    $produccion = '';
  }
  if(!empty($_POST['procesamiento'])){
    $procesamiento = $_POST['procesamiento'];
  }else{
    $procesamiento = '';
  }
  if(!empty($_POST['importacion'])){
    $importacion = $_POST['importacion'];
  }else{
    $importacion = '';
  }

  //SE DEBE COMPONER
  /*$preg9 = '';*/
  $preg9_actual = $_POST['preg9_actual'];
  if(!empty($_FILES['preg9']['name'])){
        if(file_exists($preg9_actual)){
          unlink($preg9_actual);
        }

      $_FILES["preg9"]["name"];
        move_uploaded_file($_FILES["preg9"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
        $preg9 = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
  }else{
    $preg9 = $preg9_actual;
  }


  // ACTUALIZAMOS LA INFORMACION DE LA SOLICITUD


  $updateSQL = sprintf("UPDATE solicitud_registro SET comprador_final = %s, intermediario = %s, maquilador = %s, preg1 = %s, preg2 = %s, preg3 = %s, preg4 = %s, produccion = %s, procesamiento = %s, importacion = %s, preg6 = %s, preg7 = %s, preg8 = %s, preg9 = %s, preg10 = %s, preg12 = %s, preg13 = %s, preg14 = %s, preg15 = %s WHERE idsolicitud_registro = %s",
        GetSQLValueString($comprador, "int"),
        GetSQLValueString($intermediario, "int"),
        GetSQLValueString($maquilador, "int"),
         GetSQLValueString($_POST['preg1'], "text"),
         GetSQLValueString($_POST['preg2'], "text"),
         GetSQLValueString($_POST['preg3'], "text"),
         GetSQLValueString($_POST['preg4'], "text"),
         GetSQLValueString($produccion, "int"),
         GetSQLValueString($procesamiento, "int"),
         GetSQLValueString($importacion, "int"),
         GetSQLValueString($preg6, "text"),
         GetSQLValueString($_POST['preg7'], "text"),
         GetSQLValueString($_POST['preg8'], "text"),
         GetSQLValueString($preg9, "text"),
         GetSQLValueString($_POST['preg10'], "text"),
         GetSQLValueString($_POST['preg12'], "text"),
         GetSQLValueString($preg13, "text"),
         //GetSQLValueString($preg14, "text"),
         GetSQLValueString($_POST['preg15'], "text"),
         GetSQLValueString($idsolicitud_registro, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());



  // ACTUALIZAMOS LA INFORMACION DE LA empresa
  $updateSQL = sprintf("UPDATE empresa SET nombre = %s, pais = %s, direccion_oficina = %s, email = %s, telefono = %s, sitio_web = %s, razon_social = %s, direccion_fiscal = %s, rfc = %s, ruc = %s, comprador = %s, intermediario = %s, maquilador = %s WHERE idempresa = $_POST[idempresa]",
    GetSQLValueString($_POST['nombre'], "text"),
    GetSQLValueString($_POST['pais'], "text"),
    GetSQLValueString($_POST['direccion_oficina'], "text"),
    GetSQLValueString($_POST['email'], "text"),
    GetSQLValueString($_POST['telefono'], "text"),
    GetSQLValueString($_POST['sitio_web'], "text"),
    GetSQLValueString($_POST['razon_social'], "text"),
    GetSQLValueString($_POST['direccion_fiscal'], "text"),
    GetSQLValueString($_POST['rfc'], "text"),
    GetSQLValueString($_POST['ruc'], "text"),
    GetSQLValueString($comprador, "int"),
    GetSQLValueString($intermediario, "int"),
    GetSQLValueString($maquilador, "int"),
    GetSQLValueString($_POST['idempresa'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());



  ////ACTUALIZAMOS LOS PORCENTAJES DE VENTAS
    if(isset($preg13) && $preg13 == "SI"){
      if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
        $updateSQL = sprintf("UPDATE porcentaje_productoVentas SET organico = %s, comercio_justo = %s, spp = %s, sin_certificado = %s WHERE idsolicitud_registro = %s",
          GetSQLValueString($_POST['organico'], "text"),
          GetSQLValueString($_POST['comercio_justo'], "text"),
          GetSQLValueString($_POST['spp'], "text"),
          GetSQLValueString($_POST['sin_certificado'], "text"),
          GetSQLValueString($idsolicitud_registro, "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
      }
    }  


  /*************************** INICIA INSERTAR CERTIFICACIONES ***************************/

    if(isset($_POST['certificadora'])){
      $certificadora = $_POST['certificadora'];
    }else{
      $certificadora = NULL;
    }

    if(isset($_POST['ano_inicial'])){
      $ano_inicial = $_POST['ano_inicial'];
    }else{
      $ano_inicial = NULL;
    }

    if(isset($_POST['interrumpida'])){
      $interrumpida = $_POST['interrumpida'];
    }else{
      $interrumpida = NULL;
    }

    if(isset($_POST['certificacion'])){
      $certificacion = $_POST['certificacion'];
      
      for($i=0;$i<count($certificacion);$i++){
        if($certificacion[$i] != NULL){
          #for($i=0;$i<count($certificacion);$i++){
          $insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
              GetSQLValueString($idsolicitud_registro, "int"),
              GetSQLValueString(strtoupper($certificacion[$i]), "text"),
              GetSQLValueString(strtoupper($certificadora[$i]), "text"),
              GetSQLValueString($ano_inicial[$i], "text"),
              GetSQLValueString($interrumpida[$i], "text"));

          $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
          #}
        }
      }

    }
  /*************************** TERMINA INSERTAR CERTIFICACIONES ***************************/


  /*************************** INICIA ACTUALIZAR CERTIFICACIONES ***************************/

      if(isset($_POST['certificacion_actual'])){
        $certificacion_actual = $_POST['certificacion_actual'];
      }else{
        $certificacion_actual = NULL;
      }


      if(isset($_POST['certificadora_actual'])){
        $certificadora_actual = $_POST['certificadora_actual'];
      }else{
        $certificadora_actual = NULL;
      }

      if(isset($_POST['ano_inicial_actual'])){
        $ano_inicial_actual = $_POST['ano_inicial_actual'];
      }else{
        $ano_inicial_actual = NULL;
      }

      if(isset($_POST['interrumpida_actual'])){
        $interrumpida_actual = $_POST['interrumpida_actual'];
      }else{
        $interrumpida_actual = NULL;
      }

      if(isset($_POST['idcertificacion'])){
        $idcertificacion = $_POST['idcertificacion'];
      }else{
        $idcertificacion = '';
      }

      if(isset($_POST['idcertificacion'])){
        for($i=0;$i<count($certificacion_actual);$i++){
          if($certificacion_actual[$i] != NULL){
            #for($i=0;$i<count($certificacion_actual);$i++){


            $updateSQL = sprintf("UPDATE certificaciones SET certificacion = %s, certificadora = %s, ano_inicial = %s, interrumpida = %s WHERE idcertificacion = %s",
              GetSQLValueString(strtoupper($certificacion_actual[$i]), "text"),
              GetSQLValueString(strtoupper($certificadora_actual[$i]), "text"),
              GetSQLValueString($ano_inicial_actual[$i], "text"),
              GetSQLValueString($interrumpida_actual[$i], "text"),
              GetSQLValueString($idcertificacion[$i], "int"));
            $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
          }
        }
      }    
  /*************************** TERMINA INSERTAR CERTIFICACIONES ***************************/

  /*************************** INICIA INSERTAR PRODUCTOS ***************************/
    if(isset($_POST['volumen_estimado'])){
      $volumen_estimado = $_POST['volumen_estimado'];
    }
    if(isset($_POST['volumen_terminado'])){
      $volumen_terminado = $_POST['volumen_terminado'];
    }
    if(isset($_POST['volumen_materia'])){
      $volumen_materia = $_POST['volumen_materia'];
    }
    if(isset($_POST['destino'])){
      $destino = $_POST['destino'];
    }
    if(isset($_POST['origen'])){
      $origen = $_POST['origen'];
    }
    if(isset($_POST['producto'])){
      $producto = $_POST['producto'];
      for ($i=0;$i<count($producto);$i++) { 
        if($producto[$i] != NULL){

            $str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
            $destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            $str = iconv($charset, 'ASCII//TRANSLIT', $origen[$i]);
            $origen[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            $insertSQL = sprintf("INSERT INTO productos (idempresa, idsolicitud_registro, producto, volumen_estimado, volumen_terminado, volumen_materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($_POST['idempresa'], "int"),
                    GetSQLValueString($idsolicitud_registro, "int"),
                    GetSQLValueString($producto[$i], "text"),
                    GetSQLValueString($volumen_estimado[$i], "text"),
                    GetSQLValueString($volumen_terminado[$i], "text"),
                    GetSQLValueString($volumen_materia[$i], "text"),
                    GetSQLValueString($origen[$i], "text"),
                    GetSQLValueString($destino[$i], "text"));

            $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
        }
      }
    }
  /*************************** TERMINA INSERTAR PRODUCTOS ***************************/

  /*************************** INICIA ACTUALIZAR PRODUCTOS ***************************/
    if(isset($_POST['producto_actual'])){
      $producto_actual = $_POST['producto_actual'];
    }else{
      $producto_actual = '';
    }
    if(isset($_POST['volumen_estimado_actual'])){
      $volumen_estimado_actual = $_POST['volumen_estimado_actual'];
    }
    if(isset($_POST['volumen_terminado_actual'])){
      $volumen_terminado_actual = $_POST['volumen_terminado_actual'];
    }
    if(isset($_POST['volumen_materia_actual'])){
      $volumen_materia_actual = $_POST['volumen_materia_actual'];
    }
    if(isset($_POST['destino_actual'])){
      $destino_actual = $_POST['destino_actual'];
    }
    if(isset($_POST['origen_actual'])){
      $origen_actual = $_POST['origen_actual'];
    }
    if(isset($_POST['idproducto'])){
      $idproducto = $_POST['idproducto'];
    }else{
      $idproducto = '';
    }

    if(isset($_POST['idproducto'])){
      for ($i=0;$i<count($producto_actual);$i++) { 
        if($producto_actual[$i] != NULL){

            //$str = iconv($charset, 'ASCII//TRANSLIT', $producto_actual[$i]);
            //$producto_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            $str = iconv($charset, 'ASCII//TRANSLIT', $destino_actual[$i]);
            $destino_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            $str = iconv($charset, 'ASCII//TRANSLIT', $origen_actual[$i]);
            $origen_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


            $updateSQL = sprintf("UPDATE productos SET producto = %s, volumen_estimado = %s, volumen_terminado = %s, volumen_materia = %s, origen = %s, destino = %s WHERE idproducto = %s",
              GetSQLValueString($producto_actual[$i], "text"),
              GetSQLValueString($volumen_estimado_actual[$i], "text"),
              GetSQLValueString($volumen_terminado_actual[$i], "text"),
              GetSQLValueString($volumen_materia_actual[$i], "text"),
              GetSQLValueString($origen_actual[$i], "text"),
              GetSQLValueString($destino_actual[$i], "text"),
              GetSQLValueString($idproducto[$i], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());



        }
      }
    }
  /*************************** TERMINA ACTUALIZAR PRODUCTOS ***************************/



  /*************************** INICIA INSERTAR SUB EMPRESAS ***************************/

    if(isset($_POST['servicio'])){
      $servicio = $_POST['servicio'];
    }

    if(isset($_POST['subempresa'])){
      $subempresa = $_POST['subempresa'];
      for ($i=0;$i<count($subempresa);$i++) { 
        if($subempresa[$i] != NULL){

            $insertSQL = sprintf("INSERT INTO sub_empresas (idsolicitud_registro, idempresa, nombre, servicio) VALUES (%s, %s, %s, %s)",
                    GetSQLValueString($idsolicitud_registro, "int"),
                    GetSQLValueString($_POST['idempresa'], "int"),
                    GetSQLValueString($subempresa[$i], "text"),
                    GetSQLValueString($servicio[$i], "text"));
            $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
        }
      }
    }
  /*************************** TERMINA INSERTAR SUB EMPRESAS ***************************/

  /*************************** INICIA ACTUALIZAR SUB EMPRESAS ***************************/
    if(isset($_POST['subempresa_actual'])){
      $subempresa_actual = $_POST['subempresa_actual'];
    }else{
      $subempresa_actual = '';
    }
    if(isset($_POST['servicio_actual'])){
      $servicio_actual = $_POST['servicio_actual'];
    }
    if(isset($_POST['idsub_empresa_actual'])){
      $idsub_empresa_actual = $_POST['idsub_empresa_actual'];
    }else{
      $idsub_empresa_actual = '';
    }

    if(isset($_POST['idsub_empresa_actual'])){
      for ($i=0;$i<count($subempresa_actual);$i++) { 
        if($subempresa_actual[$i] != NULL){

            $updateSQL = sprintf("UPDATE sub_empresas SET nombre = %s, servicio = %s WHERE idsub_empresas = %s",
              GetSQLValueString($subempresa_actual[$i], "text"),
              GetSQLValueString($servicio_actual[$i], "text"),
              GetSQLValueString($idsub_empresa_actual[$i], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
        }
      }
    }
  /*************************** TERMINA ACTUALIZAR SUB EMPRESAS ***************************/




  $mensaje = "Datos Actualizados Correctamente";
}
 

//****** INICIA ENVIAR COTIZACION *******///
if(isset($_POST['enviar_cotizacion']) && $_POST['enviar_cotizacion'] == "1"){
  $estatus_dspp = '4'; // COTIZACIÓN ENVIADA
  $estatus_publico = '1';
  //echo '<script>alert("1");</script>';
  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";
  $procedimiento = $_POST['procedimiento'];

  if(!empty($_FILES['cotizacion_empresa']['name'])){
      $_FILES["cotizacion_empresa"]["name"];
        move_uploaded_file($_FILES["cotizacion_empresa"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["cotizacion_empresa"]["name"]);
        $cotizacion_empresa = $rutaArchivo.basename(time()."_".$_FILES["cotizacion_empresa"]["name"]);
  }else{
    $cotizacion_empresa = NULL;
  }
  //echo '<script>alert("2");</script>';
  //ACTUALIZAMOS LA SOLICITUD DE CERTIFICACION AGREGANDO LA COTIZACIÓN
  $updateSQL = sprintf("UPDATE solicitud_registro SET tipo_procedimiento = %s, cotizacion_empresa = %s, estatus_dspp = %s WHERE idsolicitud_registro = %s",
    GetSQLValueString($procedimiento, "text"),
    GetSQLValueString($cotizacion_empresa, "text"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($idsolicitud_registro, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
  //echo '<script>alert("3");</script>';
  // ACTUALIZAMOS EL ESTATUS_DSPP DEL OPP
  $updateSQL = sprintf("UPDATE empresa SET estatus_dspp = %s WHERE idempresa = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idempresa'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
  //echo '<script>alert("4");</script>';
  //AGREGAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_registro, estatus_publico, estatus_dspp) VALUES (%s, %s, %s)",
    GetSQLValueString($idsolicitud_registro, "int"),
    GetSQLValueString($estatus_publico, "int"),
    GetSQLValueString($estatus_dspp, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
  //echo '<script>alert("5");</script>';
  //ASUNTO DEL CORREO
  $row_oc = mysql_query("SELECT * FROM oc WHERE idoc = $_POST[idoc]", $dspp) or die(mysql_error());
  $oc = mysql_fetch_assoc($row_oc);
  //echo '<script>alert("6");</script>';
  $row_empresa = mysql_query("SELECT spp, abreviacion, password FROM empresa WHERE idempresa = $_POST[idempresa]", $dspp) or die(mysql_error());
  $empresa = mysql_fetch_assoc($row_empresa);
  //echo '<script>alert("7");</script>';
  $asunto = "D-SPP Cotización (Solicitud de Registro para Compradores y otros Actores) / Price Quote (Registration Application for Buyers and other Stakeholders)";

  $cuerpo_mensaje = '
    <html>
    <head>
      <meta charset="utf-8">
    </head>
    <body>
    
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
        <thead>
          <tr>
            <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280" ><strong>Notificación de Cotización / Price Notification</strong></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="2">
              <p style="text-align:justify">
                Se ha enviado la cotización correspondiente a la Solicitud de Registro para Compradores y otros Actores. Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/">www.d-spp.org/</a> como Empresa, para poder acceder a la cotización. 
              </p>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <p style="color:red">¿Qué es lo de debo realizar ahora?. Debes "Aceptar" o "Rechazar" la cotización</p>
              <ol>

                <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Empresa.</li>
                <li>Tu Usuario: <b style="color:red">'.$empresa['spp'].'</b> y Contraseña: <b style="color:red">'.$empresa['password'].'</b></li>
                <li>Dentro de tu cuenta debes seleccionar Solicitudes > Listado Solicitudes.</li>
                <li>Dentro de la tabla solicitudes debes localizar la columna "Cotización" Y seleccionar el botón Verde (aceptar cotización) ó el botón Rojo (rechazar cotización)</li>
                <li>En caso de aceptar la cotización debes esperar a que finalice el "Periodo de Objeción"(en caso de que sea la primera vez que solicitas la certificación SPP)</li>
              </ol>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <h4>English Below</h4>
              <hr>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <p>
              The price quote corresponding to the Application for Buyers’ Registration has been sent. Please open a session as a Company at the following link: <a href="http://d-spp.org/?Empresa">www.d-spp.org/</a> in order to access the price quote.
              </p>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <span style="color:red">What should I do now? You should "Accept" or "Reject" the price quote.</span>
              <ol>

                <li>
                  You should open a session in the <a href="http://d-spp.org/">D-SPP (clic aquí)</a> system as a Company.
                </li>
                <li>Your User (SPP#): <b style="color:red">'.$empresa['spp'].'</b> and your Password is:  <b style="color:red">'.$empresa['password'].'</b></li>
                <li>Within your account you should select Applications  >  Applications List.</li>
                <li>In the Applications table, you should locate the column entitled “Price Quote” and select the Green button (accept price quote) or the Red button (reject price quote).</li>
                <li>If you accept the price quote, you will need to wait until the “Objection Period” is over (if this is the first time you are applying for SPP certification).</li>
              </ol>
            </td>
          </tr>

          <tr>
            <td colspan="2" style="color:#ff738a;">Email Organismo de Certificación / Certification Entity: '.$oc['email1'].'</td>
          </tr>

          <tr>
            <td colspan="2">
              <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                <tbody>
                  <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                    <td width="130px">Nombre de la Empresa/Company name</td>
                    <td width="130px">País / Country</td>
                    <td width="130px">Organismo de Certificación / Certification Entity</td>
                    <td width="130px">Fecha de envío / Shipping Date</td>
                 
                    
                  </tr>
                  <tr style="font-size: 12px; text-align:justify">
                    <td style="padding:10px;">
                      '.$_POST['nombre'].'
                    </td>
                    <td style="padding:10px;">
                      '.$_POST['pais'].'
                    </td>
                    <td style="padding:10px;">
                      '.$oc['nombre'].'
                    </td>
                    <td style="padding:10px;">
                    '.date('d/m/Y', $fecha).'
                    </td>
                  </tr>

                </tbody>
              </table>        
            </td>
          </tr>
  

        </tbody>
      </table>

    </body>
    </html>
  ';
    //echo '<script>alert("8");</script>';
  if(isset($_POST['email'])){
    $token = strtok($_POST['email'], "\/\,\;");
    while ($token !== false)
    {
      $mail->AddAddress($token);
      $token = strtok('\/\,\;');
    }
  }
  if(isset($_POST['contacto1_email'])){
      $token = strtok($_POST['contacto1_email'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddAddress($token);
        $token = strtok('\/\,\;');
      }
  }
  if(isset($_POST['contacto2_email'])){
      $token = strtok($_POST['contacto2_email'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddAddress($token);
        $token = strtok('\/\,\;');
      }
  }
  $mail->AddBCC($spp_global);

  if(!empty($oc['email1'])){
      $token = strtok($oc['email1'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddCC($token);
        $token = strtok('\/\,\;');
      }

  }
  if(!empty($oc['email2'])){
      $token = strtok($oc['email2'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddCC($token);
        $token = strtok('\/\,\;');
      }
  }
  //echo '<script>alert("9");</script>';
  
  //se adjunta la cotización
  $mail->AddAttachment($cotizacion_empresa);

  //$mail->Username = "soporte@d-spp.org";
  //$mail->Password = "/aung5l6tZ";
  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($cuerpo_mensaje);
  $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
  $mail->Send();
  $mail->ClearAddresses();


  $mensaje = "Se ha enviado la cotizacion a la empresa";
}
//****** TERMINA ENVIAR COTIZACION *******///



$query = "SELECT solicitud_registro.*, empresa.idempresa AS 'id_de_la_empresa', empresa.nombre, empresa.spp AS 'spp_empresa', empresa.sitio_web, empresa.email, empresa.telefono, empresa.pais, empresa.ciudad, empresa.razon_social, empresa.direccion_oficina, empresa.direccion_fiscal, empresa.rfc, empresa.ruc, oc.abreviacion AS 'abreviacionOC', porcentaje_productoVentas.* FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc LEFT JOIN porcentaje_productoVentas ON solicitud_registro.idsolicitud_registro = porcentaje_productoVentas.idsolicitud_registro WHERE solicitud_registro.idsolicitud_registro = $idsolicitud_registro";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
$solicitud = mysql_fetch_assoc($ejecutar);

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>
<div class="row" style="font-size:12px;">

  <?php 
  if(isset($mensaje)){
  ?>
  <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 style="font-size:14px;" class="text-center"><?php echo $mensaje; ?><h4/>
  </div>
  <?php
  }
  ?>

  <form action="" name="" method="POST" enctype="multipart/form-data">
    <fieldset>
      <div class="col-md-12 alert alert-primary" style="padding:7px;">
        <h3 class="text-center">Solicitud de Registro para Compradores y otros Actores</h3>
      </div>

      <div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>DATOS GENERALES</b></div>

        <div class="col-lg-12 alert alert-info" style="padding:7px;">
          <div class="col-md-12">
            <!--<div class="col-xs-4">
              <b>ENVAR AL OC (selecciona el OC al que deseas enviar la solicitud):</b>
              <input type="text" class="form-control" value="<?php echo $solicitud['abreviacionOC']; ?>" readonly>
            </div>-->
            <div class="col-xs-4">
              <b>TIPO DE SOLICITUD</b>
              <input type="text" class="form-control" value="<?php echo $solicitud['tipo_solicitud']; ?>"readonly>
              <button type="submit" class="btn btn-warning form-control" style="color:white" name="actualizar_solicitud" value="1">
                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>Actualizar Solicitud
              </button>
              <!--<input type="submit" style="color:white" class="btn btn-warning form-control" value="Actualizar Solicitud">
              <input type="hidden" name="guarda_cambios" value="1">-->

            </div>
            <div class="col-md-8">
              <?php 
              if(empty($solicitud['cotizacion_empresa'])){
              ?>
                <b>CARGAR COTIZACIÓN</b>
                <input type="file" class="form-control" id="cotizacion_empresa" name="cotizacion_empresa"> 
                <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>"> 
                <button class="btn btn-sm btn-success form-control" style="color:white" id="enviar_cotizacion" name="enviar_cotizacion" type="submit" value="1" onclick="return validar()">
                  <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar Cotización
                </button>
                <!--<button type="submit" class="btn btn-success form-control" style="color:white" name="enviar_cotizacion" value="Enviar"><span class="glyphicon glyphicon-envelope" aria-hidden="true" onclick="return validar()"></span> Enviar Cotización</button>-->

              <?php 
              }else{
                echo "<b style='font-size:14px;'>Ya se ha enviado la cotización</b>";
              }
               ?>
            </div>

          </div>
        </div>
        <div class="col-xs-12 text-center">
          <div class="row">
        <h4>Procedimiento de Certificación <br><small>(realizado por OC)</small></h4>
          </div>
        </div>
        <div class="col-xs-3 text-center">
          <div class="row">
            <div class="col-xs-12">
              <p style="font-size:10px;"><b>DOCUMENTAL "ACORTADO"</b></p> 
            </div>       
            <div class="col-xs-12">
              <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "ACORTADO"'){ echo "checked"; } ?>>

            </div>                        
          </div>
        </div>
        <div class="col-xs-3 text-center">
          <div class="row">
            <div class="col-xs-12">
              <p style="font-size:10px;"><b>DOCUMENTAL "NORMAL"</b></p> 
            </div>
            <div class="col-xs-12">
              <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "NORMAL"'){ echo "checked"; } ?>>

            </div>                
          </div>
        </div>
        <div class="col-xs-3 text-center">
          <div class="row">
            <div class="col-xs-12">
              <p style="font-size:10px;"><b>COMPLETO "IN SITU"</b></p>  
            </div>
            <div class="col-xs-12">
              <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "IN SITU"'){ echo "checked"; } ?>>

            </div>                
          </div>
        </div>
        <div class="col-xs-3 text-center">
          <div class="row">
            <div class="col-xs-12">
              <p style="font-size:10px;"><b>COMPLETO "A DISTANCIA"</b></p>  
            </div>
            <div class="col-xs-12">
              <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "A DISTANCIA"'){ echo "checked"; } ?>>

            </div>                
          </div>
        </div> 

      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMACION GENERALES</div>
          <label for="fecha_elaboracion">FECHA ELABORACIÓN</label>
          <input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>  

          <label for="spp">CODIGO DE IDENTIFICACIÓN SPP(#SPP): </label>
          <input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_empresa']; ?>" readonly>

          <label for="nombre">NOMBRE COMPLETO DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES: </label>
          <textarea name="nombre" id="nombre" class="form-control"><?php echo $solicitud['nombre']; ?></textarea>

          <label for="pais">PAÍS:</label>
           <select name="pais" id="pais" class="form-control">
            <option value="">Selecciona un País</option>
            <?php 
            while($pais = mysql_fetch_assoc($row_pais)){
              if(utf8_encode($pais['nombre']) == $solicitud['pais']){
                echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
              }else{
                echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
              }
            }
             ?>
           </select>

          <label for="direccion_oficina">DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES(CALLE, BARRIO, LUGAR, REGIÓN)</label>
          <textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $solicitud['direccion_oficina']; ?></textarea>

          <label for="email">CORREO ELECTRÓNICO:</label>
          <input type="email" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>">

          <label for="telefono">TELÉFONOS (CODIGO DE PAÍS + CÓDIGO DE ÁREA + NÚMERO):</label>
          <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>">  

          <label for="sitio_web">SITIO WEB:</label>
          <input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>">

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">DATOS FISCALES PARA FACTURACIÓN</div>

          <label for="razon_social">RAZÓN SOCIAL</label>
          <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $solicitud['razon_social']; ?>">

          <label for="direccion_fiscal">DIRECCIÓN FISCAL</label>
          <textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal"><?php echo $solicitud['direccion_fiscal']; ?></textarea>

          <label for="rfc">RFC</label>
          <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $solicitud['rfc']; ?>">

          <label for="ruc">RUC</label>
          <input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $solicitud['ruc']; ?>">
        </div>
      </div>
      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


      <!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONA(S) DE CONTACTO</div>

          <label for="persona1">PERSONA(S) DE CONTACTO</label>
          <input type="text" class="form-control" id="persona1" value="<?php echo $solicitud['contacto1_nombre']; ?>"  readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Nombre Persona 2" readonly>

          <label for="cargo">CARGO</label>
          <input type="text" class="form-control" id="cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Cargo Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_cargo']; ?>" palceholder="Cargo Persona 2" readonly>

          <label for="email">CORREO ELECTRÓNICO</label>
          <input type="email" class="form-control" id="email" name="contacto1_email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email Persona 1" readonly>
          <input type="email" class="form-control" id="" name="contacto2_email" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Persona 2" readonly>

          <label for="telefono">TELEFONO</label>
          <input type="text" class="form-control" id="telefono" name="contacto1_telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Telefono Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Telefono Persona 2" readonly>

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONA(S) ÁREA ADMINISTRATIVA</div>

          <label for="persona_adm">PERSONA(S) DEL ÁREA ADMINSITRATIVA</label>
          <input type="text" class="form-control" id="persona_adm" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Nombre Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Nombre Persona 2" readonly>

          <label for="email_adm">CORREO ELECTRÓNICO</label>
          <input type="email" class="form-control" id="email_adm" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email Persona 1" readonly>
          <input type="email" class="form-control" id="" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email Persona 2" readonly>

          <label for="telefono_adm">TELÉFONO</label>
          <input type="text" class="form-control" id="telefono_adm" value="<?php echo $solicitud['adm1_telefono']; ?>" placeholder="Telefono Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_telefono']; ?>" placeholder="Telefono Persona 2" readonly>
        </div>
      </div>
      <!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>



      <!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>

      <div class="col-md-12 alert alert-info">
        <div>
          <label for="alcance">
            SELECCIONE EL TIPO DE EMPRESA SPP PARA EL CUAL SE SOLICITA EL REGISTRO. UN INTERMEDIARIO NO PUEDE REGISTRARSE SPP SI NO CUENTA CON UN COMPRADOR FINAL REGISTRADO SPP O EN PROCESO DE REGISTRO. 
          </label>
        </div>

        <div class="checkbox">
          <label class="col-sm-4">
            <input type="checkbox"name="comprador" <?php if($solicitud['comprador_final']){echo "checked"; } ?> value="1"> COMPRADOR-FINAL
          </label>
          <label class="col-sm-4">
            <input type="checkbox"name="intermediario" <?php if($solicitud['intermediario']){echo "checked"; } ?> value="1"> INTERMEDIARIO
          </label>
          <label class="col-sm-4">
            <input type="checkbox"name="maquilador" <?php if($solicitud['maquilador']){echo "checked"; } ?> value="1"> MAQUILADOR
          </label>
        </div>
      </div>

      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE OPERACIÓN</div>

      <div class="col-lg-12">
        <div class="col-md-12">
          <label for="preg1">
            1.  ¿CUÁLES SON LAS ORGANIZACIONES DE PEQUEÑOS PRODUCTORES A LAS QUE LES COMPRA O PRETENDE COMPRAR BAJO EL ESQUEMA DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?
          </label>
          <textarea name="preg1" id="preg1" class="form-control"><?php echo $solicitud['preg1']; ?></textarea>

          <label for="preg2">
            2.  ¿QUIÉN O QUIÉNES SON LOS PROPIETARIOS DE LA EMPRESA?
          </label>
          <textarea name="preg2" id="preg2" class="form-control"><?php echo $solicitud['preg2']; ?></textarea>

          <label for="preg3">
            3. ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGNISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.<sup>4</sup>
          </label>
          <textarea name="preg3" id="preg3" class="form-control" rows="2"><?php echo $solicitud['preg3']; ?></textarea>

          <label for="preg4">
            4. SI SU EMPRESA ES UN COMPRADOR FINAL, MENCIONE SI QUIEREN INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
          </label>
          <textarea name="preg4" id="preg4" class="form-control"><?php echo $solicitud['preg4']; ?></textarea>

          <div >
            <label for="alcance">
              5. SELECCIONE EL ALCANCE QUE TIENE LA EMPRESA:
            </label>
          </div>
          <div class="col-md-4">
            <label>PRODUCCIÓN</label>
            <input type="checkbox" name="produccion" class="form-control" <?php if($solicitud['produccion']){echo "checked";} ?> value="1">
          </div>
          <div class="col-md-4">
            <label>PROCESAMIENTO</label>
            <input type="checkbox" name="procesamiento" class="form-control" <?php if($solicitud['procesamiento']){echo "checked";} ?> value="1">
          </div>
          <div class="col-md-4">
            <label>IMPORTACIÓN</label>
            <input type="checkbox" name="importacion" class="form-control" <?php if($solicitud['importacion']){echo "checked";} ?> value="1">
          </div>

        <p>
          <b>6.  SELECCIONE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN</b>
        </p>
        <div class="col-md-6">
          SI <input type="radio" class="form-control" name="preg6"  id="preg6" <?php if($solicitud['preg6'] == 'SI'){echo "checked"; } ?> value="SI">
        </div>
        <div class="col-md-6">
          NO <input type="radio" class="form-control" name="preg6"  id="preg6" <?php if($solicitud['preg6'] == 'NO'){echo "checked"; } ?> value="NO">
        </div>

        <p>
          <b style="background:#3498db;color:#ecf0f1;padding:3px;">SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA</b>
        </p>
        <div id="contenedor_tablaEmpresas" class="col-md-12" >
          <table class="table table-bordered" id="tablaEmpresas">
            <tr>
              <td>NOMBRE DE LA EMPRESA</td>
              <td>SERVICIO QUE REALIZA</td>
              <td>
                <button type="button" onclick="tablaEmpresas()" class="btn btn-primary" aria-label="Left Align">
                  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
                
              </td>

            </tr>
            <?php 
            $query_subempresa = "SELECT * FROM sub_empresas WHERE idsolicitud_registro = $idsolicitud_registro";
            $subempresa_detalle = mysql_query($query_subempresa, $dspp) or die(mysql_error());
            $contador = 0;
            while($row_subempresa = mysql_fetch_assoc($subempresa_detalle)){
            ?>
            <tr class="text-center">
              <td><input type="text" class="form-control" name="subempresa_actual[]" id="exampleInputEmail1" placeholder="EMPRESA" value="<?php echo $row_subempresa['nombre']; ?>"></td>
              <td>
                <input type="text" class="form-control" name="servicio_actual[]" id="exampleInputEmail1" placeholder="SERVICIO" value="<?php echo $row_subempresa['servicio']; ?>">
                <input type="hidden" name="idsub_empresa_actual[]" value="<?php echo $row_subempresa['idsub_empresas']; ?>">

              </td>
            </tr>
            <?php 
              $contador++; 
            } 
            ?> 
          </table>  
        </div>  

          <label for="preg7">
            7.  SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS ESTAN REGISTRADAS O VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE SU EMPRESA <sup>5</sup>
            <br>
            <small><sup>5</sup> Revisar el documento de 'Directrices Generales del Sistema SPP' en su última versión.</small>
          </label>
          <textarea name="preg7" id="preg7" class="form-control"><?php echo $solicitud['preg7']; ?></textarea>

          <label for="preg8">
            8. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.
          </label>
          <textarea name="preg8" id="preg8" class="form-control"><?php echo $solicitud['preg8']; ?></textarea>

          <label for="preg9">
            9. EN CASO DE TENER CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES,  ANEXAR UN CROQUIS GENERAL MOSTRANDO SU UBICACIÓN.
          </label>
          <?php 
          if(empty($solicitud['preg9'])){
          ?>
            <input type="file" id="preg9" name="preg9" class="form-control">
          <?php
          }else{
          ?>
            <input type="file" id="preg9" name="preg9" class="form-control">
            <input type="hidden" id="preg9_actual" name="preg9_actual" value="<?php echo $solicitud['preg9']; ?>">
            <a href="<?php echo $solicitud['preg9']; ?>" target="_blank" class="btn btn-success" style="width:40%">Descargar Croquis</a>
          <?php
          }
           ?>
          <label for="preg10">
            10. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.
          </label>
          <textarea name="preg10" id="preg10" class="form-control"><?php echo $solicitud['preg10'] ?></textarea>

          <p class="alert alert-info"><b>11. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc).</b></p>
          
          <table class="table table-bordered" id="tablaCertificaciones">
            <tr>
              <td>CERTIFICACIÓN</td>
              <td>CERTIFICADORA</td>
              <td>AÑO INICIAL DE CERTIFICACIÓN?</td>
              <td>¿HA SIDO INTERRUMPIDA?</td>
              <td>
                <button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
                  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
                
              </td>
            </tr>
            <?php 
            $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_registro = $idsolicitud_registro";
            $certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
            $contador = 0;
            while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
            ?>
              <tr class="text-center">
                <td><input type="text" class="form-control" name="certificacion_actual[]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN" value="<?echo $row_certificacion['certificacion']?>"></td>
                <td><input type="text" class="form-control" name="certificadora_actual[]" id="exampleInputEmail1" placeholder="CERTIFICADORA" value="<?echo $row_certificacion['certificadora']?>"></td>
                <td><input type="text" class="form-control" name="ano_inicial_actual[]" id="exampleInputEmail1" placeholder="AÑO INICIAL" value="<?echo $row_certificacion['ano_inicial']?>"></td>
                <td><input type="text" class="form-control" name="interrumpida_actual[]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?" value="<?echo $row_certificacion['interrumpida']?>"></td>
                <input type="hidden" name="idcertificacion[]" value="<?echo $row_certificacion['idcertificacion']?>">
              </tr>
            <?php 
              $contador++; 
            } 
            ?> 
          </table>

          <label for="preg12">
            12. DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?</label>
          <textarea name="preg12" id="preg12" class="form-control"><?php echo $solicitud['preg12']; ?></textarea>

          <p for="op_preg11">
            <b>13. DEL TOTAL DE SUS COMPRAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?</b>
            <i>(* Introducir solo cantidad, entero o decimales)</i>
            <div class="col-lg-12">
              <div class="row">
                <div class="col-xs-3">
                  <label for="organico">% ORGÁNICO</label>
                  <input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0">
                </div>
                <div class="col-xs-3">
                  <label for="comercio_justo">% COMERCIO JUSTO</label>
                  <input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0">
                </div>
                <div class="col-xs-3">
                  <label for="spp">SÍMBOLO DE PEQUEÑOS PRODUCTORES</label>
                  <input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0">
                </div>
                <div class="col-xs-3">
                  <label for="otro">SIN CERTIFICADO</label>
                  <input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0">
                </div>
              </div>
            </div>
          </p>
            

          <p><b>14. TUVO COMPRAS SPP DURANTE EL CICLO DE REGISTRO ANTERIOR?</b></p>
            <div class="col-md-6">
              SI <input type="radio" class="form-control" name="preg13" id="preg13" value="SI" <?php if($solicitud['preg13'] == 'SI' ){echo 'checked'; } ?>>
            </div>
            <div class="col-md-6">
              NO <input type="radio" class="form-control" name="preg13" id="preg13" value="NO" <?php if($solicitud['preg13'] == 'NO' ){echo 'checked'; } ?>>
            </div>      
          <p>
            <b>15. SI SU RESPUESTA FUE POSITIVA FAVOR DE SELECCIONAR EL RANGO DEL VALOR TOTAL DE SUS COMPRAS SPP DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA 
          </p>

          <div class="well col-md-12 " id="tablaVentas">
            <div class="col-md-6"><p>Hasta $3,000 USD</p></div>
            <div class="col-md-6 "><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD" <?php if($solicitud['preg14'] == 'HASTA $3,000 USD' ){echo 'checked'; } ?>></div>
          
          
            <div class="col-md-6"><p>Entre $3,000 y $10,000 USD</p></div>
            <div class="col-md-6"><input type="radio" name="preg14" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD" <?php if($solicitud['preg14'] == 'ENTRE $3,000 Y $10,000 USD' ){echo 'checked'; } ?>></div>
          
          
            <div class="col-md-6"><p>Entre $10,000 a $25,000 USD</p></div>
            <div class="col-md-6"><input type="radio" name="preg14" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD" <?php if($solicitud['preg14'] == 'ENTRE $10,000 A $25,000 USD' ){echo 'checked'; } ?>></div>
            
            <?php 
            if($solicitud['preg14'] != NULL && $solicitud['preg14'] != 'HASTA $3,000 USD' && $solicitud['preg14'] != 'ENTRE $3,000 Y $10,000 USD' && $solicitud['preg14'] != 'ENTRE $10,000 A $25,000 USD'){
            ?>
              <div class="col-md-6"><p>Más de $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Especifique la cantidad.</h6></p></div>
              <div class="col-md-6"><input type="radio" name="" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="" checked>
                <input type="text" name="preg14" class="form-control" id="" placeholder="Especifique la Cantidad" value="<?php echo $solicitud['preg14']; ?>">
              </div>

            <?php
            }else{
            ?>
              <div class="col-md-6"><p>Más de $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Especifique la cantidad.</h6></p></div>
              <div class="col-md-6"><input type="radio" name="" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="">
                <input type="text" name="preg14" class="form-control" id="oculto" style='display:none;' placeholder="Especifique la Cantidad">
              </div>
            <?php
            }
             ?>

          </div>

          <!--<p><b>14 - 15. ¿TUVO COMPRAS SPP DURANTE EL CICLO DE REGISTRO ANTERIOR?</b></p>
          <div class="col-xs-12 ">
                <?php
                  if($solicitud['preg13'] == 'SI'){
                ?>
                  <div class="col-xs-6">
                    <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
                  </div>
                  <div class="col-xs-6">
                    <?php 
                      if(empty($solicitud['preg14'])){
                     ?>
                      <p class="alert alert-danger">No se proporciono ninguna respuesta.</p>
                    <?php 
                      }else if($solicitud['preg14'] == "HASTA $3,000 USD"){
                     ?>
                      <p class="alert alert-info">HASTA $3,000 USD</p>
                    <?php 
                      }else if($solicitud['preg14'] == "ENTRE $3,000 Y $10,000 USD"){
                     ?>
                     <p class="alert alert-info">ENTRE $3,000 Y $10,000 USD</p>
                    <?php 
                      }else if($solicitud['preg14'] == "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info">ENTRE $10,000 A $25,000 USD</p>
                    <?php 
                      }else if($solicitud['preg14'] != "HASTA $3,000 USD" && $solicitud['preg14'] != "ENTRE $3,000 Y $10,000 USD" && $solicitud['preg14'] != "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info"><?php echo $solicitud['preg14']; ?></p>
                     
                    <?php 
                      }
                     ?>
                  </div>

                <?php
                  }else if($solicitud['preg13'] == 'NO'){
                ?>
                  <div class="col-xs-12">
                    <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
                  </div>
                
                <?php         
                  }
                ?>
          </div>-->

          <label for="preg15">
            16. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
          </label>
          <input type="text" class="form-control" id="preg15" name="preg15" value="<?php echo $solicitud['preg15']; ?>">


      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></div>
      <div class="col-lg-12">
        <table class="table table-bordered" id="tablaProductos">
          <tr>
            <td>Producto</td>
            <td>Volumen Total Estimado a Comercializar</td>
            <td>Volumen como Producto Terminado</td>
            <td>Volumen como Materia Prima</td>
            <td>País(es) de Origen</td>
            <td>País(es) Destino</td> 
            <td>
              <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>   
          </tr>
          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_registro = $idsolicitud_registro";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
          ?>
            <tr>
              <td>
                <input type="text" class="form-control" name="producto_actual[]" id="exampleInputEmail1" placeholder="Producto" value="<?echo $row_producto['producto']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="volumen_estimado_actual[]" id="exampleInputEmail1" placeholder="Volumen Estimado" value="<?echo $row_producto['volumen_estimado']?>">
              </td>
        
              <td>
                <input type="text" class="form-control" name="volumen_terminado_actual[]" id="exampleInputEmail1" placeholder="Volumen Terminado" value="<?echo $row_producto['volumen_terminado']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="volumen_materia_actual[]" id="exampleInputEmail1" placeholder="Volumen Materia" value="<?echo $row_producto['volumen_materia']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="origen_actual[]" id="exampleInputEmail1" placeholder="Origen" value="<?echo $row_producto['origen']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="destino_actual[]" id="exampleInputEmail1" placeholder="Destino" value="<?echo $row_producto['destino']?>">
              </td>

                <input type="hidden" name="idproducto[]" value="<?echo $row_producto['idproducto']?>">                     
            </tr>
          <?php 
          $contador++;
          }
          ?>        
          <tr>
            <td colspan="6">
              <h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
            </td>
          </tr>
        </table>
      </div>

      <div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
        <b>COMPROMISOS</b>
      </div>
      <div class="col-lg-12 text-justify">
        <p>1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Certificación.</p>
        <p>2. El proceso de Certificación comenzará en el momento que se confirme la recepción del pago correspondiente.</p>
        <p>3. La entrega y recepción de esta solicitud no garantiza que el proceso de Certificación será positivo.</p>
        <p>4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Organización de Pequeños Productores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.</p>
      </div>
      <div class="col-lg-12">

        <p style="font-size:14px;"><strong>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:</strong></p>

        <input type="hidden" name="idempresa" value="<?php echo $solicitud['id_de_la_empresa']; ?>">
        <input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro']; ?>">
        <input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" > 

        <p>
          <b>OC que recibe la solicitud:</b>
        </p>
        <p class="alert alert-info" style="padding:7px;">
          <?php echo $solicitud['abreviacionOC']; ?>
        </p>  
      </div>


    </fieldset>
  </form>
</div>


<script>
  
  function validar(){
    valor = document.getElementById("cotizacion_empresa").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de la Empresa");
      return false;
    }
    
    Procedimiento = document.getElementsByName("procedimiento");
     
    var seleccionado = false;
    for(var i=0; i<Procedimiento.length; i++) {    
      if(Procedimiento[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar un Procedimiento de Certificación");
      return false;
    }

    return true
  }

</script>

<script>
var contador=0;
  function tablaCertificaciones()
  {

    var table = document.getElementById("tablaCertificaciones");
    {
      var row = table.insertRow(1);
      var cell1 = row.insertCell(0);
      var cell2 = row.insertCell(1);
      var cell3 = row.insertCell(2);
      var cell4 = row.insertCell(3);

      cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
      cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
      cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
      cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
    contador++;
    }
    
  } 

  function tablaEmpresas()
  {
  
  var table = document.getElementById("tablaEmpresas");
    {
    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);


    cell1.innerHTML = '<input type="text" class="form-control" name="subempresa['+contador+']" id="exampleInputEmail1" placeholder="EMPRESA">';
    cell2.innerHTML = '<input type="text" class="form-control" name="servicio['+contador+']" id="exampleInputEmail1" placeholder="SERVICIO">';

    contador++;
    }
  }
  function mostrar_empresas(){
    document.getElementById('contenedor_tablaEmpresas').style.display = 'block';
  }
  function ocultar_empresas()
  {
    document.getElementById('contenedor_tablaEmpresas').style.display = 'none';
  } 

  function mostrar(){
    document.getElementById('oculto').style.display = 'block';
  }
  function ocultar()
  {
    document.getElementById('oculto').style.display = 'none';
  }

  function mostrar_ventas(){
    document.getElementById('tablaVentas').style.display = 'block';
  }
  function ocultar_ventas()
  {
    document.getElementById('tablaVentas').style.display = 'none';
  }   

  var cont=0;
  function tablaProductos()
  {

  var table = document.getElementById("tablaProductos");
    {

    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    var cell6 = row.insertCell(5);   
    
    cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
    
    cell2.innerHTML = '<input type="text" class="form-control" name="volumen_estimado['+cont+']" id="exampleInputEmail1" placeholder="Volumen Estimado">';
    
    cell3.innerHTML = '<input type="text" class="form-control" name="volumen_terminado['+cont+']" id="exampleInputEmail1" placeholder="Volumen Terminado">';
    
    cell4.innerHTML = '<input type="text" class="form-control" name="volumen_materia['+cont+']" id="exampleInputEmail1" placeholder="Volumen Materia">';
    
    cell5.innerHTML = '<input type="text" class="form-control" name="origen['+cont+']" id="exampleInputEmail1" placeholder="Origen">';
    
    cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
     
  cont++;
    }

  } 

</script>