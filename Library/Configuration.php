<?php
namespace Library;
/**
 * @class Library.Configuration
 */
class Configuration {

	private static $_instance = NULL;

	final protected function __clone() {
	}

	final protected function __construct() {
		Hooks::invoke('configuration.before');
	}

	/**
	 * @static
	 * @return \Application\Configuration
	 */
	public static function getInstance() {
		if (self::$_instance == NULL)
			self::$_instance = new \Application\Configuration();

		return self::$_instance;
	}

	/**
	 * @var \Library\Registry
	 */
	protected $registry;

	public function setRegistry(\Library\Registry $registry) {
		$this->registry = $registry;
	}

	public function addEnvironment($environment, $method) {
		Hooks::invoke('environment.add.before', $environment);
		if (!method_exists($this, $method)) {
			throw new Common\Exception('Configuration Method does not exists.', 405);
		}
		$this->registry->set('environments.' . $environment, $method);
		Hooks::invoke('environment.add.after', $environment);
	}

	public function run($environment) {
		Hooks::invoke('configuration.run.before', $environment);
		$this->{
		$this->registry->get('environments.' . $environment)
		}();
		Hooks::invoke('configuration.run.after', $environment);
	}

}
