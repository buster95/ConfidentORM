<?php

// var_dump(parse_ini_file("configuration.ini", true));
// ini::save(parse_ini_file("configuration.ini", true));
// echo ini::getValue('database','name');

class ini {

	private static $archivo = 'configuration.ini';

	public static function window_line_end($cadena) {
		$cadena = str_replace(array('<br>'), "\n", $cadena);
		return $cadena;
	}

	public static function html_line_end($cadena) {
		$cadena = str_replace(array("\n"), '<br>', $cadena);
		return $cadena;
	}

	public static function save($array) {
		$generate = '';
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$generate .= '['.$key.']'."\n";
				foreach ($value as $subkey => $subvalue) {
					$generate .= $subkey.'='.$subvalue."\n";
				}
				$generate .= "\n";

			}else if(is_string($value)){
				$generate .= $key.'='.$value."\n";
			}
		}
		file_put_contents(self::$archivo, self::window_line_end($generate));
		return true;
		// echo self::html_line_end($generate);
	}

	public static function getValue($key, $subkey='') {
		if(is_string(self::$archivo)){
			if($subkey!='' && is_string($subkey)){
				$variables = parse_ini_file(self::$archivo, true);
				$var = $variables[$key];
				return $var[$subkey];
			}else{
				$variables = parse_ini_file(self::$archivo);
				return $variables[$key];
			}
		}else{
			return null;
		}
	}

	public static function setValueSubkey($key, $subkey, $value){
	}

	public static function setValue($key, $value){
	}

	public static function cleanValue($cadena){
		return str_replace(array('=','[',']','\'',' '), '', $cadena);
	}
}

?>