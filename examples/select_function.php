<?php

require_once '../DB.php';

echo DB::table('usuarios')->select('nombre, apellido')->getJSON();
echo '<br>';
echo '<br>';

echo DB::table('usuarios')->selectAs('concat(nombre,\' \',apellido)','fullname')->getJSON();
echo '<br>';
?>