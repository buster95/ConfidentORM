<?php

require_once '../DB.php';

echo DB::table('usuarios')->findJSON(1);
//echo (MyDB::table('usuarios')->select('concat(APELLIDO,NOMBRE)')->getSQL());
echo "<br>";
echo "<br>";

echo DB::table('usuarios')->selectAs('concat(APELLIDO,\' \',nombre)','fullname')
->select('id_usuario')->findJSON(2);
echo "<br>";
?>