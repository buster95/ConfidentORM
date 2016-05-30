ConfidentORM
=====================

### REQUERIMIENTOS ###
php5 o superior

### CLASES ###
`DB.php`
`Table.php`

### USO ###


```php
$usuarios = DB::table('table_name')->get();
foreach($usuarios as $user){
	echo $user->nombre;
	echo $user->apellido;
	echo $user->id;
	echo $user->usuario;
	echo $user->password;
}
```

```php
DB::JSON_CONTENT();
echo DB::table('usuarios')->getJSON();
```

**Resultado**
```javascript
DB::JSON_CONTENT();
echo DB::table('usuarios')->getJSON();
```

### METODOS ###
* **getSQL()**

* **get()**

* **getJSON()**

* **getFirstJSON()**

