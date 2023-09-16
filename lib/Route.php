<?php
class Route {

	private $app, $pathLocated = false;

    function __construct($app) {
		$this->app = $app;
    }

	function __destruct() {
        if (!$this->pathLocated) {
			http_response_code(404);
		}
    }

	function __call($name, $args) {
		if ($name == 'get' && $_SERVER['REQUEST_METHOD'] != 'GET') return;
		if ($name == 'head' && $_SERVER['REQUEST_METHOD'] != 'HEAD') return;
		if ($name == 'post' && $_SERVER['REQUEST_METHOD'] != 'POST') return;
		if ($name == 'put' && $_SERVER['REQUEST_METHOD'] != 'PUT') return;
		$url = '/' . ltrim($args[0], '/');
		$prefix = dirname($_SERVER['PHP_SELF']);
		$uri = $_SERVER['REQUEST_URI'];
		if (substr($uri, 0, strlen($prefix)) == $prefix) {
		    $uri = substr($uri, strlen($prefix));
		}
		$uri = '/' . ltrim($uri, '/');
		$path = explode('/', parse_url($uri)['path']);
		$routes = explode('/', $url);
		$values = [];
		foreach ($path as $pk => $pv) {
			if (isset($routes[$pk]) && preg_match('/{(.*?)}/', $routes[$pk])) {
				$routes[$pk] = preg_replace('/{(.*?)}/', $pv, $routes[$pk]);
				$values[] = $pv;
			}
		}
		if ($routes === $path) {
			$this->pathLocated = true;
			if (is_string($args[1])) {
				$class = explode('@', $args[1]);
				$controller = new $class[0]($this->app);
				$returnValue = call_user_func_array([$controller, $class[1]], $values);
			} else {
				$returnValue = call_user_func_array($args[1], $values);
			}
			if ($returnValue && is_string($returnValue)) {
				echo $returnValue;
			}
		}
	}

}
?>