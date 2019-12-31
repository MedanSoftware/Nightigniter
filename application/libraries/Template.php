<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Template
 * @category Library
 * @author Agung Dirgantara <agungmasda29@gmail.com>
*/

class Template
{
	protected $ci;

	protected $module;

	public function __construct()
	{
		$this->ci =& get_instance();
	}

	/**
	* Installation template
	* 
	* @param  string $page
	* @param  array  $content_data
	*/
	public function installation($page = null, $content_data = array())
	{
		$this->ci->template_engine->initialize(array(
			'view_paths' => array(
				THEMES_PATH.'/installation/views' => THEMES_PATH.'/installation/views'
			),
			'adapter' => 'twig'
		));

		$data['themes']['uri'] = base_url(backslash_to_slash(str_replace(FCPATH, '', THEMES_PATH).'/installation/'));
		$data['themes']['assets_uri'] = base_url(backslash_to_slash(str_replace(FCPATH, '', ASSETS_PATH)));
		$this->ci->template_engine->render($page, array_merge($content_data, $data));
	}

	/**
	* Admin template
	* 
	* @param  string $page
	* @param  array  $content_data
	*/
	public function admin($page = null, $content_data = array())
	{
		$this->ci->template_engine->initialize(array(
			'view_paths' => array(
				THEMES_PATH.'/admin/'.active_theme('admin').'/views' => THEMES_PATH.'/admin/'.active_theme('admin').'/views'
			),
			'adapter' => 'twig'
		));

		$data['themes']['uri'] = base_url(backslash_to_slash(str_replace(FCPATH, '', THEMES_PATH).'/admin/'.active_theme('admin')));
		$data['themes']['assets_uri'] = base_url(backslash_to_slash(str_replace(FCPATH, '', ASSETS_PATH)));
		$this->ci->template_engine->render($page, array_merge($content_data, $data));
	}

	/**
	 * Site template
	 * 
	 * @param  string $page
	 * @param  array  $content_data
	 */
	public function site($page = null, $content_data = array())
	{
		$this->ci->template_engine->initialize(array(
			'view_paths' => array(
				THEMES_PATH.'/site/'.active_theme('site').'/views' => THEMES_PATH.'/site/'.active_theme('site').'/views'
			),
			'adapter' => 'twig'
		));

		$data['themes']['uri'] = base_url(backslash_to_slash(str_replace(FCPATH, '', THEMES_PATH).'/site/'.active_theme('site')));
		$data['themes']['assets_uri'] = base_url(backslash_to_slash(str_replace(FCPATH, '', ASSETS_PATH)));
		$this->ci->template_engine->render($page, array_merge($content_data, $data));
	}

	/**
	 * Email template
	 * 
	 * @param  boolean $from_string
	 * @param  string  $template
	 * @param  array   $data
	 */
	public function email($from_string = false, $template = null, $data = array(), $return = false)
	{
		$this->ci->template_engine->initialize(array(
			'view_paths' => array(
				ASSETS_PATH.DIRECTORY_SEPARATOR.'email' => ASSETS_PATH.DIRECTORY_SEPARATOR.'email'
			),
			'adapter' => 'twig'
		));

		if ($from_string)
		{
			return $this->ci->template_engine->get_adapter()->get_engine()->parse_string($template, $data, $return);
		}
		else
		{
			return $this->ci->template_engine->get_adapter()->get_engine()->parse_file($template, $data, $return);
		}
	}

	/**
	 * Config file
	 * 
	 * @param  string $filename
	 * @return array
	 */
	public function config_file($filename = 'theme.json', $module = null, $decode = true)
	{
		$config_file = THEMES_PATH.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.active_theme($module).DIRECTORY_SEPARATOR.$filename;

		if (file_exists($config_file))
		{
			$file_content = file_get_contents($config_file);

			return ($decode)?json_decode($file_content):$file_content;
		}
	}
}

/* End of file Template.php */
/* Location: ./application/libraries/Template.php */