<?php
namespace Library\TemplateEngine\Connector;
/**
 * @class Library.TemplateEngine.Connector.PHP
 */
use Library\TemplateEngine\Connector;

class PHP extends Connector {

	private $variables;

	public function assign($variable, $value) {
		$this->variables[$variable] = $value;
	}

	public function display($template) {
		echo $this->fetch($template);
	}

	public function fetch($template) {
		\Library\Hooks::invoke('template.fetch.before');
		extract($this->variables);
		ob_start();
		include ROOT_DIR . "Application/View/$template";
		$output = ob_get_clean();
		\Library\Hooks::invoke('template.fetch.after');
		return $output;
	}
}
