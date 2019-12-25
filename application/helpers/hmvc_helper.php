<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage HMVC
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('call_module'))
{
	/**
	 * Call module
	 * 
	 * @return mixed
	 */
	function call_module($module, $params = array())
	{
		return modules::run($module, $params);
	}
}

if (!function_exists('is_modular'))
{
	/**
	 * Is modular
	 * @return boolean
	 */
	function is_modular()
	{
		return method_exists(get_instance()->router, 'fetch_module');
	}
}

if (!function_exists('hmvc_get_module'))
{
	/**
	 * HMVC get module
	 * 
	 * @return string|null
	 */
	function hmvc_get_module()
	{
		if (method_exists(get_instance()->router, 'fetch_module'))
		{
			return get_instance()->router->fetch_module();
		}
	}
}

if (!function_exists('hmvc_get_class'))
{
	/**
	 * HMVC get class
	 * 
	 * @return string
	 */
	function hmvc_get_class()
	{
		if (method_exists(get_instance()->router, 'fetch_module'))
		{
			return get_instance()->router->fetch_class();
		}
	}
}

if (!function_exists('hmvc_get_method'))
{
	/**
	 * HMVC get method
	 * 
	 * @return string
	 */
	function hmvc_get_method()
	{
		if (method_exists(get_instance()->router, 'fetch_module'))
		{
			return get_instance()->router->fetch_method();
		}
	}
}


/* End of file hmvc_helper.php */
/* Location : ./application/helpers/hmvc_helper.php */