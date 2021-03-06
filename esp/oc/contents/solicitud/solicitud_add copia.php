<?php require_once('../Connections/dspp.php'); ?>
<?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  /*if(is_array($_POST['op_resp4'])){
    foreach($_POST['op_resp4'] as $resp4){
        $array_resp4 .= $resp4." - ";
    }
  }else{
      $array_resp4 = "no hay";
  }*/

  	$array_resp4 = "";
	if(isset($_POST['op_resp4'])){
		if(is_array($_POST['op_resp4'])){
			$resp4 = $_POST['op_resp4'];

			for ($i=0; $i < count($resp4) ; $i++) { 
				$array_resp4 .= $resp4[$i]." - ";
			}
		}else{
				$array_resp4 = NULL;
		}
	}else{
		$array_resp4 = NULL;
	}

	
	
	if(isset($_POST['op_resp13'])){
		if(isset($_POST['op_resp13']) && $_POST['op_resp13'] == "mayor"){
			$op_resp13 = $_POST['op_resp13_1'];
		}else{
			$op_resp13 = $_POST['op_resp13'];
		}
	}else{
		$op_resp13 = NULL;
	}

	if(isset($_POST['op_resp12'])){
		$op_resp12 = $_POST['op_resp12'];
	}else{
		$op_resp12 = "";
	}

  $rutaArchivo = "../opp/croquis/";

	if(!empty($_FILES['op_resp15']['name'])){
	    $_FILES["op_resp15"]["name"];
	      move_uploaded_file($_FILES["op_resp15"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["op_resp15"]["name"]);
	      $croquis = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["op_resp15"]["name"]);
	}else{
		$croquis = NULL;
	}

/*************************************************************************************************************************/
/*************************************************************************************************************************/
/*************************************************************************************************************************/


/*************************************************************************************************************************/
/*************************************************************************************************************************/
/*************************************************************************************************************************/
	if(isset($_POST['idoc'])){
	  $insertSQL = sprintf("INSERT INTO solicitud_certificacion (idoc, idopp, ciudad,  p1_nombre, p1_cargo, p1_telefono, p1_email, p2_nombre, p2_cargo, p2_telefono, p2_email, adm_nom1, adm_nom2, adm_tel1, adm_tel2, adm_email1, adm_email2, resp1, resp2, resp3, resp4, op_resp1, op_area1, op_area2, op_area3, op_area4, op_resp2, op_resp3, op_resp4, op_resp5, op_resp6, op_resp7, op_resp8, op_resp10, op_resp11, op_resp12, op_resp13, op_resp14, op_resp15, fecha_elaboracion, status, status_publico, responsable, procedimiento) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
	  					   GetSQLValueString($_POST['idoc'], "int"),
	                       GetSQLValueString($_POST['idopp'], "int"),
	                       GetSQLValueString($_POST['ciudad'], "text"),
	                       GetSQLValueString($_POST['p1_nombre'], "text"),
	                       GetSQLValueString($_POST['p1_cargo'], "text"),
	                       GetSQLValueString($_POST['p1_telefono'], "text"),
	                       GetSQLValueString($_POST['p1_email'], "text"),
	                       GetSQLValueString($_POST['p2_nombre'], "text"),
	                       GetSQLValueString($_POST['p2_cargo'], "text"),
	                       GetSQLValueString($_POST['p2_telefono'], "text"),
	                       GetSQLValueString($_POST['p2_email'], "text"),
	                       GetSQLValueString($_POST['adm_nom1'], "text"),
	                       GetSQLValueString($_POST['adm_nom2'], "text"),
	                       GetSQLValueString($_POST['adm_tel1'], "text"),
	                       GetSQLValueString($_POST['adm_tel2'], "text"),
	                       GetSQLValueString($_POST['adm_email1'], "text"),
	                       GetSQLValueString($_POST['adm_email2'], "text"),
	                       GetSQLValueString($_POST['resp1'], "text"),
	                       GetSQLValueString($_POST['resp2'], "text"),
	                       GetSQLValueString($_POST['resp3'], "text"),
	                       GetSQLValueString($_POST['resp4'], "text"),
	                       GetSQLValueString($_POST['op_resp1'], "text"),
	                       GetSQLValueString($_POST['op_area1'], "text"),
	                       GetSQLValueString($_POST['op_area2'], "text"),
	                       GetSQLValueString($_POST['op_area3'], "text"),
	                       GetSQLValueString($_POST['op_area4'], "text"),
	                       GetSQLValueString($_POST['op_resp2'], "text"),
	                       GetSQLValueString($_POST['op_resp3'], "text"),
	                       GetSQLValueString($array_resp4, "text"),
	                       GetSQLValueString($_POST['op_resp5'], "text"),
	                       GetSQLValueString($_POST['op_resp6'], "text"),
	                       GetSQLValueString($_POST['op_resp7'], "text"),
	                       GetSQLValueString($_POST['op_resp8'], "text"),
	                       GetSQLValueString($_POST['op_resp10'], "text"),
	                       GetSQLValueString($_POST['op_resp11'], "text"),
	                       GetSQLValueString($op_resp12, "text"),
	                       GetSQLValueString($op_resp13, "text"),
	                       GetSQLValueString($_POST['op_resp14'], "text"),
	                       GetSQLValueString($croquis, "text"),
	                       GetSQLValueString($_POST['fecha_elaboracion'], "int"),
	                       GetSQLValueString($_POST['tipo_solicitud'], "int"),
	                       GetSQLValueString($_POST['status_publico'], "int"),
	                       GetSQLValueString($_POST['responsable'], "text"),
	                       GetSQLValueString($_POST['procedimiento'], "text"));

	  mysql_select_db($database_dspp, $dspp);
	  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

	  $idsolicitud_certificacion = mysql_insert_id($dspp); 



	  	$certificacion = $_POST['certificacion'];
		$certificadora = $_POST['certificadora'];
		$ano_inicial = $_POST['ano_inicial'];
		$interrumpida = $_POST['interrumpida'];

		for($i=0;$i<count($certificacion);$i++){
			if($certificacion[$i] != NULL){
				#for($i=0;$i<count($certificacion);$i++){
				$insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_certificacion, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
				    GetSQLValueString($idsolicitud_certificacion, "int"),
				    GetSQLValueString($certificacion[$i], "text"),
				    GetSQLValueString($certificadora[$i], "text"),
				    GetSQLValueString($ano_inicial[$i], "text"),
				    GetSQLValueString($interrumpida[$i], "text"));

				$Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
				#}
			}
		}




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

				    $insertSQL = sprintf("INSERT INTO productos (idsolicitud_certificacion, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
				          GetSQLValueString($idsolicitud_certificacion, "int"),
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


			// Check for empty fields
			/*if(empty($_POST['name'])  		||
			   empty($_POST['email']) 		||
			   empty($_POST['message'])	||
			   !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
			   {
				echo "No arguments Provided!";
				return false;
			   }*/

			   $query = "SELECT * FROM oc WHERE idoc = $_POST[idoc]";
			   $oc = mysql_query($query,$dspp) or die(mysql_error());
			   $row_oc = mysql_fetch_assoc($oc);

			   $queryOpp = "SELECT * FROM opp WHERE idopp = $_POST[idopp]";
			   $ejecutar = mysql_query($queryOpp,$dspp) or die(mysql_error());
			   $row_opp = mysql_fetch_assoc($ejecutar);


			    $nombre = $row_opp['nombre'];
			    $abreviacion = $row_opp['abreviacion'];
			    $pais = $row_opp['pais'];
			    $nombreOC = $row_oc['nombre'];
			    $fecha_elaboracion = $_POST['fecha_elaboracion'];
			    $producto = $_POST['producto'];
			    $telefono1 = $_POST['p1_telefono'];
			    $direccion = $row_opp['direccion_fiscal'];
			    $ciudad = $_POST['ciudad'];
			    $emailOPP1 = $_POST['p1_email'];
			    $emailOPP2 = $_POST['p2_email'];
			    $fecha = date("d/m/Y", $fecha_elaboracion);
			    //$correo = $_POST['p1_email'];
			    //$correo = $_POST['p2_email'];

			    $paisEstado = $pais.' / '.$ciudad;


			    $destinatario = $row_oc['email'];
			    $asunto = "D-SPP Solicitud de Certificación para Organizaciones de Pequeños Productores"; 


			$cuerpo = '
				<html>
				<head>
					<meta charset="utf-8">
				</head>
				<body>
				
					<table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
					  <tbody>
			            <tr>
			              <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
			              <th scope="col" align="left" width="280"><strong>Notificación de Intenciones / Notification of Intentions</strong></th>
			            </tr>
			            <tr>
			              <td style="padding-top:10px;"><i>Para poder consultar la solicitud, por favor iniciar sesión en su cuenta de OC en el siguiente enlace: <a href="http://d-spp.org/?OC" target="_new">www.d-spp.org/?OC</a></i>.</td>
			            </tr>
					    <tr>
					      <td align="left">Teléfono / phone OPP: '.$telefono1.'</td>
					    </tr>
					    <tr>
					      <td align="left">'.$direccion.'</td>
					    </tr>
					    <tr>
					      <td align="left">'.$paisEstado.'</td>
					    </tr>
					    <tr>
					      <td align="left" style="color:#ff738a;">Email: '.$emailOPP1.'</td>
					    </tr>
					    <tr>
					      <td align="left" style="color:#ff738a;">Email: '.$emailOPP2.'</td>
					    </tr>

					    <tr>
					      <td colspan="2">
					        <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
					          <tbody>
					            <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
					              <td width="130px">Nombre de la organización/Organization name</td>
					              <td width="130px">Abreviación / Short name</td>
					              <td width="130px">País / Country</td>
					              <td width="130px">Organismo de Certificación / Certification Entity</td>
					           
					              <td width="130px">Fecha de solicitud/Date of application</td>
					            </tr>
					            <tr style="font-size: 12px;">
					              <td style="padding:10px;">
					              	'.$nombre.'
					              </td>
					              <td style="padding:10px;">
					                '.$abreviacion.'
					              </td>
					              <td style="padding:10px;">
					                '.$pais.'
					              </td>
					              <td style="padding:10px;">
					                '.$nombreOC.'
					              </td>
					              <td style="padding:10px;">
					              '.$fecha.'
					              </td>
					            </tr>

					          </tbody>
					        </table>        
					      </td>
					    </tr>

					    <tr>
					      <td style="text-align:justify;" colspan="2">
					        FUNDEPPO publica y notifica las “Intenciones de Certificación, Registro o Autorización” basada en nuevas solicitudes de: 1) Certificación de Organizaciones de Pequeños Productores, 2) Registro de Compradores y otros actores y 3) Autorización de Organismos de Certificación, con el objetivo de informarles y recibir las eventuales objeciones contra la incorporación de los solicitantes.
					        Estas eventuales objeciones presentadas deben estar sustentadas con información concreta y verificable con respecto a incumplimientos de la Normatividad del SPP y/o nuestro Código de Conducta (disponibles en <a href="http://www.spp.coop/">www.spp.coop</a>, en el área de Funcionamiento). Las objeciones presentadas y enviadas a <a href="cert@spp.coop">cert@spp.coop</a> serán tomadas en cuenta en los procesos de certificación, registro o autorización.
					        Estas notificaciones son enviadas por FUNDEPPO en un lapso menor a 24 horas a partir del momento en que le llegue la solicitud correspondiente. Si se presentan objeciones antes de que el solicitante se Certifique, Registre o Autorice su tratamiento por parte del Organismo de Certificación debe ser parte de la misma evaluación documental. Si la objeción se presenta cuando el Solicitante ya esta Certificado se aplica el Procedimiento de Inconformidades del Símbolo de Pequeños Productores. Las nuevas intenciones de Certificación, Registro o Autorización, se detallan al inicio de este documento.  
					        <br><br>
					        FUNDEPPO publishes and notifies the “Certification, Registration and Authorization Intentions” based on new applications submitted for: 1) Certification of Small Producers’ Organizations, 2) Registration of Buyers and other stakeholders, and 3) Authorization of Certification Entities, with the objective of keeping you informed and receiving any objections to the incorporation of any new applicants into the system.
					        Any objections submitted must be supported with concrete, verifiable information regarding non-compliance with the Standards and/or Code of Conduct of the Small Producers’ Symbol (available at <a href="http://www.spp.coop/">www.spp.coop</a> in the section on Operation). The objections submitted and sent to <a href="cert@spp.coop">cert@spp.coop</a> will be taken into consideration during certification, registration and authorization processes.
					        These notifications are sent by FUNDEPPO in a period of less than 24 hours from the time a corresponding application is received. If objections are presented before getting the Certification, Registration or Authorization, the Certification Entity must incorporate them as part of the same evaluation-process. If the objection is presented when the applicant has already been certified, the SPP Dissents Procedure has to be applied. The new intentions for Certification, Registration and Authorization are detailed at the beginning (of this document.
					      </td>
					    </tr>
					  </tbody>
					</table>

				</body>
				</html>
			';


			    //para el envío en formato HTML 
			    $headers = "MIME-Version: 1.0\r\n"; 
			    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

			    //dirección del remitente 
			    $headers .= "From: cert@spp.coop\r\n"; 

			    //dirección de respuesta, si queremos que sea distinta que la del remitente 
			    //$headers .= "Reply-To: ".$correo."\r\n"; 

			    //ruta del mensaje desde origen a destino 
			    //$headers .= "Return-path: holahola@desarrolloweb.com\r\n"; 

			    //direcciones que recibián copia 
			    //$headers .= "Cc: maria@desarrolloweb.com\r\n"; 

			    //direcciones que recibirán copia oculta 
			    $headers .= "Bcc: yasser.midnight@gmail.com\r\n"; 
			    //$headers .= "Bcc: isc.jesusmartinez@gmail.com \r\n";

			    mail($destinatario,$asunto,utf8_decode($cuerpo),$headers) ;

	}


    		//INSERTAMOS LOS DATOS DE CONTACTO DE LA SOLICITUD, DENTRO DE LOS CONTACTO DEL OPP
    		$idopp = $_POST['idopp'];
    		if(!empty($_POST['p1_nombre'])){
    			$nombre = $_POST['p1_nombre'];
    			$cargo = $_POST['p1_cargo'];
    			$telefono = $_POST['p1_telefono'];
    			$email = $_POST['p1_telefono'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if(!empty($_POST['p2_nombre'])){
    			$nombre = $_POST['p2_nombre'];
    			$cargo = $_POST['p2_cargo'];
    			$telefono = $_POST['p2_telefono'];
    			$email = $_POST['p2_telefono'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm_nom1'])) {
    			$nombre = $_POST['adm_nom1'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm_tel1'];
    			$email = $_POST['adm_email1'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}
    		if (!empty($_POST['adm_nom2'])) {
    			$nombre = $_POST['adm_nom2'];
    			$cargo = 'Administrador';
    			$telefono = $_POST['adm_tel2'];
    			$email = $_POST['adm_email2'];

    			$query = "INSERT INTO contacto(idopp, contacto, cargo, telefono1, email1) VALUES($idopp ,'$nombre', '$cargo', '$telefono', '$email')";
    			$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
    		}

			//actualizamos OPP para cambiar el estado de su solicitud
			$estado = $_POST['tipo_solicitud'];
			$ciudad = $_POST['ciudad'];
			$rfc = $_POST['rfc'];
			$ruc = $_POST['ruc'];
			$telefono = $_POST['telefono'];
			$query = "UPDATE opp SET ciudad = '$ciudad', rfc = '$rfc', ruc = '$ruc', telefono = '$telefono', estado = $estado WHERE idopp = $_POST[idopp]";
			$actualizar = mysql_query($query,$dspp) or die(mysql_error());

			//llenamos el registro de la fecha para llevar un control de las acciones que se han realizado dentro del sistema
			$fecha = time();
			$idexterno = $_POST['idoc'];
			$identificador = "OC";
			$status = $_POST['tipo_solicitud'];

			$queryFecha = "INSERT INTO fecha(fecha, idexterno, identificador, status) VALUES($fecha, $idexterno, '$identificador', $status)";
			$ejecutar = mysql_query($queryFecha,$dspp) or die(mysql_error());


		    $mensaje = "Se ha enviado la Solicitud de Certificación para Organizaciones de Pequeños Productores por parte de <b>$_SESSION[nombre]</b>";


  $insertGoTo = "?SOLICITUD&select&mensaje=Solicitud agregada correctamente, se ha notificado al OC por email.";
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

/*
$query_opp = "SELECT * FROM opp WHERE idopp='$_SESSION[idopp]'";
$opp = mysql_query($query_opp,$dspp) or die(mysql_error());
$row_opp = mysql_fetch_assoc($opp);

$query_contacto = "SELECT * FROM contacto WHERE idopp='$_SESSION[idopp]'";
$contacto = mysql_query($query_contacto,$dspp) or die(mysql_error());
$row_contacto = mysql_fetch_assoc($contacto);*/


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

$estadoPublico = "2";
$estadoInterno = "1";

/************ VARIABLES DE CONTROL ******************/


?>

<br>

<? if(isset($_POST['update'])){?>
  
	<div class="alert alert-success alert-dismissible" role="alert">
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	  <strong><? echo $_POST['update'];?></strong>
	</div>
<? }?>

<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">

	<table class="table table-bordered table-striped col-xs-8">
	    <tr valign="baseline">
	      	<td rowspan="2">
	      		<div class="col-xs-12">
	      			<h5>Enviar a </h5>
	      		</div>
	      		<div class="col-xs-12">
	      			
	      			<p><h4 class="alert alert-success"><?php echo $_SESSION['nombreOC']; ?></h4></p>
	      		</div>
	  	    </td>
	  	    <td class="text-center" colspan="3">
	  	    	<div class="col-xs-12">
	  	    		<div class="row">
						<h4>Procedimiento de Certificación <br><small>(realizado por OC)</small></h4>
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>DOCUMENTAL "ACORTADO"</b></p>	
		  	    		</div>	  	 
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"'>
	
		  	    		</div>		  	    		   			
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>DOCUMENTAL "NORMAL"</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>COMPLETO "IN SITU"</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>
	  	    	<div class="col-xs-3">
	  	    		<div class="row">
		  	    		<div class="col-xs-12">
		  	    			<p style="font-size:10px;"><b>COMPLETO "A DISTANCIA"</b></p>	
		  	    		</div>
		  	    		<div class="col-xs-12">
		  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"'>
	
		  	    		</div>		  	    		
	  	    		</div>
	  	    	</div>
	  	    	<tr style="border-lef-stylet:hidden">
	  	    		<td><h4>Tipo de Solicitud <small>(Seleccionar el tipo de procedimiento que desea realizar)</small></h4></td>
	  	    		<td colspan="2">
						<div class="col-xs-6 text-center">
			  	    		<div class="col-xs-12">
			  	    			<p style="font-size:12px;"><b>Nueva Solicitud</b></p>	
			  	    		</div>	  	 
			  	    		<div class="col-xs-12">
			  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="tipo_solicitud" value='1'>
			  	    		</div>	
						</div>
						<div class="col-xs-6 text-center">
			  	    		<div class="col-xs-12">
			  	    			<p style="font-size:12px;"><b>Renovación de Certificación</b></p>	
			  	    		</div>
			  	    		<div class="col-xs-12">
			  	    			<input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="tipo_solicitud" value='20'>
			  	    		</div>  
						</div>
	  	    		</td>
	  	    	</tr>
	  	    	<!--<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "ACORTADO"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>DOCUMENTAL "NORMAL"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "IN SITU"</div>
	  	    	<div class="col-xs-2"><button class="btn btn-danger">boton</button>COMPLETO "A DISTANCIA"</div>-->
	  	    </td>
	    <tr>


		<tr>
			<th colspan="4" class="text-center"><h3>Solicitud de Certificación para Organizaciones de Pequeños Productores</h3></th>
		</tr>	
		<tr class="success">
			<th colspan="4" class="text-center">DATOS GENERALES</th>
		</tr>
		<tr>
			<td colspan="2">
				<h4>Seleccione el OPP que envia la solicitud</h4>
			</td>
			<!--<td colspan="2">
				<input type="text" autofocus="autofocus" class="form-control" id="exampleInputEmail1" size="70" placeholder="Nombre Organización" name="" value="<?php echo $row_opp['nombre']?>" readonly>
			</td>-->
	      	<td colspan="2" class="alert alert-danger">
	      		<div class="col-xs-12">
			      	<select class="form-control" name="idopp" required>
			      		<!--<option class="form-control" value="9999">FUNDEPPO</option>-->
			      		<option class="form-control" value="">SELECCIONE UN OPP</option>
			        <?php 
			        	$query_opp = "SELECT * FROM opp where idoc='".$_SESSION['idoc']."' ORDER BY nombre ASC";
			        	$consultaOPP = mysql_query($query_opp,$dspp) or die(mysql_error());


						while ($opp_OC = mysql_fetch_assoc($consultaOPP)){  
						?>
						        <option class="form-control" value="<?php echo $opp_OC['idopp']?>"><?php echo $opp_OC['abreviacion']?></option>
						        <?php
						}
					?>
			      	</select>		
	      		</div>
	  	    </td>

		</tr>
		<!--<tr>
			<td colspan="2">RFC</td>
			<td colspan="2">
				<?php 
					if(isset($row_opp['rfc'])){
						echo "<input type='text' class='form-control' id='exampleInputEmail1' placeholder='RFC' value='$row_opp[rfc]' readonly>";

					}else{
						echo "<input type='text' class='form-control' id='exampleInputEmail1' placeholder='RFC' readonly>";

					}
				 ?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES(CALLE, BARRIO, LUGAR, REGIÓN)<br>
				<?php 
					if(isset($row_opp['direccion_fiscal'])){
						echo "<input type='text' class='form-control' name='direccion' id='exampleInputEmail1' value='$row_opp[direccion_fiscal]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='direccion' id='exampleInputEmail1' placeholder='Dirección de Oficinas' readonly>";
					}
				 ?>

			</td>
			<td colspan="1">
				<?php if(isset($row_opp['pais'])){
						echo "<input type='text' class='form-control' name='pais' id='exampleInputEmail1' placeholder='Dirección de Oficinas' value=$row_opp[pais] readonly>";}
					else{ ?>
				PAÍS<br>
		      <select required class="form-control" name="pais">
			      <option value="">Selecciona</option>
					<?php 
					do {  
					?>
					<option class="form-control" name="pais" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
					<?php
					} while ($row_pais = mysql_fetch_assoc($pais));
					?>
		      </select>
		      <?php } ?>
			</td>
		</tr>	
		<tr>
			<td colspan="2">CORREO ELECTRONICO</td>
			<td colspan="2">
				<?php 
					if(isset($row_opp['email'])){
						echo "<input type='email' class='form-control' name='email' id='exampleInputEmail1' value='$row_opp[email]' readonly>";
					}else{
						echo "<input type='email' class='form-control' name='email' id='exampleInputEmail1' placeholder='Correo Electronico' readonly>";
					}
				 ?>

			</td>
		</tr>
		<tr>
			<td colspan="3">
				SITIO WEB<br>
				<?php 
					if(isset($row_opp['sitio_web'])){
						echo "<input type='text' class='form-control' name='web' id='exampleInputEmail1' value='$row_opp[sitio_web]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='web' id='exampleInputEmail1' placeholder='Sitio Web' readonly>";
					}
				 ?>
				
			</td>
			<td colspan="1">
				TELEFONO<br>
				<?php 
					if(isset($row_contacto['telefono1'])){
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' value='$row_contacto[telefono1]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' placeholder='Telefono'>";
					}
				 ?>
				
			</td>
		</tr>-->
		<tr>
			<td class="text-center" colspan="4">
				DATOS FISCALES(PARA FACTURACIÓN COMO DOMICILIO, RFC, RUC, CIUDAD, PAÍS, ETC)<br>
			</td>
		</tr>
		<tr>
			<td colspan="1">
				<p>TELEFONO</p>
				<input type="text" class="form-control" name="telefono" value="" placeholder="Telefono">
			</td>
			<td>
				<p>RFC</p>
				<input type="text" class="form-control" name="rfc" value="" placeholder="RFC">
			</td>

			<td class="col-xs-3"><p>RUC:</p> <input type="text" class="form-control" name="ruc" id="exampleInputEmail1" placeholder="RUC"></td>
			
			<td class="col-xs-3"><p>CIUDAD:</p> <input type="text" class="form-control" name="ciudad" id="exampleInputEmail1" placeholder="Ciudad"></td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">PERSONA(S) DE CONTACTO</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>NOMBRE DE CONTACTO SOLICITUD</p>
				<input type="text" class="form-control" name="p1_nombre" id="exampleInputEmail1" placeholder="Contacto Solicitud 1" required><br>
				<input type="text" class="form-control" name="p2_nombre" id="exampleInputEmail1" placeholder="Contacto Solicitud 2"><br>
				<p>CORREO ELECTRÓNICO DE CONTACTO</p>
				<input type="email" class="form-control" name="p1_email" id="exampleInputEmail1" placeholder="Correo Electrónico 1" required><br>
				<input type="email" class="form-control" name="p2_email" id="exampleInputEmail1" placeholder="Correo Electrónico 2"><br>
			</td>
			<td colspan="2">
				<p>CARGO</p>
				<input type="text" class="form-control" name="p1_cargo" id="exampleInputEmail1" placeholder="Cargo 1" required><br>
				<input type="text" class="form-control" name="p2_cargo" id="exampleInputEmail1" placeholder="Cargo 2"><br>
				<p>TELÉFONO</p>
				<input type="text" class="form-control" name="p1_telefono" id="exampleInputtext1" placeholder="Telefono 1"><br>
				<input type="text" class="form-control" name="p2_telefono" id="exampleInputEmail1" placeholder="Telefono 2"><br>
			</td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">PERSONA DEL ÁREA ADMINISTRATIVA</td>
		</tr>

		<tr>
			<td colspan="2">
				<p>PERSONA DEL ÁREA ADMINISTRATIVA</p>
				<input type="text" class="form-control" name="adm_nom1" id="exampleInputEmail1" placeholder="Persona del Área Administrativa 1" required><br>
				<input type="text" class="form-control" name="adm_nom2" id="exampleInputEmail1" placeholder="Persona del Área Administrativa 2"><br>
				<p>CORREO ELECTRÓNICO DEL ÁREA ADMINISTRATIVA</p>
				<input type="email" class="form-control" name="adm_email1" id="exampleInputEmail1" placeholder="Correo Electrónico 1" required><br>
				<input type="email" class="form-control" name="adm_email2" id="exampleInputEmail1" placeholder="Correo Electrónico 2">
			</td>
			<td colspan="2">
				<p>TELÉFONO PERSONA DEL ÁREA ADMINISTRATIVA</p>
				<input type="text" class="form-control" name="adm_tel1" id="exampleInputEmail1" placeholder="Teléfono Área Adminsitrativa 1" required><br>
				<input type="text" class="form-control" name="adm_tel2" id="exampleInputEmail1" placeholder="Teléfono Área Administrativa 2">
			</td>
		</tr>	
		<tr >
			<td><p>NÚMERO DE SOCIOS PRODUCTORES</p></td>
			<td><input type="text" class="form-control" name="resp1" id="exampleInputEmail1" placeholder="Número de socios"></td>
			<td><p>NÚMERO DE SOCIOS PRODUCTORES DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACION</p></td>
			<td><input type="text" class="form-control" name="resp2" id="exampleInputEmail1" placeholder="Número de socios"></td>
		</tr>

		<tr >
			<td><p>VOLUMEN(ES) DE PRODUCCIÓN TOTAL POR PRODUCTO (UNIDAD DE MEDIDA):</p></td>
			<td><input type="text" class="form-control" name="resp3" id="exampleInputEmail1" placeholder="Número de socios"></td>
			<td><p>TAMAÑO MÁXIMO DE LA UNIDAD DE PRODUCCIÓN POR PRODUCTOR DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACIÓN:</p></td>
			<td><input type="text" class="form-control" name="resp4" id="exampleInputEmail1" placeholder="Número de socios"></td>
		</tr>
		<tr class="success">
			<th colspan="4" class="text-center">DATOS DE OPERACIÓN</th>
		</tr>
		<tr>
			<td colspan="4">
				<p>1. EXPLIQUE SI SE TRATA DE UNA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES DE 1ER, 2DO, 3ER O 4TO GRADO, ASÍ COMO EL NÚMERO DE OPP DE 3ER, 2DO O 1ER GRADO, Y EL NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO, EN SU CASO, CON LAS QUE CUENTA:</p>
				
				<textarea class="form-control" name="op_resp1" id="" rows="3"></textarea>
				
			</td>
		</tr>
		<tr>
			<td>
				<h5 class="col-xs-12"><p>NÚMERO DE OPP DE 3ER GRADO:</p></h5>
				<input type="text" class="col-xs-12 form-control" name="op_area1" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area1" id="" cols="10" rows="2"></textarea>-->
				
			</td>
			<td>
				<h5 class="col-xs-12"><p>NÚMERO DE OPP DE 2DO GRADO:</p></h5>	
				<input type="text" class="col-xs-12 form-control" name="op_area2" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area2" id="" cols="10" rows="2"></textarea>-->
			</td>
			<td>
				<h5 class="col-xs-12"><p>NÚMERO DE OPP DE 1ER GRADO:</p></h5>
				<input type="text" class="col-xs-12 form-control" name="op_area3" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area3" id="" cols="10" rows="2"></textarea>-->
			</td>
			<td>
				<h5 class="col-xs-12"><p>NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO:</p></h5>
				<input type="text" class="col-xs-12 form-control" name="op_area4" id="">
				<!--<textarea class="col-xs-12 form-control" name="op_area4" id="" cols="10" rows="2"></textarea>-->
				
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<p>2. ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGANISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.</p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp2" id="" class="form-control" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<p>3. MENCIONE SI SU ORGANIZACIÓN QUIERE INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.<sup>4</sup></p>
				
				<h6><sup>4</sup> Revisar el Reglamento Gráfico y la lista de Calificativos Complementarios opcionales vigentes.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp3" id="" class="form-control" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<p>4. SELECCIONE EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:</p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="col-xs-4">
					<p>PRODUCCIÓN</p> <input class="form-control" name="op_resp4[]" type="checkbox" value="PRODUCCION">
				</div>
				<div class="col-xs-4">
					<p>PROCESAMIENTO</p> <input class="form-control" name="op_resp4[]" type="checkbox" value="PROCESAMIENTO">
				</div>
				<div class="col-xs-4">
					<p>EXPORTACIÓN</p> <input class="form-control" name="op_resp4[]" type="checkbox" value="EXPORTACION">
				</div>

			</td>
		</tr>
		<tr>
			<td colspan="4">
				<p>5. ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA.</p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp5" id="" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<p>6. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES.<sup>5</sup></p>
				
				<h6><sup>5</sup> Revisar el documento de 'Directrices Generales del Sistema SPP' en su última versión.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp6" id="" rows="3"></textarea>
			</td>
		</tr>		
		<tr>
			<td colspan="4">
				<p>7. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.</p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp7" id="" rows="3"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<p>8. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.</p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp8" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<p>9. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc).</p>
			</td>
		</tr>
		<tr>


			<td colspan="4">
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
						<td><input type="date" class="form-control" name="ano_inicial[0]" id="exampleInputEmail1" placeholder="AÑO INICIAL"></td>
						<td><input type="text" class="form-control" name="interrumpida[0]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?"></td>
					</tr>

				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<p>10.DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?</p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp10" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<p>11.DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?</p>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp11" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<p>12. ¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?</p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div class="col-xs-6">
					SI <input type="radio" class="form-control" name="op_resp12" onclick="mostrar_ventas()" id="op_resp12" value="SI">
				</div>
				<div class="col-xs-6">
					NO <input type="radio" class="form-control" name="op_resp12" onclick="ocultar_ventas()" id="op_resp12" value="NO">
				</div>
			</td>
		</tr>
		
		
		<tr >
			<td colspan="4">
				13. SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR CON UNA 'X' EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA:

				<div class="well col-xs-12 " id="tablaVentas" style="display:none;">
					
						<div class="col-xs-6"><p>Hasta $3,000 USD</p></div>
						<div class="col-xs-6 "><input type="radio" name="op_resp13" class="form-control" id="ver" onclick="ocultar()" value="HASTA $3,000 USD"></div>
					
					
						<div class="col-xs-6"><p>Entre $3,000 y $10,000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control" id="ver" onclick="ocultar()" value="ENTRE $3,000 Y $10,000 USD"></div>
					
					
						<div class="col-xs-6"><p>Entre $10,000 a $25,000 USD</p></div>
						<div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control"  id="ver" onclick="ocultar()" value="ENTRE $10,000 A $25,000 USD"></div>
					
						<div class="col-xs-6"><p>Más de $25,000 USD <sup>*</sup><br><h6><sup>*</sup>Especifique la cantidad.</h6></p></div>
						<div class="col-xs-6"><input type="radio" name="op_resp13" class="form-control" id="exampleInputEmail1" onclick="mostrar()" value="mayor">
							<input type="text" name="op_resp13_1" class="form-control" id="oculto" style='display:none;' placeholder="Especifique la Cantidad">
						</div>
					
				</div>
							
			</td>
		</tr>
				
		<tr>
			<td colspan="4">
				<p>14. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.</p>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp14" id="" rows="3"></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<p>15. ANEXAR EL CROQUIS GENERAL DE SU OPP, INDICANDO LAS ZONAS EN DONDE CUENTA CON SOCIOS.</p>
			</td>
		</tr>
		<tr>
			<br><br>
			<td colspan="4">
	            <input name="op_resp15" id="op_resp15" type="file" class="filestyle" data-buttonName="btn-info" data-buttonBefore="true" data-buttonText="Cargar Croquis"> 
			</td>
		</tr>

		<tr class="success">
			<th colspan="4" class="text-center">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></th>
		</tr>



		<tr>
			<td colspan="4">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Producto</td>
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
							<input type="text" class="form-control" name="producto[0]" id="exampleInputEmail1" placeholder="Producto">
						</td>
						<td>
							<input type="text" class="form-control" name="volumen[0]" id="exampleInputEmail1" placeholder="Volumen">
						</td>
						<td>
							SI <input type="radio"  name="terminado0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="terminado0[0]" id="" value="NO" >
						</td>
						<td>
							<input type="text" class="form-control" name="materia[0]" id="exampleInputEmail1" placeholder="Materia">
						</td>
						<td>
							<input type="text" class="form-control" name="destino[0]" id="exampleInputEmail1" placeholder="Destino">
						</td>
						<td>
							SI <input type="radio"  name="marca_propia0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="marca_propia0[0]" id="" value="NO" >
						</td>
						<td>
							SI <input type="radio"  name="marca_cliente0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="marca_cliente0[0]" id="" value="NO">
						</td>
						<td>
							SI <input type="radio"  name="sin_cliente0[0]" id="" value="SI"><br>
							NO <input type="radio"  name="sin_cliente0[0]" id="" value="NO">
						</td>
					</tr>				
					<tr>
						<td colspan="8">
							<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
						</td>
					</tr>
				</table>
			</td>

		</tr>
		<tr>
			<th class="success" colspan="4">
				<p>COMPROMISOS</p>
			</th>
		</tr>
		<tr class="text-justify">
			<td colspan="4">
				<p>
					1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Certificación.<br>
					2. El proceso de Certificación comenzará en el momento que se confirme la recepción del pago correspondiente.<br>
					3. La entrega y recepción de esta solicitud no garantiza que el proceso de Certificación será positivo.<br>
					4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Organización de Pequeños Productores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:</p>
			</td>
			<td colspan="2">
				<input type="text" class="form-control" name="responsable" required>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<p>OC que recibe la solicitud:</p>
			</td>
			<td colspan="2">
				<p class="alert alert-warning"><?php echo $_SESSION['nombreOC']; ?></p>
			</td>
		</tr>		
	</table>
	<input type="hidden" name="MM_insert" value="form1">
	<input type="hidden" name="fecha_elaboracion" value="<?php echo time()?>">
	<input type="hidden" name="status_publico" value="<?php echo $estadoPublico;?>">
	<input type="hidden" name="status_interno" value="<?php echo $estadoInterno;?>">
	<input type="hidden" name="mensaje" value="Acción agregada correctamente" />
	<!--<input type="hidden" name="idopp" value="<?php echo $_SESSION['idopp']?>">-->
	<input type="hidden" name="abreviacion" value="<?php echo $row_opp['abreviacion'];?>">
	<!--<input type="hidden" name="nombreOPP" value="<?php echo $row_opp['nombre']; ?>">-->
	<!--<input type="hidden" name="paisOPP" value="<?php echo $row_opp['pais']; ?>">-->
	<input type="hidden" name="idoc" value="<?php echo $_SESSION['idoc'];?>">
	

    <button style="width:200px;" class="btn btn-primary col-xs-2 col-xs-offset-5" type="submit" value="Enviar Solicitud" aria-label="Left Align">
      <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Enviar
    </button>
  

</form>


<script>
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

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
	  cell3.innerHTML = '<input type="date" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
	  cell4.innerHTML = '<input type="text" class="form-control" name="interrumpida['+contador+']" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?">';	  
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

	  

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
	  
	  cell3.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
	  
	  cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell7.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	  

	  }

	}	

</script>



<?
mysql_free_result($pais);

mysql_free_result($oc);
?>