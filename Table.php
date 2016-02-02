<?php
require_once 'DB.php';

define('DESC',"DESC",true);
define('ASC',"ASC",true);

// CLASE TABLA PARA HACER CONSULTAS
class Table {
	// PARAMETROS DE LA TABLA
	private $tabla='';
	private $select='*';
	private $where='';
	private $order='';
	private $group='';
	private $limit='';

	private $inner='';
	private $left_join='';
	private $right_join='';
	private $cross='';
	private $on='';

	private $mydb;
	/**
	 * CONSTRUCTOR DE LA CLASE
	 * @param String $nombre1 Nombre De La Tabla en la DataBase
	 */
	function __construct($nombre1) {
		$this->mydb = new DB();
		$this->TABLE_EXISTS($nombre1);

		$this->tabla = strtoupper($nombre1);
		return $this;
	}

	/**
	 * RETORNA BOOLEAN SI EXISTE EL NOMBRE DE UNA TABLA
	 * @param Boolean $table_name NOMBRE DE LA TABLA
	 */
	private function TABLE_EXISTS($table_name){
		$consulta = 'SHOW TABLES;';
		$tablas = $this->mydb->consultar($consulta);
		while($tabla = $tablas->fetch_array(MYSQLI_NUM)){
			if(strtolower($table_name) == strtolower($tabla[0])){
				return true;
			}
		}
		throw new Exception("TABLA NO EXISTE => ".$table_name, 1);
	}

	/**
	 * LLAVE PRIMARIA DE LA TABLA
	 * @return String LLAVE PRIMARIA
	 */
	public function KEY(){
		$llaves = $this->mydb->consultar('SHOW KEYS FROM '.$this->tabla);
		while ($fila = $llaves->fetch_object()) {
			if($fila->Key_name=='PRIMARY'){
				return $fila->Column_name;
			}
		}
		return '';
	}

	/**
	 * INFORMACION CAMPOS TABLA
	 * @return Array Informacion Campos
	 */
	public function DESCRIBE(){
		$columnas = $this->mydb->consultar('DESCRIBE '.$this->tabla);
		$campos = array();
		while ($fila = $columnas->fetch_array(MYSQLI_ASSOC)) {
			$campos[] = $fila;
		}
		return $campos;
	}

	/**
	 * COLUMNAS
	 * @return String CAMPOS DE LA TABLA
	 */
	public function COLUMNAS(){
		$columnas = $this->mydb->consultar('DESCRIBE '.$this->tabla);
		$campos = array();
		while ($fila = $columnas->fetch_array(MYSQLI_ASSOC)) {
			$campos[] = $fila['Field'];
		}
		return $campos;
	}

	/**
	 * RETORNA EL TIPO DE DATO DE UNA COLUMNA
	 * RETURN NUMBER OR RETURN STRING OR BOOLEAN
	 * @param String $columna NOMBRE DE LA COLUMNA
	 */
	public function COLUMN_TYPE($columna){
		$columnas = $this->mydb->consultar('SHOW COLUMNS FROM '.$this->tabla." WHERE Field='".$columna."'");
		$number = array('int','float','double');
		$string = array('varchar');
		while ($fila = $columnas->fetch_array(MYSQLI_ASSOC)) {
			$campos[] = $fila['Field'];
		}
		return $campos;
	}

	public function IS_OPERADOR($operador){
		$operadores = array('=','>','<','>=','<=','===');
		foreach ($operadores as $key) {
			if($operador==$key){
				return true;
			}
		}
		return false;
	}

	/**
	 * LIMPIA UN PARAMETRO DE FUNCIONES Y CARACTERES
	 * @param  String $string1 PARAMETRO A VERIFICAR
	 * @return String          PARAMETRO LIMPIADO
	 */
	private function SELECT_CLEAN_PARAMS($string1){
		$verificador = strtolower($string1);
		$verificador = str_replace(array('concat(','sum(','avg(','count(','max(','min(','(',')',' '), '', $verificador);
		$verificador = str_replace(array('+','-','*','/'), ',', $verificador);
		return $verificador;
	}

	public function COLUMNS_EXISTS($columnas){
		if($columnas==''){ return true; }

		if(is_string($columnas)){
			$columnas = $this->SELECT_CLEAN_PARAMS($columnas);
		}
		if(strpos($columnas,',')>-1){
			$columnas = explode(',', $columnas);
		}
		if(is_array($columnas)){
			foreach ($columnas as $valor) {
				$this->COLUMN_EXISTS($valor);
			}
			return true;
		}
	}
	/**
	 * COMPRUEBA SI EL NOMBRE DE UNA COLUMNA EXISTE
	 * @param  String $columns NOMBRE DE LA COLUMNA
	 * @return Boolean     TRUE (if exist) or FALSE (if not exist)
	 */
	public function COLUMN_EXISTS($columna=''){
		$column_name = $columna;
		if($column_name==''){ return true; }

		if(is_string($column_name)){
			$column_name = $this->SELECT_CLEAN_PARAMS($column_name);
		}

		if (strpos($column_name, '\'')===0) {
			$is_a_text = $column_name;
			$contador = 0;
			for ($i=0; $i < strlen($column_name); $i++) {
				if (strpos($is_a_text, '\'')>-1) {
					$posicion = strpos($is_a_text, '\'');
					$is_a_text = substr($is_a_text, $posicion+1);
					$contador++;
				}
			}
			if(is_numeric($contador) && $contador>0 && $contador%2==0){
				return true;
			}else {
				throw new Exception("SQL QUERY CON => ( ".$column_name." ) APOSTROFE DE MAS", 2);
			}

		}else if (strpos($column_name, '\'') > 0) {
			throw new Exception("NO ES UN NOMBRE => ( ".$column_name." ) PARA UNA COLUMNA", 3);
		}

		$columnas = $this->mydb->consultar('DESCRIBE '.$this->tabla);
		while ($fila = $columnas->fetch_array(MYSQLI_ASSOC)) {
			if(strtolower($fila['Field']) == strtolower($column_name)){
				return true;
			}
		}
		throw new Exception("COLUMNA => ( ".$column_name." ) NO EXISTE", 1);
	}

	/**
	 * AGREGA TABLENAME A CADA PARAMETRO
	 * @param  String $string1 Parametro de Entrada
	 * @return String          Parametro Filtrado
	 */
	private function selectReplace($string1){
		$string1 = strtolower($string1);
		$string1 = strtoupper($this->tabla).'.'.$string1;
		$string1 = str_replace('(', '('.$this->tabla.'.', $string1);
		$string1 = str_replace(',', ','.$this->tabla.'.', $string1);
		$string1 = str_replace('+', '+'.$this->tabla.'.', $string1);
		$string1 = str_replace('-', '-'.$this->tabla.'.', $string1);
		$string1 = str_replace('*', '*'.$this->tabla.'.', $string1);
		$string1 = str_replace('/', '/'.$this->tabla.'.', $string1);

		$string1 = str_replace(array(' ,',', '), ',', $string1);
		$string1 = str_replace($this->tabla.'.\'', '\'', $string1);
		$string1 = str_replace($this->tabla.'.sum(', 'SUM(', $string1);
		$string1 = str_replace($this->tabla.'.avg(', 'AVG(', $string1);
		$string1 = str_replace($this->tabla.'.min(', 'MIN(', $string1);
		$string1 = str_replace($this->tabla.'.min(', 'MIN(', $string1);
		$string1 = str_replace($this->tabla.'.concat(', 'CONCAT(', $string1);
		$string1 = str_replace($this->tabla.'.count(', 'COUNT(', $string1);
		return $string1;
	}
	/**
	 * SELECT CLAUSE EN EL SQL QUERY
	 * @param  String $parametro1 NOMBRE DEL PARAMETRO A SELECCIONAR
	 * @return Table             RETURN CURRENT CLASS
	 */
	public function select($parametro1) {
		if(is_string($parametro1)){
			$this->COLUMNS_EXISTS($parametro1);
		}else{
			return $this;
		}

		if ((strpos($parametro1,'(')>-1 &&
				!strpos($parametro1,')')>-1) ||
			(!strpos($parametro1,'(')>-1 &&
				strpos($parametro1,')')>-1) ||
			(strpos($parametro1,'(')>strpos($parametro1,')')) ||
			(strpos($parametro1,'(')==false && strpos($parametro1,')')>-1)) {
			throw new Exception("NO ES UN NOMBRE => ( ".$parametro1." ) PARA UNA COLUMNA", 3);
		}

		if($this->select=='*'){
			if(is_string($parametro1)){
				$parametro1 = $this->selectReplace($parametro1);
				$this->select = $parametro1;
			}
		}else{
			if(is_string($parametro1)){
				$parametro1 = $this->selectReplace($parametro1);
				$this->select .= ','.$parametro1;
			}
		}
		return $this;
	}
	/**
	 * SELECT (PARAM AS ALIAS) CLAUSE QUERY
	 * @param  String $parametro1 NOMBRE DEL PARAMETRO
	 * @param  String $asname     ALIAS PARAMETRO
	 * @return Table             RETURN CURRENT CLASS
	 */
	public function selectAs($parametro1, $asname){
		if(is_string($parametro1)){
			$this->COLUMNS_EXISTS($parametro1);
		}else{
			return $this;
		}

		if ((strpos($parametro1,'(')>-1 &&
				!strpos($parametro1,')')>-1) ||
			(!strpos($parametro1,'(')>-1 &&
				strpos($parametro1,')')>-1) ||
			(strpos($parametro1,'(')>strpos($parametro1,')')) ||
			(strpos($parametro1,'(')==false && strpos($parametro1,')')>-1)) {
			throw new Exception("NO ES UN NOMBRE => ( ".$parametro1." ) PARA UNA COLUMNA", 3);
		}

		if ($this->select=='*') {
			if(is_string($parametro1) && is_string($asname)){
				$parametro1 = $this->selectReplace($parametro1);
				$this->select = $parametro1.' AS '.$asname;
			}
		}else{
			if(is_string($parametro1) && is_string($asname)){
				$parametro1 = $this->selectReplace($parametro1);
				$this->select .= ','.$parametro1.' AS '.$asname;
			}
		}
		return $this;
	}







	/**
	 * QUITA CARACTERES ESPECIALES DE UNA CADENA
	 * @param  String $cadena CADENA A LIMPIAR
	 * @return String         CADENA LIMPIADA
	 */
	private function SQL_CLEAN($cadena)	{
		$caracteres = array('\'','"','=','!',
			'<','>','¿','?','¡','$','\\','{',
			'}','[',']','#','&','(',')',
			'+','-',' ');
		$filtrada = str_replace($caracteres, '', $cadena);
		$filtrada = str_replace(array('%','*'), '%', $cadena);
		return $filtrada;
	}
	/**
	 * WHERE CLAUSE SQL QUERY
	 * @param  String $parametro PARAMETRO_NOMBRE
	 * @param  String $operador  OPERADOR O VALOR
	 * @param  String $valor     VALOR_PARAMETRO
	 * @return Table            RETURN CURRENT CLASS
	 */
	public function where($parametro, $operador, $valor=''){
		if($valor=='' && $operador!=''){
			$valor= $operador;
			$operador='';
		}

		if($this->where==''){
			$this->where = $parametro;
		}else{
			$this->where .= ' AND '.$parametro;
		}

		$valor = $this->SQL_CLEAN($valor);
		if(is_numeric($valor)){
			if($operador!=''){
				$this->where .= $operador.$valor;
			}else{
				$this->where .= '='.$valor;
			}
		}else if(is_string($valor)){
			$this->where .= " LIKE '".$valor."'";
		}
		return $this;
	}

	/**
	 * WHERE CLAUSE WITH OR
	 * @param  String $parametro NOMBRE DEL PARAMETRO
	 * @param  String $operador  OPERADOR O VALOR
	 * @param  String $valor     VALOR DEL PARAMETRO
	 * @return Table            RETURN CURRENT CLASS
	 */
	public function whereOr($parametro, $operador, $valor=''){
		if($valor=='' && $operador!=''){
			$valor=$operador;
			$operador='';
		}

		if($this->where==''){
			$this->where = $parametro;
		}else{
			$this->where .= ' OR '.$parametro;
		}

		$valor = $this->SQL_CLEAN($valor);
		if(is_numeric($valor)){
			if($operador!=''){
				$this->where .= $operador.$valor;
			}else{
				$this->where .= '='.$valor;
			}
		}else if(is_string($valor)){
			$this->where .= " LIKE '".$valor."'";
		}
		return $this;
	}








	/**
	 * INNER JOIN CLAUSE SQL
	 * @param  Table  $table         [description]
	 * @param  [type] $table_param   [description]
	 * @param  [type] $current_param [description]
	 * @return [type]                [description]
	 */
	public function inner(Table $table, $table_param, $current_param){
	}

	/**
	 * ORDER BY CLAUSE SQL QUERY
	 * @param  String $parametro PARAMETRO_ORDEN
	 * @param  String $tipo      ORDEN TYPE ASC_DESC
	 * @return Table            RETURN CURRENT CLASS
	 */
	public function order($parametro, $tipo=''){
		if($this->order==''){
			if ($tipo!='') {
				$this->order = $parametro.' '.$tipo;
			}else{
				$this->order = $parametro;
			}
		}else{
			if ($tipo!='') {
				$this->order .= ', '.$parametro.' '.$tipo;
			}else{
				$this->order .= ', '.$parametro;
			}
		}
		return $this;
	}

	/**
	 * GROUP BY CLAUSE SQL QUERY
	 * @param  String $parametro PARAMETRO_GROUP
	 * @return Table            RETURN CURRENT CLASS
	 */
	public function group($parametro=''){
		if ($parametro!='') {
			if($this->group==''){
				$this->group = $parametro;
			}else{
				$this->group .= ', '.$parametro;
			}
		}
		return $this;
	}

	/**
	 * LIMIT CLAUSE SQL QUERY
	 * @param  integer $cantidad CANTIDAD DE DATOS
	 * @return Table            RETURN CURRENT CLASS
	 */
	public function limit($cantidad=1000){
		if(is_numeric($cantidad)){
			$this->limit=$cantidad;
		}
		return $this;
	}






	/**
	 * AUMENTAR UN PARAMETRO NUMERICO
	 * @param  String $parametro NombreDelAtributo
	 * @return Boolean true or false
	 */
	public function aumentar($parametro){
	}

	/**
	 * DISMINUIR UN PARAMETRO NUMERICO
	 * @param  String $parametro NombreDelParametro
	 * @return Boolean	true or false
	 */
	public function disminuir($parametro){
	}

	/**
	 * MAXIMO VALOR DE UN ATRIBUTO NUMERICO
	 * @param  String $parametro NombreDelAtributo
	 * @return Int ValorMaximo
	 */
	public function max($parametro){
	}

	/**
	 * VALOR MINIMO DE UN ATRIBUTO NUMERICO
	 * @param  String $parametro NombreDelAtributo
	 * @return Int ValorMinimo
	 */
	public function min($parametro){
	}

	/**
	 * VALOR PROMEDIO DE UN ATRIBUTO NUMERICO
	 * @param  String $parametro NombreDelAtributo
	 * @return Int ValorPromedio
	 */
	public function avg($parametro){
	}

	/**
	 * SUMATORIA DE UN ATRIBUTO NUMERICO
	 * @param  String $parametro NombreDelAtributo
	 * @return Int ValorSumatoria
	 */
	public function sum($parametro){
	}

	/**
	 * CANTIDAD DE VALORES REGISTRADOS
	 * @return Int CantidadDeValores
	 */
	public function count(){
	}








	/**
	 * OBTENER LA CONSULTA DE UNA BUSQUEDA POR ID
	 * @param  Int $id ID_REGISTRO
	 * @return String ConsultaDeObtencion
	 */
	public function findSQL($id){
		if(is_numeric($id)){
			$consulta = "SELECT ".$this->select." FROM ".$this->tabla." WHERE ".$this->KEY()."=".$id;
			return $consulta;
		}else{
			throw new Exception("findSQL RECIBE UN NUMERO, '".$id."' NO ES UN NUMERO", 4);
		}
	}

	/**
	 * BUSQUEDA POR ID PRIMARY KEY
	 * @param  Int $id Primary Key a Buscar
	 * @return Object RegistroObtenido
	 */
	public function find($id){
		if(is_numeric($id)){
			$consulta = $this->findSQL($id);
			$resultado = $this->mydb->consultar($consulta);
			if($this->mydb->count_rows($resultado)>0){
				return $resultado->fetch_object();
			}else{
				return null;
			}
		}else{
			throw new Exception("find RECIBE UN NUMERO, '".$id."' NO ES UN NUMERO", 4);
		}
	}

	/**
	 * OBTENER UN REGISTRO EN FORMATO JSON
	 * @param  Int $id ID_REGISTRO
	 * @return String REGISTRO_JSON
	 */
	public function findJSON($id){
		if(is_numeric($id)){
			$consulta = $this->findSQL($id);
			$resultado = $this->mydb->consultar($consulta);

			if($this->mydb->count_rows($resultado)>0){
				$json = $this->mydb->jsonrow($resultado->fetch_object());
				return $json;
			}else{
				return '{}';
			}
		}else{
			throw new Exception("findJSON RECIBE UN NUMERO, '".$id."' NO ES UN NUMERO", 4);
		}
	}










	public function getSQL(){
		$consulta = "SELECT ".$this->select." FROM ".strtoupper($this->tabla);
		if($this->inner!=''){
			$consulta.= $this->inner;
		}
		if($this->where!=''){
			$consulta.=" WHERE ".$this->where;
		}
		if($this->group!=''){
			$consulta.=" GROUP BY ".$this->group;
		}
		if($this->order!=''){
			$consulta.=" ORDER BY ".$this->order;
		}
		if($this->limit!=''){
			$consulta.=" LIMIT ".$this->limit;
		}
		return $consulta;
	}

	public function get(){
		$resultados = $this->mydb->consultar($this->getSQL());
		$lista = $this->mydb->listar($resultados);
		return $lista;
	}

	public function getFirts(){
		$resultados = $this->mydb->consultar($this->getSQL());
		return $resultados->fetch_object();
	}

	public function getJSON(){
		$resultados = $this->mydb->consultar($this->getSQL());
		return $this->mydb->jsondata($resultados);
	}

	public function getFirtsJSON(){
		$resultados = $this->mydb->consultar($this->getSQL());
		return $this->mydb->jsonrow($resultados->fetch_object());
	}







	public function save($datos){
		$consulta="INSERT INTO ".strtoupper($this->tabla).'(';
		$insert = '';
		$values = ') VALUES (';
		if(is_object($datos) || is_array($datos)){
			foreach ($datos as $key => $value) {
				$insert .= $key.',';

				if(is_numeric($value)){
					$values .= $value.',';
				}else if(is_string($value)){
					$values .= "'".$value."',";
				}
			}
		}
		$consulta .= $insert.$values.')';
		$consulta = str_replace(',)', ')', $consulta);
		$consulta = str_replace("'current_date()'", 'current_date()', $consulta);
		return $consulta;
	}

	public function save_update($datos){
	}

	public function update($datos){
	}

	public function delete($datos){
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