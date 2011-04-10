<?php
namespace Library;
/**
 * @class Library.Logger
 */
class Logger {

	private static $_instance = NULL;

	private function __clone() {
	}

	private function __construct() {
		Hooks::invoke('logger.before');
		$this->generateProcessID();
	}

	private $process_id = 0;
	private $pattern = '%ID% %SEVERITY%  %MESSAGE% %FILE%:%LINE%';

	/**
	 * @static
	 * @return \Library\Logger
	 */
	public static function getInstance() {
		if (self::$_instance == NULL)
			self::$_instance = new self();

		return self::$_instance;
	}

	private function generateProcessID() {
		$this->process_id = (string) (rand(100, 999) . implode('', preg_split('/[\.\s]+/', microtime(false))));
	}

	public function setLogPath($path) {
		ini_set('error_log', $path);
	}

	public function setLogPattern($pattern) {
		$this->pattern = $pattern;
	}

	public static function log($severity, $message, $_ = NULL) {
		$that = self::getInstance();
		$history = debug_backtrace();

		$file = $history[0]['file'];
		$line = $history[0]['line'];

		$args = func_get_args();
		array_shift($args);

		foreach ($args as $i => $value)
		{
			if (is_array($value) || is_object($value)) {
				$value = (is_object($value) ? (get_class($value) != 'stdClass' ? get_class($value) : 'object') : 'array')
						. '({' . urldecode(http_build_query($value, NULL, ', ')) . '})';
			}
			settype($value, 'string');
			$args[$i] = $value;
		}

		$message = implode(', ', $args);

		$string = $that->createLogMessage($severity, $message, $file, $line);

		error_log($string);
	}

	public function createLogMessage($severity, $message, $file, $line) {
		return strtr($this->pattern, array(
			'%ID%' => $this->process_id,
			'%SEVERITY%' => str_pad($severity, 11, ' ', STR_PAD_RIGHT),
			'%MESSAGE%' => $message,
			'%FILE%' => $file,
			'%LINE%' => $line
		));
	}

	private function getErrorName($errno) {
		$error_codes = get_defined_constants(true);
		$error_codes = $error_codes['Core'];
		foreach ($error_codes as $code => $value)
		{
			if (preg_match('/^E_/ui', $code) && $value == $errno)
				return str_replace('_', ' ', preg_replace('/^E_/ui', '', $code));
		}
	}

	public function exceptionHandler(Exception $e) {
		$string = $this->createLogMessage('EXCEPTION', $e->getMessage(), $e->getFile(), $e->getLine());

		error_log($string);
	}

	public function errorHandler($errno, $errstr, $errfile, $errline) {
		if (!(error_reporting() & $errno)) {
			return;
		}
		$string = $this->createLogMessage($this->getErrorName($errno), $errstr, $errfile, $errline);

		error_log($string);
	}

	public function handleExceptions() {
		set_exception_handler(array($this, 'exceptionHandler'));
	}

	public function handleErrors() {
		set_error_handler(array($this, 'errorHandler'));
	}

	public function freeErrors() {
		restore_error_handler();
	}

	public function freeExceptions() {
		restore_exception_handler();
	}

}
