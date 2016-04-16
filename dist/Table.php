<?php

require_once 'DB.php';

define('DESC', 'DESC', true);
define('ASC', 'ASC', true);

// ERROR LA COLUMNA NO EXISTE: CODE 1
// ERROR APOSTROFE EN LA CONSULTA: CODE 2
// ERROR NO ES NOMBRE PARA COLUMNA: CODE 3
// PARAMETRO VACIO O NULO: CODE 9

class Table {

    private $tabla = '';
    private $select = '*';
    private $where = '';
    private $order = '';
    private $group = '';
    private $limit = '';
    private $inner = '';
    private $left_join = '';
    private $right_join = '';
    private $cross = '';
    private $on = '';
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
     * RETORNA TRUE SI EXISTE EL NOMBRE DE UNA TABLA
     * @param Boolean $table_name NOMBRE DE LA TABLA
     */
    private function TABLE_EXISTS($table_name) {
        $consulta = 'SHOW TABLES;';
        $tablas = $this->mydb->consultar($consulta);
        while ($tabla = $tablas->fetch_array(MYSQLI_NUM)) {
            if (strtolower($table_name) == strtolower($tabla[0])) {
                return true;
            }
        }
        throw new Exception("TABLA NO EXISTE => " . $table_name, 1);
    }

    /**
     * LLAVE PRIMARIA DE LA TABLA
     * @return String LLAVE PRIMARIA
     */
    public function KEY() {
        $llaves = $this->mydb->consultar('SHOW KEYS FROM ' . $this->tabla);
        while ($fila = $llaves->fetch_object()) {
            if ($fila->Key_name == 'PRIMARY') {
                return $fila->Column_name;
            }
        }
        return '';
    }

    /**
     * INFORMACION CAMPOS TABLA
     * @return Array Informacion Campos
     */
    public function DESCRIBE() {
        $columnas = $this->mydb->consultar('DESCRIBE ' . $this->tabla);
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
    public function COLUMNAS() {
        $columnas = $this->mydb->consultar('DESCRIBE ' . $this->tabla);
        $campos = array();
        while ($fila = $columnas->fetch_array(MYSQLI_ASSOC)) {
            $campos[] = $fila['Field'];
        }
        return $campos;
    }

    /**
     * RETORNA EL TIPO DE DATO DE UNA COLUMNA
     * RETURN NUMBER, STRING, DATE, BOOLEAN
     * @param String $columna NOMBRE DE LA COLUMNA
     */
    public function COLUMN_TYPE($columna = '') {
        if ($columna == '') {
            throw new Exception("PARAMETRO VACIO", 9);
        }
        $columnas = $this->mydb->consultar('SHOW COLUMNS FROM ' . $this->tabla . " WHERE Field='" . $columna . "'");

        $number = array('bigint', 'int', 'smallint', 'mediumint', 'real', 'float', 'double', 'decimal');
        $string = array('varchar', 'nvarchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext');
        $date = array('date', 'time', 'datetime', 'timestamp', 'year');
        $booleano = array('tinyint');
        $binario = array('binary', 'tinyblob', 'blob', 'mediumblob', 'bigblob', 'longblob', 'varbinary');

        $filter_type = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '(', ')');
        $tipo = false;

        while ($fila = $columnas->fetch_array(MYSQLI_ASSOC)) {
            $tipo = strtolower($fila['Type']);
            $tipo = str_replace($filter_type, '', $tipo);

            if ($this->search_array($number, $tipo)) {
                return "NUMBER";
            } else if ($this->search_array($string, $tipo)) {
                return "STRING";
            } else if ($this->search_array($date, $tipo)) {
                return "DATE";
            } else if ($this->search_array($booleano, $tipo)) {
                return "BOOLEAN";
            } else if ($this->search_array($binario, $tipo)) {
                return "BINARY";
            }
        }
        throw new Exception("COLUMNA => ( " . $tipo . " ) NO EXISTE", 1);
    }

    /**
     * RETORNA SI UNA CADENA ES UN OPERADOR
     * @param Boolean $operador TRUE,FALSE
     */
    private function IS_OPERADOR($operador) {
        $operadores = array('=', '!=', '===', '!==', '>', '<', '>=', '<=');
        if ($this->search_array($operadores, $operador)) {
            return true;
        }
        return false;
    }

    /**
     * LIMPIA UN PARAMETRO DE FUNCIONES Y CARACTERES
     * @param  String $string1 PARAMETRO A VERIFICAR
     * @return String          PARAMETRO LIMPIADO
     */
    private function SELECT_CLEAN_PARAMS($string1) {
        $verificador = strtolower($string1);
        $verificador = str_replace(array('concat(', 'sum(', 'avg(', 'count(', 'max(', 'min(', '(', ')', ' '), '', $verificador);
        $verificador = str_replace(array('+', '-', '*', '/'), ',', $verificador);
        return $verificador;
    }

    /**
     * COMPRUEBA EL NOMBRE DE VARIAS COLUMNAS
     * @param Boolean $columnas TRUE:EXISTE,FALSE:NO_EXISTE
     */
    private function COLUMNS_EXISTS($columnas) {
        if ($columnas == '') {
            return true;
        }
        $columnas = $this->SELECT_CLEAN_PARAMS($columnas);
        if (strpos($columnas, ',') > -1) {
            $columnas = explode(',', $columnas);
        }

        if (is_array($columnas)) {
            foreach ($columnas as $valor) {
                $this->COLUMN_EXISTS($valor);
            }
            return true;
        } else if (is_string($columnas)) {
            $this->COLUMN_EXISTS($columnas);
            return true;
        }
    }

    /**
     * COMPRUEBA SI EL NOMBRE DE UNA COLUMNA EXISTE
     * @param  String $columna NOMBRE DE LA COLUMNA
     * @return Boolean     TRUE (if exist) or FALSE (if not exist)
     */
    private function COLUMN_EXISTS($columna = '') {
        $column_name = $columna;
        if ($column_name == '') {
            return true;
        }
        if (is_string($column_name)) {
            $column_name = $this->SELECT_CLEAN_PARAMS($column_name);
        }
        if (strpos($column_name, '\'') === 0) {
            $is_a_text = $column_name;
            $contador = 0;
            for ($i = 0; $i < strlen($column_name); $i++) {
                if (strpos($is_a_text, '\'') > -1) {
                    $posicion = strpos($is_a_text, '\'');
                    $is_a_text = substr($is_a_text, $posicion + 1);
                    $contador++;
                }
            }
            if (is_numeric($contador) && $contador > 0 && $contador % 2 == 0) {
                return true;
            } else {
                throw new Exception("SQL QUERY CON => ( " . $column_name . " ) APOSTROFE DE MAS", 2);
            }
        } else if (strpos($column_name, '\'') > 0) {
            throw new Exception("NO ES UN NOMBRE => ( " . $column_name . " ) PARA UNA COLUMNA", 3);
        }

        $columnas = $this->mydb->consultar('DESCRIBE ' . $this->tabla);
        while ($fila = $columnas->fetch_array(MYSQLI_ASSOC)) {
            if (strtolower($fila['Field']) == strtolower($column_name)) {
                return true;
            }
        }
        throw new Exception("COLUMNA => ( " . $column_name . " ) NO EXISTE", 1);
    }

    /**
     * AGREGA TABLENAME A CADA PARAMETRO
     * @param  String $string1 Parametro de Entrada
     * @return String          Parametro Filtrado
     */
    private function TABLENAME_ADD_TO_PARAM($string1) {
        $string1 = strtolower($string1);
        $string1 = $this->tabla . '.' . $string1;
        $string1 = str_replace('(', '(' . $this->tabla . '.', $string1);
        $string1 = str_replace(',', ',' . $this->tabla . '.', $string1);
        $string1 = str_replace('+', '+' . $this->tabla . '.', $string1);
        $string1 = str_replace('-', '-' . $this->tabla . '.', $string1);
        $string1 = str_replace('*', '*' . $this->tabla . '.', $string1);
        $string1 = str_replace('/', '/' . $this->tabla . '.', $string1);

        $string1 = str_replace(array(' ,', ', '), ',', $string1);
        $string1 = str_replace($this->tabla . '.\'', '\'', $string1);
        $string1 = str_replace($this->tabla . '.sum(', 'SUM(', $string1);
        $string1 = str_replace($this->tabla . '.avg(', 'AVG(', $string1);
        $string1 = str_replace($this->tabla . '.min(', 'MIN(', $string1);
        $string1 = str_replace($this->tabla . '.min(', 'MIN(', $string1);
        $string1 = str_replace($this->tabla . '.concat(', 'CONCAT(', $string1);
        $string1 = str_replace($this->tabla . '.count(', 'COUNT(', $string1);
        return $string1;
    }

    /**
     * SELECT CLAUSE EN EL SQL QUERY
     * @param  String $parametro1 NOMBRE DEL PARAMETRO A SELECCIONAR
     * @return Table             RETURN CURRENT CLASS
     */
    public function select($parametro1) {
        if (is_string($parametro1)) {
            $this->COLUMNS_EXISTS($parametro1);
        } else {
            return $this;
        }

        if ((strpos($parametro1, '(') > -1 &&
                !strpos($parametro1, ')') > -1) ||
                (!strpos($parametro1, '(') > -1 &&
                strpos($parametro1, ')') > -1) ||
                (strpos($parametro1, '(') > strpos($parametro1, ')')) ||
                (strpos($parametro1, '(') == false && strpos($parametro1, ')') > -1)) {
            throw new Exception("NO ES UN NOMBRE => ( " . $parametro1 . " ) PARA UNA COLUMNA", 3);
        }

        if ($this->select == '*') {
            if (is_string($parametro1)) {
                $parametro1 = $this->TABLENAME_ADD_TO_PARAM($parametro1);
                $this->select = $parametro1;
            }
        } else {
            if (is_string($parametro1)) {
                $parametro1 = $this->TABLENAME_ADD_TO_PARAM($parametro1);
                $this->select .= ',' . $parametro1;
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
    public function selectAs($parametro1, $asname) {
        if (is_string($parametro1)) {
            $this->COLUMNS_EXISTS($parametro1);
        } else {
            return $this;
        }

        if ((strpos($parametro1, '(') > -1 &&
                !strpos($parametro1, ')') > -1) ||
                (!strpos($parametro1, '(') > -1 &&
                strpos($parametro1, ')') > -1) ||
                (strpos($parametro1, '(') > strpos($parametro1, ')')) ||
                (strpos($parametro1, '(') == false && strpos($parametro1, ')') > -1)) {
            throw new Exception("NO ES UN NOMBRE => ( " . $parametro1 . " ) PARA UNA COLUMNA", 3);
        }

        if ($this->select == '*') {
            if (is_string($parametro1) && is_string($asname)) {
                $parametro1 = $this->TABLENAME_ADD_TO_PARAM($parametro1);
                $this->select = $parametro1 . ' AS ' . $asname;
            }
        } else {
            if (is_string($parametro1) && is_string($asname)) {
                $parametro1 = $this->TABLENAME_ADD_TO_PARAM($parametro1);
                $this->select .= ',' . $parametro1 . ' AS ' . $asname;
            }
        }
        return $this;
    }

    /**
     * WHERE CLAUSE SQL QUERY
     * @param  String $parametro PARAMETRO_NOMBRE
     * @param  String $operador  OPERADOR O VALOR
     * @param  String $valor     VALOR_PARAMETRO
     * @param  boolean $clean_special     CLEAN STRING RICH_TEXT OR LOGIN DEFAULT LOGIN
     * @return Table            RETURN CURRENT CLASS
     */
    public function where($parametro, $operador, $valor = '', $clean_special = false) {
        if ($valor == '' && $valor !== 0 && $operador != '') {
            $valor = $operador;
            $operador = '';
        }
        $this->COLUMN_EXISTS($parametro);
        if ($this->where == '') {
            $this->where = $this->TABLENAME_ADD_TO_PARAM($parametro);
        } else {
            $this->where .= ' AND ' . $this->TABLENAME_ADD_TO_PARAM($parametro);
        }

        if (is_bool($clean_special)) {
            if ($clean_special) {
                $valor = DB::SQL_CLEAN_SPECIAL($valor);
            } else {
                $valor = DB::SQL_CLEAN($valor);
            }
        } else {
            $valor = DB::SQL_CLEAN($valor);
        }

//        if ($operador === '' && !(is_string($valor) || $valor === 0)) {
//            $this->where .= '=';
//        } else {
//            if (is_string($valor) && $valor !== 0) {
//                $this->where .= ' LIKE ';
//            } else {
//                $this->where .= $operador;
//            }
//        }

        // if($this->COLUMN_TYPE($parametro)==='NUMBER'){
        // 	$this->where .= $valor;
        // }else if($this->COLUMN_TYPE($parametro)==='STRING'){
        // 	$this->where .= "'".$valor."'";
        // }

        if ($this->COLUMN_TYPE($parametro) === 'NUMBER' && (is_numeric($valor) || $valor === 0)) {
            if ($operador==='') {
                $this->where .= '='.$valor;
            }else{
                $this->where .= $operador.$valor;
            }
        } else if ($this->COLUMN_TYPE($parametro) === 'STRING' && (is_string($valor) && $valor !== 0)) {
            $this->where .= " LIKE '" . $valor . "' ";
        } else {
            throw new Exception("COLUMNA TIPO DE DATO DIFERENTE AL VALOR", 1);
        }
        return $this;
    }

    /**
     * WHERE CLAUSE WITH OR
     * @param  String $parametro PARAMETRO_NOMBRE
     * @param  String $operador  OPERADOR O VALOR
     * @param  String $valor     VALOR_PARAMETRO
     * @param  boolean $clean_special     CLEAN STRING RICH_TEXT OR LOGIN DEFAULT LOGIN
     * @return Table            RETURN CURRENT CLASS
     */
    public function whereOr($parametro, $operador, $valor = '', $clean_special = false) {
        if ($valor == '' && $valor !== 0 && $operador != '') {
            $valor = $operador;
            $operador = '';
        }
        $this->COLUMN_EXISTS($parametro);
        if ($this->where == '') {
            $this->where = $this->TABLENAME_ADD_TO_PARAM($parametro);
        } else {
            $this->where .= ' OR ' . $this->TABLENAME_ADD_TO_PARAM($parametro);
        }

        if (is_bool($clean_special)) {
            if ($clean_special) {
                $valor = DB::SQL_CLEAN_SPECIAL($valor);
            } else {
                $valor = DB::SQL_CLEAN($valor);
            }
        } else {
            $valor = DB::SQL_CLEAN($valor);
        }

//        if ($operador == '' && !(is_string($valor) && $valor !== 0)) {
//            $this->where .= '=';
//        } else {
//            if (is_string($valor) && $valor !== 0) {
//                $this->where .= ' LIKE ';
//            } else {
//                $this->where .= $operador;
//            }
//        }

        if ($this->COLUMN_TYPE($parametro) === 'NUMBER' && (is_numeric($valor) || $valor === 0)) {
            if ($operador==='') {
                $this->where .= '='.$valor;
            }else{
                $this->where .= $operador.$valor;
            }
        } else if ($this->COLUMN_TYPE($parametro) === 'STRING' && (is_string($valor) && $valor !== 0)) {
            $this->where .= " LIKE '" . $valor . "' ";
        } else {
            throw new Exception("COLUMNA TIPO DE DATO DIFERENTE AL VALOR", 1);
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
    public function inner(Table $table, $table_param, $current_param) {

    }

    /**
     * ORDER BY CLAUSE SQL QUERY
     * @param  String $parametro PARAMETRO_ORDEN
     * @param  String $tipo      ORDEN TYPE ASC_DESC
     * @return Table            RETURN CURRENT CLASS
     */
    public function order($parametro, $tipo = '') {
        if ($this->order == '') {
            if ($tipo != '') {
                $this->order = $parametro . ' ' . $tipo;
            } else {
                $this->order = $parametro;
            }
        } else {
            if ($tipo != '') {
                $this->order .= ', ' . $parametro . ' ' . $tipo;
            } else {
                $this->order .= ', ' . $parametro;
            }
        }
        return $this;
    }

    /**
     * GROUP BY CLAUSE SQL QUERY
     * @param  String $parametro PARAMETRO_GROUP
     * @return Table            RETURN CURRENT CLASS
     */
    public function group($parametro = '') {
        if ($parametro != '') {
            if ($this->group == '') {
                $this->group = $parametro;
            } else {
                $this->group .= ', ' . $parametro;
            }
        }
        return $this;
    }

    public function min_key(){
        $consulta = "SELECT MIN(".$this->KEY().") as maxid FROM ".$this->tabla;
        $resultado = $this->mydb->consultar($consulta)->fetch_object()->maxid;
        return $resultado;
    }
    public function max_key(){
        $consulta = "SELECT MAX(".$this->KEY().") as maxid FROM ".$this->tabla;
        $resultado = $this->mydb->consultar($consulta)->fetch_object()->maxid;
        return $resultado;
    }

    /**
     * LIMIT CLAUSE SQL QUERY
     * @param  integer $cantidad CANTIDAD DE DATOS
     * @return Table            RETURN CURRENT CLASS
     */
    public function limit($cantidad = 1000) {
        if (is_numeric($cantidad)) {
            $this->limit = $cantidad;
        }
        return $this;
    }

    /**
     * AUMENTAR UN PARAMETRO NUMERICO
     * @param  String $parametro NombreDelAtributo
     * @return Boolean true or false
     */
    public function aumentar($parametro) {

    }

    /**
     * DISMINUIR UN PARAMETRO NUMERICO
     * @param  String $parametro NombreDelParametro
     * @return Boolean	true or false
     */
    public function disminuir($parametro) {

    }

    /**
     * MAXIMO VALOR DE UN ATRIBUTO NUMERICO
     * @param  String $parametro NombreDelAtributo
     * @return Int ValorMaximo
     */
    public function max($parametro) {

    }

    /**
     * VALOR MINIMO DE UN ATRIBUTO NUMERICO
     * @param  String $parametro NombreDelAtributo
     * @return Int ValorMinimo
     */
    public function min($parametro) {

    }

    /**
     * VALOR PROMEDIO DE UN ATRIBUTO NUMERICO
     * @param  String $parametro NombreDelAtributo
     * @return Int ValorPromedio
     */
    public function avg($parametro) {

    }

    /**
     * SUMATORIA DE UN ATRIBUTO NUMERICO
     * @param  String $parametro NombreDelAtributo
     * @return Int ValorSumatoria
     */
    public function sum($parametro) {

    }

    /**
     * CANTIDAD DE VALORES REGISTRADOS
     * @return Int CantidadDeValores
     */
    public function count() {

    }

    /**
     * OBTENER LA CONSULTA DE UNA BUSQUEDA POR ID
     * @param  Int $id ID_REGISTRO
     * @return String ConsultaDeObtencion
     */
    public function findSQL($id) {
        if (is_numeric($id)) {
            $consulta = "SELECT " . $this->select . " FROM " . $this->tabla . " WHERE " . $this->KEY() . "=" . $id;
            return $consulta;
        } else {
            throw new Exception("findSQL RECIBE UN NUMERO, '" . $id . "' NO ES UN NUMERO", 4);
        }
    }

    /**
     * BUSQUEDA POR ID PRIMARY KEY
     * @param  Int $id Primary Key a Buscar
     * @return Object RegistroObtenido
     */
    public function find($id) {
        $consulta = $this->findSQL($id);
        $resultado = $this->mydb->consultar($consulta);
        if ($this->mydb->count_rows($resultado) > 0) {
            return $resultado->fetch_object();
        } else {
            return null;
        }
    }

    /**
     * OBTENER UN REGISTRO EN FORMATO JSON
     * @param  Int $id ID_REGISTRO
     * @return String REGISTRO_JSON
     */
    public function findJSON($id) {
        $consulta = $this->findSQL($id);
        $resultado = $this->mydb->consultar($consulta);
        if ($this->mydb->count_rows($resultado) > 0) {
            $rowToJSON = DB::jsonrow($resultado->fetch_array(MYSQLI_ASSOC));
            return $rowToJSON;
        } else {
            return '{}';
        }
    }

    public function getSQL() {
        $consulta = "SELECT " . $this->select . " FROM " . $this->tabla;
        if ($this->inner != '') {
            $consulta.= $this->inner;
        }
        if ($this->where != '') {
            $consulta.=" WHERE " . $this->where;
        }
        if ($this->group != '') {
            $consulta.=" GROUP BY " . $this->group;
        }
        if ($this->order != '') {
            $consulta.=" ORDER BY " . $this->order;
        }
        if ($this->limit != '') {
            $consulta.=" LIMIT " . $this->limit;
        }
        return $consulta;
    }

    public function get() {
        $resultados = $this->mydb->consultar($this->getSQL());
        $lista = DB::listar($resultados);
        return $lista;
    }

    public function getFirst() {
        $resultados = $this->mydb->consultar($this->getSQL());
        return $resultados->fetch_object();
    }

    public function getJSON() {
        $resultados = $this->mydb->consultar($this->getSQL());
        return DB::jsondata($resultados);
    }

    public function getFirstJSON() {
        $resultados = $this->mydb->consultar($this->getSQL());
        return DB::jsonrow($resultados->fetch_array(MYSQLI_ASSOC));
    }

    public function isExists($sensitive = false) {
        $consulta = "SELECT " . $this->select . " FROM " . $this->tabla;
        if ($this->where != '') {
            $consulta.=" WHERE " . $this->where;
        }

        if ($sensitive) {
            //$consulta = str_replace('\' ', '\' COLLATE utf8_bin ', $consulta);
            $consulta = str_replace('LIKE', 'LIKE BINARY', $consulta);
        }

        $consulta.=';';
        $consulta = str_replace(' ;', ';', $consulta);

        $resultados = $this->mydb->consultar($consulta);
        $fila = $resultados->fetch_object();

        if ($fila !== null) {
            return true;
        } else {
            return false;
        }
    }


    public function save($datos) {
        $consulta = "INSERT INTO " . strtoupper($this->tabla) . '(';
        $insert = '';
        $values = ') VALUES (';

        if (is_object($datos) || is_array($datos)) {
            foreach ($datos as $key => $value) {
                if ($this->COLUMN_EXISTS(strtoupper($key))) {
                    $insert .= $key . ',';
                    $value = DB::SQL_CLEAN_TEXT($value);

                    switch ($this->COLUMN_TYPE($key)) {
                        case 'STRING':
                            $values .= "'" . $value . "',";
                            break;
                        case 'DATE':
                            $values .= "'" . $value . "',";
                            break;
                        case 'NUMBER':
                            $values .= $value . ',';
                            break;
                        case 'BOOLEAN':
                            $values .= $value . ',';
                            break;
                        default:
                            $values .= "'" . $value . "',";
                            break;
                    }
                } else {
                    throw new Exception("NO ES UN NOMBRE => ( " . $parametro1 . " ) PARA UNA COLUMNA", 3);
                }
            }
        }else{
            throw new Exception("SAVE RECIBE ARREGLO", 5);
        }

        $consulta .= $insert . $values . ');';
        $consulta = str_replace(',)', ')', $consulta);
        $consulta = str_replace("'CURRENT_DATE()'", 'CURRENT_DATE()', $consulta);
        $consulta = str_replace("'current_date()'", 'CURRENT_DATE()', $consulta);

        $resultado = $this->mydb->ejecutar($consulta);

        if ($resultado > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function save_update($datos) {
    }

    public function update($id, $datos) {
        $consulta = "UPDATE ".strtoupper($this->tabla).' SET ';
        $update = '';

        if (is_object($datos) || is_array($datos)) {
            foreach ($datos as $key => $value) {
                if ($this->COLUMN_EXISTS(strtoupper($key))) {
                    $update .= $key.'=';
                    if (!is_bool($value) && !is_numeric($value)) {
                        $value = DB::SQL_CLEAN_TEXT($value);
                    }

                    switch ($this->COLUMN_TYPE($key)) {
                        case 'STRING':
                            $update .= "'" . $value."',";
                            break;
                        case 'DATE':
                            $update .= "'" . $value."',";
                            break;
                        case 'NUMBER':
                            $update .= $value.',';
                            break;
                        case 'BOOLEAN':
                            if($value===true){
                                $update .= 'true,';
                            }else{
                                $update .= 'false,';
                            }
                            break;
                        default:
                            $update .= "'" . $value."',";
                            break;
                    }
                } else {
                    throw new Exception("NO ES UN NOMBRE => ( " . $key . " ) PARA UNA COLUMNA", 3);
                }
            }
        }else{
            throw new Exception("UPDATE RECIBE ARREGLO", 5);
        }
        $consulta .= $update.' WHERE '.$this->KEY().'='.$id;
        $consulta = str_replace(array("', ","''"),"' ", $consulta);
        $consulta = str_replace("'CURRENT_DATE()'", 'CURRENT_DATE()', $consulta);
        $consulta = str_replace("'current_date()'", 'CURRENT_DATE()', $consulta);
        $consulta = str_replace(", WHERE", ' WHERE', $consulta);
        $resultado = $this->mydb->ejecutar($consulta);
        var_dump($consulta);
        if ($resultado > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id) {
        if(is_numeric($id)){
            $consulta = "DELETE FROM ".strtoupper($this->tabla);
            $consulta .= " WHERE ".$this->KEY()."=".$id;
            $resultado = $this->mydb->ejecutar($consulta);
            if ($resultado > 0) {
                return true;
            } else {
                return false;
            }
        }else{
            throw new Exception("DELETE RECIBE INT", 5);

        }
    }

    /**
     * BUSCA UNA CADENA EN UN ARRAY
     * @param  Array $array CONJUNTO DE DATOS
     * @param  String $value DATO A BUSCAR
     * @return Boolean        TRUE,FALSE
     */
    private function search_array($array, $value) {
        if (is_array($array)) {
            foreach ($array as $key) {
                if ($value == $key) {
                    return true;
                }
            }
        }
        return false;
    }

}

/**
 * 	SQL QUERY CONSULTA
 */
class Query {

    private $query;
    private $db_conexion;

    function __construct($consulta) {
        $this->db_conexion = new DB();
        $this->query = $consulta;
        return $this;
    }

    public function execute(){
        $resultado = $this->db_conexion->ejecutar($this->query);
        if($resultado>0){
            return true;
        }else{
            return false;
        }
    }

    public function get() {
        $resultados = $this->db_conexion->consultar($this->query);
        $lista = DB::listar($resultados);
        return $lista;
    }

    public function getFirst() {
        $resultados = $this->db_conexion->consultar($this->query);
        return $resultados->fetch_object();
    }

    public function getJSON() {
        $resultados = $this->db_conexion->consultar($this->query);
        return DB::jsondata($resultados);
    }

    public function getFirstJSON() {
        $resultados = $this->db_conexion->consultar($this->query);
        return DB::jsonrow($resultados->fetch_array(MYSQLI_ASSOC));
    }

    public function isExists($sensitive = false) {
        $consulta = $this->query;
        if ($sensitive) {
            $consulta = str_replace('LIKE', 'LIKE BINARY', $consulta);
        }
        $consulta.=';';
        $consulta = str_replace(' ;', ';', $consulta);

        $resultados = $this->db_conexion->consultar($consulta);
        $fila = $resultados->fetch_object();

        if ($fila !== null) {
            return true;
        } else {
            return false;
        }
    }
}

?>