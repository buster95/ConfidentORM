<?php

require_once '../DB.php';

echo DB::table('usuarios')->getJSON();

echo DB::table('usuarios')->order('id_usuario')->getSQL();

?>