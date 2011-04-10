<?php
namespace Library;
/**
 * @class Library.Bootstrap
 */
abstract class Bootstrap {

	/**
	 * @var \Library\Logger
	 */
	protected static $logger;

	/**
	 * @var \Library\Router
	 */
	protected static $router;

	/**
	 * @var \Library\Registry
	 */
	protected static $registry;

	/**
	 * @var \Application\Configuration
	 */
	protected static $config;

	/**
	 * @var \Library\Database\ConnectionFactory
	 */
	protected static $database;

	/**
	 * @var \Library\View
	 */
	protected static $view;

	abstract public function init();

	abstract static protected function setupLogger();

	abstract static protected function setupEnvironment();

	abstract static protected function setupRegistry();

	abstract static protected function setupConfiguration();

	abstract static protected function setupDatabase();

	abstract static protected function setupRoutes();

	abstract static protected function setupView();

	final public function __construct() {
		Hooks::invoke('application.before');
	}

	final public function __destruct() {
		Hooks::invoke('application.after');
	}

	final public function unitTestInit()
	{
		ob_start();
		$this->init();
		ob_get_contents();
	}

}
