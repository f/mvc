<?php
namespace Library\Cache\Connector;

use Library\Cache\Connector;

class Memcache extends Connector {

	/**
	 * @var \Memcache
	 */
	private $cache;

	public function connect() {
		if (!class_exists('Memcache')) {
			throw new \Library\Common\Exception('Memcache is not available', 501);
		}

		$this->cache = new \Memcache();
		$this->cache->connect('localhost', 11211);
	}

	public function set($key, $value, $expire = 0) {
		$this->cache->set($key, $value, MEMCACHE_COMPRESSED, $expire);
	}

	public function get($key) {
		return $this->cache->get($key);
	}

	public function delete($key) {
		$this->cache->delete($key);
	}

}
