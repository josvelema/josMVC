<?php

define('BASE_PATH', realpath(dirname(__FILE__)));

require BASE_PATH . '/config.php';

spl_autoload_register(function($class) {
	if (file_exists(BASE_PATH . '/lib/' . $class . '.php')) {
		require_once BASE_PATH . '/lib/' . $class . '.php';
	} else if (file_exists(BASE_PATH . '/app/models/' . $class . '.php')) {
		require_once BASE_PATH . '/app/models/' . $class . '.php';
	} else if (file_exists(BASE_PATH . '/app/controllers/' . $class . '.php')) {
		require_once BASE_PATH . '/app/controllers/' . $class . '.php';
	}
});

require BASE_PATH . '/app/app.php';

?>