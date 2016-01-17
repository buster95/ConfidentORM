<?php

require_once 'Generador/MyDB.php';

echo MyDB::tabla('usuarios')->select('usuario, apellido')->where('usuario','walter2015')->getSQL();
echo "<br>";
echo "<br>";
$usuarios = MyDB::tabla('usuarios')->select('nombre, apellido')->get();
echo "<br>";
echo "<br>";
foreach ($usuarios as $user) {
	echo $user->nombre.' '.$user->apellido;
	echo "<br>";
}

var_dump(MyDB::tabla('usuarios')->find(2));

?>