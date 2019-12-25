<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Security
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('csrf_token'))
{
	/**
	 * CSRF token name
	 * 
	 * @return string
	 */
	function csrf_token()
	{
		return get_instance()->security->get_csrf_token_name();
	}	
}

if (!function_exists('csrf_hash'))
{
	/**
	 * CSRF token hash
	 * 
	 * @return string
	 */
	function csrf_hash()
	{
		return get_instance()->security->get_csrf_hash();	
	}
}

/* End of file MY_security_helper.php */
/* Location : ./application/helpers/MY_security_helper.php */