<?php

echo "Bienvenidos a la presentacion de este ORM";

require_once '/../generador/DB.php';
echo "<br>";

$usuarios =  DB::table('usuarios')->get();

foreach ($usuarios as $user) {
	echo $user->nombre." ".$user->apellido;
	echo "<br>";
}

?>