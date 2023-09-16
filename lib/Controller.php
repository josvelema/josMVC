<?php

class Controller {

	private $data = [];

    function __construct($app) {
		$this->app = $app;
		if ($this->app->db !== null) {
			$this->db = $this->app->db;
		}
		if (method_exists($this, 'init')) {
			$this->init();
		}
    }

	function __set($name, $value) {
		$this->data[$name] = $value;
    }

    function __get($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

	function session() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		$args = func_get_args();
		if (count($args) == 1) {
			return isset($_SESSION[$args[0]]) ? $_SESSION[$args[0]] : null;
		}
		$_SESSION[$args[0]] = $args[1];
	}

	function session_start() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	function session_regenerate_id() {
		session_regenerate_id();
	}

	function post() {
		$args = func_get_args();
		if (count($args) == 1) {
			return isset($_POST[$args[0]]) ? $_POST[$args[0]] : null;
		}
		return $_POST;
	}

	function get() {
		$args = func_get_args();
		if (count($args) == 1) {
			return isset($_GET[$args[0]]) ? $_GET[$args[0]] : null;
		}
		return $_GET;
	}

	function view($file, $arr = array()) {
		View::make($file, $arr);
	}

}

?>