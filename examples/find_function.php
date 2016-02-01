<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Busqueda por ID</title>

	<link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>

	<div class="container" style="margin-top:25px;">
		<div class="row">
			<div class="col-md-12">

			</div>
		</div>
		<div class="row">


			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Busqueda Simple
					</div>
					<div class="panel-body">
						<codigo>
							<code>
								require_once '../DB.php'; <br>
								echo DB::table('usuarios')->findJSON(1);
							</code>

							<code-result>
								<h4>Resultado JSON</h4>
								<?php
									require_once '../DB.php';
									echo DB::table('usuarios')->findJSON(1)
								?>
							</code-result>
						</codigo>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Busqueda con sentencia Select
					</div>
					<div class="panel-body">
						<codigo>
							<code class="code-line">
								require_once '../DB.php'; <br>
								echo DB::table('usuarios')->selectAs('concat(APELLIDO,' ',nombre)','fullname')->select('id_usuario')->findJSON(2);
							</code>

							<code-result>
								<h4>Resultado Obtenido en JSON</h4>
								<?php
								echo DB::table('usuarios')->selectAs('concat(APELLIDO,\' \',nombre)','fullname')
								->select('id_usuario')->findJSON(2);
								?>
							</code-result>
						</codigo>
					</div>
				</div>
			</div>

		</div>
	</div>

</body>
</html>