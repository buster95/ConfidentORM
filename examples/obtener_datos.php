<?php

echo "Bienvenidos a la presentacion de este ORM";
echo '<br>';

require_once '/../DB.php';
echo "<br>";

$usuarios =  DB::table('usuarios')->get();

foreach ($usuarios as $user) {
	echo $user->nombre." ".$user->apellido;
	echo "<br>";
}

?>