<?php

require_once 'Generador/MyDB.php';

echo MyDB::table('estudiantes')
->where('id_estudiante',2)
->getJSON();

echo "<br>";

echo MyDB::table('usuarios')
->select('id_usuario,nombre,apellido,usuario')
->findJSON(1);

echo "<br>";
echo "<br>";

// $usuario->nombre='Walter Ramon';
// $usuario->apellido='Corrales Diaz';
// $usuario->fecha='current_date()';
// $usuario->estado='1';

$usuario1['nombre']='Walter Ramon';
$usuario1['apellido']='Corrales Diaz';
$usuario1['fecha']='current_date()';
$usuario1['estado']='1';

echo MyDB::table('usuarios')->save($usuario1);
?>