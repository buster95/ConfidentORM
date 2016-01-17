<?php
require_once 'MyDB.php';

// CLASE TABLA PARA HACER CONSULTAS
class Table {
	// PARAMETROS DE LA TABLA
	private $tabla='';
	private $select='*';
	private $where='';
	private $inner='';
	private $order='';
	private $group='';
	private $limit='';

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
		$columna = $this->mydb->db_key($this->tabla);
		return $columna;
	}

	public function columnas(){
	}

	public function select($parametros1) {
		$this->select = $parametros1;
		return $this;
	}

	public function where($parametro,$valor){
		if($this->where==''){
			$this->where = $parametro;
		}else{
			$this->where .= ' and '.$parametro;
		}

		if(is_numeric($valor)){
			$this->where .= '='.$valor;
		}else if(is_string($valor)){
			$this->where .= " like '".$valor."' ";
		}
		return $this;
	}

	public function whereBool($parametro,$numeric_operator='',$valor){
		if($this->where==''){
			$this->where = $parametro;
		}else{
			$this->where .= ' and '.$parametro;
		}

		if(is_numeric($valor)){
			if($numeric_operator!=''){
				$this->where .= $numeric_operator.$valor;
			}else{
				$this->where .= '='.$valor;
			}
		}else if(is_string($valor)){
			$this->where .= " like '".$valor."' ";
		}
		return $this;
	}

	public function whereOr($parametro, $valor, $numeric_operator=''){
		if($this->where==''){
			$this->where = $parametro;
		}else{
			$this->where .= ' or '.$parametro;
		}

		if(is_numeric($valor)){
			if($numeric_operator!=''){
				$this->where .= $numeric_operator.$valor;
			}else{
				$this->where .= '='.$valor;
			}
		}else if(is_string($valor)){
			$this->where .= " like '".$valor."' ";
		}
		return $this;
	}

	public function inner($table2){
	}

	public function order($parametro='', $tipo=''){
		if($parametro!=''){
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

	public function aumentar($parametro){
	}

	public function disminuir($parametro){
	}

	public function disminuir($parametro){
	}

	/**
	 * Busqueda Por ID Primary Key
	 * @param  Int $id Primary Key a Buscar
	 * @return Object Registro Obtenido
	 */
	public function find($id){
		if(is_numeric($id)){
			$consulta = "select * from ".$this->tabla." where ".$this->key()."=".$id;
			$resultado = $this->mydb->consultar($consulta);
			return $resultado->fetch_object();
		}else{
			return '';
		}
	}

	public function findSQL($id){
		if(is_numeric($id)){
			$consulta = "select * from ".$this->tabla." where ".$this->key()."=".$id;
			return $consulta;
		}else{
			return '';
		}
	}

	public function findJSON($id){
		if(is_numeric($id)){
			$consulta = "select * from ".$this->tabla." where ".$this->key()."=".$id;
			$resultado = $this->mydb->consultar($consulta);
			$json = $this->mydb->jsonrow($resultado->fetch_object());
			return $json;
		}else{
			return '';
		}
	}

	public function getSQL(){
		$consulta = "SELECT ".$this->select." FROM ".$this->tabla;
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
	}

	public function save_update($datos){
	}

	public function update($datos){
	}

	public function delete($datos){
	}
}

?>