<?php
require_once('../Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);
if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?EMPRESA";

	if(!$_SESSION["autentificado"]){
		header("Location:".$redireccion);
	}
}


// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
  
  $logoutGoTo = "../index.php?EMPRESA";
  if ($logoutGoTo) {
    session_start();
    session_destroy();

    header("Location: $logoutGoTo");
    exit;
  }
}


$idempresa = $_SESSION['idempresa'];

$row_empresa = mysql_query("SELECT spp, abreviacion, pais, maquilador, comprador, intermediario FROM empresa WHERE idempresa = $idempresa", $dspp) or die(mysql_error());
$empresa = mysql_fetch_assoc($row_empresa);
$tipo_empresa = '';
if($empresa['maquilador']){
  $tipo_empresa = 'MAQUILADOR';
}else if($empresa['comprador']){
  $tipo_empresa = 'COMPRADOR FINAL';
}else if($empresa['intermediario']){
  $tipo_empresa = 'INTERMEDIARIO';
}

?>


<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/FUNDEPPO.png">



    <title>D-SPP.ORG</title>

    <!---LIBRERIAS DE Bootstrap File Input-->
    <!--<link href="../css/fileinput.css" media="all" rel="stylesheet" type="text/css" />-->
    <!-- Bootstrap core CSS -->
   <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!--<script src="../js/fileinput.min.js" type="text/javascript"></script>
    <script src="../js/fileinput_locale_es.js"></script>-->


     <!---LIBRERIAS DE Bootstrap File Input-->

    <script type="text/javascript" src="../js/bootstrap-filestyle.js"></script>
    <link rel="stylesheet" href="../chosen/chosen.css">


    <!------------------- bootstrap-switch -------------->

      <link href="../bootstrap-switch-master/bootstrap-switch.css" rel="stylesheet">
      <script src="../bootstrap-switch-master/bootstrap-switch.js"></script>

    <!------------------- bootstrap-switch -------------->    

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="main_menu.php">D-SPP.ORG</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
        
          <? include("contents/menu.php");?>
          
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        
        <? include("selector.php");?>

        </div>
      </div>
    </div>
    
    <!------------------- bootstrap-switch -------------->
    <script>
      $("[name='my-checkbox']").bootstrapSwitch();
    </script>
    <!------------------- bootstrap-switch -------------->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <!--<script src="../bootstrap/js/vendor/holder.js"></script>-->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<script src="../bootstrap/js/ie10-viewport-bug-workaround.js"></script>-->
  <script src="../chosen/chosen.jquery.js" type="text/javascript"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>

      <script>
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
    </script>
    



  </body>
</html>