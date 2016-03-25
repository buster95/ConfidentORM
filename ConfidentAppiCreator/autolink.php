<!DOCTYPE html>
<html lang="es">
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
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<!-- <base href="/Confident/confident/"> -->
	<title>AutoLink RESTful</title>

	<link rel="stylesheet" href="complements/bootstrap/bootstrap.css">
	<link rel="stylesheet" href="complements/estilos.css">

	<link rel="stylesheet" href="complements/icon/icon-moon.css">
	<link rel="stylesheet" href="complements/icon/css/font-awesome.min.css">
</head>



<body ng-app="applink">
<nav class="navbar navbar-default">
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
            <li><a href="#/">INICIO</a></li>
            <li><a href="#create">CREAR</a></li>
            <li><a href="#configure">CONFIGURAR</a></li>
            <li><a data-target="#logoutDialog" data-toggle="modal">SALIR</a></li>
        </ul>
    </div>
</nav>

<div ng-view>
</div>


<div class="modal modal-default fade" id="logoutDialog" role="dialog">
    <div class="modal-dialog" style="max-width: 300px;">
    	<div class="modal-content">
        	<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal">&times;</button>
          		<h4 class="modal-title">Confirmar Cierre de Sesión</h4>
        	</div>

        	<div class="modal-body table-responsive">
				<h5>¿Desea Cerrar Sesión?</h5>
			</div>

	        <div class="modal-footer">
	          	<a type="button" class="btn btn-primary" href="configuration/logout.php">Aceptar</a>
	          	<a type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</a>
	        </div>
     	</div>
    </div>
</div>



<script type="text/javascript" src="complements/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="complements/bootstrap/bootstrap.js"></script>

<script type="text/javascript" src="complements/angular/angular.min.js"></script>
<script type="text/javascript" src="complements/angular/angular-route.min.js"></script>
<script type="text/javascript">
	var app = angular.module('applink', ['ngRoute']);

	app.config(function($routeProvider, $locationProvider) {
		$routeProvider.when('/', {
			templateUrl: 'views/autolink.index.html',
			title: 'AutoLink RESTful'

		}).when('/create',{
			templateUrl: 'views/autolink.new.html',
			title: 'Create AutoLink'

		}).when('/configure',{
			templateUrl: 'views/autolink.config.html',
			title: 'Configure AutoLink'

		}).otherwise({
			redirectTo: '/'
		});

		//$locationProvider.html5Mode(true);
		//$locationProvider.html5Mode(true).hashPrefix('/');
		//$locationProvider.html5Mode.enabled = true;
		//$locationProvider.html5Mode.rewriteLinks = true;
		//$locationProvider.html5Mode.requireBase = true;
	});

	app.run(function($rootScope, $route, $location){
		$rootScope.$on('$routeChangeStart', function(scope, next, current) {
		});

		$rootScope.$on('$routeChangeSuccess', function() {
			if($route.current.title!='' && $route.current.title!=undefined){
				document.title = $route.current.title;
			}
		});
	});

	app.controller('ctrlinicio', function($scope){

	});
</script>

</body>
</html>