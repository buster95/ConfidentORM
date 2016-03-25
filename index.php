<?php

require_once 'dist/DB.php';

echo DB::query('select * from usuarios')->getJSON();

?>