<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 


  $rutaArchivo = "../formatos/anexos/";

  if(isset($_POST['enviarA'])){
    $asunto = $_POST['asunto'];
    $mensaje = $_POST['mensaje'];

    if(!empty($_FILES['archivo']['name'])){
        $_FILES["archivo"]["name"];
          move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaArchivo.$_FILES["archivo"]["name"]);
          $archivo = $rutaArchivo.basename($_FILES["archivo"]["name"]);
          $mail->AddAttachment($archivo);
    }
        $mail->Subject = utf8_decode($asunto);
        $mail->Body = utf8_decode($mensaje);
        $mail->MsgHTML(utf8_decode($mensaje));


    foreach($_POST['enviarA'] as $enviarA){
      //echo $enviarA."<br>";
      if($enviarA == "todos"){

        $query = "SELECT email FROM opp WHERE email != ''";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        while($correo = mysql_fetch_assoc($ejecutar)){
          $mail->AddAddress($correo['email']);
        }
          $mail->Send();
          $mail->ClearAddresses(); 
        /* SE ENVIA EL CORREO A LOS OPPs*/

        $query = "SELECT email FROM com WHERE email != ''";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        while($correo = mysql_fetch_assoc($ejecutar)){
          $mail->AddAddress($correo['email']);
        }
          $mail->Send();
          $mail->ClearAddresses(); 
        /* SE ENVIA EL CORREO A LOS OPPs*/


        $query = "SELECT email FROM oc WHERE email != ''";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        while($correo = mysql_fetch_assoc($ejecutar)){
          $mail->AddAddress($correo['email']);
        }
          $mail->Send();
          $mail->ClearAddresses(); 
        /* SE ENVIA EL CORREO A LOS OCs*/

        $query = "SELECT email FROM adm WHERE email != ''";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        while($correo = mysql_fetch_assoc($ejecutar)){
          $mail->AddAddress($correo['email']);
        }
          $mail->Send();
          $mail->ClearAddresses(); 
        /* SE ENVIA EL CORREO A LOS ADMs*/


          if($mail->Send()){
            echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
          }else{
                echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
          }


      }else{
        $query = "SELECT email FROM $enviarA WHERE email != ''";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        while($correo = mysql_fetch_assoc($ejecutar)){
          $mail->AddAddress($correo['email']);
        }

          if($mail->Send()){
            echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
          }else{
                echo "<script>alert('Error, no se pudo enviar el correo');location.href ='javascript:history.back()';</script>";
          }
          $mail->ClearAddresses(); 
      }
    }
  }

/**************************************** TERMINA EL ENVIO DE CORREOS  **************************************************************/


 ?>

<script>
  function validar(){

    
    enviarA = document.getElementsByName("enviarA[]");
     
    var seleccionado = false;
    for(var i=0; i<enviarA.length; i++) {    
      if(enviarA[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar un destinatario");
      return false;
    }

    return true
  }
 /* function mostrar(){
    document.getElementById('oculto').style.display = 'block';
  }
  function ocultar()
  {
    document.getElementById('oculto').style.display = 'none';
  }*/


  function ocultar()
  {
    document.getElementById('todos').checked = 0;
  }
  function ocultarTodos()
  {
    document.getElementById('checkbox1').checked = 0;
    document.getElementById('checkbox2').checked = 0;
    document.getElementById('checkbox3').checked = 0;
    document.getElementById('checkbox4').checked = 0;

  }

</script>
  

 <!--<div class="col-xs-3">
  Destinatario<input type="text" class="form-control" name="destinatario"> 
 </div>-->

 <form action="" method="post" name="correo1" enctype="multipart/form-data">
  <div class="well">
    <div class="col-md-2">
     Enviar a:
    </div>
    <div class="col-md-2">
      <div class="col-md-4">
        <input type="checkbox" class="form-control" id="todos" name="enviarA[]" value="todos" onclick="ocultarTodos()">
      </div>
      <div class="col-md-6">
        TODOS
      </div>
    </div>

    <div class="col-md-2">
      <div class="col-md-4">
        <input type="checkbox" class="form-control"  id="checkbox1" name="enviarA[]" value="opp" onclick="ocultar()">
      </div>
      <div class="col-md-6">
        OPP
      </div>
    </div>

    <div class="col-md-2">
      <div class="col-md-4">
        <input type="checkbox" class="form-control"  id="checkbox2" name="enviarA[]" value="com" onclick="ocultar()">
      </div>
      <div class="col-md-6">
        EMPRESA
      </div>
    </div>


    <div class="col-md-2">
      <div class="col-md-4">
        <input type="checkbox" class="form-control"  id="checkbox3" name="enviarA[]" value="oc" onclick="ocultar()">
      </div>
      <div class="col-md-6">
        OC
      </div>
    </div>

    <div class="col-md-2">
      <div class="col-md-4">
        <input type="checkbox" class="form-control"  id="checkbox4" name="enviarA[]" value="adm" onclick="ocultar()">
      </div>
      <div class="col-md-6">
        ADM
      </div>
    </div>

  </div>




   <div class="col-xs-12">
    Asunto: <input type="text" class="form-control" name="asunto">
   </div>
   <div class="col-xs-12">
    Mensaje: <textarea name="mensaje" id="textareaMensaje" cols="30" rows="10" class="form-control"></textarea>   
   </div>
   <div class="col-xs-12">
     Archivos
     <input type="file" name="archivo" class="form-control">
   </div>

   <div class="col-xs-12" style="margin-top:10px;">
    <input type="submit" class="btn btn-success" onclick="validar()">   
    <input type="hidden" name="correo1" value="1">
   </div>

 </form>