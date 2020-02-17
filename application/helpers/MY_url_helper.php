<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage URL
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @param	string	$uri
 * @param   boolean $query_string
 * @param	string	$protocol
 * @return	string
 */
function site_url($uri = '', $query_string = false, $protocol = null)
{
	return get_instance()->config->site_url($uri, $protocol).(filter_var($query_string, FILTER_VALIDATE_BOOLEAN)?get_http_build_query():FALSE);
}


/**
 * Base URL
 *
 * Create a local URL based on your basepath.
 * Segments can be passed in as a string or an array, same as site_url
 * or a URL to a file can be passed in, e.g. to an image file.
 *
 * @param	string	$uri
 * @param   boolean $query_string
 * @param	string	$protocol
 * @return	string
 */
function base_url($uri = '', $query_string = false, $protocol = null)
{
	return get_instance()->config->base_url($uri, $protocol).(filter_var($query_string, FILTER_VALIDATE_BOOLEAN)?get_http_build_query():FALSE);
}

/**
 * Current URL
 * 
 * @param  boolean $query_string
 * @return string
 */
function current_url($query_string = true)
{
	return base_url(uri_string().config_item('url_suffix')).(filter_var($query_string, FILTER_VALIDATE_BOOLEAN)?get_http_build_query():FALSE);
}

if (!function_exists('api_url'))
{
	/**
	 * API URL
	 * 
	 * @param  string $uri
	 * @return string
	 */
	function api_url($uri = '')
	{
		return (!isset(APP_CONFIG['api_url']))?base_url('api/'.$uri):APP_CONFIG['api_url'].'/'.$uri;
	}
}

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