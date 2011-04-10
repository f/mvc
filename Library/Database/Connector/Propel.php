<?php
namespace Library\Database\Connector;

use Library\Database\Connector;

class Propel extends Connector {

	private $configuration;

	private $connection;

	private $development = false;

	public function connect() {

		require_once 'propel/Propel.php';

		\Library\Hooks::invoke('database.connection.before');

		$this->connection = \Propel::initConnection($this->configuration, 'propel');

		\Library\Hooks::invoke('database.connection.after');
		return $this->connection;
	}

	public function isInDevelopment($bool) {
		$this->development = (boolean) $bool;
	}

	public function setConfiguration($config) {
		$this->configuration = $config;
	}

}
