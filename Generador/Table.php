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

	private $consulta='';

	function __construct($nombre1) {
		$this->tabla = $nombre1;
		return $this;
	}

	public function select($parametros1) {
		$this->select = $parametros1;
		return $this;
	}

	public function where($parametro, $valor, $numeric_operator=''){
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

	public function order(){
	}

	public function group(){
	}

	public function getSQL(){
		$consulta = "select ".$this->select." from ".$this->tabla;
		if($this->where!=''){
			$consulta.=" where ".$this->where;
		}
		return $consulta;
	}

	public function get(){
		$resultados = MyDB::consultar($this->getSQL());
		$lista = MyDB::listar($resultados);
		return $lista;
	}

	public function getJSON(){
		$resultados = MyDB::consultar($this->getSQL());
		return MyDB::jsondata($resultados);
	}

	public function getJSONROW(){
		$resultados = MyDB::consultar($this->getSQL());
		return MyDB::jsonrow($resultados->fetch_object());
	}
}

?>