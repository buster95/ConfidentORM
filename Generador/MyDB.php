<?php
require_once 'Table.php';
/**
* GENERADOR DE CONSULTAS PARA MYSQL
*/
class MyDB{
	// VARIABLES DE CONFIGURACION
	private $user='root'; // Usuario de la BASE DE DATOS
	private $password=''; // Password del usuario de la BASE DE DATOS
	private $database='prueba'; // nombre de la BASE DE DATOS
	private $host='127.0.0.1'; // IPv4 o host de conexion
	private $port='3306'; // puerto de conexion

	public static function tabla($nombre_tabla) {
		$tabla = new Table($nombre_tabla);
		return $tabla;
	}

	/**
	 * [Function] db_key
	 * @param  String Nombre de la Tabla
	 * @return String PrimaryKey Tabla
	 */
	public function db_key($table_name){
		$consulta="SELECT t2.`COLUMN_NAME` as columna
		FROM `information_schema`.`TABLE_CONSTRAINTS` t1
		JOIN `information_schema`.`KEY_COLUMN_USAGE` t2
		USING (`CONSTRAINT_NAME`, `TABLE_SCHEMA`, `TABLE_NAME`)
		WHERE t1.`CONSTRAINT_TYPE` = 'PRIMARY KEY'
		AND t1.`TABLE_SCHEMA` = '".$this->database."'
		AND t1.`TABLE_NAME` = '".$table_name."'";
		$resultados = $this->consultar($consulta);
		$fila = $resultados->fetch_object();
		return $fila->columna;
	}

	/**
	 * @return mysqli_connect Conexion MySQL
	 */
	public function conectar() {
		$con = new mysqli($this->getHOST(), $this->user, $this->password, $this->database);
		if($con->connect_error){
			trigger_error('Database connection failed: '.$con->connect_error, E_USER_ERROR);
		}else{
			return $con;
		}
	}

	public function consultar($consulta){
		$conx = $this->conectar();
		$resultado = $conx->query($consulta);
		return $resultado;
	}

	public function jsonrow($fila){
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

	public function jsondata($resultados){
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

	public function listar($resultados)	{
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