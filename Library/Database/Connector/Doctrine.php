<?php
namespace Library\Database\Connector;

use Library\Database\Connector;

class Doctrine extends Connector {

	/**
	 * @var \Doctrine\ORM\Configuration
	 */
	private $config;

	private $configuration;

	private $evm;

	private $development = false;

	private $connection;

	public function setConfiguration($config) {
		$this->configuration = $config;
	}

	public function isInDevelopment($bool) {
		$this->development = (boolean) $bool;
	}

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function connect() {
		\Library\Hooks::invoke('database.connection.before');

		$this->config = new \Doctrine\ORM\Configuration();
		$this->config->setProxyDir(ROOT_DIR . 'Application/Database/Proxy');
		$this->config->setProxyNamespace('Application\Database\Proxy');
		$this->config->setAutoGenerateProxyClasses($this->development);

		$driver = new \Doctrine\ORM\Mapping\Driver\XmlDriver(ROOT_DIR . 'Application/Database/Mappings');
		$driver->setFileExtension('.xml');
		$this->config->setMetadataDriverImpl($driver);

		if ($this->development)
			$cache = new \Doctrine\Common\Cache\ArrayCache();
		else
			$cache = new \Doctrine\Common\Cache\ApcCache();


		$this->config->setMetadataCacheImpl($cache);
		$this->config->setQueryCacheImpl($cache);

		$this->evm = new \Doctrine\Common\EventManager();
		$this->connection = \Doctrine\ORM\EntityManager::create($this->configuration, $this->config, $this->evm);

		\Library\Hooks::invoke('database.connection.after');
		return $this->connection;
	}

}
