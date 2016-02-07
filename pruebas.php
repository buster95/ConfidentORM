<?php

require_once 'DB.php';

echo DB::table('usuarios')->findJSON(1);
echo '<br>';
echo DB::table('usuarios')->findJSON(2);
echo '<br>';
echo DB::table('usuarios')->where('nombre','walter*')->getJSON();

?>