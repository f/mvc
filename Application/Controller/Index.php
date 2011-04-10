<?php
namespace Application\Controller;

use Library\Controller as Controller;

class Index extends Controller {

	/**
	 * @var \Library\Registry
	 */
	private $registry;
	private $database;
	private $view;

	protected function init()
	{
		$this->registry = \Library\Registry::getInstance();
		$this->database = $this->registry->get('database.connection');
		$this->view = $this->registry->get('view')->getTemplateEngineLayer();
	}

	public function IndexAction($parameters)
	{

		//$user = $this->database->createQuery('SELECT u FROM Application\Model\User u WHERE u.id = 18');
		//var_dump($user->getResult());

		//$request = \Library\Request::factory('test/fatih/adi');
		//var_dump($request->execute());

		$this->view->assign('only_php', 'This is generated by PHPConnector');
		$this->view->assign('only_smarty', 'This is generated by SmartyConnector');
		$this->view->assign('only_twig', 'This is generated by TwigConnector');
		$this->view->assign('only_phptal', 'This is generated by PHPTALConnector');
		$this->view->display('Index/Text.php');
	}

}