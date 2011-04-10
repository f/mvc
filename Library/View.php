<?php
namespace Library;
/**
 * @namespace Library
 */
class View {

	private static $_instance = NULL;

	final private function __clone() {
	}

	/**
	 * @static
	 * @return \Library\View
	 */
	public static function getInstance() {
		if (self::$_instance == NULL)
			self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * @var \Library\TemplateEngine\Connector
	 */
	private $engine = false;

	/**
	 * @var \Library\Transcoder\Connector
	 */
	private $transcoder = false;

	final private function __construct() {
		Hooks::invoke('view.before');
	}

	public function setTemplateEngine($connector) {
		$this->engine = \Library\TemplateEngine\Connector::factory($connector);
	}

	/**
	 * @return \Library\TemplateEngine\Connector
	 */
	public function getTemplateEngineLayer() {
		return $this->engine;
	}

	public function engine() {
		return $this->engine->engine();
	}

	public function setTranscoder($connector) {
		$this->transcoder = \Library\Transcoder\Connector::factory($connector);
	}
}
