<?php
namespace Library\TemplateEngine\Connector;
/**
 * @class Library.TemplateEngine.Connector.Smarty
 */
use Library\TemplateEngine\Connector;

class Smarty extends Connector {

	private $smarty;

	public function __construct() {
		try {
			require_once 'Smarty/Smarty.class.php';

			$this->smarty = new \Smarty();
			$this->smarty->setTemplateDir(ROOT_DIR . 'Application/View');
			$this->smarty->setCompileDir(ROOT_DIR . 'Application/View/Engine/Compile');
			$this->smarty->setCacheDir(ROOT_DIR . 'Application/View/Engine/Cache');
			$this->smarty->setConfigDir(ROOT_DIR . 'Application/View');

			$this->registerEngine($this->smarty);

		} catch (\Exception $e) {
			throw new \Library\Common\Exception('Smarty connection error: ' . $e->getMessage());
		}
	}

	public function assign($variable, $value) {
		$this->smarty->assign($variable, $value);
	}

	public function display($template) {
		\Library\Hooks::invoke('template.display.before');
		$this->smarty->display($template);
		\Library\Hooks::invoke('template.display.after');
	}

	public function fetch($template) {
		\Library\Hooks::invoke('template.fetch.before');
		return $this->smarty->fetch($template);
	}
}
