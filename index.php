<?php

require_once 'MyGen/MyDB.php';

echo MyDB::tabla('usuarios')->select('nombre, apellido')->getSQL();

$usuarios = MyDB::tabla('usuarios')->select('nombre, apellido')->get();
var_dump($usuarios);
// foreach ($usuarios as $user) {
// 	echo $user->nombre;
// 	echo $user->apellido;

// 	echo "<br>";
// }






?>