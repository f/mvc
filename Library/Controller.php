<?php
namespace Library;
/**
 * @class Library.Controller
 */
abstract class Controller {

	/**
	 * @var \Library\Request
	 */
	private $request;

	abstract protected function init();

	final public function __construct(Request $request) {
		$this->request = $request;

		Hooks::invoke('controller.init.before', get_class($this));
		$this->init($request);
		Hooks::invoke('controller.init.after', get_class($this));
	}

	protected function getRequest() {
		return $this->request;
	}

	final public function __destruct() {
		Hooks::invoke('controller.after', get_class($this));
	}

}