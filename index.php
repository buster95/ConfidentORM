<?php

require_once 'MyGen/MyDB.php';

echo MyDB::tabla('usuarios')->select('nombre, apellido')->getSQL();

echo "<br>";
echo "<br>";
$usuarios = MyDB::tabla('usuarios')->select('nombre, apellido')->getJSON();
echo "<br>";
echo "<br>";
echo $usuarios;

// foreach ($usuarios as $user) {
// 	echo $user->nombre.' '.$user->apellido;
// 	echo "<br>";
// }

?>