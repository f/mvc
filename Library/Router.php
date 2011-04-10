<?php
namespace Library;
/**
 * @class Library.Router
 * @throws Library.Common.Exception
 */
class Router {

	private static $_instance = NULL;

	private function __clone() {
	}

	/**
	 * @static
	 * @return Library\Router
	 */
	public static function getInstance() {
		if (self::$_instance == NULL)
			self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * @var URI
	 */
	private $uri;

	/**
	 * @var array
	 */
	private $routes;

	/**
	 * @var Library\Route
	 */
	private $route;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Library\Cache\Connector
	 */
	private $cache = false;

	private function __construct() {
		Hooks::invoke('router.before');
		$this->uri = URI::getInstance();
	}

	public function setRouteCache(Cache\Connector $cache) {
		$this->cache = $cache;
	}

	private function createURI() {
		if (in_array('mod_rewrite', apache_get_modules())) {
			if (!array_key_exists('q', $_GET))
				$_GET['q'] = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';

			$this->uri->setUri(trim($_GET['q'], '/'));
			define('LINK_PREFIX', '');

			array_shift($_GET);

		} else {

			$this->uri->setUri(trim(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '', '/'));
			define('LINK_PREFIX', 'index.php/');
			trigger_error("You should enable mod_rewrite on your server!", E_USER_NOTICE);

		}

		$this->uri->setBaseUri(preg_replace('#' . preg_quote($this->uri->getUri(), '$#') . '#ui', '', trim($_SERVER['REQUEST_URI'], '/')));
	}

	/**
	 *
	 * Rota ayarla.
	 * @static
	 * @return void
	 */
	public function addRoute($regexp, array $path) {
		$this->routes[$regexp] = $path;
	}

	public function getRoute() {
		$this->createURI();

		if ($this->cache && false !== ($this->route = $this->cache->get('URI.route[' . $this->uri->getUri() . ']'))) {
			$this->uri->parseParameters($this->route['regexp']);
			Hooks::invoke('route.found', $this->route);
			return $this->route;
		}

		Hooks::invoke('route.loop.start');
		foreach ($this->routes as $regexp => $path)
		{
			if ($this->uri->parseParameters($regexp)) {
				$parameters = $this->uri->getParameters();

				$this->route['regexp'] = $regexp;
				$this->route['controller'] = preg_replace('#\$([A-Za-z0-9\_]+)#ue', '$parameters[\'\\1\'];', $path[0]);
				$this->route['action'] = preg_replace('#\$([A-Za-z0-9\_]+)#ue', '$parameters[\'\\1\'];', $path[1]);

				if ($this->cache)
					$this->cache->set('URI.routes[' . $this->uri->getUri() . ']', $this->route);

				Hooks::invoke('route.found', $this->route);
				return $this->route;
			}
		}
		Hooks::invoke('route.loop.end');
		Hooks::invoke('route.loop.notfound');
		throw new Common\Exception('Route not found.', 404);
	}

	public function getRequest() {
		if (is_null($this->route)) {
			$this->getRoute();
		}

		if (is_null($this->request)) {
			$this->request = new Request();
			$this->request->setMethod($_SERVER['REQUEST_METHOD']);
			$this->request->setQuery($_GET);
			$this->request->setPost($_POST);
			$this->request->setRoute($this->uri->getUri());
			$this->request->setProtocol(strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http');
			$this->request->setMethod($_SERVER['REQUEST_METHOD']);
			$this->request->setUrl($this->request->getProtocol() . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
			$this->request->setBaseUri($this->uri->getBaseUri());
			$this->request->setParameters($this->uri->getParameters());
			$this->request->setController($this->route['controller']);
			$this->request->setAction($this->route['action'] ? : 'index');

			Registry::getInstance()->set('router.request', $this->request);
		}

		return $this->request;
	}

	public function getControllerPath() {
		$this->getRequest();

		if (!$this->request instanceof Request) {
			throw new Common\Exception('Unexpected Request', 407);
		}

		$class = '\\' . implode('\\', array('Application', 'Controller', $this->request->getController()));

		return $class;
	}

	public function dispatch() {
		Hooks::invoke('dispatch.before');
		$controller = $this->getControllerPath();
		$controller = new $controller($this->request);

		if (!method_exists($controller, $this->request->getAction() . 'Action')) {
			throw new Common\Exception('Action not found.', 406);
		}

		$controller->{
		$this->request->getAction() . 'Action'
		}($this->request->getParameters());
		Hooks::invoke('dispatch.after');
	}

	public static function run($route) {

		$that = self::getInstance();
		$exact_route = '/' . $that->uri->getBaseUri() . ltrim($route, '/');
		if (!headers_sent()) {
			header('Location: ' . $exact_route);
		} else {
			echo sprintf('<script type="text/javascript">window.location.href = %s;</script>', json_encode($exact_route));
		}
		exit;
	}

}