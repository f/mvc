<?php
namespace Library\TemplateEngine\Connector;
/**
 * @class Library.TemplateEngine.Connector.Twig
 */
use Library\TemplateEngine\Connector;

class Twig extends Connector {

	private $twig;

	private $variables = array();

	public function __construct() {
		try {
			require_once 'Twig/Autoloader.php';
			\Twig_Autoloader::register();
			$loader = new \Twig_Loader_Filesystem(ROOT_DIR . 'Application/View');
			$this->twig = new \Twig_Environment($loader, array(
				'cache' => ROOT_DIR . 'Application/View/Engine/Compile'
			));

			$this->registerEngine($this->twig);

		} catch (\Exception $e) {
			throw new \Library\Common\Exception('Twig connection error: ' . $e->getMessage());
		}

	}

	public function assign($variable, $value) {
		$this->variables[$variable] = $value;
	}

	public function display($template) {
		\Library\Hooks::invoke('template.display.before');
		echo $this->fetch($template);
		\Library\Hooks::invoke('template.display.after');
	}

	public function fetch($template) {
		\Library\Hooks::invoke('template.fetch.before');
		return $this->twig->loadTemplate($template)->render($this->variables);
	}

}
