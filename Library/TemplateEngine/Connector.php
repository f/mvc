<?php
namespace Library\TemplateEngine;
/**
 * @class Library.TemplateEngine.Connector
 */
abstract class Connector {

	private $_engine;

	abstract public function assign($variable, $value);

	abstract public function display($template);

	abstract public function fetch($template);

	protected function registerEngine($object) {
		$this->_engine = $object;
	}

	public function engine() {
		if ($this->_engine)
			return $this->_engine;
		else
			return $this;
	}

	/**
	 * @static
	 * @param  $connector
	 * @return \Library\TemplateEngine\Connector
	 */
	public static function factory($connector) {
		\Library\Hooks::invoke('templateengine.create.before');
		$class = 'Library\\TemplateEngine\\Connector\\' . $connector;
		return new $class;
	}
}