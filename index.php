<?php

require_once 'Generador/MyDB.php';

// echo MyDB::tabla('usuarios')->select('usuario, apellido')->where('usuario','walter2015')->whereOr('nombre','walter')->getSQL();
// echo "<br>";
// echo "<br>";
// $usuarios = MyDB::tabla('usuarios')->select('nombre, apellido')->get();
// foreach ($usuarios as $user) {
// 	echo $user->nombre.' '.$user->apellido;
// 	echo "<br>";
// }

// var_dump(MyDB::tabla('usuarios')->find(2));
// var_dump(MyDB::tabla('estudiantes')->find(1));
// var_dump(MyDB::tabla('estudiantes')->find(2));

echo MyDB::tabla('usuarios')->order('id_usuario')->order('nombre')->group('id_usuario')->limit(10)->getSQL();

?>