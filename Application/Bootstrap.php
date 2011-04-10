<?php
namespace Application;

class Bootstrap extends \Library\Bootstrap {

	/**
	 * Başlangıç methodu, setup hazırlanıyor.
	 *
	 * @return void
	 */
	public function init()
	{
		self::setupLogger();
		self::setupRegistry();
		self::setupEnvironment();
		self::setupConfiguration();
		self::setupDatabase();
		self::setupRoutes();
		self::setupView();

		self::$router->dispatch();
	}

	/**
	 * Loglama işlemleri
	 */
	protected static function setupLogger()
	{
		self::$logger = \Library\Logger::getInstance();
		self::$logger->handleExceptions();
		self::$logger->handleErrors();
		self::$logger->setLogPath(sprintf(ROOT_DIR . '/log/%s.log', date('Ym')));

		/*
		\Library\Logger::log('ERROR', 'naber');
		\Library\Logger::log('DEBUG', 'naber');
		\Library\Logger::log('WARNING', 'naber');
		\Library\Logger::log('NOTICE', 'naber');
		\Library\Logger::log('EXCEPTION', 'naber');
		\Library\Logger::log('DEBUG', 'naber');
		*/
	}

	/**
	 * Registry kullanıma hazırlanılıyor.
	 *
	 * @static
	 * @return void
	 */
	protected static function setupRegistry() {
		self::$registry = \Library\Registry::getInstance();
	}

	/**
	 * Çevresel işlemler
	 *
	 * @static
	 * @return void
	 */
	protected static function setupEnvironment()
	{
		ini_set('display_errors', 1);
		error_reporting(E_ALL);

		//Burada çevreyi tanımlayan bir algoritma işlenebilir.
		define('APPLICATION_ENVIRONMENT', 'development');

		$cache = \Library\Cache\Connector::getInstance('Memcache');
		$cache->connect();

		self::$registry->set('cache', $cache);
	}

	/**
	 *
	 * Konfigürasyon ayarları çevrelere göre ayarlanıyor.
	 *
	 * @static
	 * @return void
	 */
	protected static function setupConfiguration()
	{
		self::$config = \Application\Configuration::getInstance();

		self::$config->setRegistry(self::$registry);

		//Ayarlar belli çevrelerde çalışır.
		self::$config->addEnvironment('development', 'configDevelopment');
		self::$config->addEnvironment('stage', 'configStage');
		self::$config->addEnvironment('production', 'configProduction');

		//Çevreye göre ayar çalıştırılır.
		self::$config->run(APPLICATION_ENVIRONMENT);
	}

	protected static function setupDatabase()
	{
		self::$database = \Library\Database\Connector::getInstance(self::$registry->get('database.connector'));
		self::$database->setConfiguration(self::$registry->get('database.settings'));
		self::$database->isInDevelopment((APPLICATION_ENVIRONMENT == 'development'));

		$connection = self::$database->connect();
		self::$registry->set('database.connection', $connection);
	}

	protected static function setupRoutes()
	{

		self::$router = \Library\Router::getInstance();
		self::$router->setRouteCache(self::$registry->get('cache'));

		//Rotalar ekleniyor. Detaylı bilgi için addRoute'un PHPDoc'una bakınız.

		self::$router->addRoute('^users/(?P<username>[A-Za-z0-9]+)/(?P<id>\d+)$', array('Index','Test'));
		self::$router->addRoute('^test/(?P<action>[a-z]+)/(?P<username>[A-Za-z0-9]+)$', array('Test\Abc\Xyz','$action'));

		//Error Route
		self::$router->addRoute('^error/(?P<code>\d+)$', array('Error', 'error'));
		//Default route.
		self::$router->addRoute('^.*', array('Index', 'index'));
	}

	protected static function setupView()
	{
		self::$view = \Library\View::getInstance();
		self::$view->setTemplateEngine('PHP');
		//self::$view->setTranscoder('Mobility');
		self::$registry->set('view', self::$view);
	}

}
