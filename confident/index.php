<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Automatic RESTful</title>

	<link rel="stylesheet" href="bootstrap/bootstrap.css">}
	<style>
		body{
			background-color: black;
		}
		#formlogin{
			max-width:400px;
			margin-top:40px;
			//background-color: red;
			padding: 20px 30px;

			border-radius: 6px;
		}
	</style>
</head>
<body>


<div class="container">
	<div class="row">

		<form action="config/login.php" method="POST" id="formlogin" class="bg-danger center-block">
			<h3 align="center" style="margin-top: 0px;">Login AutoLink</h3>
			<div class="form-group">
				<label for="usuario">Usuario</label>
				<input type="text" name="usuario" id="usuario" class="form-control" required>
			</div>

			<div class="form-group">
				<label for="clave">Contraseña</label>
				<input type="password" name="clave" id="clave" class="form-control" required>
			</div>

			<button type="submit" class="btn btn-primary">Entrar</button>
		</form>

	</div>
</div>

<script type="text/javascript" src="bootstrap/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="bootstrap/bootstrap.js"></script>

</body>
</html>