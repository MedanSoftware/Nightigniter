<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Validation
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('valid_hostname'))
{
	/**
	 * Valid hostname
	 * 
	 * @param  string $hostname
	 * @return boolean
	 */
	function valid_hostname($hostname = null)
	{
		$validator = new Zend\Validator\Hostname();
		return $validator->isValid($hostname);
	}
}

if (!function_exists('valid_json'))
{
	/**
	 * Valid JSON
	 * 
	 * @param  string $json
	 * @return boolean
	 */
	function valid_json($json = null)
	{
		json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
	}	
}

if (!function_exists('valid_image_url'))
{
	/**
	 * Valid image URL
	 * 
	 * @param  string $url
	 * @return boolean
	 */
	function valid_image_url($url)
	{
	    $headers = get_headers($url);

	    if (substr($headers[0], 9, 3) == 200)
	    {
	   	   	$size = getimagesize($url);
	        return (strtolower(substr($size['mime'], 0, 5)) == 'image' ? true : false);
	    }

	    return FALSE;
	}	
}

if (!function_exists('is_used_port'))
{
	/**
	 * Is used port
	 * 
	 * @param  string  $host  target host want to check the port
	 * @param  integer $port  the port number want to check
	 * @return boolean
	 */
	function is_used_port($host, $port)
	{
		$fp = @fsockopen($host, $port, $errno, $errstr, 30);

		if (!$fp)
		{
			return false;
		}
		else
		{
			fclose($fp);
			return true;
		}
	}
}


/* End of file validation_helper.php */
/* Location : ./application/helpers/validation_helper.php */