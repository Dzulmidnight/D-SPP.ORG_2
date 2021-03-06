<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


/************ VARIABLES DE CONTROL ******************/
//ESTATUS PUBLICO //////////////////////////

//1) Solicitud
//2) En proceso
//3) Evaluacion positiva
//4) Certificada
//5) No certificada

//ESTATUS INTERNO ///////////////////////////
//1) 1ra Evaluacion
//2) Completar informacion
//3) 2da revision
//4) Proceso interrumpido
//5) Evaluacion in situ
//6) Informe de evaluacion
//7) Acciones correctivas
//8) Dictamen positivo
//9) Dictamen negativo
//10) Certificada
//11) Certificado expirado
//12) Certificado por expirar
//13) Suspendida
//14) Cancelada
//15) Desactivacion
//16) Aviso de renovacion del certificado
//20) Renovación del certificado
$charset='utf-8';
$fecha = time();
$idopp = $_SESSION['idopp'];
$ruta_croquis = "../../archivos/oppArchivos/croquis/";
$spp_global = "cert@spp.coop";
$auxiliar = "acc@spp.coop";
$administrador = "yasser.midnight@gmail.com";
/************ VARIABLES DE CONTROL ******************/



if(isset($_POST['insertar_solicitud']) && $_POST['insertar_solicitud'] == 1){
	$estatus_publico = 1; // EN REVISIÓN
	$estatus_interno = NULL;
	$estatus_dspp = 1; // SOLICITUD EN REVISIÓN
	$alcance_opp = "";


	///ACTUALIZAMOS LA INFORMACIÓN PRINCIPAL DE LA OPP DE ACUERDO A LA SOLICITUD
	$updateSQL = sprintf("UPDATE opp SET nombre = %s, sitio_web = %s, email = %s, telefono = %s, pais = %s, razon_social = %s, direccion_oficina = %s, direccion_fiscal = %s, rfc = %s, ruc = %s WHERE idopp = %s",
		GetSQLValueString($_POST['nombre'], "text"),
		GetSQLValueString($_POST['sitio_web'], "text"),
		GetSQLValueString($_POST['email'], "text"),
		GetSQLValueString($_POST['telefono'], "text"),
		GetSQLValueString($_POST['pais'], "text"),
		GetSQLValueString($_POST['razon_social'], "text"),
		GetSQLValueString($_POST['direccion_oficina'], "text"),
		GetSQLValueString($_POST['direccion_fiscal'], "text"),
		GetSQLValueString($_POST['rfc'], "text"),
		GetSQLValueString($_POST['ruc'], "text"),
		GetSQLValueString($idopp, "int"));

	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
	/* INICIA CAPTURA ALCANCE DEL OPP */
	if(isset($_POST['produccion'])){
		$produccion = $_POST['produccion'];
	}else{
		$produccion = '';
	}
	if(isset($_POST['procesamiento'])){
		$procesamiento = $_POST['procesamiento'];
	}else{
		$procesamiento = '';
	}
	if(isset($_POST['exportacion'])){
		$exportacion = $_POST['exportacion'];
	}else{
		$exportacion = '';
	}

	/* TERMINA CAPTURA ALCANCE DEL OPP */

	if(isset($_POST['op_preg12'])){
		$op_preg12 = $_POST['op_preg12'];
	}else{
		$op_preg12 = "";
	}

	if(isset($_POST['op_preg13'])){
		if($_POST['op_preg13'] == 'mayor'){
			$op_preg13 = $_POST['op_preg13_1'];
		}else{
			$op_preg13 = $_POST['op_preg13'];
		}
	}else{
		$op_preg13 = "";
	}


	if(!empty($_FILES['op_preg15']['name'])){
	    $_FILES["op_preg15"]["name"];
	      move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	      $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
	}else{
		$croquis = NULL;
	}

	if($_POST['idoc'] == 'TODOS'){
		$query_oc = "SELECT * FROM oc";
		$consultar = mysql_query($query_oc) or die(mysql_error());

		while($detalle_oc = mysql_fetch_assoc($consultar)){ /// INICIA WHILE ENVIAR A OCS

			// INGRESAMOS LA INFORMACION A LA SOLICITUD DE CERTIFICACION
			$insertSQL = sprintf("INSERT INTO solicitud_certificacion (tipo_solicitud, idopp, idoc, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, resp1, resp2, resp3, resp4, op_preg1, preg1_1, preg1_2, preg1_3, preg1_4, op_preg2, op_preg3, produccion, procesamiento, exportacion, op_preg5, op_preg6, op_preg7, op_preg8, op_preg10, op_preg12, op_preg13, op_preg14, op_preg15, responsable, fecha_registro, estatus_dspp) VALUES (%s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
				   GetSQLValueString($_POST['tipo_solicitud'], "text"),
				   GetSQLValueString($idopp, "int"),
		           GetSQLValueString($detalle_oc['idoc'], "int"),
		           GetSQLValueString($_POST['contacto1_nombre'], "text"),
		           GetSQLValueString($_POST['contacto2_nombre'], "text"),
		           GetSQLValueString($_POST['contacto1_cargo'], "text"),
		           GetSQLValueString($_POST['contacto2_cargo'], "text"),
		           GetSQLValueString($_POST['contacto1_email'], "text"),
		           GetSQLValueString($_POST['contacto2_email'], "text"),
		           GetSQLValueString($_POST['contacto1_telefono'], "text"),
		           GetSQLValueString($_POST['contacto2_telefono'], "text"),
		           GetSQLValueString($_POST['adm1_nombre'], "text"),
		           GetSQLValueString($_POST['adm2_nombre'], "text"),
		           GetSQLValueString($_POST['adm1_email'], "text"),
		           GetSQLValueString($_POST['adm2_email'], "text"),
		           GetSQLValueString($_POST['adm1_telefono'], "text"),
		           GetSQLValueString($_POST['adm2_telefono'], "text"),
		           GetSQLValueString($_POST['resp1'], "text"),
		           GetSQLValueString($_POST['resp2'], "text"),
		           GetSQLValueString($_POST['resp3'], "text"),
		           GetSQLValueString($_POST['resp4'], "text"),
		           GetSQLValueString($_POST['op_preg1'], "text"),
		           GetSQLValueString($_POST['preg1_1'], "text"),
		           GetSQLValueString($_POST['preg1_2'], "text"),
		           GetSQLValueString($_POST['preg1_3'], "text"),
		           GetSQLValueString($_POST['preg1_4'], "text"),
		           GetSQLValueString($_POST['op_preg2'], "text"),
		           GetSQLValueString($_POST['op_preg3'], "text"),
		           GetSQLValueString($produccion, "int"),
		           GetSQLValueString($procesamiento, "int"),
		           GetSQLValueString($exportacion, "int"),
		           GetSQLValueString($_POST['op_preg5'], "text"),
		           GetSQLValueString($_POST['op_preg6'], "text"),
		           GetSQLValueString($_POST['op_preg7'], "text"),
		           GetSQLValueString($_POST['op_preg8'], "text"),
		           GetSQLValueString($_POST['op_preg10'], "text"),
		           GetSQLValueString($op_preg12, "text"),
		           GetSQLValueString($op_preg13, "text"),
		           GetSQLValueString($_POST['op_preg14'], "text"),
		           GetSQLValueString($croquis, "text"),
		           GetSQLValueString($_POST['responsable'], "text"),
		           GetSQLValueString($fecha, "int"),
		           GetSQLValueString($estatus_dspp, "int"));


				  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
				 
				 $idsolicitud_certificacion = mysql_insert_id($dspp); 


					 // INGRESAMOS EL PORCENTAJE DE VENTA DE LOS PRODUCTOS

					 	if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
					 		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_certificacion, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
					 			GetSQLValueString($_POST['organico'], "text"),
					 			GetSQLValueString($_POST['comercio_justo'], "text"),
					 			GetSQLValueString($_POST['spp'], "text"),
					 			GetSQLValueString($_POST['sin_certificado'], "text"),
					 			GetSQLValueString($idsolicitud_certificacion, "int"),
					 			GetSQLValueString($idopp, "int"));
					 		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
					 	}


					/*************************** INICIA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/
					$insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_publico, estatus_interno, estatus_dspp, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
						GetSQLValueString($idsolicitud_certificacion, "int"),
						GetSQLValueString($estatus_publico, "int"),
						GetSQLValueString($estatus_interno, "int"),
						GetSQLValueString($estatus_dspp, "int"),
						GetSQLValueString($fecha, "int"));
					$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
					/*************************** TERMINA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/

					/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/
						if(isset($_POST['certificacion'])){
							$certificacion = $_POST['certificacion'];
						}else{
							$certificacion = NULL;
						}


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

						for($i=0;$i<count($certificacion);$i++){
							if($certificacion[$i] != NULL){
								#for($i=0;$i<count($certificacion);$i++){
								$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_certificacion, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
								    GetSQLValueString($idsolicitud_certificacion, "int"),
								    GetSQLValueString(strtoupper($certificacion[$i]), "text"),
								    GetSQLValueString(strtoupper($certificadora[$i]), "text"),
								    GetSQLValueString($ano_inicial[$i], "text"),
								    GetSQLValueString($interrumpida[$i], "text"));

								$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
								#}
							}
						}
					/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/


					/*************************** INICIA INSERTAR PRODUCTOS ***************************/
					$producto_general = $_POST['producto_general'];
					$producto = $_POST['producto'];
					$volumen = $_POST['volumen'];
					$materia = $_POST['materia'];
					$destino = $_POST['destino'];
					/*$marca_propia = $_POST['marca_propia'];
					$marca_cliente = $_POST['marca_cliente'];
					$sin_cliente = $_POST['sin_cliente'];*/

					for ($i=0;$i<count($producto);$i++) { 
						if($producto[$i] != NULL){

								$array1[$i] = "terminado".$i; 
								$array2[$i] = "marca_propia".$i;
								$array3[$i] = "marca_cliente".$i;
								$array4[$i] = "sin_cliente".$i;

								if(isset($_POST[$array1[$i]])){
									$terminado = $_POST[$array1[$i]];
								}else{
									$terminado = null;
								}
								if(isset($_POST[$array2[$i]])){
									$marca_propia = $_POST[$array2[$i]];
								}else{
									$marca_propia = null;
								}
								if(isset($_POST[$array3[$i]])){
									$marca_cliente = $_POST[$array3[$i]];
								}else{
									$marca_cliente = null;
								}
								if(isset($_POST[$array4[$i]])){
									$sin_cliente = $_POST[$array4[$i]];
								}else{
									$sin_cliente = null;
								}

								//$terminado = $_POST[$array1[$i]];
								//$marca_propia = $_POST[$array2[$i]];
								//$marca_cliente = $_POST[$array3[$i]];
								//$sin_cliente = $_POST[$array4[$i]];

								/*$str = iconv($charset, 'ASCII//TRANSLIT', $producto_general[$i]);
								$producto_general[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

								$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
								$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

								$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
								$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

								$str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
								$materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));*/


							    $insertSQL = sprintf("INSERT INTO productos (idopp, idsolicitud_certificacion, producto_general, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
							    	GetSQLValueString($idopp, "int"),
							        GetSQLValueString($idsolicitud_certificacion, "int"),
							        GetSQLValueString($producto_general[$i], "text"),
							        GetSQLValueString($producto[$i], "text"),
							        GetSQLValueString($volumen[$i], "text"),
							        GetSQLValueString($terminado[$i], "text"),
							        GetSQLValueString($materia[$i], "text"),
							        GetSQLValueString($destino[$i], "text"),
							        GetSQLValueString($marca_propia[$i], "text"),
							        GetSQLValueString($marca_cliente[$i], "text"),                    
							        GetSQLValueString($sin_cliente[$i], "text"));
							  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
						}
					}
					/***************************** TERMINA INSERTAR PRODUCTOS ******************************/

					///// INICIA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
					$asunto = "D-SPP Solicitud de Certificación para Organizaciones de Pequeños Productores";


					$cuerpo_correo = '
						<html>
						<head>
							<meta charset="utf-8">
						</head>
						<body>
						
							<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; text-align:justify; color: #797979;" border="0" width="650px">
							  <thead>
							    <tr>
							      <th scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
							      <th scope="col" align="left" width="280"><strong>Solicitud de Certificación para Organizaciones de Pequeños Productores / Certification Application for Small Producers’ Organizations </strong></th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <td colspan="2" style="padding-top:10px;">
							        <p>
							          Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
							        </p>
							        <p>
							          In order to consult the application, please open a session in your  Certification Entity (CE) account at the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
							        </p>
							      </td>
							    </tr>
							    <tr>
							      <td colspan="2">
							        <p>
							          <b>Teléfono Organización / Organization phone:</b> '.$_POST['telefono'].'
							        </p>
							        <p>
							          <b>Pais / Country:</b> '.$_POST['pais'].'
							        </p>
							        <p>
							          <b>Email:</b> '.$_POST['email'].'<br>
							          <b>Email:</b> '.$_POST['contacto1_email'].'
							        </p>
							      </td>
							    </tr>
							    <tr>
							      <td colspan="2">
							        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
							          <tbody>
							            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
							              <td width="130px">Nombre de la organización/Organization name</td>
							              <td width="130px">País / Country</td>
							              <td width="130px">Organismo de Certificación / Certification Entity</td>
							           
							              <td width="130px">Fecha de solicitud/Date of application</td>
							            </tr>
							            <tr style="font-size: 12px;">
							              <td style="padding:10px;">
							                '.$_POST['nombre'].'
							              </td>
							              <td style="padding:10px;">
							                '.$_POST['pais'].'
							              </td>
							              <td style="padding:10px;">
							                '.$detalle_oc['nombre'].'
							              </td>
							              <td style="padding:10px;">
							              '.date('d/m/Y', $fecha).'
							              </td>
							            </tr>

							          </tbody>
							        </table>  
							      </td>
							    </tr>
							    <tr>
							      <td colspan="2">
							        <p><span style="color:red">¿Qué es lo de debo realizar ahora?. Debes revisar la solicitud y cargar una cotización</span></p>
							        <ol>
							          <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organismo de Certificación(OC).</li>
							          <li>Dentro de tu cuenta debes seleccionar Solicitudes > Solicitudes OPP.</li>
							          <li>Dentro de la tabla solicitudes debes localizar la columna "Acciones" Y seleccionar el boton Azul Consultar.</li>
							          <li>Para poder enviar la cotización debes seleccionar el "Procedimiento de Certificación" y cargar la cotización.</li>
							        </ol>
							      </td>
							    </tr>
							    <tr>
							      <td colspan="2">
							        <p><span style="color:red">What should I do now? You should review the application and upload a price quote.</span></p>
							        <ol>
							          <li>You should open a session in the D-SPP system as a Certification Entity (CE).</li>
							          <li>Within your account, you should select ApplicationsSolicitudes > SPO Applications Solicitudes OPP.</li>
							          <li>In the applications table, you should locate the column entitled “Actions” and select the Blue Consult button.</li>
							          <li>To send your price quote, you should select “Certification Procedure” and upload your price quote.</li>
							        </ol>

							      </td>
							    </tr>
							  </tbody>
							</table>

						</body>
						</html>
					';
					///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
					$destinatario = $detalle_oc['email1'];
					if(isset($detalle_oc['email1'])){
						//$mail->AddAddress($detalle_oc['email1']);

						$token = strtok($detalle_oc['email1'], "\/\,\;");
						while ($token !== false)
						{
							$mail->AddAddress($token);
							$token = strtok('\/\,\;');
						}

					}
					$destinatario = $detalle_oc['email2'];
					if(isset($detalle_oc['email2'])){
						//$mail->AddAddress($oc['email2']);
						$token = strtok($detalle_oc['email2'], "\/\,\;");
						while ($token !== false)
						{
							$mail->AddAddress($token);
							$token = strtok('\/\,\;');
						}

					}
					if(isset($_POST['email'])){
						//$mail->AddCC($_POST['email']);
						$token = strtok($_POST['email'], "\/\,\;");
						while ($token !== false)
						{
							$mail->AddCC($token);
							$token = strtok('\/\,\;');
						}

					}
					if(isset($_POST['contacto1_email'])){
						//$mail->AddCC($_POST['contacto1_email']);
						$token = strtok($_POST['contacto1_email'], "\/\,\;");
						while ($token !== false)
						{
							$mail->AddCC($token);
							$token = strtok('\/\,\;');
						}

					}

				    $mail->AddCC($administrador);
				    //$mail->AddBCC($administrador);
				    $mail->AddBCC($spp_global);
				    $mail->AddBCC($auxiliar);
			        //$mail->Username = "soporte@d-spp.org";
			        //$mail->Password = "/aung5l6tZ";
			        $mail->Subject = utf8_decode($asunto);
			        $mail->Body = utf8_decode($cuerpo_correo);
			        $mail->MsgHTML(utf8_decode($cuerpo_correo));
			        $mail->Send();
			        $mail->ClearAddresses();

		}//// TERMINA WHILIE ENVIAR A OCS



	}else{

		// INGRESAMOS LA INFORMACION A LA SOLICITUD DE CERTIFICACION
		$insertSQL = sprintf("INSERT INTO solicitud_certificacion (tipo_solicitud, idopp, idoc, contacto1_nombre, contacto2_nombre, contacto1_cargo, contacto2_cargo, contacto1_email, contacto2_email, contacto1_telefono, contacto2_telefono, adm1_nombre, adm2_nombre, adm1_email, adm2_email, adm1_telefono, adm2_telefono, resp1, resp2, resp3, resp4, op_preg1, preg1_1, preg1_2, preg1_3, preg1_4, op_preg2, op_preg3, produccion, procesamiento, exportacion, op_preg5, op_preg6, op_preg7, op_preg8, op_preg10, op_preg12, op_preg13, op_preg14, op_preg15, responsable, fecha_registro, estatus_dspp) VALUES (%s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			   GetSQLValueString($_POST['tipo_solicitud'], "text"),
			   GetSQLValueString($idopp, "int"),
	           GetSQLValueString($_POST['idoc'], "int"),
	           GetSQLValueString($_POST['contacto1_nombre'], "text"),
	           GetSQLValueString($_POST['contacto2_nombre'], "text"),
	           GetSQLValueString($_POST['contacto1_cargo'], "text"),
	           GetSQLValueString($_POST['contacto2_cargo'], "text"),
	           GetSQLValueString($_POST['contacto1_email'], "text"),
	           GetSQLValueString($_POST['contacto2_email'], "text"),
	           GetSQLValueString($_POST['contacto1_telefono'], "text"),
	           GetSQLValueString($_POST['contacto2_telefono'], "text"),
	           GetSQLValueString($_POST['adm1_nombre'], "text"),
	           GetSQLValueString($_POST['adm2_nombre'], "text"),
	           GetSQLValueString($_POST['adm1_email'], "text"),
	           GetSQLValueString($_POST['adm2_email'], "text"),
	           GetSQLValueString($_POST['adm1_telefono'], "text"),
	           GetSQLValueString($_POST['adm2_telefono'], "text"),
	           GetSQLValueString($_POST['resp1'], "text"),
	           GetSQLValueString($_POST['resp2'], "text"),
	           GetSQLValueString($_POST['resp3'], "text"),
	           GetSQLValueString($_POST['resp4'], "text"),
	           GetSQLValueString($_POST['op_preg1'], "text"),
	           GetSQLValueString($_POST['preg1_1'], "text"),
	           GetSQLValueString($_POST['preg1_2'], "text"),
	           GetSQLValueString($_POST['preg1_3'], "text"),
	           GetSQLValueString($_POST['preg1_4'], "text"),
	           GetSQLValueString($_POST['op_preg2'], "text"),
	           GetSQLValueString($_POST['op_preg3'], "text"),
	           GetSQLValueString($produccion, "int"),
	           GetSQLValueString($procesamiento, "int"),
	           GetSQLValueString($exportacion, "int"),
	           GetSQLValueString($_POST['op_preg5'], "text"),
	           GetSQLValueString($_POST['op_preg6'], "text"),
	           GetSQLValueString($_POST['op_preg7'], "text"),
	           GetSQLValueString($_POST['op_preg8'], "text"),
	           GetSQLValueString($_POST['op_preg10'], "text"),
	           GetSQLValueString($op_preg12, "text"),
	           GetSQLValueString($op_preg13, "text"),
	           GetSQLValueString($_POST['op_preg14'], "text"),
	           GetSQLValueString($croquis, "text"),
	           GetSQLValueString($_POST['responsable'], "text"),
	           GetSQLValueString($fecha, "int"),
	           GetSQLValueString($estatus_dspp, "int"));


			  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
			 
			 $idsolicitud_certificacion = mysql_insert_id($dspp); 

				 // INGRESAMOS EL PORCENTAJE DE VENTA DE LOS PRODUCTOS

				 	if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
				 		$insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_certificacion, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
				 			GetSQLValueString($_POST['organico'], "text"),
				 			GetSQLValueString($_POST['comercio_justo'], "text"),
				 			GetSQLValueString($_POST['spp'], "text"),
				 			GetSQLValueString($_POST['sin_certificado'], "text"),
				 			GetSQLValueString($idsolicitud_certificacion, "int"),
				 			GetSQLValueString($idopp, "int"));
				 		$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
				 	}


				/*************************** INICIA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/
				$insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_certificacion, estatus_publico, estatus_interno, estatus_dspp, fecha_registro) VALUES (%s, %s, %s, %s, %s)",
					GetSQLValueString($idsolicitud_certificacion, "int"),
					GetSQLValueString($estatus_publico, "int"),
					GetSQLValueString($estatus_interno, "int"),
					GetSQLValueString($estatus_dspp, "int"),
					GetSQLValueString($fecha, "int"));
				$insertar = mysql_query($insertSQL,$dspp) or die(mysql_error());
				/*************************** TERMINA INSERTAR PROCESO DE CERTIFICACIÓN ***************************/

				/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/
					if(isset($_POST['certificacion'])){
						$certificacion = $_POST['certificacion'];
					}else{
						$certificacion = NULL;
					}


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

					for($i=0;$i<count($certificacion);$i++){
						if($certificacion[$i] != NULL){
							#for($i=0;$i<count($certificacion);$i++){
							$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_certificacion, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
							    GetSQLValueString($idsolicitud_certificacion, "int"),
							    GetSQLValueString(strtoupper($certificacion[$i]), "text"),
							    GetSQLValueString(strtoupper($certificadora[$i]), "text"),
							    GetSQLValueString($ano_inicial[$i], "text"),
							    GetSQLValueString($interrumpida[$i], "text"));

							$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
							#}
						}
					}
				/*************************** INICIA INSERTAR CERTIFICACIONES ***************************/


				/*************************** INICIA INSERTAR PRODUCTOS ***************************/
				$producto_general = $_POST['producto_general'];
				$producto = $_POST['producto'];
				$volumen = $_POST['volumen'];
				$materia = $_POST['materia'];
				$destino = $_POST['destino'];
				/*$marca_propia = $_POST['marca_propia'];
				$marca_cliente = $_POST['marca_cliente'];
				$sin_cliente = $_POST['sin_cliente'];*/

				for ($i=0;$i<count($producto);$i++) { 
					if($producto[$i] != NULL){

							$array1[$i] = "terminado".$i; 
							$array2[$i] = "marca_propia".$i;
							$array3[$i] = "marca_cliente".$i;
							$array4[$i] = "sin_cliente".$i;

							if(isset($_POST[$array1[$i]])){
								$terminado = $_POST[$array1[$i]];
							}else{
								$terminado = null;
							}
							if(isset($_POST[$array2[$i]])){
								$marca_propia = $_POST[$array2[$i]];
							}else{
								$marca_propia = null;
							}
							if(isset($_POST[$array3[$i]])){
								$marca_cliente = $_POST[$array3[$i]];
							}else{
								$marca_cliente = null;
							}
							if(isset($_POST[$array4[$i]])){
								$sin_cliente = $_POST[$array4[$i]];
							}else{
								$sin_cliente = null;
							}

							//$terminado = $_POST[$array1[$i]];
							//$marca_propia = $_POST[$array2[$i]];
							//$marca_cliente = $_POST[$array3[$i]];
							//$sin_cliente = $_POST[$array4[$i]];

							/*$str = iconv($charset, 'ASCII//TRANSLIT', $producto_general[$i]);
							$producto_general[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

							$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
							$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

							$str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
							$destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

							$str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
							$materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));*/


						    $insertSQL = sprintf("INSERT INTO productos (idopp, idsolicitud_certificacion, producto_general, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
						    	GetSQLValueString($idopp, "int"),
						        GetSQLValueString($idsolicitud_certificacion, "int"),
						        GetSQLValueString($producto_general[$i], "text"),
						        GetSQLValueString($producto[$i], "text"),
						        GetSQLValueString($volumen[$i], "text"),
						        GetSQLValueString($terminado[$i], "text"),
						        GetSQLValueString($materia[$i], "text"),
						        GetSQLValueString($destino[$i], "text"),
						        GetSQLValueString($marca_propia[$i], "text"),
						        GetSQLValueString($marca_cliente[$i], "text"),                    
						        GetSQLValueString($sin_cliente[$i], "text"));
						  $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
					}
				}
				/***************************** TERMINA INSERTAR PRODUCTOS ******************************/

				///// INICIA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
				$asunto = "D-SPP Solicitud de Certificación para Organizaciones de Pequeños Productores";
				$row_oc = mysql_query("SELECT * FROM oc WHERE idoc = $_POST[idoc]", $dspp) or die(mysql_error());
				$oc = mysql_fetch_assoc($row_oc);

				$cuerpo_correo = '
					<html>
					<head>
						<meta charset="utf-8">
					</head>
					<body>
					
						<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; text-align:justify; color: #797979;" border="0" width="650px">
						  <thead>
						    <tr>
						      <th scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
						      <th scope="col" align="left" width="280"><strong>Solicitud de Certificación para Organizaciones de Pequeños Productores / Certification Application for Small Producers’ Organizations </strong></th>
						    </tr>
						  </thead>
						  <tbody>
						    <tr>
						      <td colspan="2" style="padding-top:10px;">
						        <p>
						          Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC(Organismo de Certificación) en el siguiente enlace: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
						        </p>
						        <p>
						          In order to consult the application, please open a session in your  Certification Entity (CE) account at the following link: <a href="http://d-spp.org" target="_new">www.d-spp.org</a>
						        </p>
						      </td>
						    </tr>
						    <tr>
						      <td colspan="2">
						        <p>
						          <b>Teléfono Organización / Organization phone:</b> '.$_POST['telefono'].'
						        </p>
						        <p>
						          <b>Pais / Country:</b> '.$_POST['pais'].'
						        </p>
						        <p>
						          <b>Email:</b> '.$_POST['email'].'<br>
						          <b>Email:</b> '.$_POST['contacto1_email'].'
						        </p>
						      </td>
						    </tr>
						    <tr>
						      <td colspan="2">
						        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
						          <tbody>
						            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
						              <td width="130px">Nombre de la organización/Organization name</td>
						              <td width="130px">País / Country</td>
						              <td width="130px">Organismo de Certificación / Certification Entity</td>
						           
						              <td width="130px">Fecha de solicitud/Date of application</td>
						            </tr>
						            <tr style="font-size: 12px;">
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
						    <tr>
						      <td colspan="2">
						        <p><span style="color:red">¿Qué es lo de debo realizar ahora?. Debes revisar la solicitud y cargar una cotización</span></p>
						        <ol>
						          <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organismo de Certificación(OC).</li>
						          <li>Dentro de tu cuenta debes seleccionar Solicitudes > Solicitudes OPP.</li>
						          <li>Dentro de la tabla solicitudes debes localizar la columna "Acciones" Y seleccionar el boton Azul Consultar.</li>
						          <li>Para poder enviar la cotización debes seleccionar el "Procedimiento de Certificación" y cargar la cotización.</li>
						        </ol>
						      </td>
						    </tr>
						    <tr>
						      <td colspan="2">
						        <p><span style="color:red">What should I do now? You should review the application and upload a price quote.</span></p>
						        <ol>
						          <li>You should open a session in the D-SPP system as a Certification Entity (CE).</li>
						          <li>Within your account, you should select ApplicationsSolicitudes > SPO Applications Solicitudes OPP.</li>
						          <li>In the applications table, you should locate the column entitled “Actions” and select the Blue Consult button.</li>
						          <li>To send your price quote, you should select “Certification Procedure” and upload your price quote.</li>
						        </ol>

						      </td>
						    </tr>
						  </tbody>
						</table>

					</body>
					</html>
				';
				///// TERMINA ENVIO DEL MENSAJE POR CORREO AL OC y a SPP GLOBAL
				$destinatario = $oc['email1'];
				if(isset($oc['email1'])){
					//$mail->AddAddress($oc['email1']);

					$token = strtok($oc['email1'], "\/\,\;");
					while ($token !== false)
					{
						$mail->AddAddress($token);
						$token = strtok('\/\,\;');
					}

				}
				$destinatario = $oc['email2'];
				if(isset($oc['email2'])){
					//$mail->AddAddress($oc['email2']);
					$token = strtok($oc['email2'], "\/\,\;");
					while ($token !== false)
					{
						$mail->AddAddress($token);
						$token = strtok('\/\,\;');
					}

				}
				if(isset($_POST['email'])){
					//$mail->AddCC($_POST['email']);
					$token = strtok($_POST['email'], "\/\,\;");
					while ($token !== false)
					{
						$mail->AddCC($token);
						$token = strtok('\/\,\;');
					}

				}
				if(isset($_POST['contacto1_email'])){
					//$mail->AddCC($_POST['contacto1_email']);
					$token = strtok($_POST['contacto1_email'], "\/\,\;");
					while ($token !== false)
					{
						$mail->AddCC($token);
						$token = strtok('\/\,\;');
					}

				}

			    $mail->AddCC($administrador);
			    //$mail->AddBCC($administrador);
			    $mail->AddBCC($spp_global);
			    $mail->AddBCC($auxiliar);
		        //$mail->Username = "soporte@d-spp.org";
		        //$mail->Password = "/aung5l6tZ";
		        $mail->Subject = utf8_decode($asunto);
		        $mail->Body = utf8_decode($cuerpo_correo);
		        $mail->MsgHTML(utf8_decode($cuerpo_correo));
		        $mail->Send();
		        $mail->ClearAddresses();


	}


	///INGRESAMOS EL TIPO DE SOLICITUD A LA TABLA OPP y EL ALCANCE DE LA OPP
	$updateSQL = sprintf("UPDATE opp SET produccion = %s, procesamiento = %s, exportacion = %s, estatus_opp = %s WHERE idopp = %s",
		GetSQLValueString($produccion, "int"),
		GetSQLValueString($procesamiento, "int"),
		GetSQLValueString($exportacion, "int"),
		GetSQLValueString($_POST['tipo_solicitud'], "int"),
		GetSQLValueString($idopp, "int"));
	$actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

	// INGRESAMOS LOS CONTACTOS DE LA SOLICITUD A LA TABLA DE CONTACTOS
	if(!empty($_POST['contacto1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['contacto1_nombre'], "text"),
			GetSQLValueString($_POST['contacto1_cargo'], "text"),
			GetSQLValueString($_POST['contacto1_telefono'], "text"),
			GetSQLValueString($_POST['contacto1_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['contacto2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['contacto2_nombre'], "text"),
			GetSQLValueString($_POST['contacto2_cargo'], "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm1_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['adm1_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['adm1_telefono'], "text"),
			GetSQLValueString($_POST['adm1_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}
	if(!empty($_POST['adm2_nombre'])){
		$insertSQL = sprintf("INSERT INTO contactos(idopp, nombre, cargo, telefono1, email1, idsolicitud_certificacion) VALUES (%s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($_POST['adm2_nombre'], "text"),
			GetSQLValueString('ADMINISTRATIVO', "text"),
			GetSQLValueString($_POST['contacto2_telefono'], "text"),
			GetSQLValueString($_POST['contacto2_email'], "text"),
			GetSQLValueString($idsolicitud_certificacion, "int"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	}

	// INGRESAMOS EL NUMERO DE SOCIOS A LA TABLA NUM_SOCIOS
	if(isset($_POST['resp1'])){
		if($_POST['tipo_solicitud'] == "NUEVA"){ //si es nueva se inserta un registro de numero de socios
			$insertSQL = sprintf("INSERT INTO num_socios (idopp, numero, fecha_registro) VALUES (%s, %s, %s)",
				GetSQLValueString($idopp, "int"),
				GetSQLValueString($_POST['resp1'], "text"),
				GetSQLValueString($fecha, "int"));
			$ejecutar = mysql_query($insertSQL,$dspp) or die(mysql_error());
		}else{ //si es renovacion, se actualiza el registro de numero de socios
			$updateSQL = sprintf("UPDATE num_socios SET numero = %s, fecha_registro = %s WHERE idopp = %s",
				GetSQLValueString($_POST['resp1'], "text"),
				GetSQLValueString($fecha, "int"), 
				GetSQLValueString($idopp, "int"));
			$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
		}
	}




 		$mensaje = "Se ha enviado la Solicitud de Certificacion al OC, en breve seras contactado";


}

  //$insertGoTo = "main_menu.php?SOLICITUD&add&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
$query = "SELECT * FROM opp WHERE idopp = $idopp";
$row_opp = mysql_query($query,$dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);

if($dias < 100){
?>
	<h4 class="alert alert-danger text-justify">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> No puedes ingresar una nueva Solicitud de Certificación para Organizaciones de Pequeños Productores, ya que actualmente cuenta con una solicitud en proceso. Para saber más por favor ponerse en contacto con el area de soporte: soporte@d-spp.org
	</h4>
<?php
}else{
?>
	<div class="row" style="font-size:12px;">
		<?php 
		if(isset($mensaje)){
		?>
		<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <?php echo $mensaje; ?>
		</div>
		<?php
		}
		?>

		<form action="" name="" method="POST" enctype="multipart/form-data">

				<div class="col-md-12 alert alert-primary" style="padding:7px;">
					<h3 class="text-center">Solicitud de Certificación para Organizaciones de Pequeños Productores</h3>
				</div>

				<div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>DATOS GENERALES</b></div>

				<div class="row">

						
							<div class="col-md-12 alert alert-warning" style="padding:5px;">
								<ul>
									<li>
										<b>
											SI CONTARON CON CERTITICACION DE SPP ANTERIORMENTE (CON EL ORGANISMO DE CERTIFICACIÓN ACTUAL ó CON OTRO ORGANISMO DE CERTIFICACIÓN)DEBEN ELEGIR <span style="color:red">"RENOVACION DEL CERTIFICADO"</span>
										</b>
									</li>
									<li><b>SI ES LA PRIMERA VEZ QUE SE CERTIFICAN DEBEN ELEGIR <span style="color:red">"PRIMERA VEZ"</span></b></li>
								</ul>
							</div>

							<div class="col-md-6">
								<div class="col-xs-12">
									<b>ENVAR AL OC (selecciona el OC al que deseas enviar la solicitud):</b>
								</div>
								<div class="col-xs-12">
									<select class="form-control" name="idoc" id="" required>
										<option value="">Seleccione un OC</option>
										<?php 
										$query = "SELECT idoc, abreviacion FROM oc";
										$row_oc = mysql_query($query,$dspp) or die(mysql_error());

										while($oc = mysql_fetch_assoc($row_oc)){
										?>
										<option value="<?php echo $oc['idoc']; ?>" <?php if($opp['idoc'] == $oc['idoc']){ echo "selected"; } ?>><?php echo $oc['abreviacion']; ?></option>
										<?php
										}
										 ?>
										 <option value="TODOS">ENVIAR A TODOS LOS OC</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="col-xs-12">
									<p class="text-center"><strong>SELECCIONE EL TIPO DE SOLICITUD</strong></p>
								</div>
								<div class="col-xs-6">
									<label for="nueva">PRIMERA VEZ</label>
									<input type="radio" class="form-control" id="nueva" name="tipo_solicitud" value="NUEVA">
								</div>
								<div class="col-xs-6">
									<label for="renovacion">RENOVACIÓN DEL CERTIFICADO</label>
									<input type="radio" class="form-control" id="renovacion" name="tipo_solicitud" value="RENOVACION">
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
						<input type="text" class="form-control" id="spp" name="spp" value="<?php echo $opp['spp']; ?>">

						<label for="nombre">NOMBRE COMPLETO DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES: </label>
						<textarea name="nombre" id="nombre" class="form-control"><?php echo $opp['nombre']; ?></textarea>


						<label for="pais">PAÍS:</label>
						<?php 
						$row_pais = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
						 ?>
						 <select name="pais" id="pais" class="form-control">
						 	<option value="">Selecciona un País</option>
						 	<?php 
						 	while($pais = mysql_fetch_assoc($row_pais)){
						 		if(utf8_encode($pais['nombre']) == $opp['pais']){
						 			echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
						 		}else{
						 			echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
						 		}
						 	}
						 	 ?>
						 </select>

						<label for="direccion_oficina">DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES(CALLE, BARRIO, LUGAR, REGIÓN)</label>
						<textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $opp['direccion_oficina']; ?></textarea>

						<label for="email">CORREO ELECTRÓNICO:</label>
						<input type="text" class="form-control" id="email" name="email" value="<?php echo $opp['email']; ?>">

						<label for="email">TELÉFONOS (CODIGO DE PAÍS + CÓDIGO DE ÁREA + NÚMERO):</label>
						<input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $opp['telefono']; ?>">	

						<label for="sitio_web">SITIO WEB:</label>
						<input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $opp['sitio_web']; ?>">

					</div>

					<div class="col-md-6">
						<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">DATOS FISCALES PARA FACTURACIÓN</div>

						<label for="razon_social">RAZÓN SOCIAL</label>
						<input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $opp['razon_social']; ?>">

						<label for="direccion_fiscal">DIRECCIÓN FISCAL</label>
						<textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal"><?php echo $opp['direccion_fiscal']; ?></textarea>

						<label for="rfc">RFC</label>
						<input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $opp['rfc']; ?>">

						<label for="ruc">RUC</label>
						<input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $opp['ruc']; ?>">
					</div>
				</div>
				<!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


				<!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>

					<div class="col-md-6">
						<div class="col-xs-12 text-center alert alert-warning" style="padding:7px;">PERSONA(S) DE CONTACTO</div>

						<div class="col-xs-12">
							<div class="row">
								<label for="persona1">PERSONA(S) DE CONTACTO</label>
								<input type="text" class="form-control" id="persona1" name="contacto1_nombre" placeholder="* Nombre Persona 1" required>
								<input type="text" class="form-control" id="" name="contacto2_nombre" placeholder="Nombre Persona 2">

								<label for="cargo">CARGO</label>
								<input type="text" class="form-control" id="cargo" name="contacto1_cargo" placeholder="* Cargo Persona 1" required>
								<input type="text" class="form-control" id="" name="contacto2_cargo" placeholder="Cargo Persona 2">

								<label for="email">CORREO ELECTRÓNICO</label>
								<input type="email" class="form-control" id="email" name="contacto1_email" placeholder="* Email Persona 1" required>
								<input type="email" class="form-control" id="" name="contacto2_email" placeholder="Email Persona 2">

								<label for="telefono">TELEFONO</label>
								<input type="text" class="form-control" id="telefono" name="contacto1_telefono" placeholder="* Telefono Persona 1" required>
								<input type="text" class="form-control" id="" name="contacto2_telefono" placeholder="Telefono Persona 2">
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONA(S) ÁREA ADMINISTRATIVA</div>

						<label for="persona_adm">PERSONA(S) DEL ÁREA ADMINSITRATIVA</label>
						<input type="text" class="form-control" id="persona_adm" name="adm1_nombre" placeholder="Nombre Persona 1">
						<input type="text" class="form-control" id="" name="adm2_nombre" placeholder="Nombre Persona 2">

						<label for="email_adm">CORREO ELECTRÓNICO</label>
						<input type="email" class="form-control" id="email_adm" name="adm1_email" placeholder="Email Persona 1">
						<input type="email" class="form-control" id="" name="adm2_email" placeholder="Email Persona 2">

						<label for="telefono_adm">TELÉFONO</label>
						<input type="text" class="form-control" id="telefono_adm" name="adm1_telefono" placeholder="Telefono Persona 1">
						<input type="text" class="form-control" id="" name="adm2_telefono" placeholder="Telefono Persona 2">
					</div>

				<!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>



				<!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>

				<div class="col-lg-12">
					<div class="col-md-12">
						<label for="resp1">NÚMERO DE SOCIOS PRODUCTORES</label>
						<input type="number" class="form-control" id="resp1" name="resp1" placeholder="Solo numero" required>

						<label for="resp2">NÚMERO DE SOCIOS PRODUCTORES DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACION:</label>
						<input type="text" class="form-control" id="resp2" name="resp2" >

						<label for="resp3">VOLUMEN(ES) DE PRODUCCIÓN TOTAL POR PRODUCTO (UNIDAD DE MEDIDA):</label>
						<input type="text" class="form-control" id="resp3" name="resp3" >
						
						<label for="resp4">TAMAÑO MÁXIMO DE LA UNIDAD DE PRODUCCIÓN POR PRODUCTOR DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACIÓN:</label>
						<input type="text" class="form-control" id="resp4" name="resp4" >
					</div>
				</div>

				<div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE OPERACIÓN</div>

				<div class="col-lg-12">
					<div class="col-md-12">
						<label for="op_preg1">
							1. EXPLIQUE SI SE TRATA DE UNA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES DE 1ER, 2DO, 3ER O 4TO GRADO, ASÍ COMO EL NÚMERO DE OPP DE 3ER, 2DO O 1ER GRADO, Y EL NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO, EN SU CASO, CON LAS QUE CUENTA:
						</label>
						<textarea name="op_preg1" id="op_preg1" class="form-control" rows="2"></textarea>

						<div class="col-xs-3">
							<label for="preg1_1">
								1.1: NÚMERO DE OPP DE 3ER GRADO:
							</label>
							<input type="text" class="form-control" id="preg1_1" name="preg1_1" >
						</div>
						<div class="col-xs-3">
							<label for="preg1_2">
								1.2: NÚMERO DE OPP DE 2DO GRADO:
							</label>
							<input type="text" class="form-control" id="preg1_2" name="preg1_2" >
						</div>
						<div class="col-xs-3">
							<label for="preg1_3">
								1.3: NÚMERO DE OPP DE 1ER GRADO:
							</label>
							<input type="text" class="form-control" id="preg1_3" name="preg1_3" >
						</div>
						<div class="col-xs-3">
							<label for="preg1_4">
								1.4: NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO:
							</label>
							<input type="text" class="form-control" id="preg1_4" name="preg1_4" >
						</div>


						<label for="op_preg2">
							2. ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGANISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.
						</label>
						<textarea name="op_preg2" id="op_preg2" class="form-control"></textarea>

						<label for="op_preg3">
							3. MENCIONE SI SU ORGANIZACIÓN QUIERE INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
							<br>
							<small>
								<sup>4</sup> Revisar el Reglamento Gráfico y la lista de Calificativos Complementarios opcionales vigentes.
							</small>
							
						</label>
						<input type="text" class="form-control" id="op_preg3" name="op_preg3">

						<div >
							<label for="alcance_opp">
								4. SELECCIONE EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:
							</label>
						</div>
						<div class="col-xs-4">
							<label>PRODUCCIÓN</label>
							<input type="checkbox" name="produccion" class="form-control" value="1">
						</div>
						<div class="col-xs-4">
							<label>PROCESAMIENTO</label>
							<input type="checkbox" name="procesamiento" class="form-control" value="1">
						</div>
						<div class="col-xs-4">
							<label>EXPORTACIÓN</label>
							<input type="checkbox" name="exportacion" class="form-control" value="1">
						</div>

						<label for="op_preg5">
							5. ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA.
						</label>
						<textarea name="op_preg5" id="op_preg5" class="form-control"></textarea>

						<label for="op_preg6">
							6. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES. <sup>5</sup>
							<br>
							<small><sup>5</sup> Revisar el documento de 'Directrices Generales del Sistema SPP' en su última versión.</small>
						</label>
						<textarea name="op_preg6" id="op_preg6" class="form-control"></textarea>

						<label for="op_preg7">
							7. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.
						</label>
						<textarea name="op_preg7" id="op_preg7" class="form-control"></textarea>

						<label for="op_preg8">
							8. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.
						</label>
						<textarea name="op_preg8" id="op_preg8" class="form-control"></textarea>
						<p class="alert alert-info"><b>9. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc).</b></p>

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
							<tr class="text-center">
								<td><input type="text" class="form-control" name="certificacion[0]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN"></td>
								<td><input type="text" class="form-control" name="certificadora[0]" id="exampleInputEmail1" placeholder="CERTIFICADORA"></td>
								<td><input type="text" class="form-control" name="ano_inicial[0]" id="exampleInputEmail1" placeholder="AÑO INICIAL"></td>
								<!--<td><input type="text" class="form-control" name="interrumpida[0]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?"></td>-->
								<td>
									<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
									<div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
								</td>
							</tr>
						</table>	

						<label for="op_preg10">
							10.DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?</label>
						<textarea name="op_preg10" id="op_preg10" class="form-control"></textarea>

						<p for="op_preg11">
							<b>11.DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?</b>
							<i>(* Introducir solo cantidad, entero o decimales)</i>
							<div class="col-lg-12">
								<div class="row">
									<div class="col-xs-3">
										<label for="organico">% ORGÁNICO</label>
										<input type="number" step="any" class="form-control" id="organico" name="organico" placeholder="Ej: 0.0">
									</div>
									<div class="col-xs-3">
										<label for="comercio_justo">% COMERCIO JUSTO</label>
										<input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" placeholder="Ej: 0.0">
									</div>
									<div class="col-xs-3">
										<label for="spp">SÍMBOLO DE PEQUEÑOS PRODUCTORES</label>
										<input type="number" step="any" class="form-control" id="spp" name="spp" placeholder="Ej: 0.0">
									</div>
									<div class="col-xs-3">
										<label for="otro">SIN CERTIFICADO</label>
										<input type="number" step="any" class="form-control" id="otro" name="sin_certificado" placeholder="Ej: 0.0">
									</div>
								</div>
							</div>
						</p>
						
						<p><b>12. ¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?</b></p>
							<div class="col-xs-6">
								SI <input type="radio" class="form-control" name="op_preg12" id="op_preg12_si" value="SI">
							</div>
							<div class="col-xs-6">
								NO <input type="radio" class="form-control" name="op_preg12" id="op_preg12_no" value="NO">
							</div>			

						<p>
							<b>13. SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR CON UNA 'X' EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA:</b>
						</p>

						<div class="well col-xs-12 " >
							<div class="col-xs-6"><p>Hasta $3,000 USD</p></div>
							<div class="col-xs-6 "><input type="radio" name="op_preg13" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
						
							<div class="col-xs-6"><p>Entre $3,000 y $10,000 USD</p></div>
							<div class="col-xs-6"><input type="radio" name="op_preg13" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
						
							<div class="col-xs-6"><p>Entre $10,000 a $25,000 USD</p></div>
							<div class="col-xs-6"><input type="radio" name="op_preg13" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
						
							<div class="col-xs-6"><p>Más de $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Especifique la cantidad.</h6></p></div>
							<div class="col-xs-6"><input type="radio" name="op_preg13" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
								<input type="text" name="op_preg13_1" class="form-control" id="oculto" style='display:none;' placeholder="Especifique la Cantidad">
							</div>

						</div>
								
						<label for="op_preg14">
							14. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
						</label>
						<input type="text" class="form-control" id="op_preg14" name="op_preg14">

						<label for="op_preg15">
							15. ANEXAR EL CROQUIS GENERAL DE SU OPP, INDICANDO LAS ZONAS EN DONDE CUENTA CON SOCIOS.
						</label>
						<input type="file" class="form-control" id="op_preg15" name="op_preg15">
					</div>
				</div>

				<!------ FIN INFORMACION DATOS DE OPERACIÓN ------>

				<div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></div>
				<div class="col-lg-12">
					<table class="table table-bordered" id="tablaProductos">
						<tr>
							<td><b>Producto General</b> (ej: cafe, cacao, miel, etc...)</td>
							<td><b>Producto Especifico</b> (ej: cafe verde, cacao en polvo, miel de abeja)</td>
							<td>Volumen Total Estimado a Comercializar</td>
							<td>Producto Terminado</td>
							<td>Materia Prima</td>
							<td>País(es) de Destino</td>
							<td>Marca Propia</td>
							<td>Marca de un Cliente</td>
							<td>Sin cliente aún</td>
							<td>
								<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
								  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
								
							</td>					
						</tr>
						<tr>
							<td>
								<input type="text" class="form-control" name="producto_general[0]" id="exampleInputEmail1" onBlur="ponerMayusculas(this)" placeholder="Producto General">
							</td>
							<td>
								<input type="text" class="form-control" name="producto[0]" id="exampleInputEmail1" onBlur="ponerMayusculas(this)" placeholder="Producto Específico">
							</td>
							<td>
								<input type="text" class="form-control" name="volumen[0]" id="exampleInputEmail1" placeholder="Volumen">
							</td>
							<td>
								SI <input type="radio"  name="terminado[0]" id="" value="SI"><br>
								NO <input type="radio"  name="terminado[0]" id="" value="NO" >
							</td>
							<td>
								<input type="text" class="form-control" name="materia[0]" id="exampleInputEmail1" placeholder="Materia">
							</td>
							<td>
								<input type="text" class="form-control" name="destino[0]" id="exampleInputEmail1" placeholder="Destino">
							</td>
							<td>
								SI <input type="radio"  name="marca_propia[0]" id="" value="SI"><br>
								NO <input type="radio"  name="marca_propia[0]" id="" value="NO" >
							</td>
							<td>
								SI <input type="radio"  name="marca_cliente[0]" id="" value="SI"><br>
								NO <input type="radio"  name="marca_cliente[0]" id="" value="NO">
							</td>
							<td>
								SI <input type="radio"  name="sin_cliente[0]" id="" value="SI"><br>
								NO <input type="radio"  name="sin_cliente[0]" id="" value="NO">
							</td>
						</tr>				
						<tr>
							<td colspan="9">
								<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
							</td>
						</tr>
					</table>
				</div>

				<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
					<b>COMPROMISOS</b>
				</div>
				<div class="col-lg-12 text-justify">
					<p>1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Certificación. </p>
					<p>2. El proceso de Certificación comenzará en el momento que se confirme la recepción del pago correspondiente.</p>
					<p>3. La entrega y recepción de esta solicitud no garantiza que el proceso de Certificación será positivo.</p>
					<p>4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Organización de Pequeños Productores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.</p>
				</div>
				<div class="col-lg-12">
					<p style="font-size:14px;">
						<strong>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:</strong>
					</p>
					<input type="text" class="form-control" id="responsable" name="responsable" placeholder="Nombre del Responsable" required>	

					<!--<label for="nombre_oc">
						OC que recibe la solicitud:
					</label>
					<input type="text" class="form-control" id="nombre_oc" name="nombre_oc">-->
				</div>
				<div class="col-xs-12">
					<hr>
					<input type="hidden" name="insertar_solicitud" value="1">
					<input type="submit" class="btn btn-primary form-control" style="color: white;font-size:14px" value="Enviar Solicitud" onclick="return validar()">
				</div>

	
		</form>
	</div>


	<script>
		
	  function validar(){

        resp1 = document.getElementById("resp1").value;
        if ( resp1 == null || resp1.length == 0 || /^\s+$/.test(resp1)) {
        // Si no se cumple la condicion...
            alert('DEBES INGRESAR EL NÚMERO DE SOCIOS');
            document.getElementById("resp1").focus();
            return false;

        }

	    tipo_solicitud = document.getElementsByName("tipo_solicitud");
	    tuvo_ventas = document.getElementsByName("op_preg12");
	    opcion_venta = document.getElementsByName("op_preg13");
	     
	    // INICIA SELECCION TIPO SOLICITUD
	    var seleccionado = false;
	    for(var i=0; i<tipo_solicitud.length; i++) {    
	      if(tipo_solicitud[i].checked) {
	        seleccionado = true;
	        break;
	      }
	    }
	     
	    if(!seleccionado) {
	      alert("Debes de seleecionar un Tipo de Solicitud");
	      return false;
	    }
	    //// TERMINA SELECCION TIPO SOLICITUD

	    /// INICIA OPCION DE VENTAS
	    var ventas = false;
	    var valor_venta = '';
	    for(var i=0; i<tuvo_ventas.length; i++) {    
	      if(tuvo_ventas[i].checked) {
	      	valor_venta = tuvo_ventas[i].value;
	        ventas = true;
	        break;
	      }
	    }
	     
	    if(!ventas) {
	      alert("Debe seleccionar \"SI\" tuvo ó \"NO\" ventas");
	      return false;
	    }
	    /// TERMINA OPCION DE VENTAS


	    if(valor_venta != 'NO'){
		    var monto = false;
		    for(var i=0; i<opcion_venta.length; i++) {    
		      if(opcion_venta[i].checked) {
		        monto = true;
		        break;
		      }
		    }
		     
		    if(!monto) {
		      alert("Seleccionaste que \"SI\" tuviste ventas, debes seleccionar el monto de ventas SPP");
		      return false;
		    }

	    }

	    return true
	  }

	</script>

	<script>
    function ponerMayusculas(nombre) 
    { 
        nombre.value=nombre.value.toUpperCase(); 
    } 

	var contador=0;
		function tablaCertificaciones()
		{
			contador++;
		var table = document.getElementById("tablaCertificaciones");
		  {
		  var row = table.insertRow(2);
		  var cell1 = row.insertCell(0);
		  var cell2 = row.insertCell(1);
		  var cell3 = row.insertCell(2);
		  var cell4 = row.insertCell(3);

		  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
		  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
		  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
		  cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
		  }
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
		cont++;

		  var row = table.insertRow(1);
		  var cell1 = row.insertCell(0);
		  var cell2 = row.insertCell(1);
		  var cell3 = row.insertCell(2);
		  var cell4 = row.insertCell(3);
		  var cell5 = row.insertCell(4);
		  var cell6 = row.insertCell(5);
		  var cell7 = row.insertCell(6); 
		  var cell8 = row.insertCell(7);
		  var cell9 = row.insertCell(8);


		  cell1.innerHTML = '<input type="text" class="form-control" name="producto_general['+cont+']" id="exampleInputEmail1" onBlur="ponerMayusculas(this)" placeholder="Producto General">';

		  cell2.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" onBlur="ponerMayusculas(this)" placeholder="Producto Específico">';
		  
		  cell3.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
		  
		  cell4.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
		  
		  cell5.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
		  
		  cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
		  
		  cell7.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
		  
		  cell8.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
		  
		  cell9.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	  

		  }

		}	

	</script>
<?php
}
?>