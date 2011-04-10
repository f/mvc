<?php
namespace Library;
/**
 * @class Library.Request
 */
class Request {

	private $controller;

	private $action;

	private $method = 'GET';

	private $query = array();

	private $post = array();

	private $route = '/';

	private $url = '';

	private $protocol = 'http';

	private $baseUri;

	private $is_remote = false;

	private static $executable = false;

	/**
	 * @var array
	 */
	private $parameters;

	public function setAction($action) {
		$this->action = $action;
	}

	public function getAction() {
		return $this->action;
	}

	public function setController($controller) {
		$this->controller = $controller;
	}

	public function getController() {
		return $this->controller;
	}

	public function setParameters($parameters) {

		$this->parameters = $parameters;
	}

	public function getParameters() {
		return $this->parameters;
	}

	public function setQuery(&$query) {
		$this->query = $query;
	}

	public function getQuery() {
		return $this->query;
	}

	public function setMethod($method) {
		$this->method = $method;
	}

	public function getMethod() {
		return $this->method;
	}

	public function setPost(&$post) {
		$this->post = $post;
	}

	public function getPost() {
		return $this->post;
	}

	public function setRoute($route) {
		$this->route = $route;
	}

	public function getRoute() {
		return $this->route;
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}

	public function getProtocol() {
		return $this->protocol;
	}

	public function setBaseUri($baseUri) {
		$this->baseUri = $baseUri;
	}

	public function getBaseUri() {
		return $this->baseUri;
	}

	public static function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public static function getIP() {
		return $_SERVER['REMOTE_ADDR'];
	}

	public static function factory($route) {
		$original_request = Registry::getInstance()->get('router.request');

		self::$executable = true;
		$request = new self();
		$request->executable = true;
		if (preg_match('/(http|https):\/\//ui', $route)) {
			$request->is_remote = true;
			$request->setUrl($route);
		} else {
			$request->setUrl($_SERVER['SERVER_NAME']
					. '/' . $original_request->getBaseUri()
					. '/' . $route
			);
		}

		return $request;
	}

	public function execute() {
		if ($this->executable) {
			$context = stream_context_create(array(
				'http' => array(
					'method' => $this->getMethod(),
					'content' => http_build_query(($this->getMethod() == 'POST' ? $this->getPost() : $this->getQuery())),
					'timeout' => 10,
				)
			));

			self::$executable = false;
			$query = ($this->getMethod() == 'POST' ? '?' . http_build_query($this->getQuery()) : '');
			if ($this->is_remote) {
				$url = $this->getUrl() . $query;
			} else {
				$url = $this->getProtocol() . '://' . $this->getUrl() . $query;
			}
			return @file_get_contents($url, false, $context);

		} else {
			throw new Common\Exception('You cannot execute an incoming or unexecutable Request.', 408);
		}
	}
}