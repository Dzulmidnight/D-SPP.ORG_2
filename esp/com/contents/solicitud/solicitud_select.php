<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OPP";

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

    $remitente = "cert@spp.coop";
    $fecha_actual = time();

if(isset($_POST['com_propuesta'])){

    $idcom = $_SESSION['idcom'];
    $idoc = $_POST['idoc'];
    $identificador = "SOLICITUD";
    $status = $_POST['propuesta_txt'];
    $idexterno = $_POST['idsolicitud_registro'];


  if($_POST['estadoCOM'] == 20){ // INICIA IF estadoCOM == 20
    if($_POST['propuesta_txt'] == 18){ // INICIA IF propuesta_txt == 18
      $status_publico = 8;
    }// TERMINA IF propuesta_txt == 18
    else{ // INICIA ELSE propuesta_txt == 18
      $status_publico = 10;
    }// TERMINA ELSE propuesta_txt == 18

  //if($_POST['estadoCOM'] == 20){
    //$status_publico = 8;

    $updateSQL = "UPDATE solicitud_registro SET 
    status_interno = '".$_POST['propuesta_txt']."',
    status_publico = '".$status_publico."'
    WHERE idsolicitud_registro= '".$_POST['idsolicitud_registro']."'";
    $Result = mysql_query($updateSQL, $dspp) or die(mysql_error());


    //$query = "INSERT INTO fecha(fecha, idexterno, identificador, status, status_publico) VALUES($fecha_actual, $idcom, '$identificador', $status, $status_publico)";
    //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

    $query = "INSERT INTO fecha(fecha, idexterno, idcom, idoc, identificador, status, status_publico) VALUES($fecha_actual, $idexterno, $idcom, $idoc, '$identificador', $status, $status_publico)";
    $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

  } // TERMINA IF estadoCOM == 20
  else{ // INICIA ELSE estadoCOM == 20
    $updateSQL = "UPDATE solicitud_registro SET 
    status_interno = '".$_POST['propuesta_txt']."'
    WHERE idsolicitud_registro= '".$_POST['idsolicitud_registro']."'";
    $Result = mysql_query($updateSQL, $dspp) or die(mysql_error());

    //$query = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha_actual, $idcom, '$identificador', $status)";
    //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

    $query = "INSERT INTO fecha(fecha, idexterno, idcom, idoc, identificador, status) VALUES($fecha_actual, $idexterno, $idcom, $idoc, '$identificador', $status)";
    $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

  }

    //$actualizar = "UPDATE solicitud_registro SET status = ".$_POST['']."";

  $emailCOM1 = $_POST['emailCOM1'];
  $emailCOM2 = $_POST['emailCOM2'];
  $emailOC = $_POST['emailOC'];
  $nombreOC = $_POST['nombreOC'];
  $nombreCOM = $_POST['nombreCOM'];
  $emailFundeppo = "soporteinforganic@gmail.org";
  $telefonoCOM = $_POST['telefonoCOM'];
  $abreviacionCOM = $_POST['abreviacionCOM'];
  $paisCOM =$_POST['paisCOM'];
  $ciudad = $_POST['ciudadCOM'];
  $fecha = date("d/m/Y", time());
  $nombreCOM1 = $_POST['nombreCOM1'];
  $nombreCOM2 = $_POST['nombreCOM2'];
  $paisEstado = $paisCOM.' / '.$ciudad;
  $fecha_elaboracion = $_POST['fecha_elaboracion'];
  $totalFecha = $_POST['totalFecha'];
/*****************************INICIO MAIL OC***************************************************/
/********************************************************************************/

        //$correo = $_POST['p1_correo'];
        //$correo = $_POST['p2_correo'];

        $destinatario = $emailOC;
        
        $asunto = "D-SPP - Cotización Registro para Compradores y otros Actores";

        if($_POST['propuesta_txt'] == 18){

          if($totalFecha > 0){
            $estatusInterno = 19; // E.I = CERTIFICACIÓN INICIADA
            $estatusPublico = 8; // E.P = PROCESO DE CERTIFICACIÓN

            $query = "UPDATE solicitud_registro SET status_interno = '$estatusInterno', status_publico = '$estatusPublico' WHERE idsolicitud_registro = $_POST[idsolicitud_registro]";
            $insertar = mysql_query($query,$dspp) or die(mysql_error());


            $mensajeEmail = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Felicidades se ha aceptado su cotización, por favor ponerse en contacto con <b>'.$nombreCOM.'</b> para inciar el proceso de renovación del certificado.</td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone OPP: '.$telefonoCOM.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM1.' | '.$emailCOM1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM2.' | '.$emailCOM2.'</td>
                    </tr>


                  </tbody>
                </table>

              </body>
              </html>
            ';

            $destinatario2 = "cert@spp.coop";
            
            $asunto2 = "D-SPP - Renovación de Registro para Compradores y otros Actores"; 


            $mensajeEmail2 = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Se ha aceptado la cotizacion de <b>'.$nombreOC.'</b>.
                      <br><br>Se ha iniciado el proceso de renovacion del certificado.
                      </td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone OPP: '.$telefonoCOM.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM1.' | '.$emailCOM1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM2.' | '.$emailCOM2.'</td>
                    </tr>


                    <tr>
                      <td colspan="2">
                        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                          <tbody>
                            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                              <td width="162.5px">Nombre de la organización/Organization name</td>
                              <td width="162.5px">Abreviación / Short name</td>
                              <td width="162.5px">País / Country</td>
                              <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                            </tr>
                            <tr style="font-size: 12px; text-align:justify">
                              <td style="padding:10px;">
                                '.$nombreCOM.'
                              </td>
                              <td style="padding:10px;">
                                '.$abreviacionCOM.'
                              </td>
                              <td style="padding:10px;">
                                '.$paisEstado.'
                              </td>
                              <td style="padding:10px;">
                                '.$nombreOC.'
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


          }// FIN DEL PROCESO DE RENOVACIÓN
          else{ // EL COM INICIA POR PRIMERA VEZ LA CERTIFICACIÓN PARA EL REGISTRO
            $estatusAceptado = 18; //18 = PROCESO INICIADO
            $estatusDenegado = 24;//24 = COTIZACIÓN RECHAZADA

            $updateSQL = "UPDATE solicitud_registro SET status_interno = $estatusDenegado WHERE idsolicitud_registro != $_POST[idsolicitud_registro] AND fecha_elaboracion = $fecha_elaboracion";
            $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());


            $mensajeEmail = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Felicidades se ha aceptado su cotización, sera informado una vez que inicie el período de objeción, después podra ponerse en contacto con:</td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone COM: '.$telefonoCOM.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM1.' | '.$emailCOM1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM2.' | '.$emailCOM2.'</td>
                    </tr>


                    <tr>
                      <td colspan="2">
                        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                          <tbody>
                            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                              <td width="162.5px">Nombre de la Empresa/Company name</td>
                              <td width="162.5px">Abreviación / Short name</td>
                              <td width="162.5px">País / Country</td>
                              <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                            </tr>
                            <tr style="font-size: 12px; text-align:justify">
                              <td style="padding:10px;">
                                '.$nombreCOM.'
                              </td>
                              <td style="padding:10px;">
                                '.$abreviacionCOM.'
                              </td>
                              <td style="padding:10px;">
                                '.$paisEstado.'
                              </td>
                              <td style="padding:10px;">
                                '.$nombreOC.'
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
            $mail->AddAddress($destinatario);

            //$mail->Username = "soporte@d-spp.org";
            //$mail->Password = "/aung5l6tZ";
            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($mensajeEmail);
            $mail->MsgHTML(utf8_decode($mensajeEmail));
            $mail->Send();
            $mail->ClearAddresses();

           // INICIA EL ENVIO DE MENSAJE DE DENEGACIÓN MASIVA DE COTIZACIONES

            $queryMensaje = "INSERT INTO mensajes(idcom, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, '$asunto', '$mensajeEmail', 'OC', 'COM', $fecha_actual)";
            $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());


              $query = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idcom, solicitud_registro.idoc, solicitud_registro.status_interno, solicitud_registro.fecha_elaboracion, solicitud_registro.cotizacion_com, oc.idoc, oc.nombre, oc.email FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idcom = $idcom AND status_interno = $estatusDenegado AND fecha_elaboracion = $fecha_elaboracion AND cotizacion_com != ''";
              $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

              while($datosSolicitud = mysql_fetch_assoc($ejecutar)){
                $destinatario = $datosSolicitud['email'];

                $mensajeEmail = '
                  <html>
                  <head>
                    <meta charset="utf-8">
                  </head>
                  <body>
                  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                      <tbody>
                        <tr>
                          <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                          <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                        </tr>

                        <tr>
                          <td align="left" style="color:#ff738a;">Lo sentimos, su propuesta de cotización ha sido rechazada</td>
                        </tr>

                        <tr>
                          <td align="left">Teléfono / phone OPP: '.$telefonoCOM.'</td>
                        </tr>
                        <tr>
                          <td align="left">'.$paisEstado.'</td>
                        </tr>
                        <tr>
                          <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM1.' | '.$emailCOM1.'</td>
                        </tr>
                        <tr>
                          <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM2.' | '.$emailCOM2.'</td>
                        </tr>


                        <tr>
                          <td colspan="2">
                            <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                              <tbody>
                                <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                                  <td width="162.5px">Nombre de la Empresa/Company name</td>
                                  <td width="162.5px">Abreviación / Short name</td>
                                  <td width="162.5px">País / Country</td>
                                  <td width="162.5px">Organismo de Certificación / Certification Entity</td> 

                                <tr style="font-size: 12px; text-align:justify">
                                  <td style="padding:10px;">
                                    '.$nombreCOM.'
                                  </td>
                                  <td style="padding:10px;">
                                    '.$abreviacionCOM.'
                                  </td>
                                  <td style="padding:10px;">
                                    '.$paisEstado.'
                                  </td>
                                  <td style="padding:10px;">
                                    '.$datosSolicitud['nombre'].'
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
                $mail->AddAddress($destinatario);

                //$mail->Username = "soporte@d-spp.org";
                //$mail->Password = "/aung5l6tZ";
                $mail->Subject = utf8_decode($asunto);
                $mail->Body = utf8_decode($mensajeEmail);
                $mail->MsgHTML(utf8_decode($mensajeEmail));
                $mail->Send();
                $mail->ClearAddresses();


              } // FIN ENVIAR MENSAJE DENEGACIÓN DE COTIZACIÓN MASIVA

                $destinatario2 = "cert@spp.coop";
                    
                $asunto2 = "D-SPP - Cotización Registro para Compradores y otros Actores"; 


                $cuerpo2 = '
                  <html>
                  <head>
                    <meta charset="utf-8">
                  </head>
                  <body>
                  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                      <tbody>
                        <tr>
                          <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                          <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                        </tr>

                        <tr>
                          <td align="left" style="color:#ff738a;">Se ha aceptado la cotizacion de <b>'.$nombreOC.'</b>.
                          <br><br>Para poder iniciar el período de objeción por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/?ADM">www.d-spp.org/?ADM</a>.
                          </td>
                        </tr>

                        <tr>
                          <td align="left">Teléfono / phone COM: '.$telefonoCOM.'</td>
                        </tr>
                        <tr>
                          <td align="left">'.$paisEstado.'</td>
                        </tr>
                        <tr>
                          <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM1.' | '.$emailCOM1.'</td>
                        </tr>
                        <tr>
                          <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM2.' | '.$emailCOM2.'</td>
                        </tr>


                        <tr>
                          <td colspan="2">
                            <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                              <tbody>
                                <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                                  <td width="162.5px">Nombre de la Empresa/Company name</td>
                                  <td width="162.5px">Abreviación / Short name</td>
                                  <td width="162.5px">País / Country</td>
                                  <td width="162.5px">Organismo de Certificación / Certification Entity</td> 
                                </tr>
                                <tr style="font-size: 12px; text-align:justify">
                                  <td style="padding:10px;">
                                    '.$nombreCOM.'
                                  </td>
                                  <td style="padding:10px;">
                                    '.$abreviacionCOM.'
                                  </td>
                                  <td style="padding:10px;">
                                    '.$paisEstado.'
                                  </td>
                                  <td style="padding:10px;">
                                    '.$nombreOC.'
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
              }

                    $mail->AddAddress($destinatario2);

                    //$mail->Username = "soporte@d-spp.org";
                    //$mail->Password = "/aung5l6tZ";
                    $mail->Subject = utf8_decode($asunto2);
                    $mail->Body = utf8_decode($cuerpo2);
                    $mail->MsgHTML(utf8_decode($cuerpo2));
                    $mail->Send();
                    $mail->ClearAddresses();


                    $queryMensaje = "INSERT INTO mensajes(idcom, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, '$asunto2', '$cuerpo2', 'ADM', 'COM', $fecha_actual)";
                    $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

          }else{ // EL OPP HA RECHAZADO LA PROPUESTA

            $estatusDenegado = 24;
            $updateSQL = "UPDATE solicitud_registro SET status_interno = $estatusDenegado WHERE idsolicitud_registro != $_POST[idsolicitud] AND fecha_elaboracion = $fecha_elaboracion";
            $ejecutar = mysql_query($updateSQL,$dspp) or die(mysql_error());

            $mensajeEmail = '
              <html>
              <head>
                <meta charset="utf-8">
              </head>
              <body>
              
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="6" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><strong>Notificación de Propuesta / Notification of Proposal ('.$fecha.')</strong></th>
                    </tr>

                    <tr>
                      <td align="left" style="color:#ff738a;">Lo sentimos, su propuesta de cotización ha sido rechazada</td>
                    </tr>

                    <tr>
                      <td align="left">Teléfono / phone OPP: '.$telefonoCOM.'</td>
                    </tr>
                    <tr>
                      <td align="left">'.$paisEstado.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM1.' | '.$emailCOM1.'</td>
                    </tr>
                    <tr>
                      <td align="left" style="color:#ff738a;">Nombre: '.$nombreCOM2.' | '.$emailCOM2.'</td>
                    </tr>


                    <tr>
                      <td colspan="2">
                        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                          <tbody>
                            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                              <td width="162.5px">Nombre de la Empresa/Company name</td>
                              <td width="162.5px">Abreviación / Short name</td>
                              <td width="162.5px">País / Country</td>
                              <td width="162.5px">Organismo de Certificación / Certification Entity</td> 

                            <tr style="font-size: 12px; text-align:justify">
                              <td style="padding:10px;">
                                '.$nombreCOM.'
                              </td>
                              <td style="padding:10px;">
                                '.$abreviacionCOM.'
                              </td>
                              <td style="padding:10px;">
                                '.$paisEstado.'
                              </td>
                              <td style="padding:10px;">
                                '.$nombreOC.'
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
          


            $mail->AddAddress($destinatario);

            //$mail->Username = "soporte@d-spp.org";
            //$mail->Password = "/aung5l6tZ";
            $mail->Subject = utf8_decode($asunto);
            $mail->Body = utf8_decode($mensajeEmail);
            $mail->MsgHTML(utf8_decode($mensajeEmail));
            $mail->Send();
            $mail->ClearAddresses();


            $queryMensaje = "INSERT INTO mensajes(idcom, idoc, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, $idoc, '$asunto', '$mensajeEmail', 'OC', 'COM', $fecha_actual)";
            $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

      /****************************** FIN MAIL OC **************************************************/
      /********************************************************************************/
          
      }
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 20;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
  $query_com = "SELECT com.* ,solicitud_registro.*, com.nombre AS 'nombreCOM', oc.idoc AS 'idoc', oc.nombre AS 'nombreOC', oc.idoc, oc.email FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = '".$_GET['query']."'  AND solicitud_registro.status_interno != 24 ORDER BY solicitud_registro.fecha_elaboracion DESC";
  #$query_com = "SELECT * FROM solicitud_registro where idsolicitud_registro ='".$_GET['query']."' ORDER BY fecha DESC";
}else{
  #SELECT solicitud_registro.* FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE com.idcom = 15

  $query_com = "SELECT com.*, com.nombre AS 'nombreCOM' ,solicitud_registro.*, oc.idoc AS 'idoc', oc.nombre AS 'nombreOC', oc.idoc, oc.email FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE solicitud_registro.idcom = '".$_SESSION['idcom']."' AND solicitud_registro.status_interno != 24 ORDER BY solicitud_registro.fecha_elaboracion DESC"; 

  #$query_com = "SELECT com.* ,solicitud_registro.* FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom ORDER BY solicitud_registro.fecha_elaboracion ASC";  

  #$query_com = "SELECT * FROM solicitud_registro ORDER BY fecha ASC";
}

/****************************************************************************************************/
/***********************************   CARGAR COMPROBANTE  ******************************************/
/****************************************************************************************************/
if(isset($_POST['membresia']) && $_POST['membresia'] == "1"){
  $fecha_actual = time();
  $fechaupload = $_POST['fechaupload'];
  $status = $_POST['statusInterno'];
  $idcom = $_POST['membresiaidcom'];
  $idoc = $_POST['membresiaidoc'];
  $idcertificado = $_POST['idcertificado'];
  $idsolicitud_registro = $_POST['idsolicitud_registro'];
  $statuspago = "REVISION";
  $identificador = "MEMBRESIA";
  $idexterno = $idsolicitud_registro;

  $ruta = "../../archivos/comArchivos/membresia/comprobante/";

  if(!empty($_FILES['comprobante']['name'])){
    $_FILES['comprobante']['name'];
        move_uploaded_file($_FILES["comprobante"]["tmp_name"], $ruta.time()."_".$_FILES["comprobante"]["name"]);
        $comprobantePago = $ruta.basename(time()."_".$_FILES["comprobante"]["name"]);

      /******************************** INICIO MAIL FUNDEPPO************************************************/
      /********************************************************************************/
        
        //$correo = $_POST['p1_correo'];
        //$correo = $_POST['p2_correo'];

        $destinatario2 = "cert@spp.coop";
        $fecha = date("d/m/Y", time());
        $asunto2 = "D-SPP - Comprobante de Pago - Membresia"; 


        $cuerpo2 = '
                  <html>
                  <head>
                    <meta charset="utf-8">
                  </head>
                  <body>
                  
                    <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                      <tbody>
                        <tr>
                          <th rowspan="3" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                          <th scope="col" align="left" width="280"><strong>Notificación de Estado / Status Notification ('.$fecha.')</strong></th>
                        </tr>
                        <tr>
                        <td>Se ha cargado el comprobante de pago, por favor inicie sesión en la cuenta de administrador (<a href="http://d-spp.org/?ADM">www.d-spp.org/?ADM</a>) para poder revisarlo.</td>
                        </tr>
                        
                      </tbody>
                    </table>

                  </body>
                  </html>
        ';

        $mail->AddAddress($destinatario2);

        //$mail->Username = "soporte@d-spp.org";
        //$mail->Password = "/aung5l6tZ";
        $mail->Subject = utf8_decode($asunto2);
        $mail->Body = utf8_decode($cuerpo2);
        $mail->MsgHTML(utf8_decode($cuerpo2));
        $mail->Send();
        $mail->ClearAddresses();

         // $query = "INSERT INTO fecha(fecha, idcom, identificador, status) VALUES($fecha_actual, $idcom, '$identificador', '$statuspago')";
          //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());


        $queryMensaje = "INSERT INTO mensajes(idcom, asunto, mensaje, destinatario, remitente, fecha) VALUES($idcom, '$asunto2', '$cuerpo2', 'ADM', 'COM', $fecha_actual)";
        $ejecutar = mysql_query($queryMensaje,$dspp) or die(mysql_error());

      /******************************* FIN MAIL FUNDEPPO *************************************************/
      /********************************************************************************/


  }else{
    $comprobantePago = NULL;
  }
  $adjunto = $comprobantePago;

  $query = "INSERT INTO membresia (estado, adjunto,fechaupload,idcom) VALUES ('$statuspago','$adjunto',$fechaupload,$idcom)";
  $insertar = mysql_query($query,$dspp) or die(mysql_error());
  
  //echo "la consulta es: ".$query;

  $idmembresia = mysql_insert_id($dspp);



  //$queryFecha = "INSERT INTO fecha (fecha, idexterno, identificador, status) VALUES ($fechaupload, $idexterno, '$identificador', '$statuspago')";
  //$insertarFecha = mysql_query($queryFecha,$dspp) or die(mysql_error());

  $queryFecha = "INSERT INTO fecha (fecha, idexterno, idcom, idoc, idmembresia, identificador, status) VALUES ($fecha_actual, $idexterno, $idcom, $idoc, $idmembresia, '$identificador', '$statuspago')";
  $insertarFecha = mysql_query($queryFecha,$dspp) or die(mysql_error());
  //echo "<br>".$queryFecha;

  $update = "UPDATE certificado SET statuspago = '$statuspago' WHERE idcertificado = $idcertificado";
  $insertarupdate = mysql_query($update,$dspp) or die(mysql_error());
  //echo "<br>".$update;
}
/****************************************************************************************************/
/****************************************************************************************************/
/****************************************************************************************************/



$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());
//$row_com = mysql_fetch_assoc($com);

if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_com);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = ceil($totalRows_com/$maxRows_com)-1;

$queryString_com = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_com") == false && 
        stristr($param, "totalRows_com") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_com = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);

/*************************** VARIABLES DE CONTROL **********************************/
  $estado_interno = "2";

 

/*************************** VARIABLES DE CONTROL **********************************/

?>

<hr>

<div class="panel panel-primary">
  <div class="panel-heading">Solicitudes</div>
  <div class="panel-body">

<?php 
      if(isset($_POST['mensaje'])){
    ?>
      <div class="alert alert-success" role="alert"><?php echo "<b>".$_POST['mensaje']."</b>"; ?></div>
    <?php
      }
     ?>
            <div class="col-xs-12">
              <?php if(isset($_POST['membresia']) && $_POST['membresia'] == "1"){ ?>
                <div class="alert alert-info alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Su <strong>comprobante de pago</strong> ha sido enviado a revisión, se le notificara cuando haya sido aprobada su membresia.
                </div>
              <?php } ?>
            </div>



    <table class="table table-bordered table-striped" style="font-size:12px">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Fecha Solicitud</th>
          <!--<th class="text-center">Nombre</th>-->
          <th class="text-center">Empresa</th>
          <!--<th class="text-center">Contacto OPP</th>
          <th class="text-center">País</th>-->
          <th class="text-center">OC</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización</th>
          <th class="text-center">Observaciones Solicitud</th>
          <th class="text-center">Resolución de Objeción</th>
          <th class="text-center">Certificación</th>

          <!--<th>Razón social</h6></th>
          <th>Dirección fiscal</th>
          <th>RFC</th>-->
          <!--<th>Eliminar</th>-->
        </tr>
      </thead>
      <tbody>
        <?php $cont=0; while ($row_com = mysql_fetch_assoc($com)) {$cont++; ?>

          <tr class="">
            <td>
              <?php 
              echo $row_com['idsolicitud_registro']; 
              $consultaFecha = "SELECT idfecha FROM fecha WHERE idcom = '$row_com[idcom]' AND identificador = 'COM' AND status = 20";
              $ejecutar = mysql_query($consultaFecha,$dspp) or die(mysql_error());
              $totalFecha = mysql_num_rows($ejecutar);

              ?>
            </td>
<!-------------------------------- INICIAR BOTON FECHA ELABORACION ---------------------------------->
            <?php  $fecha = $row_com['fecha_elaboracion']; ?>
              <?php 
                if($row_com['status_interno'] == $estado_interno){
              ?>
                <td>      
                  <a class="btn btn-sm btn-primary" style="width:100%" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_com['idsolicitud_registro']; ?>&contact" aria-label="Left Align">

                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    <?php echo  date("d/m/Y", $fecha); ?><br>Ver solicitud
                  </a>   
                </td>
              <?php
                }else{
              ?>
                <td>
                  <a class="btn btn-sm btn-primary" style="width:100%" href="?SOLICITUD&amp;detailBlock&amp;idsolicitud=<?php echo $row_com['idsolicitud_registro']; ?>&contact" aria-label="Left Align">

                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    <?php echo  date("d/m/Y", $fecha); ?><br>Ver solicitud
                  </a>    
                </td>
              <?php 
                }
              ?>
<!-------------------------------- TERMINA BOTON FECHA ELABORACION ---------------------------------->
              
              

          <!-------------------------------- INICIA NOMBRE DE LA EMPRESA  ---------------------------------->              
              <td>
                <?php echo $row_com['nombreCOM']; ?>
              </td>
          <!-------------------------------- TERMINA NOMBRE DE LA EMPRESA ---------------------------------->


          <!-------------------------------- INICIA NOMBRE DEL OC ---------------------------------->
                <td>
                  
                    <?php 
                      if(isset($row_com['nombreOC'])){
                        echo $row_com['nombreOC'];
                      }else{
                        echo "No Disponible";
                      } 
                    ?>
                  
                </td>
          <!-------------------------------- TERMINA NOMBRE DEL OC ---------------------------------->

          <!-------------------------------- INICIA ESTATUS SOLICITUD ---------------------------------->
              <td>
              <?php 
                $query = "SELECT * FROM status_publico WHERE idstatus_publico = $row_com[status_publico]";
                $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                $estatus_publico = mysql_fetch_assoc($ejecutar);

                $query = "SELECT * FROM status WHERE idstatus = $row_com[status_interno]";
                $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                $estatus_interno = mysql_fetch_assoc($ejecutar);
               ?>

                <?php if($estatus_interno['idstatus'] == 2){ ?>
                
                    <a class="btn btn-sm btn-warning" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_com['idsolicitud_registro']; ?>">
                      <span class="glyphicon glyphicon-list-alt"></span> <?php echo $estatus_interno['nombre']; ?>
                    </a>
                 

                <?php }else if($row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15 && $row_com['status_interno'] != 24){?>
                    <p class="alert alert-warning" style="padding:7px;"><?php echo $estatus_interno['nombre']; ?></p>
                  <?php }else{ ?>
                    <p class="alert alert-danger" style="padding:7px;"><?php echo $estatus_publico['nombre']; ?></p>
                <?php } ?>


              </td>
          <!-------------------------------- TERMINA ESTATUS SOLICITUD ---------------------------------->

          <!-------------------------------- INICIA SECCIÓN COTIZACION ---------------------------------->
              <td class="text-center" style="width:150px;">
                  <form action="" method="post">  

                <?php 
                if(!empty($row_com['cotizacion_com'])){
                ?>
                  <a class="btn btn-sm btn-success" href="<?echo $row_com['cotizacion_com']?>" target="_blank" type="button" data-toggle="tooltip" data-placement="top" title="Descargar Cotización">
                    <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>
                  </a>
                <?php
                }else{
                ?>
                  <a class="btn btn-sm btn-default disabled" href="">
                    <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>
                  </a>
                <?php
                }
                 ?>
   
                <?php 
                    if($row_com['status_interno'] == 1 || $row_com['status_interno'] == 2 || $row_com['status_interno'] == 3 || $row_com['status_interno'] == 20){
                 ?>
                    <button class="btn btn-sm btn-default" disabled>
                      <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                    </button>
                    <button class="btn btn-sm btn-default" disabled>
                      <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 
                    </button>
                 <?php 
                  }else if($row_com['status_interno'] == "17" ){
                  ?>
                    <button class="btn btn-sm btn-danger" type="submit" name="propuesta_txt" data-toggle="tooltip" data-placement="top" title="Aceptar Cotización" value="18"·>
                      <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                    </button>

                    <button class="btn btn-sm btn-default" type="submit" name="propuesta_txt" data-toggle="tooltip" data-placement="top" title="Rechazar Cotización" value="18">
                      <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 
                    </button>
                      <input type="hidden" name="fecha_elaboracion" value="<?php echo $row_com['fecha_elaboracion']; ?>" >

                <?php 
                  }else if($row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15){
                 ?>   
                    <button class="btn btn-sm btn-success" type="submit" name="propuesta_txt" disabled>
                      <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Aceptado
                    </button>
                <?php
                  }else if($row_com['status_interno'] == 24){
                ?>
                    <button class="btn btn-sm btn-danger" type="submit" name="propuesta_txt" disabled>
                      <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Rechazada
                    </button>
                <?php 
                  } ?>
                    <input type="hidden" value="Haz aceptado la propuesta, en breve seras contactado" name="mensaje" />
                    <input type="hidden" value="1" name="com_propuesta" />
                    <input type="hidden" value="<?php echo $row_com['p1_correo']?>" name="emailCOM1">
                    <input type="hidden" value="<?php echo $row_com['p2_correo']?>" name="emailCOM2">
                    <input type="hidden" value="<?php echo $row_com['email']?>" name="emailOC">
                    <input type="hidden" value="<?php echo $row_com['nombreOC']?>" name="nombreOC">
                    <input type="hidden" value="<?php echo $row_com['nombre']?>" name="nombreCOM">
                    <input type="hidden" value="<?php if(isset($row_com['telefono'])){echo $row_com['telefono'];}else if(isset($row_com['p1_telefono'])){echo $row_com['p1_telefono'];}?>" name="telefonoCOM">
                    <input type="hidden" value="<?php echo $row_com['abreviacion']?>" name="abreviacionCOM">
                    <input type="hidden" value="<?php echo $row_com['pais']?>" name="paisCOM">
                    <input type="hidden" value="<?php echo $row_com['idsolicitud_registro']; ?>" name="idsolicitud_registro" />
                    <input type="hidden" value="<?php echo $row_com['p1_nombre'];?>" name="nombreCOM1">
                    <input type="hidden" value="<?php echo $row_com['p2_nombre'];?>" name="nombreCOM2">
                    <input type="hidden" value="<?php echo $row_com['ciudad'] ?>" name="ciudadCOM">
                    <input type="hidden" value="<?php echo $row_com['estado']?>" name="estadoCOM">
                    <input type="hidden" value="<?php echo $row_com['idoc'] ?>" name="idoc">
                    <input type="hidden" value="<?php echo $totalFecha?>" name="totalFecha">
                  </form>
         
              </td>
          <!-------------------------------- TERMINA SECCIÓN COTIZACION ---------------------------------->



              <!-------------------------------- OBSERVACIONES ---------------------------------->
              <td class="text-center">
                <?php if(empty($row_com['observaciones'])){ ?>
                
                    <button class="btn btn-sm btn-default" disabled>
                      <span class="glyphicon glyphicon-list-alt"></span> Consultar
                    </button> 
                       
                <?php }else{ ?>
                  
                   <a class="btn btn-sm btn-info" style="width:100%" href="?SOLICITUD&amp;detail&amp;idsolicitud=<?php echo $row_com['idsolicitud_registro']; ?>&contact" aria-label="Left Align">
                      <span class="glyphicon glyphicon-list-alt"></span> Consultar
                    </a>
                
                <?php } ?>
              </td>
              <!-------------------------------- OBSERVACIONES ---------------------------------->


            <?php 
              $query_objecion = "SELECT * FROM objecion WHERE idsolicitud_registro = $row_com[idsolicitud_registro]";
              $ejecutar = mysql_query($query_objecion, $dspp) or die(mysql_error());
              $registroObjecion = mysql_fetch_assoc($ejecutar);

             ?>

              <!-------------------------------- RESOLUCION DE OBJECION ---------------------------------->
              <td>
                <?php 
                  if(!empty($totalFecha)){
                    echo "<h6 class='alert alert-success' style='margin:7px;'>Proceso de Renovación</h6>";
                  }else{
                 ?>
                <?php 
                if(isset($registroObjecion['dictamen'])){
                  echo "<p class='alert alert-info' style='padding:7px;display:inline;'>$registroObjecion[dictamen]</p>";
                }
                 ?>
                
                  <?php if($row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15){ ?>
                    <?php if(!empty($registroObjecion['adjunto'])){ ?>

                      <a class="btn btn-sm btn-info" href="<?echo $registroObjecion['adjunto'];?>" target="_blank">
                        <span class="glyphicon glyphicon-download-alt">
                      </a>

                    <?php }else if(!empty($registroObjecion)){ ?>
                    <?php 
                      $query = "SELECT * FROM status_publico WHERE idstatus_publico = $registroObjecion[status]";
                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                      $statusObjecion = mysql_fetch_assoc($ejecutar);
                     ?>
                      <p class="alert alert-danger" role="alert" style="padding:7px;"><?php echo $statusObjecion['nombre']; ?></p>
                    <?php } ?>
                  <?php } ?>
                
                <?php } ?>

                <?php /*
                  $consultaFecha = "SELECT idfecha FROM fecha WHERE idexterno = '$row_com[idcom]' AND identificador = 'COM' AND status = 20";
                  $ejecutar = mysql_query($consultaFecha,$dspp) or die(mysql_error());
                  $total = mysql_num_rows($ejecutar);

                  if(!empty($total)){
                    echo "<h6 class='alert alert-success'>Proceso de Renovación</h6>";
                  }else{
                 ?>


                  <?php if($row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15){ ?>
                    <?php if(!empty($registroObjecion['adjunto'])){ ?>
                        <a class="btn btn-sm btn-info" href="<?echo $registroObjecion['adjunto'];?>" target="_blank">
                        <span class="glyphicon glyphicon-download-alt"></span> Descargar<br>Resolución</a>
                    <?php }else if(!empty($registroObjecion)){ ?>
                    <?php 
                      $query = "SELECT * FROM status_publico WHERE idstatus_publico = $registroObjecion[status]";
                      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                      $statusObjecion = mysql_fetch_assoc($ejecutar);
                     ?>
                      <div class="alert alert-danger" role="alert"><?php echo $statusObjecion['nombre']; ?></div>
                    <?php } ?>
                  <?php } ?>

                <?php } */?>
              </td>
              <!-------------------------------- RESOLUCION DE OBJECION ---------------------------------->

              <!-------------------------------- CERTIFICACION ---------------------------------->
              <td>
                  <?php if($row_com['status_interno'] != 1 && $row_com['status_interno'] != 2 && $row_com['status_interno'] != 3 && $row_com['status_interno'] != 14 && $row_com['status_interno'] != 15 && $row_com['status_interno'] != 17 && $row_com['status_interno'] != 20  && $row_com['status_interno'] != 24){ ?>
                      <button class="btn btn-sm btn-warning" data-toggle="modal" <?php echo "data-target='#myModal".$row_com['idsolicitud_registro']."'"?>>
                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar <br>Status
                      </button>
                  <?php }else{?>
                      <button class="btn btn-sm btn-default" disabled><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Consultar<br>Status</button>
                  <?php } ?>

              </td>
              <!-------------------------------- CERTIFICACION ---------------------------------->

                
                    <?php 
                      //$query = "SELECT * FROM certificado WHERE idsolicitud = $row_com[idsolicitud_registro]";
                      //$query = "SELECT certificado.*, MAX(fecha) AS 'fecha',fecha.idfecha, fecha.idexterno FROM certificado INNER JOIN fecha ON certificado.idcertificado = fecha.idexterno WHERE certificado.idsolicitud_registro = $row_com[idsolicitud_registro]";

                      $query = "SELECT certificado.*, MAX(fecha) AS 'fecha',fecha.idfecha, fecha.idexterno, fecha.idcertificado FROM certificado INNER JOIN fecha ON certificado.idcertificado = fecha.idcertificado WHERE certificado.idsolicitud_registro = $row_com[idsolicitud_registro]";
                      $ejecutar = mysql_query($query, $dspp) or die(mysql_error());
                      $registroCertificado = mysql_fetch_assoc($ejecutar);

                     ?>
                    <!-- Modal -->
                    <form action="" method="post" id="pagoCertificado" enctype="multipart/form-data">
                      <div class="modal fade" <?php echo "id='myModal".$row_com['idsolicitud_registro']."'" ?> tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title" id="myModalLabel">Status Certificado</h4>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-xs-12">

                                  <!------>
                                  <div class="col-xs-6 ">

                                      <?php if(empty($registroCertificado['idcertificado'])){ ?>
                                        <?php if($row_com['status_interno'] == 19){ ?>
                                          <div class="col-xs-12 alert alert-warning" role="alert">
                                            <div class="col-xs-12">
                                              Status Certificado: <strong>Se ha iniciado el proceso de certificación.</strong>  
                                            </div>                                  
                                          </div>
                                        <?php }else{ ?>
                                          <div class="col-xs-12 alert alert-danger" role="alert">
                                            <div class="col-xs-12">
                                              Status Certificado: <strong>No se ha iniciado el proceso de certificación.</strong>  
                                            </div>                                  
                                          </div>
                                        <?php } ?>
                                      <?php }else{ ?>
                                        <div class="col-xs-12 alert alert-success" role="alert">
                                          <div class="col-xs-12">
                                            Status Certificado al día: <b><?echo date("Y/m/d", $registroCertificado['fecha']) ?></b>
                                          </div>
                                          <hr>
                                          <div class="col-xs-12 ">
                                          <?
                                            $query = "SELECT * FROM status WHERE idstatus = $registroCertificado[status]";
                                            $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
                                            $estatus = mysql_fetch_assoc($ejecutar);
                                          ?>

                                            <h4><?php echo $estatus['nombre']; ?></h4>
                                          </div>        
                                        </div>                                
                                      <?php } ?>
                                    <?php if($registroCertificado['statuspago'] == "REVISION"){ ?>
                                      <div class="col-xs-12 alert alert-warning" role="alert">
                                        <div class="col-xs-12"><h4>Certificado</h4></div>
                                        <hr>
                                        <div class="col-xs-6">Su certificado vence el dia: <?echo $registroCertificado['vigenciafin']?></div>
                                        <div class="col-xs-6">
                                          <button class="btn btn-default" disabled>Descargar Certificado</button>
                                        </div> 
                                      </div>
                                    <?php }else if(empty($registroCertificado['statuspago'])){ ?>
                                      <div class="col-xs-12 alert alert-danger" role="alert">
                                        <div class="col-xs-12"><h4>Certificado</h4></div>
                                        <hr>
                                        <div class="col-xs-12">No se ha finalizado el proceso de certificación.</div>
                                      </div>
                                    <?php }else if($registroCertificado['statuspago'] == "APROBADO"){ ?>
                                      <div class="col-xs-12 alert alert-success" role="alert">
                                        <div class="col-xs-12"><h4>Certificado</h4></div>
                                        <hr>
                                        <div class="col-xs-6">Su certificado vence el dia: <?echo $registroCertificado['vigenciafin']?></div>
                                        <div class="col-xs-6">
                                          <a class="btn btn-info" href="<?echo $registroCertificado['adjunto'];?>" target="_blank">Descargar Certificado</a>
                                        </div> 
                                      </div>                            
                                    <?php }else if($registroCertificado['statuspago'] == "POR REALIZAR"){ ?>
                                      <div class="col-xs-12 alert alert-warning" role="alert">
                                        <div class="col-xs-12"><h4>Certificado</h4></div>
                                        <hr>
                                        <div class="col-xs-6">Su certificado vence el dia: <?echo $registroCertificado['vigenciafin']?></div>
                                        <div class="col-xs-6">
                                          <button class="btn btn-default" disabled>Descargar Certificado</button>
                                        </div> 
                                      </div>
                                    
                                    <?php } ?>


                                  </div>
                                  <?php if($registroCertificado['statuspago'] == "APROBADO"){ ?>
                                    <div class="col-xs-6 alert alert-success">
                                      <div class="col-xs-12">
                                      <p>Status Membresia: <b><?if(empty($registroCertificado['statuspago'])){echo "No se ha realizado el pago";}else{echo $registroCertificado['statuspago'];}?></b></p>
                                      <p><strong>Felicidades!!!</strong> su membresia ha sido acreditada, desde este momento podra disponer de su certificado.</p>
                                      </div>
                                      <div class="col-xs-12">

                                        <!--<button class="btn btn-info">Cargar Comprobante de Pago</button>-->
                                        <!--<input name="comprobante" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Comprobante de Pago"> -->
                                      </div>
                                    </div>
                                  <?php }else if($registroCertificado['statuspago'] == "REVISION"){ ?>
                                    <div class="col-xs-6 alert alert-warning">
                                      <div class="col-xs-12">
                                      <p>Status Membresia: <b><?if(empty($registroCertificado['statuspago'])){echo "No se ha realizado el pago";}else{echo $registroCertificado['statuspago'];}?></b></p>
                                      <p>Su comprobante de pago ha sido enviado a revisión, se le notificara cuando haya sido aprobado su membresia</p>
                                      </div>
                                      <div class="col-xs-12">

                                        <!--<button class="btn btn-info">Cargar Comprobante de Pago</button>-->
                                        <!--<input name="comprobante" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Comprobante de Pago"> -->
                                      </div>
                                    </div>
                                  <?php }else if($registroCertificado['statuspago'] == "POR REALIZAR" || empty($registroCertificado['statuspago'])){ ?>
                                    <div class="col-xs-6 alert alert-danger">
                                      <div class="col-xs-12">
                                      <p>Status Membresia: <b><?if(empty($registroCertificado['statuspago'])){echo "No se ha realizado el pago";}else{echo $registroCertificado['statuspago'];}?></b></p>
                                      <p>Una vez realizado el pago de la membresia y haberse acreditado el mismo, podra disponer de su certificado.</p>
                                      </div>
                                      <div class="col-xs-12">
                                        <?php if(empty($registroCertificado['adjunto'])){ ?>
                                          <p class="well">Una vez terminada la certificación, se desbloqueara la opción para que pueda realizar el pago correpondiente a su membresia.</p>
                                        <?php }else{ ?>
                                          <input name="comprobante" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Comprobante de Pago"> 
                                        <?php } ?>
                                        <!--<button class="btn btn-info">Cargar Comprobante de Pago</button>-->
                                      </div>
                                    </div>
                                  <?php } ?>

                                  <!------>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                              <?php if($registroCertificado['statuspago'] == "POR REALIZAR"){ ?>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                              <?php }?>
                              
                              <input type="hidden" name="membresiaidcom" value="<?php echo $row_com['idcom'];?>">
                              <input type="hidden" name="membresiaidoc" value="<?php echo $row_com['idoc'];?>">
                              <input type="hidden" name="idcertificado" value="<?php echo $registroCertificado['idcertificado'];?>">
                              <input type="hidden" name="fechaupload" value="<?php echo time();?>">
                              <input type="hidden" name="membresia" value="1">
                              <input type="hidden" name="statusInterno" value="10">
                              <input type="hidden" name="idsolicitud_registro" value="<?echo $row_com['idsolicitud_registro'];?>">

                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                    <!-- Modal -->
          </tr>
          <?php }  ?>

          <? if($cont==0){?>
          <tr><td colspan="12" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
          <? }?>        
      </tbody>

    </table>






  </div>
</div>




<table>
<tr>
<td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>
<?php
mysql_free_result($com);
?>