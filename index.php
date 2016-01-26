<?php

require_once 'Generador/MyDB.php';

echo MyDB::table('estudiantes')->KEY();
echo "<br>";

//var_dump(MyDB::table('estudiantes')->columna_existe('nombre'));
//echo "<br>";

echo MyDB::table('usuarios')->getJSON();
//echo (MyDB::table('usuarios')->select('concat(APELLIDO,NOMBRE)')->getSQL());
echo "<br>";

echo MyDB::table('usuarios')->selectAs('concat(APELLIDO,\' \',nombre)','fullname')
->select('id_usuario')->getSQL();
echo "<br>";
?>