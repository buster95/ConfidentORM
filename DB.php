<?php
require_once 'Table.php';
/**
* GENERADOR DE CONSULTAS PARA MYSQL
*/
class DB{
	// VARIABLES DE CONFIGURACION
	private $user='root'; // USUARIO de la BASE DE DATOS
	private $password=''; // PASSWORD del usuario de la BASE DE DATOS
	private $database='prueba'; // BASE DE DATOS
	private $host='127.0.0.1'; // IPv4 o HOST de conexion
	private $port='3306'; // puerto de conexion DEFAULT:3306

	public static function table($nombre_tabla) {
		$tabla = new Table($nombre_tabla);
		return $tabla;
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

	private function getHOST(){
		if($this->port==''){
			return $this->host;
		}else{
			return $this->host.':'.$this->port;
		}
	}

	public function consultar($consulta){
		$conx = $this->conectar();
		$resultado = $conx->query($consulta);
		return $resultado;
	}

	public function ejecutar($consultar){
		$conx = $this->conectar();
		$conx->query($consulta);
		return $conx->affected_rows;
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

	public function count_rows($resultado){
		return mysqli_num_rows($resultado);
	}
}

?>