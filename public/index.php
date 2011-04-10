<?php
use Library\Common\Autoloader;

define('PUBLIC_DIR', __DIR__);
define('ROOT_DIR', realpath(PUBLIC_DIR.'/../').'/');

require_once ROOT_DIR . '/Library/Common/Autoloader.php';

try {

	Autoloader::register();
	//Autoloader::setPathCache(\Library\Cache\Connector::getInstance('APC'));

	//Uygulamayı başlat.
	//$asd = 'a';
	$application = new \Application\Bootstrap();
	$application->init();

} catch (\Library\Common\Exception $e) { //Kütüphaneden gelen exceptionları yakala

	\Library\Router::run('/error/'.$e->getCode().'/'.urlencode($e->getMessage()));

} catch (\Exception $e) { //PHP'den gelen exceptionları yakala

	echo 'PHP: '.$e->__toString();

}