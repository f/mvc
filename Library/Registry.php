<?php
namespace Library;
/**
 * @class Library.Registry
 */
class Registry {

	private static $_instance = NULL;

	private function __clone() {
	}

	private function __construct() {
		Hooks::invoke('registry.before');
	}

	/**
	 * @static
	 * @return \Library\Registry
	 */
	public static function getInstance() {

		if (self::$_instance == NULL)
			self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * @var array
	 */
	private $registered_variables = array();

	public function set($key, $value) {
		Hooks::invoke('registry.set.before');
		$this->registered_variables[$key] = $value;
		Hooks::invoke('registry.set.after');
	}

	public function get($key) {
		return $this->registered_variables[$key];
	}

	public function has($key) {
		return array_key_exists($key, $this->registered_variables);
	}

	public function __set($key, $value) {
		$this->set(str_replace('_', '.', $key), $value);
	}

	public function __get($key) {
		return $this->get(str_replace('_', '.', $key));
	}

}