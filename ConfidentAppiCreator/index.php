<!DOCTYPE html>
<html lang="es">
<?php

if(!isset($_SESSION)){
	session_start();
}

if(isset($_SESSION['autolinklogin'])){
	if($_SESSION['autolinklogin']==true){
		header("Location: autolink.php");
	}
}else{
	session_destroy();
	if(isset($_POST['usuario']) && isset($_POST['clave'])){
		require_once 'configuration/ini.php';

		$username = ini::getValue('login','usuario');
		$password = ini::getValue('login','clave');

		if ($_POST['usuario']==$username && $_POST['clave']==$password) {
			session_start();
			$_SESSION['autolinklogin'] = true;
			header("Location: autolink.php");
		}else{
			$error = true;
		}
	}
}

?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Automatic RESTful</title>

	<link rel="stylesheet" href="complements/bootstrap/bootstrap.css">
	<style>
		body{
			background-color: black;
		}

		.panel{
			border: none;
			max-width: 400px;
			margin: 0 auto;
		}
		.panel .panel-heading{
			background-color: #3D9970 !important;
		}
		.panel .panel-heading h3{
			margin-top: 5px;
			margin-bottom: 5px;
		}
		.panel .panel-body{
			background-color: #EAEAEC;
			padding: 15px 25px;
		}
		.panel .panel-footer{
			background-color: white;
		}
		.panel .panel-footer .btn{
			background-color: #3D9970;
			color: white;
		}
		.panel .panel-footer .btn:hover{
			background-color: #289465;
		}
	</style>
</head>
<body>


<div class="container">
	<div class="row">

		<form action="index.php" method="POST" style="margin-top:60px;" class="center-block">
			<div class="panel panel-success">

				<div class="panel-heading">
					<h3 align="center" style="color:white;">Login AutoLink Confident</h3>
				</div>

				<div class="panel-body">
						<div class="form-group">
							<label for="usuario">Usuario</label>
							<input type="text" name="usuario" id="usuario" placeholder="username" class="form-control" required>
						</div>

						<div class="form-group" style="margin-bottom: 0px;">
							<label for="clave">Contraseña</label>
							<input type="password" name="clave" id="clave" placeholder="password" class="form-control" required>
						</div>

				</div>

				<div class="panel-footer">
					<button type="submit" class="btn btn-block">Entrar</button>
				</div>

			</div>
		</form>

	</div>
</div>

<script type="text/javascript" src="complements/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="complements/bootstrap/bootstrap.js"></script>

<?php
if (isset($error) && $error == true) {
	require_once 'complements/php/notify.php';
	$notify = new notify();
	$notify->error('Acceso Denegado','Credenciales Incorrectas');
}
?>

</body>
</html>