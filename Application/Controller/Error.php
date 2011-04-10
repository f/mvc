<?php
namespace Application\Controller;

use Library\Controller;

class Error extends Controller {

	/**
	 * @var Library\TemplateEngine\Connector
	 */
	private $view;

	public function init()
	{
		$this->view = \Library\Registry::getInstance()->get('view')->getTemplateEngineLayer();
	}

	public function errorAction($parameters)
	{
		$this->view->assign('code', $parameters['code']);
		$this->view->display('Error/error.tpl');
	}

}
