<?php

require_once 'Generador/MyDB.php';

echo MyDB::tabla('usuarios')->select('usuario, apellido')->where('usuario','walter2015')->where('id_usuario',2)->getSQL();

echo "<br>";
echo "<br>";
$usuarios = MyDB::tabla('usuarios')->select('nombre, apellido')->getJSON();
echo "<br>";
echo "<br>";
echo $usuarios;

var_dump(MyDB::tabla('usuarios')->find(2));

// foreach ($usuarios as $user) {
// 	echo $user->nombre.' '.$user->apellido;
// 	echo "<br>";
// }

?>