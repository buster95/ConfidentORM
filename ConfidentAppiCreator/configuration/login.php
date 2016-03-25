<?php

if(isset($_POST['usuario']) && isset($_POST['clave'])){
	require_once 'ini.php';

	$user = ini::getValue('login','usuario');
	$pass = ini::getValue('login','clave');

	if ($_POST['usuario']==$user && $_POST['clave']==$pass) {
		echo "aceptado";
		session_start();
		$_SESSION['autolinklogin']=true;
		header("Location: ../autolink.php");
	}else{
		echo "no aceptado";
		header("Location: ../index.php");
	}
}else{
	echo "no aceptado";
	header("Location: ../index.php");
}

?>