<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Themes
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('amp_html'))
{
	/**
	 * AMP HTML
	 * 
	 * @return boolean
	 */
	function amp_html()
	{
		return filter_var(get_instance()->input->get('amp_html'),FILTER_VALIDATE_BOOLEAN);
	}
}

if (!function_exists('modules_has_theme'))
{
	/**
	 * Get modules has theme
	 * 
	 * @return boolean
	 */
	function modules_has_theme()
	{
		return array_keys($GLOBALS['module_themes']);
	}
}

if (!function_exists('is_module_has_theme'))
{
	/**
	 * Check is module has theme
	 * 
	 * @return boolean
	 */
	function is_module_has_theme($module = null)
	{
		return (!empty($module))?in_array($module, array_keys($GLOBALS['module_themes'])):array_keys($GLOBALS['module_themes']);
	}
}

if (!function_exists('active_theme'))
{
	/**
	 * Active Theme
	 * 
	 * @param  string $module module name
	 * @return string
	 */
	function active_theme($module)
	{
		if ($module == 'installation')
		{
			return '/'.$module.'/';
		}
		else
		{
			$config = read_config_file();

			if (!isset($config['active_theme']))
			{
				$config['active_theme'] = array();
			}

			if (array_key_exists($module, $config['active_theme']))
			{
				return $config['active_theme'][$module];
			}
			else
			{
				write_config_file(array('active_theme' => array_merge(array($module => 'default'), $config['active_theme'])), $config);
				return 'default';
			}
		}
	}
}

/* End of file themes_helper.php */
/* Location : ./application/helpers/themes_helper.php */