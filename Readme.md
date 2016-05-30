ConfidentORM
=====================

### REQUERIMIENTOS ###
php5 o superior

### CLASES ###
```php
DB.php
Table.php
```

### USO ###
**Modulo** `ngNotify`

**Servicio** `$notify`

```php
DB::table('table_name')->get();

```

```javascript
angular.module('myapp',['ngNotify']).
controller('ctrlmain', function($scope, $notify){
	$scope.click = function(){
		$notify.success('title','message');
	}

	$notify.info('title','message');
	$notify.warning('title','message');
	$notify.error('title','message');
});
```
### METODOS ###
* **setPosition**
```javascript
angular.module('myapp',['ngNotify']).
controller('ctrlmain', function($scope, $notify){
	$scope.click = function(){

		// bottom-left, bottom-center, bottom-right
		// top-left, top-center, bottom-right
		// bottom-full-width, top-full-width
		$notify.setPosition('bottom-left');

		$notify.success('title','message');
	}
});
```

* **setTime**
```javascript
angular.module('myapp',['ngNotify']).
controller('ctrlmain', function($scope, $notify){
	$scope.click = function(){
		$notify.setTime(5); // set time in seg
		$notify.success('title','message');
	}
});
```

* **setTimeExtend**
```javascript
angular.module('myapp',['ngNotify']).
controller('ctrlmain', function($scope, $notify){
	$scope.click = function(){
		$notify.setTimeExtend(5); // set time in seg
		$notify.success('title','message');
	}
});
```

* **showCloseButton**
```javascript
angular.module('myapp',['ngNotify']).
controller('ctrlmain', function($scope, $notify){
	$scope.click = function(){
		$notify.showCloseButton(true); // set boolean
		$notify.success('title','message');
	}
});
```

* **showProgressBar**
```javascript
angular.module('myapp',['ngNotify']).
controller('ctrlmain', function($scope, $notify){
	$scope.click = function(){
		$notify.showProgressBar(true); // set boolean
		$notify.success('title','message');
	}
});
```

* **onclick**
```javascript
angular.module('myapp',['ngNotify']).
controller('ctrlmain', function($scope, $notify){
	$scope.click = function(){
		$notify.onclick(function(){
			document.location.href = "https://google.com.ni";
		}); // set a function
		$notify.success('title','message');
	}
});
```

## MULTI LLAMADA ##
```javascript
$notify.setTime(10).setPosition('bottom-right').showCloseButton(true).showProgressBar(true);
```

