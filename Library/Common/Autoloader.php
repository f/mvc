<?php
namespace Library\Common;

class Autoloader {

	private static $paths_options = array();

	/**
	 * @var \Library\Cache\Connector
	 */
	private static $cache;

	/**
	 * Parametresiz kullanıldığında kütüphaneyi yükler.
	 *
	 * @static
	 * @param null $path
	 * @param array $options
	 * @return void
	 */
	public static function register($path = NULL, $options = array()) {
		if (is_null($path)) {
			$path = __DIR__ . '/../../';
		}

		if (is_dir($path))
			set_include_path($path . PATH_SEPARATOR . get_include_path());

		self::$paths_options[$path] = $options;

		spl_autoload_extensions('.php');
		spl_autoload_register(array(__CLASS__, 'loadClass'));
	}

	public static function setPathCache(\Library\Cache\Connector $cache) {
		$cache->connect();
		self::$cache = $cache;
	}

	public static function findClassPath($class_name, $extension = NULL) {

		$class_namespace = explode('\\', ltrim($class_name, '\\'));
		$class_name = end($class_namespace);
		unset($class_namespace[count($class_namespace) - 1]);

		$include_path = explode(PATH_SEPARATOR, get_include_path());

		$class_path = implode(DIRECTORY_SEPARATOR, $class_namespace);
		$include_path = array_unique($include_path);

		foreach ($include_path as $inc_path)
		{
			if (isset(self::$paths_options[$inc_path]['suffix']))
				$suffix = self::$paths_options[$inc_path]['suffix'];
			else
				$suffix = '';

			if (isset(self::$paths_options[$inc_path]['prefix']))
				$prefix = self::$paths_options[$inc_path]['prefix'];
			else
				$prefix = '';

			$file = realpath($inc_path . DIRECTORY_SEPARATOR . $class_path . DIRECTORY_SEPARATOR . $prefix . $class_name . $suffix . $extension);

			if ($file) {
				return $file;
			}
		}

		return false;
	}

	public static function loadClass($class_name) {
		if (self::$cache && false !== ($class_path = self::$cache->get('Autoloader.' . $class_name))) {
			require $class_path;
			return;
		}

		$extensions = array_map('trim', explode(',', spl_autoload_extensions()));

		foreach ($extensions as $extension)
		{
			if (false !== ($class_path = self::findClassPath($class_name, $extension))) {

				if (self::$cache) {
					self::$cache->set('Autoloader.' . $class_name, $class_path);
				}

				require $class_path;
				return;
			}
		}
	}

}