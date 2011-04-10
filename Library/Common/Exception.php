<?php
namespace Library\Common;

use \Exception as PHPException;

class Exception extends PHPException {

	public function __construct($message, $code = NULL, $previous = NULL) {
		\Library\Hooks::invoke('exception.throw.before');
		parent::__construct($message, $code, $previous);
	}

}
