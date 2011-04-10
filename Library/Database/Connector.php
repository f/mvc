<?php
namespace Library\Database;

abstract class Connector {

	abstract public function setConfiguration($config);

	abstract public function isInDevelopment($bool);

	abstract public function connect();

	private static $_instances = array();

	/**
	 * @static
	 * @param  $connector
	 * @return \Library\Database\Connector
	 */
	private static function factory($connector) {
		\Library\Hooks::invoke('database.create.before');
		$class = 'Library\\Database\\Connector\\' . $connector;
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
