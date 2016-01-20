<?php
require_once 'MyDB.php';

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
	private $consulta='';

	public static $DESC='DESC';
	public static $ASC='ASC';

	/**
	 * [Constructor de instancia]
	 * @param String $nombre1 Nombre De La Tabla en la DataBase
	 */
	function __construct($nombre1) {
		$this->tabla = $nombre1;
		$this->mydb = new MyDB();
		return $this;
	}

	public function key(){
		$llave = $this->mydb->db_key($this->tabla);
		return $llave;
	}

	public function columnas(){
	}

	private function describe(){
		$consulta = "describe";
	}

	private function Exist(){

	}

	/**
	 * ADD TABLENAME A CADA PARAMETRO
	 * @param  String $string1 Parametro de Entrada
	 * @return String          Parametro Filtrado
	 */
	private function selectReplace($string1){
		$string1 = strtolower($string1);
		$string1 = strtoupper($this->tabla).'.'.$string1;
		$string1 = str_replace('(', '('.strtoupper($this->tabla).'.', $string1);
		$string1 = str_replace(',', ','.strtoupper($this->tabla).'.', $string1);
		$string1 = str_replace('+', '+'.strtoupper($this->tabla).'.', $string1);
		$string1 = str_replace('-', '-'.strtoupper($this->tabla).'.', $string1);
		$string1 = str_replace('*', '*'.strtoupper($this->tabla).'.', $string1);
		$string1 = str_replace('/', '/'.strtoupper($this->tabla).'.', $string1);

		$string1 = str_replace(strtoupper($this->tabla).'.sum(', 'sum(', $string1);
		$string1 = str_replace(strtoupper($this->tabla).'.avg(', 'avg(', $string1);
		$string1 = str_replace(strtoupper($this->tabla).'.min(', 'min(', $string1);
		$string1 = str_replace(strtoupper($this->tabla).'.max(', 'max(', $string1);
		$string1 = str_replace(strtoupper($this->tabla).'.count(', 'count(', $string1);
		return $string1;
	}
	public function select($parametro1) {
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

		if(is_array($parametro1)){
			foreach ($parametro1 as $valor) {
				$this->select($valor);
			}
		}
		return $this;
	}
	public function selectAs($parametro1, $asname){
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

	public function where($parametro, $operador, $valor=''){
		if($valor=='' && $operador!=''){
			$valor=$operador;
			$operador='';
		}

		if($this->where==''){
			$this->where = $parametro;
		}else{
			$this->where .= ' AND '.$parametro;
		}

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

	public function inner($table, $parametro1, $parametro2){
	}

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
			$consulta = "SELECT ".$this->select." FROM ".$this->tabla." WHERE ".$this->key()."=".$id;
			return $consulta;
		}else{
			return '';
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
			return $resultado->fetch_object();
		}else{
			return '';
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
			$json = $this->mydb->jsonrow($resultado->fetch_object());
			return $json;
		}else{
			return '';
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

	public function getJSONROW(){
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
}

?>