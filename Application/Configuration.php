<?php
namespace Application;

class Configuration extends \Library\Configuration {

	public function configDevelopment()
	{
		$this->registry->set('database.connector', 'MySQL');
		$this->registry->set('database.settings', array(
			'driver' => 'pdo_mysql',
			'host' => 'localhost',
			'dbname' => 'test',
			'user' => 'root',
			'password' => 'root'
		));
	}

	public function configStage()
	{

	}


	public function configProduction()
	{

	}


}
