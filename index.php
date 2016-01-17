<?php

require_once 'Generador/MyDB.php';

echo MyDB::tabla('usuarios')->where('usuario','walter2015')->where('id_usuario',2)->getSQL();

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