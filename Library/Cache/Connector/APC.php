<?php
namespace Library\Cache\Connector;

use Library\Cache\Connector;

class APC extends Connector {

	public function connect() {
		if (!function_exists('apc_store')) {
			throw new \Library\Common\Exception('APC Cache is not available', 501);
		}
	}

	public function set($key, $value, $expire = 0) {
		apc_store($key, $value, $expire);
	}

	public function get($key) {
		return apc_fetch($key);
	}

	public function delete($key) {
		apc_delete($key);
	}

}
