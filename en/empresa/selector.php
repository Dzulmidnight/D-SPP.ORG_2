<?
if(isset($_GET['buscador'])){
	include('contents/buscador.php');
}else if(isset($_GET['SOLICITUD'])){
	include ("contents/solicitud/solicitud.php");
}else if(isset($_GET['INFORME'])){
	include ("contents/informe/informe.php");
}else if(isset($_GET['EMPRESA'])){
	include ("contents/empresa/empresa.php");
}else{
	include("contents/main.php");
}
?>