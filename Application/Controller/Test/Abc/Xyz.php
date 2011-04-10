<?php
namespace Application\Controller\Test\Abc;

use Library\Controller;

class Xyz extends Controller {

	protected function init()
	{

	}

	public function fatihAction($parameters)
	{
		$this->view->assign('adi', array('fatih', 'kadir', time()));
		$this->view->display('Index/Index2.tpl');
	}

}
