<?php
	require_once '/../dist/DB.php';

	echo DB::cifrar("Walter","12345");
	echo '<br>';
	echo DB::descifrar("jJKep5mn","12345");
?>