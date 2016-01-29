<?php

require_once '/../generador/DB.php';

echo DB::table('estudiantes')->KEY();
echo "<br>";

//var_dump(MyDB::table('estudiantes')->columna_existe('nombre'));
//echo "<br>";

echo DB::table('usuarios')->findJSON(3);
//echo (MyDB::table('usuarios')->select('concat(APELLIDO,NOMBRE)')->getSQL());
echo "<br>";
echo "<br>";

echo DB::table('usuarios')->selectAs('concat(APELLIDO,\' \',nombre)','fullname')
->select('id_usuario')->getSQL();
echo "<br>";
?>