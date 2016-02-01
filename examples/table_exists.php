<?php

require_once '../DB.php';

echo DB::table('usuarios')->getJSON();

?>