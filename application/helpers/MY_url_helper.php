<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage URL
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('get_http_build_query'))
{
	/**
	 * HTTP build query
	 * 
	 * @return string
	 */
	function get_http_build_query()
	{
		return (!empty(get_instance()->input->get()))?'?'.http_build_query(get_instance()->input->get()):FALSE;
	}
}

if (!function_exists('get_current_url'))
{
	/**
	 * Current URL
	 * 
	 * @param  boolean $http_build_query with the query params
	 * @return string
	 */
	function get_current_url($http_build_query = true)
	{
		return ($http_build_query)?base_url(uri_string().config_item('url_suffix')).get_http_build_query():base_url(uri_string().config_item('url_suffix'));
	}
}

if (!function_exists('module_link'))
{
	/**
	 * Module link
	 * 
	 * @param  string $path append the path
	 * @return string
	 */
	function module_link($path = null)
	{
		$path = (!empty($path))?$path:'';
		return reduce_double_slashes(base_url(get_instance()->router->module.'/'.$path));
	}
}

/* End of file MY_url_helper.php */
/* Location : ./application/helpers/MY_url_helper.php */