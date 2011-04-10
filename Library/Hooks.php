<?php
namespace Library;
/**
 * @class Library.Hooks
 */
abstract class Hooks {

	private static $hooks = array();

	public static function register($key, $callback) {
		self::$hooks[$key][] = $callback;
	}

	public static function invoke($key) {
		if (!array_key_exists($key, self::$hooks))
			return;

		foreach (self::$hooks[$key] as $i => $hook)
		{
			$parameters = func_get_args();
			array_shift($parameters);
			$parameters['__hookOrder'] = $i;

			call_user_func_array($hook, $parameters);
		}
	}

}