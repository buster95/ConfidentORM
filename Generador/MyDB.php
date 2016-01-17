<?php
require_once 'Table.php';
/**
* GENERADOR DE CONSULTAS PARA MYSQL
*/
class MyDB{
	// VARIABLES DE CONFIGURACION
	private $user='root';
	private $password='';
	private $database='prueba';
	private $host='127.0.0.1';
	private $port='3306';
	// VARIABLES DE CONFIGURACION

	public static function tabla($nombre_tabla) {
		$tabla = new Table($nombre_tabla);
		return $tabla;
	}

	public function db_key($table_name){
		$consulta="SELECT t2.`COLUMN_NAME` as columna
		FROM `information_schema`.`TABLE_CONSTRAINTS` t1
		JOIN `information_schema`.`KEY_COLUMN_USAGE` t2
		USING (`CONSTRAINT_NAME`, `TABLE_SCHEMA`, `TABLE_NAME`)
		WHERE t1.`CONSTRAINT_TYPE` = 'PRIMARY KEY'
		AND t1.`TABLE_SCHEMA` = '".$this->database."'
		AND t1.`TABLE_NAME` = '".$table_name."'";
		$resultados = MyDB::consultar($consulta);
		$fila = $resultados->fetch_object();
		return $fila->columna;
	}

	public function conectar() {
		$con = new mysqli($this->getHOST(), $this->user, $this->password, $this->database);
		if($con->connect_error){
			trigger_error('Database connection failed: '.$con->connect_error, E_USER_ERROR);
		}else{
			return $con;
		}
	}

	public static function consultar($consulta){
		$clase = new MyDB();
		$conx = $clase->conectar();
		$resultado = $conx->query($consulta);
		return $resultado;
	}

	public static function jsonrow($fila){
		foreach ($fila as $key => $valor) {
			if(is_string($valor)){
				$acentos = array('é','í','ó','ú','á','ñ');
				foreach ($acentos as $llave => $acento) {
					if(strpos($valor, $acento)){
						$fila->$key = utf8_decode($valor);
						break;
					}
				}
			}
		}
		$json = json_encode($fila, JSON_NUMERIC_CHECK);
		if($json!=false){
			return $json;
		}else{
			return "{}";
		}
	}

	public static function jsondata($resultados){
		$filas = array();

		while ($row = $resultados->fetch_array(MYSQLI_ASSOC)){
			foreach ($row as $key => $valor) {
				if(is_string($valor)){
					$acentos = array('é','í','ó','ú','á','ñ');
					foreach ($acentos as $llave => $acento) {
						if(strpos($valor, $acento)){
							$row[$key] = utf8_decode($valor);
							break;
						}
					}
				}
			}
			$filas[] = array_map('utf8_encode', $row);
		}
		$json = json_encode($filas, JSON_NUMERIC_CHECK);

		if($json!=false){
			return $json;
		}else{
			return "[]";
		}
	}

	public static function listar($resultados)	{
		$datos = array();
		while ($fila = $resultados->fetch_object()) {
			$datos[] = $fila;
		}
		return $datos;
	}

	private function getHOST(){
		if($this->port==''){
			return $this->host;
		}else{
			return $this->host.':'.$this->port;
		}
	}
}

?>