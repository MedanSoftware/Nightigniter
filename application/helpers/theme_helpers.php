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

if (!function_exists('active_theme'))
{
	/**
	 * Active Theme
	 * 
	 * @param  string $module module name
	 * @param  string $return array|object|value
	 * @return string
	 */
	function active_theme($module, $return = 'value')
	{
		if ($module == 'installation')
		{
			return '/'.$module.'/';
		}
		else
		{
			if (db_has_table('setting'))
			{
				$active_theme = get_instance()->db->get_where('setting', array('group' => 'theme', 'name' => $module));
				if ($active_theme->num_rows()> 0)
				{
					switch ($return)
					{
						case 'array':
							return $active_theme->row_array();
						break;

						case 'object':
							return $active_theme->row();
						break;
						
						default:
							if (!empty($active_theme->row()->value)) 
							{
								/* active theme */
								return $active_theme->row()->value;
							}
							else
							{
								/* default theme */
								return 'default';
							}
						break;
					}
				}
				else
				{
					get_instance()->db->insert('setting', array(
						'group' => 'theme',
						'name' => $module,
						'value' => 'default'
					));

					return 'default';
				}
			}
		}
	}
}

/* End of file theme_helpers.php */
/* Location : ./application/helpers/theme_helpers.php */