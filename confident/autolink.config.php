<!DOCTYPE html>
<?php
if(!isset($_SESSION)){
	session_start();
}

if(isset($_SESSION['autolinklogin'])){
	if($_SESSION['autolinklogin']!=true){
		header("Location: index.php");
	}
}else{
	header("Location: index.php");
}
?>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>AutoLink RESTful</title>

	<link rel="stylesheet" href="complements/bootstrap/bootstrap.css">
	<link rel="stylesheet" href="complements/estilos.css">
</head>
<body>

<nav class="navbar navbar-default" ng-controller="ctrlmenu">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="autolink.php">AutoLink</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1">
        <ul class="navbar-nav nav navbar-right">
            <li><a href="autolink.php">INICIO</a></li>
            <li><a href="autolink.new.php">CREAR</a></li>
            <li><a href="#">CONFIGURAR</a></li>
            <li><a href="configuration/logout.php">SALIR</a></li>
        </ul>
    </div>
</nav>

<div class="jumbotron text‐center" style="margin-top:0px;margin-bottom:0px;padding:10px 0px;">
	<h1 align="center" style="margin-top:0px;"><small>AutoLink RESTful</small> </h1>
	<h2 align="center">CONFIGURACIONES</h2>
</div>

<div class="container-fluid" style="margin-top: 0px;">
	<div class="row">
		<div class="col md-12 table-responsive">

		</div>
	</div>
</div>

<script type="text/javascript" src="complements/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="complements/bootstrap/bootstrap.js"></script>

</body>
</html>