<?php
namespace Library;
/**
 * @package Library
 */
class URI {

	private static $_instance = NULL;

	private function __clone() {
	}

	private function __construct() {
	}

	/**
	 * @static
	 * @return \Library\URI
	 */
	public static function getInstance() {
		if (self::$_instance == NULL)
			self::$_instance = new self();

		return self::$_instance;
	}

	private $uri;

	private $parameters = array();

	private $baseUri;

	public function setUri($uri) {
		$this->uri = $uri;
	}

	public function getUri() {
		return $this->uri;
	}

	public function setBaseUri($baseUri) {
		$this->baseUri = $baseUri;
	}

	public function getBaseUri() {
		return $this->baseUri;
	}

	public function parseParameters($regex) {
		if (preg_match('{' . $regex . '}u', $this->uri, $matches)) {
			$params = array();
			array_shift($matches);
			foreach ($matches as $key => $match)
			{
				if (is_string($key))
					$params[$key] = $match;
			}
			$this->parameters = $params;
			return true;
		}
		return false;
	}

	public function getParameter($key) {
		return $this->parameters[$key];
	}

	public function getParameters() {
		return $this->parameters;
	}

}