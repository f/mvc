<?php
namespace Library\Transcoder;
/**
 * @class Library.Transcoder.Connector
 */
abstract class Connector {

	abstract public function startBuffer();

	abstract public function stopBuffer();

	abstract public function transcode($buffer);

	abstract public function init();

	final public function __construct() {
		\Library\Hooks::invoke('transcoder.buffer.before');
		$this->init();
		$this->startBuffer();
		\Library\Hooks::invoke('transcoder.buffer.after');
	}

	final public function __destruct() {
		$this->stopBuffer();
	}

	/**
	 * @static
	 * @param  $connector
	 * @return \Library\Transcoder\Connector
	 */
	public static function factory($connector) {
		\Library\Hooks::invoke('transcoder.create.before');
		$class = 'Library\\Transcoder\\Connector\\' . $connector;
		return new $class;
	}
}