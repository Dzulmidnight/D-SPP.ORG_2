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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
mysql_select_db($database_dspp, $dspp);

$idadministrador = $_SESSION['idadministrador'];
$fecha_actual = time();
/* MUESTRA LAS SOLICITUDES CON LOS OPPs SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
opp.pais = 'PerÃº'

SELECT opp.idopp, opp.pais, opp.estatus_opp, opp.estatus_dspp, num_socios.idnum_socios, num_socios.idopp, num_socios.numero FROM num_socios INNER JOIN opp ON num_socios.idopp = opp.idopp WHERE opp.pais = 'PerÃº' AND (opp.estatus_opp != 'CANCELADO' OR opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) GROUP BY num_socios.idopp*/
if(isset($_POST['agregar_tarea'])){


  $tipo_tarea = $_POST['tipo_tarea'];
  $status_tarea = 2; //tarea iniciada

  switch ($tipo_tarea) {
    case '1': // enviar correo
      $asunto = $_POST['asunto'];
      $contenido = $_POST['contenido'];
      $fecha_fin = strtotime($_POST['fecha_fin']);
      $hora = $_POST['hora'];

      foreach ($_POST['idcontacto'] as $value) {
        
      }
      $insertSQL = sprintf("INSERT INTO tareas(fecha_fin, tipo_tarea, status_tarea, titulo, detalle, hora, responsable, fecha_registro)",
        GetSQLValueString());
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
      break;
    case '2': // reunión
      $asunto_reunion = $_POST['asunto_reunion'];
      $descripcion_reunion = $_POST['descripcion_reunion'];
      $fecha_reunion = strtotime($_POST['fecha_reunion']);
      $hora_reunion = $_POST['hora_reunion'];
      $responsable_reunion = $_POST['responsable_reunion'];

      $insertSQL = sprintf("INSERT INTO tareas(fecha_fin, tipo_tarea, status_tarea, titulo, detalle, hora, responsable, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($fecha_reunion, "int"),
        GetSQLValueString($tipo_tarea, "int"),
        GetSQLValueString($status_tarea, "text"),
        GetSQLValueString($asunto_reunion, "text"),
        GetSQLValueString($descripcion_reunion, "text"),
        GetSQLValueString($hora_reunion, "text"),
        GetSQLValueString($responsable_reunion, "int"),
        GetSQLValueString($fecha_actual, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //creamos el registro de la tarea registrada
      $idtarea = mysql_insert_id($dspp);

      $insertSQL = sprintf("INSERT INTO tareas_adm(idtarea, idadm) VALUES (%s, %s)",
        GetSQLValueString($idtarea, "int"),
        GetSQLValueString($idadministrador, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //registramos los involucrados en la tarea
      if(isset($_POST['cliente_reunion'])){
        $cliente_reunion = $_POST['cliente_reunion'];
        foreach($_POST['cliente_reunion'] as $value) {
          $insertSQL = sprintf("INSERT INTO involucrados_tarea(idtarea, idcontacto) VALUES (%s, %s)",
            GetSQLValueString($idtarea, "int"),
            GetSQLValueString($value, "int"));
          $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
        }
      }

      ///se guardan los administradores involucrados en la reunion
      if(isset($_POST['adm_reunion'])){
        foreach ($_POST['adm_reunion'] as $value) {
          $insertSQL = sprintf("INSERT INTO involucrados_tarea(idtarea, idadm) VALUES(%s, %s)",
            GetSQLValueString($idtarea, "int"),
            GetSQLValueString($value, "int"));
          $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
        }
      }

      /// revisamos si se va a agregar un recordatorio de la reunión
      if(isset($_POST['recordatorio']) && $_POST['recordatorio'] == 'SI'){
        $fecha_recordatorio = strtotime($_POST['fecha_recordatorio']);
        $recordatorio = 'SI';

        $updateSQL = sprintf("UPDATE tareas SET recordatorio = %s, fecha_recordatorio = %s WHERE idtarea = %s",
          GetSQLValueString($recordatorio, "text"),
          GetSQLValueString($fecha_recordatorio, "int"),
          GetSQLValueString($idtarea, "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

        echo "<script>alert('Se ha agregado una nueva reunión');</script>";
      }

      echo "<script>alert('se ha creado una nueva reunión');</script>";
      break;
    case '3': // llamada
      $asunto_llamada = $_POST['asunto_llamada']; //titulo
      $descripcion_llamada = $_POST['descripcion_llamada']; //detalle
      $fecha_llamada = strtotime($_POST['fecha_llamada']); //fecha_fin
      $hora_llamada = $_POST['hora_llamada']; //hora
      $responsable_llamada = $_POST['responsable_llamada']; //responsable
      $cliente_llamada = $_POST['cliente_llamada']; //idcontacto

      $insertSQL = sprintf("INSERT INTO tareas(fecha_fin, tipo_tarea, status_tarea, titulo, detalle, hora, idcontacto, responsable, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($fecha_llamada, "int"),
        GetSQLValueString($tipo_tarea, "int"),
        GetSQLValueString($status_tarea, "text"),
        GetSQLValueString($asunto_llamada, "text"),
        GetSQLValueString($descripcion_llamada, "text"),
        GetSQLValueString($hora_llamada, "text"),
        GetSQLValueString($cliente_llamada, "int"),
        GetSQLValueString($responsable_llamada, "int"),
        GetSQLValueString($fecha_actual, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //creamos el registro de la tarea registrada
      $idtarea = mysql_insert_id($dspp);

      $insertSQL = sprintf("INSERT INTO tareas_adm(idtarea, idadm) VALUES (%s, %s)",
        GetSQLValueString($idtarea, "int"),
        GetSQLValueString($idadministrador, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      echo "<script>alert('Se ha registrado una nueva llamada');</script>";
      break;
    case '4': // evento
      $nombre_evento = $_POST['nombre_evento'];
      $detalle_evento = $_POST['detalle_evento'];
      $fecha_inicio = strtotime($_POST['fecha_inicio']);
      $fecha_fin = strtotime($_POST['fecha_fin']);
      $hora_evento = $_POST['hora_evento'];
      $responsable_evento = $_POST['responsable_evento'];

      $insertSQL = sprintf("INSERT INTO tareas(fecha_inicio, fecha_fin, tipo_tarea, status_tarea, titulo, detalle, hora, responsable, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($fecha_inicio, 'int'),
        GetSQLValueString($fecha_fin, "int"),
        GetSQLValueString($tipo_tarea, "int"),
        GetSQLValueString($status_tarea, "text"),
        GetSQLValueString($nombre_evento, "text"),
        GetSQLValueString($detalle_evento, "text"),
        GetSQLValueString($hora_evento, "text"),
        GetSQLValueString($responsable_evento, "int"),
        GetSQLValueString($fecha_actual, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //creamos el registro de la tarea registrada
      $idtarea = mysql_insert_id($dspp);

      $insertSQL = sprintf("INSERT INTO tareas_adm(idtarea, idadm) VALUES (%s, %s)",
        GetSQLValueString($idtarea, "int"),
        GetSQLValueString($idadministrador, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //registramos los clientes involucrados en el evento
      if(isset($_POST['cliente_evento'])){
        $cliente_evento = $_POST['cliente_evento'];
        foreach($_POST['cliente_evento'] as $value) {
          $insertSQL = sprintf("INSERT INTO involucrados_tarea(idtarea, idcontacto) VALUES (%s, %s)",
            GetSQLValueString($idtarea, "int"),
            GetSQLValueString($value, "int"));
          $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
        }
      }

      ///se guardan los administradores involucrados en el evento
      if(isset($_POST['adm_evento'])){
        foreach ($_POST['adm_evento'] as $value) {
          $insertSQL = sprintf("INSERT INTO involucrados_tarea(idtarea, idadm) VALUES(%s, %s)",
            GetSQLValueString($idtarea, "int"),
            GetSQLValueString($value, "int"));
          $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
        }
      }

      /// revisamos si se va a agregar un recordatorio de la reunión
      if(isset($_POST['recordatorio_evento']) && $_POST['recordatorio_evento'] == 'SI'){
        $fecha_recordatorio_evento = strtotime($_POST['fecha_recordatorio_evento']);
        $recordatorio = 'SI';

        $updateSQL = sprintf("UPDATE tareas SET recordatorio = %s, fecha_recordatorio = %s WHERE idtarea = %s",
          GetSQLValueString($recordatorio, "text"),
          GetSQLValueString($fecha_recordatorio_evento, "int"),
          GetSQLValueString($idtarea, "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

        echo "<script>alert('Se ha agregado un recordatorio del evento');</script>";
      }

      echo "<script>alert('Se ha creado un nuevo evento');</script>";
      break;
    case '5': // tarea
      $fecha_inicio = strtotime($_POST['fecha_inicio']);
      $fecha_fin = strtotime($_POST['fecha_fin']);
      $titulo_tarea = $_POST['titulo_tarea'];
      $descripcion_tarea = $_POST['descripcion_tarea'];
      $responsable_tarea = $_POST['responsable_tarea'];

      //creamos la nueva tarea
      $insertSQL = sprintf("INSERT INTO tareas(fecha_inicio, fecha_fin, tipo_tarea, status_tarea, titulo, detalle, responsable, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($fecha_inicio, "int"),
        GetSQLValueString($fecha_fin, "int"),
        GetSQLValueString($tipo_tarea, "int"),
        GetSQLValueString($status_tarea, "text"),
        GetSQLValueString($titulo_tarea, "text"),
        GetSQLValueString($descripcion_tarea, "text"),
        GetSQLValueString($responsable_tarea, "int"),
        GetSQLValueString($fecha_actual, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //creamos el registro de la tarea registrada
      $idtarea = mysql_insert_id($dspp);

      $insertSQL = sprintf("INSERT INTO tareas_adm(idtarea, idadm) VALUES (%s, %s)",
        GetSQLValueString($idtarea, "int"),
        GetSQLValueString($idadministrador, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

      //registramos los involucrados en la tarea
      if(isset($_POST['idcontacto'])){
        $idcontacto = $_POST['idcontacto'];
        foreach($_POST['idcontacto'] as $value) {
          $insertSQL = sprintf("INSERT INTO involucrados_tarea(idtarea, idcontacto) VALUES (%s, %s)",
            GetSQLValueString($idtarea, "int"),
            GetSQLValueString($value, "int"));
          $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
        }
      }
      echo "<script>alert('Se ha creado una nueva tarea');</script>";

      break;
    default: // no se selecciono una opción valida
        echo "<script>alert('No se pudo crear la tarea');</script>";
      break;
  }

  /*if($_POST['agregar_tarea'] == 1){
    echo "<script>window.location='?CRM&po_clientes'</script>";
  }*/

}
if(isset($_POST['agregar_reunion'])){
  $tipo_tarea = 2; // tipo_tarea = reunion
  $status_tarea = 2; //status_crm = Tarea Iniciada
  $titulo = $_POST['titulo'];
  $detalle = $_POST['detalle'];
  $fecha_fin = strtotime($_POST['fecha_fin']);
  $responsable = $_POST['responsable'];
  $hora = $_POST['hora'];
  $recordatorio = 'NO';

  $insertSQL = sprintf("INSERT INTO tareas (fecha_fin, tipo_tarea, status_tarea, titulo, detalle, hora, responsable, fecha_registro, recordatorio) VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($fecha_fin, "int"),
    GetSQLValueString($tipo_tarea, "int"),
    GetSQLValueString($status_tarea, "int"),
    GetSQLValueString($titulo, "text"),
    GetSQLValueString($detalle, "text"),
    GetSQLValueString($hora, "text"),
    GetSQLValueString($responsable, "int"),
    GetSQLValueString($fecha_actual, "int"),
    GetSQLValueString($recordatorio, "text"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $idtarea = mysql_insert_id($dspp);
  ///registramos quien creo la reunion
  $insertSQL = sprintf("INSERT INTO tareas_adm(idtarea, idadm) VALUES (%s, %s)",
    GetSQLValueString($idtarea, "int"),
    GetSQLValueString($idadministrador, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  ///se guardan los contactos involucrados en la reunion
  if(isset($_POST['clientes_reunion'])){
    foreach ($_POST['clientes_reunion'] as $value) {
      $insertSQL = sprintf("INSERT INTO involucrados_tarea(idtarea, clientes_reunion) VALUES(%s, %s)",
        GetSQLValueString($idtarea, "int"),
        GetSQLValueString($value, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }
  }


  ///se guardan los administradores involucrados en la reunion
  if(isset($_POST['adm_involucrado'])){
    foreach ($_POST['adm_involucrado'] as $value) {
      $insertSQL = sprintf("INSERT INTO involucrados_tarea(idtarea, idadm) VALUES(%s, %s)",
        GetSQLValueString($idtarea, "int"),
        GetSQLValueString($value, "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }
  }

  /// revisamos si se va a agregar un recordatorio de la reunión
  if(isset($_POST['recordatorio']) && $_POST['recordatorio'] == 'SI'){
    $fecha_recordatorio = strtotime($_POST['fecha_recordatorio']);
    $recordatorio = 'SI';

    $updateSQL = sprintf("UPDATE tareas SET recordatorio = %s, fecha_recordatorio = %s WHERE idtarea = %s",
      GetSQLValueString($recordatorio, "text"),
      GetSQLValueString($fecha_recordatorio, "int"),
      GetSQLValueString($idtarea, "int"));
    $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

    echo "<script>alert('Se ha agregado una nueva reunión');</script>";
  }


}

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());

if($_GET['po_clientes'] == 'add_reunion'){
?>
  <form action="" method="POST">
    <h4>Crear, Nueva Reunion</h4>
    <div class="row">
      <div class="col-lg-12">
        <button type="submit" name="agregar_tarea" value="1" class="btn btn-default">Guardar</button>
        <button type="submit" name="agregar_tarea" value="2" class="btn btn-default">Guardar y Crear nuevo</button>
        <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>
        <hr>
      </div>
      <div class="col-lg-6">
          <h4>Información sobre la nueva reunión</h4>

          <div class="form-group">
            <label for="titulo">Asunto de la Reunión</label>
            <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Asunto de la reunión">
          </div>
          <div class="form-group">
            <label for="detalle">Descripción sobre la reunión</label>
            <textarea name="detalle" id="detalle" class="form-control" rows="2" placeholder="Descripción de la reunión"></textarea>
          </div>


          <div class="form-group">
            <label for="responsable">Responsable de la Reunión</label>
            <br>
            <select name="responsable" id="responsable">
              <option value="">---</option>
              <?php 
              $row_adm = mysql_query("SELECT idadm, nombre FROM adm", $dspp) or die(mysql_error());
              while($adm = mysql_fetch_assoc($row_adm)){
                if($adm['idadm'] == $idadministrador){
                  echo "<option value='$adm[idadm]' selected>".utf8_encode($adm['nombre'])."</option>";
                }else{
                  echo "<option value='$adm[idadm]'>".utf8_encode($adm['nombre'])."</option>";
                }
              }
               ?>
            </select>
          </div>

      </div>
      <div class="col-lg-6">
          <div class="form-group">
            <label for="fecha_fin">Fecha de la Reunión</label>
            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="dd/mm/aaaa">
          </div>
          <div class="form-group">
            <label for="hora">Hora de la Reunión</label>
            <input type="text" class="form-control" name="hora" id="hora" placeholder="Hora">
          </div>

          <h4 style="color:#e74c3c">Involucrados en la Reunión</h4>
          <div class="form-group">
            <label for="idcontacto">Agregar Posibles clientes a la reunión</label>
              <select id="idcontacto" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="idcontacto[]"  multiple>
                <?php
                $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

                while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
                  echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
                }
                 ?>
              </select>
          </div>

          <div class="form-group">
            <label for="adm_involucrado">Agregar administradores a la reunión</label>
              <select id="adm_involucrado" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="adm_involucrado[]"  multiple>
                <?php
                $row_adm = mysql_query("SELECT idadm, username, nombre FROM adm", $dspp) or die(mysql_error());

                while($adm = mysql_fetch_assoc($row_adm)){
                  echo "<option value='$adm[idadm]'>".utf8_decode($adm['nombre'])."</option>";
                }
                 ?>
              </select>
          </div>

          <!--<div class="form-group">
            <label for="status" class="col-sm-2 control-label">status</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="status" id="status" value="" readonly>
            </div>
          </div>-->
      </div>
      <div class="col-lg-12">
        <h4>¿Desea enviar un recordatorio antes de la reunión?</h4>
        <div class="radio">
          <label>
            <input onchange="validarRecordatorio()" type="radio" name="recordatorio" id="recordatorio" value="SI">
            Si deseo enviar un recordatorio.
          </label>
        </div>
        <div class="radio">
          <label>
            <input onchange="validarRecordatorio()" type="radio" name="recordatorio" id="recordatorio" value="NO">
            No deseo enviar recordatorio.
          </label>
        </div>
        <div id="fecha_recordatorio" style="display:none">
          <h4 style="color:#e74c3c">Seleccione la fecha en la que se enviara el recordatorio</h4>
          <input type="date" name="fecha_recordatorio" placeholder="dd/mm/aaaa" required>  
        </div>
        

          <div class="text-center">
            <hr>
            <button type="submit" name="agregar_reunion" value="1" class="btn btn-default">Guardar</button>
            <button type="submit" name="agregar_reunion" value="2" class="btn btn-default">Guardar y Crear Nuevo</button>
            <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>        
          </div>
       
      </div>

    </div>
  </form>
  <script>
/*    function funcionReunion(){
      var opcion = document.getElementById('tipo_tarea').value;

      if(opcion == 2){
        document.getElementById('descripcion_tarea').style.display = 'block';
      }else if(opcion == 3){
        document.getElementById('descripcion_tarea').style.display = 'block';
      }
    }
*/
    function validarRecordatorio(){
      
      /// evaluamos si el usuario quiere que se envie un recordatorio
      var recordatorio = '';
      recordatorio = document.getElementsByName("recordatorio");

      var opcion_recordatorio = '';
      for(var i=0; i<recordatorio.length; i++) {    
        if(recordatorio[i].checked) {
          opcion_recordatorio = recordatorio[i].value;
          ventas = true;
          break;
        }
      }
      if(opcion_recordatorio == 'SI'){
        document.getElementById('fecha_recordatorio').style.display = 'block';
      }else{
        document.getElementById('fecha_recordatorio').style.display = 'none'
      }
    }
  </script>
<?php
}else{
?>

  <form action="" method="POST"> <!--- INICIA FORM NUEVA TAREA -->
    <h4>Crear, Nueva Tarea</h4>
    <div class="row">
      <div class="col-lg-12">
        <button type="submit" name="agregar_tarea" value="1" class="btn btn-default">Guardar</button>
        <button type="submit" name="agregar_tarea" value="2" class="btn btn-default">Guardar y Crear nuevo</button>
        <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>
        <hr>
      </div>
        <div class="col-lg-12">
            <div class="form-group">
              <label for="tipo_tarea">Seleccione el tipo de tarea</label>
              <br>
              <select name="tipo_tarea" id="tipo_tarea" onchange="funcionSelect()">
                <?php
                $row_tarea = mysql_query("SELECT idtipo_tarea, tipo FROM tipo_tarea", $dspp) or die(mysql_error());
                while($tipo_tarea = mysql_fetch_assoc($row_tarea)){
                  if($tipo_tarea['idtipo_tarea'] == 5){
                    echo "<option value='$tipo_tarea[idtipo_tarea]' selected>$tipo_tarea[tipo]</option>";
                  }else{
                    echo "<option value='$tipo_tarea[idtipo_tarea]'>$tipo_tarea[tipo]</option>";
                  }
                }
                 ?>
              </select>
            </div>          
        </div>
        <div id="div_tarea" style="display:block"> <!-- INICIA DIV_TAREA -->
          <div class="col-lg-6">
              <div class="form-group">
                <label for="titulo_tarea">Titulo</label>
                <input type="text" class="form-control" name="titulo_tarea" id="titulo_tarea" placeholder="Titulo de la tarea">
              </div>
              <div class="form-group">
                <label for="descripcion_tarea">Descripción de la tarea</label>
                <textarea name="descripcion_tarea" id="descripcion_tarea" class="form-control" rows="2" placeholder="Descripción de la tarea"></textarea>
              </div>


              <div class="form-group">
                <label for="responsable_tarea">Responsable de la tarea</label>
                <br>
                <select name="responsable_tarea" id="responsable_tarea">
                  <option value="">---</option>
                  <?php 
                  $row_adm = mysql_query("SELECT idadm, nombre FROM adm", $dspp) or die(mysql_error());
                  while($adm = mysql_fetch_assoc($row_adm)){
                    if($adm['idadm'] == $idadministrador){
                      echo "<option value='$adm[idadm]' selected>".utf8_encode($adm['nombre'])."</option>";
                    }else{
                      echo "<option value='$adm[idadm]'>".utf8_encode($adm['nombre'])."</option>";
                    }
                  }
                   ?>
                </select>
              </div>

          </div>
          <div class="col-lg-6">
              <div class="form-group">
                <label for="fecha_inicio">Fecha Inicio</label>
                <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" placeholder="dd/mm/aaaa">
              </div>
              <div class="form-group">
                <label for="fecha_fin">Fecha Fin</label>
                <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="dd/mm/aaaa">
              </div>
              <div class="form-group">
                <label for="cliente_tarea">Posible Cliente Involucrado </label>
                  <select id="cliente_tarea" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="cliente_tarea[]"  multiple>
                    <?php
                    $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

                    while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
                      echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
                    }
                     ?>
                  </select>
              </div>
          </div>          
        </div><!-- TERMINA DIV_TAREA --> 

        <div id="div_correo" style="display:none"><!-- INICIA DIV_CORREO -->
          <div class="col-lg-6">
              <div class="form-group">
                <label for="titulo">Asunto del correo</label>
                <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Titulo de la tarea">
              </div>
              <div class="form-group">
                <label for="detalle">Contenido del correo</label>
                <textarea name="detalle" id="detalle" class="form-control" rows="2" placeholder="Descripción del correo"></textarea>
              </div>
          </div>
          <div class="col-lg-6">

              <div class="form-group">
                <label for="fecha_fin">Fecha de envio del correo</label>
                <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="dd/mm/aaaa">
              </div>
              <div class="form-group">
                <label for="hora">Hora</label>
                <input type="text" class="form-control" name="hora" id="hora" placeholder="Hora">
              </div>
              <div class="form-group">
                <label for="idcontacto">Posible Cliente Involucrado 2</label>
                  <select id="idcontacto" class="chosen-select" data-placeholder="Posibles Clientes" name="idcontacto[]"  multiple>
                    <?php
                    $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

                    while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
                      echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
                    }
                     ?>
                  </select>
              </div>
          </div>  
        </div><!-- TERMINA DIV_CORREO -->
        <div id="div_reunion" style="display:none"><!-- INICIA DIV_REUNION -->
            <div class="col-lg-6">
                <h4>Información sobre la nueva reunión</h4>

                <div class="form-group">
                  <label for="asunto_reunion">Asunto de la Reunión</label>
                  <input type="text" class="form-control" name="asunto_reunion" id="asunto_reunion" placeholder="Asunto de la reunión">
                </div>
                <div class="form-group">
                  <label for="descripcion_reunion">Descripción sobre la reunión</label>
                  <textarea name="descripcion_reunion" id="descripcion_reunion" class="form-control" rows="2" placeholder="Descripción de la reunión"></textarea>
                </div>


                <div class="form-group">
                  <label for="responsable_reunion">Responsable de la Reunión</label>
                  <br>
                  <select name="responsable_reunion" id="responsable_reunion">
                    <option value="">---</option>
                    <?php 
                    $row_adm = mysql_query("SELECT idadm, nombre FROM adm", $dspp) or die(mysql_error());
                    while($adm = mysql_fetch_assoc($row_adm)){
                      if($adm['idadm'] == $idadministrador){
                        echo "<option value='$adm[idadm]' selected>".utf8_encode($adm['nombre'])."</option>";
                      }else{
                        echo "<option value='$adm[idadm]'>".utf8_encode($adm['nombre'])."</option>";
                      }
                    }
                     ?>
                  </select>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="form-group">
                  <label for="fecha_reunion">Fecha de la Reunión</label>
                  <input type="date" class="form-control" name="fecha_reunion" id="fecha_reunion" placeholder="dd/mm/aaaa">
                </div>
                <div class="form-group">
                  <label for="hora_reunion">Hora de la Reunión</label>
                  <input type="text" class="form-control" name="hora_reunion" id="hora_reunion" placeholder="Hora">
                </div>

                <h4 style="color:#e74c3c">Involucrados en la Reunión</h4>
                <div class="form-group">
                  <label for="cliente_reunion">Agregar Posibles clientes a la reunión</label>
                    <select id="cliente_reunion" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="cliente_reunion[]"  multiple>
                      <?php
                      $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

                      while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
                        echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
                      }
                       ?>
                    </select>
                </div>

                <div class="form-group">
                  <label for="adm_reunion">Agregar administradores a la reunión</label>
                    <select id="adm_reunion" class="chosen-select" data-placeholder="Posibles Clientes" name="adm_reunion[]"  multiple>
                      <?php
                      $row_adm = mysql_query("SELECT idadm, username, nombre FROM adm", $dspp) or die(mysql_error());

                      while($adm = mysql_fetch_assoc($row_adm)){
                        echo "<option value='$adm[idadm]'>".utf8_decode($adm['nombre'])."</option>";
                      }
                       ?>
                    </select>
                </div>

                <!--<div class="form-group">
                  <label for="status" class="col-sm-2 control-label">status</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="status" id="status" value="" readonly>
                  </div>
                </div>-->
            </div>
            <div class="col-lg-12">
              <h4>¿Desea enviar un recordatorio antes de la reunión?</h4>
              <div class="radio">
                <label>
                  <input onchange="validarRecordatorio()" type="radio" name="recordatorio" id="recordatorio" value="SI">
                  Si deseo enviar un recordatorio.
                </label>
              </div>
              <div class="radio">
                <label>
                  <input onchange="validarRecordatorio()" type="radio" name="recordatorio" id="recordatorio" value="NO">
                  No deseo enviar recordatorio.
                </label>
              </div>
              <div id="fecha_recordatorio" style="display:none">
                <h4 style="color:#e74c3c">Seleccione la fecha en la que se enviara el recordatorio</h4>
                <input type="date" id="valor_fecha" name="fecha_recordatorio" placeholder="dd/mm/aaaa">  
              </div>
              


             
            </div>



          <script>
        /*    function funcionReunion(){
              var opcion = document.getElementById('tipo_tarea').value;

              if(opcion == 2){
                document.getElementById('descripcion_tarea').style.display = 'block';
              }else if(opcion == 3){
                document.getElementById('descripcion_tarea').style.display = 'block';
              }
            }
        */
            function validarRecordatorio(){
              
              /// evaluamos si el usuario quiere que se envie un recordatorio
              var recordatorio = '';
              recordatorio = document.getElementsByName("recordatorio");

              var opcion_recordatorio = '';
              for(var i=0; i<recordatorio.length; i++) {    
                if(recordatorio[i].checked) {
                  opcion_recordatorio = recordatorio[i].value;
                  ventas = true;
                  break;
                }
              }
              if(opcion_recordatorio == 'SI'){
                document.getElementById('fecha_recordatorio').style.display = 'block';
              }else{
                document.getElementById('fecha_recordatorio').style.display = 'none'
              }
            }
          </script>
        </div><!-- TERMINA DIV_REUNION -->
        <div id="div_llamada" style="display:none"><!-- INICIA DIV_LLAMADA -->
          <div class="col-lg-6">
              <div class="form-group">
                <label for="asunto_llamada">Asunto de la llamada</label>
                <input type="text" class="form-control" name="asunto_llamada" id="asunto_llamada" placeholder="Asunto de la llamada">
              </div>
              <div class="form-group">
                <label for="descripcion_llamada">Descripción de la llamada</label>
                <textarea name="descripcion_llamada" id="descripcion_llamada" class="form-control" rows="2" placeholder="Descripción de la llamada"></textarea>
              </div>


              <div class="form-group">
                <label for="responsable_llamada">Responsable de la llamada</label>
                <br>
                <select name="responsable_llamada" id="responsable_llamada">
                  <option value="">---</option>
                  <?php 
                  $row_adm = mysql_query("SELECT idadm, nombre FROM adm", $dspp) or die(mysql_error());
                  while($adm = mysql_fetch_assoc($row_adm)){
                    if($adm['idadm'] == $idadministrador){
                      echo "<option value='$adm[idadm]' selected>".utf8_encode($adm['nombre'])."</option>";
                    }else{
                      echo "<option value='$adm[idadm]'>".utf8_encode($adm['nombre'])."</option>";
                    }
                  }
                   ?>
                </select>
              </div>

          </div>
          <div class="col-lg-6">
              <div class="form-group">
                <label for="fecha_llamada">Fecha de la llamada</label>
                <input type="date" class="form-control" name="fecha_llamada" id="fecha_llamada" placeholder="dd/mm/aaaa">
              </div>
              <div class="form-group">
                <label for="hora_llamada">Hora</label>
                <input type="text" class="form-control" name="hora_llamada" id="hora_llamada" placeholder="Hora">
              </div>
              <div class="form-group">
                <label for="cliente_llamada">Posible Cliente Involucrado</label>
                  <select id="cliente_llamada" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="cliente_llamada">
                    <?php
                    $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

                    while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
                      echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
                    }
                     ?>
                  </select>
              </div>
          </div> 
        </div><!-- TERMINA DIV_LLAMADA -->
        <div id="div_evento" style="display:none"><!-- INICIA DIV_EVENTO -->

            <div class="col-lg-6">
                <h4>Información sobre el nuevo evento</h4>

                <div class="form-group">
                  <label for="nombre_evento">Nombre del evento</label>
                  <input type="text" class="form-control" name="nombre_evento" id="nombre_evento" placeholder="Nombre del evento">
                </div>
                <div class="form-group">
                  <label for="detalle_evento">Detalles sobre el evento</label>
                  <textarea name="detalle_evento" id="detalle_evento" class="form-control" rows="2" placeholder="Detalles del evento"></textarea>
                </div>


                <div class="form-group">
                  <label for="responsable_evento">Responsable del evento</label>
                  <br>
                  <select name="responsable_evento" id="responsable_evento">
                    <option value="">---</option>
                    <?php 
                    $row_adm = mysql_query("SELECT idadm, nombre FROM adm", $dspp) or die(mysql_error());
                    while($adm = mysql_fetch_assoc($row_adm)){
                      if($adm['idadm'] == $idadministrador){
                        echo "<option value='$adm[idadm]' selected>".utf8_encode($adm['nombre'])."</option>";
                      }else{
                        echo "<option value='$adm[idadm]'>".utf8_encode($adm['nombre'])."</option>";
                      }
                    }
                     ?>
                  </select>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="form-group">
                  <label for="fecha_inicio">Fecha de Inicio</label>
                  <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" placeholder="dd/mm/aaaa">
                </div>
                <div class="form-group">
                  <label for="fecha_fin">Fecha de Fin</label>
                  <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="dd/mm/aaaa">
                </div>
                <div class="form-group">
                  <label for="hora_evento">Hora del evento</label>
                  <input type="text" class="form-control" name="hora_evento" id="hora_evento" placeholder="Hora">
                </div>

                <h4 style="color:#e74c3c">Involucrados en el evento</h4>
                <div class="form-group">
                  <label for="cliente_eventp">Agregar Posibles clientes en el evento</label>
                    <select id="cliente_eventp" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="cliente_eventp[]"  multiple>
                      <?php
                      $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

                      while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
                        echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
                      }
                       ?>
                    </select>
                </div>

                <div class="form-group">
                  <label for="adm_evento">Agregar administradores al evento</label>
                    <select id="adm_evento" class="chosen-select" data-placeholder="Posibles Clientes" name="adm_evento[]"  multiple>
                      <?php
                      $row_adm = mysql_query("SELECT idadm, username, nombre FROM adm", $dspp) or die(mysql_error());

                      while($adm = mysql_fetch_assoc($row_adm)){
                        echo "<option value='$adm[idadm]'>".utf8_decode($adm['nombre'])."</option>";
                      }
                       ?>
                    </select>
                </div>

                <!--<div class="form-group">
                  <label for="status" class="col-sm-2 control-label">status</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="status" id="status" value="" readonly>
                  </div>
                </div>-->
            </div>
            <div class="col-lg-12">
              <h4>¿Desea enviar un recordatorio antes del evento?</h4>
              <div class="radio">
                <label>
                  <input onchange="validarRecordatorio2()" type="radio" name="recordatorio_evento" id="recordatorio_evento" value="SI">
                  Si deseo enviar un recordatorio.
                </label>
              </div>
              <div class="radio">
                <label>
                  <input onchange="validarRecordatorio2()" type="radio" name="recordatorio_evento" id="recordatorio_evento" value="NO">
                  No deseo enviar recordatorio.
                </label>
              </div>
              <div id="fecha_recordatorio_evento" style="display:none">
                <h4 style="color:#e74c3c">Seleccione la fecha en la que se enviara el recordatorio</h4>
                <input type="date" id="valor_fecha2" name="fecha_recordatorio_evento" placeholder="dd/mm/aaaa">  
              </div>

             
            </div>

          <script>
        /*    function funcionReunion(){
              var opcion = document.getElementById('tipo_tarea').value;

              if(opcion == 2){
                document.getElementById('descripcion_tarea').style.display = 'block';
              }else if(opcion == 3){
                document.getElementById('descripcion_tarea').style.display = 'block';
              }
            }
        */
            function validarRecordatorio2(){
              
              /// evaluamos si el usuario quiere que se envie un recordatorio
              var recordatorio = '';
              recordatorio = document.getElementsByName("recordatorio_evento");

              var opcion_recordatorio = '';
              for(var i=0; i<recordatorio.length; i++) {    
                if(recordatorio[i].checked) {
                  opcion_recordatorio = recordatorio[i].value;
                  ventas = true;
                  break;
                }
              }
              if(opcion_recordatorio == 'SI'){
                document.getElementById('fecha_recordatorio_evento').style.display = 'block';
              }else{
                document.getElementById('fecha_recordatorio_evento').style.display = 'none'
              }
            }
          </script>
        </div><!-- TERMINA DIV_EVENTO -->

      <div class="col-lg-12">
          <div class="text-center">
            <hr>
            <button type="submit" name="agregar_tarea" value="1" class="btn btn-default">Guardar</button>
            <button type="submit" name="agregar_tarea" value="2" class="btn btn-default">Guardar y Crear Nuevo</button>
            <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>        
          </div>
       
      </div>

    </div>
  </form> <!-- TERMINA FORM NUEVA TAREA -->

<script>
function funcionSelect() {
    var valor_select = document.getElementById("tipo_tarea").value;
    if(valor_select == 1){ //enviar correo
      document.getElementById('div_correo').style.display = 'block';
      document.getElementById('div_tarea').style.display = 'none';
      document.getElementById('div_reunion').style.display = 'none';
      document.getElementById('div_llamada').style.display = 'none';
      document.getElementById('div_evento').style.display = 'none';
    }else if(valor_select == 2){ //reunion
      document.getElementById('div_reunion').style.display = 'block';
      document.getElementById('div_tarea').style.display = 'none';
      document.getElementById('div_correo').style.display = 'none';
      document.getElementById('div_llamada').style.display = 'none';
      document.getElementById('div_evento').style.display = 'none';
    }else if(valor_select == 3){ //llamada
      document.getElementById('div_llamada').style.display = 'block';
      document.getElementById('div_tarea').style.display = 'none';
      document.getElementById('div_reunion').style.display = 'none';
      document.getElementById('div_correo').style.display = 'none';
      document.getElementById('div_evento').style.display = 'none';
    }else if(valor_select == 4){ //evento
      document.getElementById('div_evento').style.display = 'block';
      document.getElementById('div_tarea').style.display = 'none';
      document.getElementById('div_reunion').style.display = 'none';
      document.getElementById('div_llamada').style.display = 'none';
      document.getElementById('div_correo').style.display = 'none';
    }else if(valor_select == 5){ //tarea
      document.getElementById('div_tarea').style.display = 'block';
      document.getElementById('div_correo').style.display = 'none';
      document.getElementById('div_reunion').style.display = 'none';
      document.getElementById('div_llamada').style.display = 'none';
      document.getElementById('div_evento').style.display = 'none';
    }else{
      document.getElementById('div_tarea').style.display = 'block';
      document.getElementById('div_correo').style.display = 'none';
      document.getElementById('div_reunion').style.display = 'none';
      document.getElementById('div_llamada').style.display = 'none';
      document.getElementById('div_evento').style.display = 'none';
    }
}
</script>

<?php
}
?>