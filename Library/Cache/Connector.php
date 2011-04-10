<?php
namespace Library\Cache;

/**
 * @namespace Library\Cache
 */
abstract class Connector {

	abstract public function connect();

	abstract public function set($key, $value, $expire = false);

	abstract public function get($key);

	abstract public function delete($key);

	private static $_instances = array();

	/**
	 * @static
	 * @param  $connector
	 * @return \Library\Database\Connector
	 */
	private static function factory($connector) {
		\Library\Hooks::invoke('cache.create.before');
		$class = 'Library\\Cache\\Connector\\' . $connector;
		return new $class;
	}

	/**
	 * @static
	 * @param  $connector
	 * @return \Library\Database\Connector
	 */
	public static function getInstance($connector) {
		if (!isset(self::$_instances[$connector]))
			self::$_instances[$connector] = self::factory($connector);

		return self::$_instances[$connector];
	}
}
