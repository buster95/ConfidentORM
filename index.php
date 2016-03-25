<?php

require_once 'DB.php';

echo DB::query('select * from usuarios')->getJSON();

?>