<?php
namespace Library\Utility;
/**
 * @class Library.Utility.Security
 */
class Security {

	private static $cacheSetter = 'apc_store';
	private static $cacheGetter = 'apc_fetch';
	private static $cacheRemover = 'apc_delete';

	public static function setCacheSetter($callback) {
		self::$cacheSetter = $callback;
	}

	public static function setCacheGetter($callback) {
		self::$cacheGetter = $callback;
	}

	public static function setCacheRemover($callback) {
		self::$cacheRemover = $callback;
	}

	public static function setCacheHandler($object) {
		self::setCacheGetter(array($object, 'get'));
		self::setCacheSetter(array($object, 'set'));
		self::setCacheRemover(array($object, 'delete'));
	}


	/**
	 * Gönderilen anahtar değeri ile ilgili yapılan işlem sayısını kontrol eder.
	 * Eğer belirtilen sınıra ulaşılmışsa ya da aşılmışsa geriye true, aşılmamışsa
	 * geriye false döner.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * if (Security::checkBruteForce('login', 100))
	 * {
	 *	 echo 'Genel hatalı oturum açma işlemi sayısı aşıldı';
	 *	 exit;
	 * }
	 *
	 * $username = array_key_exists('username', $_POST) ? $_POST['username'] : '';
	 * $password = array_key_exists('password', $_POST) ? $_POST['password'] : '';
	 * if (Security::checkBruteForce('login.'.$username, 10))
	 * {
	 *	 echo 'Kullanıcı için hatalı oturum açma işlemi sayısı aşıldı.';
	 *	 exit;
	 * }
	 *
	 * $login_status = login($username, $password);
	 * // Eğer kullanıcı oturum açmışsa
	 * if ($login_status)
	 * {
	 *	 Security::clearBruteForceAttempt('login.'.$username);
	 * }else{
	 *	 Security::updateBruteForceAttempt('login', 60 * 5);
	 *	 Security::updateBruteForceAttempt('login.'.$username, 60*5);
	 * }
	 * ?>
	 * </code>
	 *
	 * @param string $key
	 * @param integer $total_attempt_count
	 * @return boolean
	 */
	public static function checkBruteForce($key, $total_attempt_count = 10) {
		$apc_key = "{$_SERVER['SERVER_NAME']}~{$key}:{$_SERVER['REMOTE_ADDR']}";
		$attempts = (int) call_user_func(self::$cacheGetter, $apc_key);
		return $attempts >= $total_attempt_count;
	}

	/**
	 * Gönderilen anahtar değeri için kayıt altına alınan işlem sayısı değerini
	 * arttırır. Kayıt altında tutulan değer zaman aşımı değerine göre saklanır.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * if (Security::checkBruteForce('login', 100))
	 * {
	 *	 echo 'Genel hatalı oturum açma işlemi sayısı aşıldı';
	 *	 exit;
	 * }
	 *
	 * $username = array_key_exists('username', $_POST) ? $_POST['username'] : '';
	 * $password = array_key_exists('password', $_POST) ? $_POST['password'] : '';
	 * if (Security::checkBruteForce('login.'.$username, 10))
	 * {
	 *	 echo 'Kullanıcı için hatalı oturum açma işlemi sayısı aşıldı.';
	 *	 exit;
	 * }
	 *
	 * $login_status = login($username, $password);
	 * // Eğer kullanıcı oturum açmışsa
	 * if ($login_status)
	 * {
	 *	 Security::clearBruteForceAttempt('login.'.$username);
	 * }else{
	 *	 Security::updateBruteForceAttempt('login', 60 * 5);
	 *	 Security::updateBruteForceAttempt('login.'.$username, 60*5);
	 * }
	 * ?>
	 * </code>
	 *
	 * @param string $key
	 * @param integer $timeout
	 * @return void
	 */
	public static function updateBruteForceAttempt($key, $timeout = 600) {
		$apc_key = "{$_SERVER['SERVER_NAME']}~{$key}:{$_SERVER['REMOTE_ADDR']}";
		$attempts = (int) call_user_func(self::$cacheGetter, $apc_key);
		call_user_func(self::$cacheSetter, $apc_key, $attempts + 1, $timeout);
	}

	/**
	 * Anahtar değeri için kayıt altına alınan işlem sayısını siler.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * if (Security::checkBruteForce('login', 100))
	 * {
	 *	 echo 'Genel hatalı oturum açma işlemi sayısı aşıldı';
	 *	 exit;
	 * }
	 *
	 * $username = array_key_exists('username', $_POST) ? $_POST['username'] : '';
	 * $password = array_key_exists('password', $_POST) ? $_POST['password'] : '';
	 * if (Security::checkBruteForce('login.'.$username, 10))
	 * {
	 *	 echo 'Kullanıcı için hatalı oturum açma işlemi sayısı aşıldı.';
	 *	 exit;
	 * }
	 *
	 * $login_status = login($username, $password);
	 * // Eğer kullanıcı oturum açmışsa
	 * if ($login_status)
	 * {
	 *	 Security::clearBruteForceAttempt('login.'.$username);
	 * }else{
	 *	 Security::updateBruteForceAttempt('login', 60 * 5);
	 *	 Security::updateBruteForceAttempt('login.'.$username, 60*5);
	 * }
	 * ?>
	 * </code>
	 *
	 * @param string $key
	 * @return void
	 */
	public static function clearBruteForceAttempt($key) {
		$apc_key = "{$_SERVER['SERVER_NAME']}~{$key}:{$_SERVER['REMOTE_ADDR']}";
		call_user_func(self::$cacheRemover, $apc_key);
	}

	/**
	 * Gönderilen anahtar için csrf kontrol jetonu üretir.
	 *
	 * @param string $str
	 * @return string
	 */
	public static function _createCsrfTokenKey($key) {
		return md5(md5($key . time()));
	}

	/**
	 * Gönderilen anahtar için csrf kontrol jetonu üretir ve saklar.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * $csrf_token = Security::generateCsrfToken('account_form');
	 *
	 * if ($_SERVER['REQUEST_METHOD'] == 'POST')
	 * {
	 *	 $form_csrf_token = array_key_exists('csrf_token', $_POST) ? $_POST['csrf_token'] : '';
	 *	 if (Security::checkCsrfToken('account_form', $form_csrf_token))
	 *	 {
	 *		 echo 'Token doğru.';
	 *	 }else{
	 *		 echo 'Token doğru değil.';
	 *	 }
	 * }
	 * ?>
	 * <form method="POST" action="page.php">
	 *	 <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
	 *	 <button type="submit">Send</button>
	 * </form>
	 * </code>
	 *
	 * @param string $key
	 * @return string
	 */
	public static function generateCsrfToken($key) {
		$token = self::_createCsrfTokenKey($key);
		$_SESSION['csrf.token.' . $key] = $token;
		return $token;
	}

	/**
	 * Üretilen csrf güvenlik jetonunun doğruluğunu kontrol eder.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * $csrf_token = Security::generateCsrfToken('account_form');
	 *
	 * if ($_SERVER['REQUEST_METHOD'] == 'POST')
	 * {
	 *	 $form_csrf_token = array_key_exists('csrf_token', $_POST) ? $_POST['csrf_token'] : '';
	 *	 if (Security::checkCsrfToken('account_form', $form_csrf_token))
	 *	 {
	 *		 echo 'Token doğru.';
	 *	 }else{
	 *		 echo 'Token doğru değil.';
	 *	 }
	 * }
	 * ?>
	 * <form method="POST" action="page.php">
	 *	 <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
	 *	 <button type="submit">Send</button>
	 * </form>
	 * </code>
	 *
	 * @param string $key
	 * @param string $token
	 * @return boolean
	 */
	public static function checkCsrfToken($key, $token) {
		$session_token = array_key_exists('csrf.token.' . $key, $_SESSION) ? $_SESSION['csrf.token.' . $key] : '';
		if ($session_token == $token) {
			unset($_SESSION['csrf.token.' . $key]);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Metni anahtar ile şifreler ve şifrelenen metni base64 formatında geri
	 * döndürür. Şifrelenen metnin çözülebilmesi için decrypt metodu kullanılır.
	 * Metin yerine objeleri şifrelemek için metni göndermeden önce serialize
	 * fonksiyonundan geçirebilirsiniz.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * $secret_data = Security::encrypt('key', 'data');
	 *
	 * // Çözülen şifreli metin.
	 * echo Security::decrypt('key', $secret_data);
	 *
	 * $data = array(
	 *	 'a' => 1,
	 *	 'b' => 2
	 * );
	 * $secret_data = Security::encrypt('key', serialize($data));
	 *
	 * // Çözülen şifreli obje
	 * var_export(serialize(Security::decrypt('key', $secret_data)));
	 * ?>
	 * </code>
	 *
	 * @param string $key
	 * @param string $text
	 * @return string
	 */
	public static function encrypt($key, $text) {
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $text, MCRYPT_MODE_CBC, md5(md5($key))));
	}

	/**
	 * Bir anahtar ile şifrelenen metni çözer ve geriye çözülmüş metni döner.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * $secret_data = Security::encrypt('key', 'data');
	 *
	 * // Çözülen şifreli metin.
	 * echo Security::decrypt('key', $secret_data);
	 *
	 * $data = array(
	 *	 'a' => 1,
	 *	 'b' => 2
	 * );
	 * $secret_data = Security::encrypt('key', serialize($data));
	 *
	 * // Çözülen şifreli obje
	 * var_export(serialize(Security::decrypt('key', $secret_data)));
	 * ?>
	 * </code>
	 *
	 * @param string $key
	 * @param string $data
	 * @return string
	 */
	public static function decrypt($key, $data) {
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($data), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	}

	/**
	 * Gönderilen anahtara göre mevcut oturum id sini şifreler. Metod session_start
	 * fonksiyonundan sonra çalıştırıldığında bir anlam kazanır. Geriye dönen
	 * veri base64 formatındadır.
	 *
	 * Kullanım:
	 * <code>
	 * <?php
	 * $secret_session_id = Security::createSecretSessionId('upload', 60 * 5);
	 *
	 * if (Security::checkSecretSessionId('upload', $secret_session_id) !== false)
	 * {
	 *	 echo 'secret data is safe';
	 * }else{
	 *	 echo 'secret data is not safe';
	 * }
	 * ?>
	 * </code>
	 *
	 * @param string $key
	 * @param integer $timeout
	 * @return string
	 */
	public static function createSecretSessionId($key, $timeout) {
		$session_id = serialize(array(
			'session_id' => session_id(),
			'last_access_time' => time() + $timeout
		));
		return base64_encode(self::encrypt($key, $session_id));
	}

	/**
	 * Verinin gizli oturum id si olup olmadığını kontrol eder. Eğer işlem olumlu
	 * ise geriye tanımlanan session_id değeri döner. İşlem olumsuzsa false değeri
	 * döner.
	 *
	 * <code>
	 * <?php
	 * $secret_session_id = Security::createSecretSessionId('upload', 60 * 5);
	 *
	 * if (Security::checkSecretSessionId('upload', $secret_session_id) !== false)
	 * {
	 *	 echo 'secret data is safe';
	 * }else{
	 *	 echo 'secret data is not safe';
	 * }
	 * ?>
	 * </code>
	 *
	 * @param string $key
	 * @param string $data
	 * @return mixed
	 */
	public static function checkSecretSessionId($key, $data) {
		$data = self::decrypt($key, base64_decode($data));
		$data = @unserialize($data);

		if (is_array($data)
				&& array_key_exists('session_id', $data)
				&& array_key_exists('last_access_time', $data)
				&& $data['last_access_time'] > time()) {
			return $data['session_id'];
		} else {
			return false;
		}
	}

	/**
	 * Gönderilen metnin içindeki sql/xss saldırı cümleciklerini tespit eder.
	 *
	 * Kullanım:
	 * <code>
	 * if(Security::checkInjection($value)) {
	 *	 error_log('Possible SQL/XSS injection attack detected with the request '.$value);
	 * }
	 * </code>
	 *
	 * @author fatihkadirakin.com
	 * @param string $value
	 * @return boolean
	 */
	public static function checkInjection($value) {
		$injections = array(
			//SQL injections
			'/(\%27)|(\')|(\-\-)|(\%23)|(#)/im',
			'/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/im',
			'/\d*((\%6F)|o|(\%4F))((\%72)|r|(\%52)).*(=).*/im',
			'/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/im',
			'/((\%27)|(\'))union/im',
			'/(exec|call)(\s|\+)+(s|x)p\w+/im',
			//XSS Injection
			'/((\%3C)|<)((\%2F)|\/)*[a-z0-9\%]+((\%3E)|>)/im',
			'/((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/im',
			'/((\%3C)|<)[^\n]+((\%3E)|>)/i'
		);

		foreach ($injections as $regexp)
		{
			if (preg_match($regexp, $value)) {
				return true;
			}
		}

		return false;
	}
}