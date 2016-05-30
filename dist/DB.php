<?php

require_once 'Table.php';

define('zona_horaria', '+6');
date_default_timezone_set('Etc/GMT' . zona_horaria);
// date_default_timezone_get();

/**
 * GENERADOR DE CONSULTAS PARA MYSQL
 */
class DB {

    // VARIABLES DE CONFIGURACION
    private $user = 'root'; // USUARIO de la BASE DE DATOS
    private $password = ''; // PASSWORD del usuario de la BASE DE DATOS
    private $database = 'sisfer'; // BASE DE DATOS
    private $host = '127.0.0.1'; // IPv4 o HOST DE CONEXION
    private $port = '3306'; // PUERTO DE CONEXION DEFAULT:3306

    public static function table($nombre_tabla) {
        $tabla = new Table($nombre_tabla);
        return $tabla;
    }

    public static function query($consulta) {
        if ($consulta != null & $consulta != '') {
            $query = new Query($consulta);
            return $query;
        }
        throw new Exception("Consulta No aceptada", 1);
    }

    public static function call($procedure) {
    }

    public static function funct($funtName) {
    }

    public static function view($viewName){
        if ($viewName != null & $viewName != '') {
            $vista = new View($viewName);
            return $vista;
        }
        throw new Exception("Consulta No aceptada", 1);
    }

    /**
     * @return mysqli_connect Conexion MySQL
     */
    private function conectar() {
        $con = new mysqli($this->getHOST(), $this->user, $this->password, $this->database);
        if ($con->connect_error) {
            trigger_error('Database connection failed: ' . $con->connect_error, E_USER_ERROR);
        } else {
            return $con;
        }
    }

    private function getHOST() {
        if ($this->port == '') {
            return $this->host;
        } else {
            return $this->host . ':' . $this->port;
        }
    }

    public function restore() {

    }

    public function backup() {
        $conexion = new DB();
        $backup = "------------------------------------------------------------------------\n";
        $backup .= "--                         CONFIDENT BACKUP                          \n";
        $backup .= "-- DATABASE: " . strtoupper($conexion->database) . "\n";
        $backup .= "-- HOST: " . $conexion->host . "         PORT: " . $conexion->port . "\n";
        $backup .= "-- MYSQL SERVER " . self::query('SELECT VERSION() as version;')->getFirst()->version . "\n";
        $backup .= "-----------------------------------------------------------------------\n\n";

        $backup .= "CREATE DATABASE IF NOT EXISTS `" . $conexion->database . "` ";
        $backup .= "/*!40100 DEFAULT CHARACTER SET " . self::query('select @@character_set_database as charset;')->getFirst()->charset . "*/;\n";
        $backup .= "USE `" . $conexion->database . "`;\n\n";

        $backup .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        $backup .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        $backup .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        $backup .= "/*!40101 SET NAMES utf8 */;\n";
        $backup .= "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;\n";
        $backup .= "/*!40103 SET TIME_ZONE='+00:00' */;\n";
        $backup .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n";
        $backup .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
        $backup .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
        $backup .= "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n";

        $tablas = $conexion->consultar('SHOW TABLES');
        while ($tabla = $tablas->fetch_array(MYSQLI_NUM)) {
            $create = 'DROP TABLE IF EXISTS `' . $tabla[0] . "`;\n";
            $create .= "/*!40101 SET @saved_cs_client = @@character_set_client */;\n";
            $create .= "/*!40101 SET character_set_client = utf8 */;\n";

            $object = $conexion->consultar('SHOW CREATE TABLE ' . $tabla[0])->fetch_array(MYSQLI_NUM);
            $create .= $object[1] . ";\n";
            $create .= "/*!40101 SET character_set_client = @saved_cs_client */;\n\n";
            $backup .= $create;

            $backup .= "/*!40000 ALTER TABLE `" . $tabla[0] . "` DISABLE KEYS */;\n";
            $query = 'REPLACE INTO `' . $tabla[0] . '`';
            // HACIENDO DUMP DE LOS CAMPOS
            $atributos = '';
            $columnas = $conexion->consultar('DESCRIBE ' . $tabla[0]);
            while ($columna = $columnas->fetch_array(MYSQLI_ASSOC)) {
                $atributos .= $columna['Field'] . ',';
            }
            $query .= '(' . $atributos . ") VALUES \n";

            // HACIENDO DUMP DE LOS DATOS
            $datos = '';
            $insertados = $conexion->consultar('SELECT * FROM ' . $tabla[0]);
            while ($fila = $insertados->fetch_array(MYSQLI_ASSOC)) {
                $datos .= "(";
                foreach ($fila as $key => $value) {
                    $type = self::table($tabla[0])->COLUMN_TYPE($key);
                    if ($type === 'STRING' || $type === 'DATE') {
                        $datos .= '\'' . $value . '\',';
                    } else {
                        $datos .= $value . ',';
                    }
                }
                $datos .= "),\n";
            }
            $query .= $datos . '~';

            // SI NO HAY DATOS, NO SE AÑADE EL INSERT
            if (self::count_rows($insertados) > 0) {
                $query = str_replace(',)', ')', $query);
                $query = str_replace('(,', '(', $query);
                $query = str_replace("),\n~", ");", $query);
                $backup .= $query . "\n";
            }
            $backup .= "/*!40000 ALTER TABLE `" . $tabla[0] . "` ENABLE KEYS */;\n\n";
        }

        $backup .= "/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;\n";
        $backup .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
        $backup .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
        $backup .= "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n";
        $backup .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        $backup .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        $backup .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
        $backup .= "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n\n";

        $backup .= "------------------------------------------------------------------------\n";
        $backup .= "--                    CONFIDENT BACKUP COMPLETADO                       \n";
        $backup .= "------------------------------------------------------------------------\n";

        file_put_contents(strtoupper($conexion->database) . '_BACKUP_' . date('d-M-Y') . '.sql', $backup);
        return $create . $backup;
    }

    public function consultar($consulta) {
        $conx = $this->conectar();
        $resultado = $conx->query($consulta);
        $conx->close();
        return $resultado;
    }

    public function ejecutar($consulta) {
        $conx = $this->conectar();
        $conx->query($consulta);
        $affected_rows = $conx->affected_rows;
        $conx->close();
        return $affected_rows;
    }

    public static function acentos($string){
        $acentos = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
        foreach ($acentos as $llave => $acento) {
            if (strpos($string, $acento)>-1) {
                $string = utf8_decode($string);
                break;
            }
        }
        return utf8_encode($string);
    }
    /*
      HEADER JSON FORMAT PHP
     */
    public static function JSON_CONTENT() {
        header('Content-Type: application/json');
    }

    /*
      CONVERTIR UNA FILA A JSON FORMAT
     */
    public static function jsonrow($fila) {
        foreach ($fila as $key => $valor) {
            if (is_string($valor)) {
                // $fila[$key] = self::acentos($valor);
                $acentos = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
                foreach ($acentos as $llave => $acento) {
                    if (strpos($valor, $acento)>-1) {
                        $fila[$key] = utf8_decode($valor);
                        break;
                    }
                }
            }
        }
        $fila = array_map('utf8_encode', $fila);
        $json = json_encode($fila, JSON_NUMERIC_CHECK);
        if ($json != false) {
            return $json;
        } else {
            return "{}";
        }
    }

    /*
      CONVERTIR CONJUNTO DE DATOS A JSON FORMAT
     */
    public static function jsondata($resultados) {
        $filas = array();
        while ($row = $resultados->fetch_array(MYSQLI_ASSOC)) {
            foreach ($row as $key => $valor) {
                if (is_string($valor)) {
                    // $row[$key] = self::acentos($valor);
                    $acentos = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
                    foreach ($acentos as $llave => $acento) {
                        if (strpos($valor, $acento)>-1) {
                            $row[$key] = utf8_decode($valor);
                            break;
                        }
                    }
                }
            }
            $filas[] = array_map('utf8_encode', $row);
        }
        $json = json_encode($filas, JSON_NUMERIC_CHECK);

        if ($json != false) {
            return $json;
        } else {
            return "[]";
        }
    }

    /*
      CONVIERTE LAS FILAS DE MYSQLI EN UN ARREGLO DE OBJETOS
     */

    public static function listar($resultados) {
        $datos = array();
        while ($fila = $resultados->fetch_object()) {
            $datos[] = $fila;
        }
        return $datos;
    }

    /*
      CUENTA LOS DATOS DE UN ARRAY
     */

    public static function count_data($objetos) {
        if (is_array($objetos)) {
            $x = 0;
            foreach ($objetos as $obj) {
                $x++;
            }
            return $x;
        } else {
            throw new Exception("Error en count_data $objetos no es array", 5);
        }
    }

    /*
      CUENTA EL NUMERO DE FILAS DE UN  MYSQLI_RESULT
     */

    public static function count_rows($resultado) {
        return mysqli_num_rows($resultado);
    }

    /*
      VERIFICA SI UN STRING TIENE UN TAMAÑO ESPECIFICO
     */

    public static function size_string_verify($valor, $size) {
        if (is_string($valor) && is_numeric($size)) {
            if (strlen($valor) >= $size) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!is_string($valor) && is_numeric($size)) {
                throw new Exception("Value no es un string", 4);
            } else if (is_string($valor) && !is_numeric($size)) {
                throw new Exception("Size no es un numero", 4);
            } else {
                throw new Exception("Parametros Invalidos", 4);
            }
        }
    }

    /**
     * LIMPIA UNA CADENA SOLO QUITANDO APOSTROFE
     * @param String $cadena CADENA A LIMPIAR
     * @return String 		 CADENA LIMPIADA
     */
    public static function SQL_CLEAN_TEXT($cadena = '') {
        $caracteres = array('\'');
        $filtrada = str_replace($caracteres, '', $cadena);
        return $filtrada;
    }

    /**
     * QUITA CARACTERES ESPECIALES DE UNA CADENA
     * @param  String $cadena CADENA A LIMPIAR
     * @return String         CADENA LIMPIADA
     */
    public static function SQL_CLEAN($cadena) {
        $caracteres = array('\'', '"', '=', '!',
            '<', '>', '¿', '?', '¡', '$', '\\', '{',
            '}', '[', ']', '#', '&',
            '+');
        $filtrada = str_replace($caracteres, '', $cadena);
        $filtrada = str_replace(array('%', '*'), '%', $filtrada);
        return $filtrada;
    }

    public static function SQL_CLEAN_SPECIAL($cadena) {
        $caracteres = array('\'', '"', '=', '!',
            '<', '>', '¿', '?', '¡', '$', '\\', '{',
            '}', '[', ']', '#', '&', '(', ')',
            '+', '-', '%', '*');
        $filtrada = str_replace($caracteres, '', $cadena);
        return $filtrada;
    }

    /*
      CAPITALIZE PALABRAS
     */
    public static function capitalize($value, $allwords=false) {
        if($allwords==true){
            $textos_procesados = '';
            $textos = explode(' ', $value);
            foreach ($textos as $palabras){
                $letter = substr($palabras, 0, 1);
                $word = substr($palabras, 1);
                $newWord = strtoupper($letter) . strtolower($word);
                $textos_procesados .= ' '.$newWord;
            }
            return substr($textos_procesados, 1);
        }else{
            $letter = substr($value, 0, 1);
            $word = substr($value, 1);
            $newWord = strtoupper($letter) . strtolower($word);
            return $newWord;
        }
        return strtoupper($letter) . strtolower($word);
    }




    /*
     * QUITA TODOS LOS ESPACIOS DE UNA CADENA
     */

    public static function trim($cadena) {
        return str_replace(array(' ', '\''), '', $cadena);
    }

    /*
      CONVERTIR A MAYUSCULAS UNA PALABRA O UN TEXTO
     */

    public static function mayuscula($cadena) {
        return strtoupper($cadena);
    }

    /*
      CONVERTIR A MINUSCULAS UNA PALABRA O UN TEXTO
     */

    public static function minuscula($cadena) {
        return strtolower($cadena);
    }

    public static function tokens() {
        $tokens = crypt('tokens aleatorio');
        $tokens = str_replace(array('$', '.', '/'), '', $tokens);
        return $tokens;
    }

    public static function cifrar($cadena, $llave) {
        $result = '';
        for ($i = 0; $i < strlen($cadena); $i++) {
            $char = substr($cadena, $i, 1);
            $keychar = substr($llave, ($i % strlen($llave)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        }
        return base64_encode($result);
    }

    public static function descifrar($cadena, $llave) {
        $result = '';
        $cadena = base64_decode($cadena);
        for ($i = 0; $i < strlen($cadena); $i++) {
            $char = substr($cadena, $i, 1);
            $keychar = substr($llave, ($i % strlen($llave)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result.=$char;
        }
        return $result;
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
     * ENCRIPTADO DE TEXTO CON LLAVE
     * @param  String $cadena        TEXTO A ENCRIPTAR
     * @param  String $llave_cifrado LLAVE DE CIFRADO
     * @return String                TEXTO CIFRADO
     */
    public function encriptar($cadena, $llave_cifrado) {
        $encrypted = mcrypt_ecb(MCRYPT_DES, $llave_cifrado, $cadena, MCRYPT_ENCRYPT);
        return $encrypted;
    }

    /**
     * DESENCRIPTADO DE DATOS CON LLAVE
     * @param  String $cadena           TEXTO A DESCIFRAR
     * @param  String $llave_descifrado LLAVE DE DESENCRIPTADO
     * @return String                   TEXTO DESENCRIPTADO
     */
    public function desencriptar($cadena, $llave_descifrado) {
        $decrypted = mcrypt_ecb(MCRYPT_DES, $llave_descifrado, $cadena, MCRYPT_DECRYPT);
        return $decrypted;
    }

}

?>