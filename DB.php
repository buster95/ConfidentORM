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

	public static function cifrar($cadena, $llave){
		$result = '';
		for($i=0; $i<strlen($cadena); $i++) {
			$char = substr($cadena, $i, 1);
			$keychar = substr($llave, ($i % strlen($llave))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		return base64_encode($result);
	}

	public static function descifrar($cadena, $llave){
		$result = '';
		$cadena = base64_decode($cadena);
		for($i=0; $i<strlen($cadena); $i++) {
			$char = substr($cadena, $i, 1);
			$keychar = substr($llave, ($i % strlen($llave))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}






	/**
	 * ENCRIPTADO DE TEXTO CON LLAVE
	 * @param  String $cadena        TEXTO A ENCRIPTAR
	 * @param  String $llave_cifrado LLAVE DE CIFRADO
	 * @return String                TEXTO CIFRADO
	 */
	public function encriptar($cadena, $llave_cifrado){
		$encrypted = mcrypt_ecb( MCRYPT_DES, $llave_cifrado, $cadena, MCRYPT_ENCRYPT );
		return $encrypted;
	}


	// $algorithm = MCRYPT_BLOWFISH;
	// $key = 'That golden key that opens the palace of eternity.';
	// $data = 'The chicken escapes at dawn. Send help with Mr. Blue.';
	// $mode = MCRYPT_MODE_CBC;

	// $iv = mcrypt_create_iv(mcrypt_get_iv_size($algorithm, $mode), MCRYPT_DEV_URANDOM);

	// $encrypted_data = mcrypt_encrypt($algorithm, $key, $data, $mode, $iv);
	// $plain_text = base64_encode($encrypted_data);
	// echo $plain_text . "\n";

	// $encrypted_data = base64_decode($plain_text);
	// $decoded = mcrypt_decrypt($algorithm, $key, $encrypted_data, $mode, $iv);
	// echo $decoded . "\n";


	/**
	 * DESENCRIPTADO DE DATOS CON LLAVE
	 * @param  String $cadena           TEXTO A DESCIFRAR
	 * @param  String $llave_descifrado LLAVE DE DESENCRIPTADO
	 * @return String                   TEXTO DESENCRIPTADO
	 */
	public function desencriptar($cadena, $llave_descifrado){
		$decrypted = mcrypt_ecb( MCRYPT_DES, $llave_descifrado, $cadena, MCRYPT_DECRYPT );
		return $decrypted;
	}
}

?>