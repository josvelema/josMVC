<?php
class App {

	private $data = [];

    function __construct($params) {
		foreach ($params as $k => $v) {
			$this->data[$k] = $v;
		}
    }

	function __set($name, $value) {
        $this->data[$name] = $value;
    }

    function __get($name) {
		switch ($name) {
			case 'route':
				if (!isset($this->data[$name])) {
					$this->data[$name] = new Route($this);
				}
			break;
		}
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

	static function root_url() {
		$base_url = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http';
		$base_url .= '://' . rtrim($_SERVER['HTTP_HOST'], '/');
		$base_url .= $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 || strpos($_SERVER['HTTP_HOST'], ':') !== false ? '' : ':' . $_SERVER['SERVER_PORT'];
		$base_url .= '/' . rtrim(ltrim(substr(str_replace('\\', '/', realpath(__DIR__ . '/..')), strlen($_SERVER['DOCUMENT_ROOT'])), '/'), '/');
		return $base_url;
	}

	static function redirect($location) {
		header('Location: ' . App::root_url() . '/' . ltrim($location, '/'));
		exit;
	}

	static function print_r($value) {
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}

	static function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
		$w = floor($diff->d / 7);
		$diff->d -= $w * 7;
		$string = ['y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second'];
		foreach ($string as $k => &$v) {
			if ($k == 'w' && $w) {
				$v = $w . ' week' . ($w > 1 ? 's' : '');
			} else if (isset($diff->$k) && $diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

}
?>