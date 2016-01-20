<?php

echo "Bienvenidos a la presentacion de este ORM";

require_once 'Generador/MyDB.php';
echo "<br>";

$usuarios =  MyDB::tabla('usuarios')->get();

foreach ($usuarios as $user) {
	echo $user->nombre." ";
	echo $user->apellido;
	echo "<br>";
}

?>