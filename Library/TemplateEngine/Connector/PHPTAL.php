<?php
namespace Library\TemplateEngine\Connector;
/**
 * @class Library.TemplateEngine.Connector.PHPTAL
 */
use Library\TemplateEngine\Connector;

class PHPTAL extends Connector {

	private $phptal;

	public function __construct() {
		try {

			\PHPTAL::autoloadRegister();
			$this->phptal = new \PHPTAL();
			$this->phptal->setTemplateRepository(ROOT_DIR . 'Application/View');
			$this->phptal->setPhpCodeDestination(ROOT_DIR . 'Application/View/Engine/Compile');

			$this->registerEngine($this->phptal);

		} catch (\Exception $e) {
			throw new \Library\Common\Exception('PHPTAL connection error: ' . $e->getMessage());
		}

	}

	public function assign($variable, $value) {
		$this->phptal->set($variable, $value);
	}

	public function display($template) {
		$this->phptal->setTemplate($template);
		$this->phptal->echoExecute();
	}

	public function fetch($template) {
		$this->phptal->setTemplate($template);
		return $this->phptal->execute();
	}

}
