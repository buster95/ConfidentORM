<?php
require_once 'MyDB.php';

// CLASE TABLA PARA HACER CONSULTAS
class Table {
	// PARAMETROS DE LA TABLA
	private $tabla='';
	private $parametros='*';
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
		$this->parametros = $parametros1;
		return $this;
	}

	public function where(){
	}

	public function inner(){
	}

	public function order(){
	}

	public function group(){
	}

	public function getSQL(){
		$consulta = "select ".$this->parametros." from ".$this->tabla;
		return $consulta;

	}

	public function get(){
		$resultado = MyDB::consultar($this->getSQL());
		return $resultado;
	}

	public function getJSON(){
	}

	public function getJSONROW(){
	}
}

?>