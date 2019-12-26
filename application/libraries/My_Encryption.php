<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage My Encryption
 * @category Library
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

class My_Encryption
{
	protected $ci;

	protected $encryption_key;

	protected $encrypt_method;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->encryption_key = config_item('encryption_key');
		$this->encrypt_method = 'AES-256-CBC';
	}

	/**
	 * Encrypt String.
	 * 
	 * @param string $string 	The original string to be encrypt.
	 * @param string $key 		The key.
	 * @return string 			Return encrypted string.
	 * 
	 * @link https://stackoverflow.com/questions/41222162/encrypt-in-php-openssl-and-decrypt-in-javascript-cryptojs Reference.
	 */
	public function encrypt($string, $key = null)
	{
		$key = (!empty($key))?$key:$this->encryption_key;
		$ivLength = openssl_cipher_iv_length($this->encrypt_method);
		$iv = openssl_random_pseudo_bytes($ivLength);

		$salt = openssl_random_pseudo_bytes(256);
		$iterations = 999;
		$hashKey = hash_pbkdf2('sha512', $key, $salt, $iterations, ($this->encrypt_method_length() / 4));
		$encryptedString = openssl_encrypt($string, $this->encrypt_method, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
		$encryptedString = base64_encode($encryptedString);
		unset($hashKey);

		$output = ['ciphertext' => $encryptedString, 'iv' => bin2hex($iv), 'salt' => bin2hex($salt), 'iterations' => $iterations];
		unset($encryptedString, $iterations, $iv, $ivLength, $salt);
		
		return base64_encode(json_encode($output));
	}

	/**
	 * Decrypt String.
	 * 
	 * @param string $encryptedString	The encrypted string that is base64 encode.
	 * @param string $key 				The key.
	 * @return mixed 					Return original value
	 * 
	 * @link https://stackoverflow.com/questions/41222162/encrypt-in-php-openssl-and-decrypt-in-javascript-cryptojs Reference.
	 */
	public function decrypt($encryptedString, $key = null)
	{
		$key = (!empty($key))?$key:$this->encryption_key;
		$json = json_decode(base64_decode($encryptedString), true);

		try
		{
			$salt = hex2bin($json["salt"]);
			$iv = hex2bin($json["iv"]);
		}
		catch (Exception $e)
		{
			return null;
		}

		$cipherText = base64_decode($json['ciphertext']);
		$iterations = intval(abs($json['iterations']));

		if ($iterations <= 0)
		{
			$iterations = 999;
		}

		$hashKey = hash_pbkdf2('sha512', $key, $salt, $iterations, ($this->encrypt_method_length() / 4));
		unset($iterations, $json, $salt);

		$decrypted= openssl_decrypt($cipherText , $this->encrypt_method, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
		unset($cipherText, $hashKey, $iv);

		return $decrypted;
	}

	/**
	 * Get encrypt method length number (128, 192, 256).
	 * 
	 * @return integer
	 */
	protected function encrypt_method_length()
	{
		$number = filter_var($this->encrypt_method, FILTER_SANITIZE_NUMBER_INT);
		return intval(abs($number));
	}

	/**
	 * Set encryption method.
	 * 
	 * @param string $cipherMethod
	 * 
	 * @link http://php.net/manual/en/function.openssl-get-cipher-methods.php Available methods.
	 */
	public function setCipherMethod($cipherMethod)
	{
		$this->encrypt_method = $cipherMethod;
	}
}

/* End of file My_Encryption.php */
/* Location : ./application/libraries/My_Encryption.php */