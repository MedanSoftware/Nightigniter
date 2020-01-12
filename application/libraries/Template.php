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
		$data['themes']['assets_url'] = (isset(APP_CONFIG['assets']))?APP_CONFIG['assets']['url']:base_url(backslash_to_slash(str_replace(FCPATH, '', ASSETS_PATH)));
		$data['themes']['memory_usage'] = $this->ci->benchmark->memory_usage();
		$data['themes']['elapsed_time'] = $this->ci->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
		$this->ci->template_engine->render($page, array_merge($content_data, $data));
	}

	/**
	 * User template
	 * 
	 * @param  string $page
	 * @param  array  $content_data
	 */
	public function user($page = null, $content_data = array())
	{
		$this->ci->template_engine->initialize(array(
			'view_paths' => array(
				THEMES_PATH.'/user/'.active_theme('user').'/views' => THEMES_PATH.'/user/'.active_theme('user').'/views'
			),
			'adapter' => 'twig'
		));

		$data['themes']['uri'] = base_url(backslash_to_slash(str_replace(FCPATH, '', THEMES_PATH).'/user/'.active_theme('user')));
		$data['themes']['assets_url'] = (isset(APP_CONFIG['assets']))?APP_CONFIG['assets']['url']:base_url(backslash_to_slash(str_replace(FCPATH, '', ASSETS_PATH)));
		$data['themes']['memory_usage'] = $this->ci->benchmark->memory_usage();
		$data['themes']['elapsed_time'] = $this->ci->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
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
		$data['themes']['assets_url'] = (isset(APP_CONFIG['assets']))?APP_CONFIG['assets']['url']:base_url(backslash_to_slash(str_replace(FCPATH, '', ASSETS_PATH)));
		$data['themes']['memory_usage'] = $this->ci->benchmark->memory_usage();
		$data['themes']['elapsed_time'] = $this->ci->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
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
	 * Get config file
	 * 
	 * @param  string  $module
	 * @param  string  $filename
	 * @param  boolean $regex
	 * @param  boolean $decode
	 * @return mixed
	 */
	public function config_file($module = null, $filename = '/[t|T]heme\.json/', $regex = true, $decode = true)
	{
		$path = THEMES_PATH.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.active_theme($module).DIRECTORY_SEPARATOR;

		if ($regex)
		{
			$config_file = preg_grep($filename, directory_map(THEMES_PATH.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.active_theme($module), 1, TRUE));

			if ($config_file)
			{
				$config_file = array_shift($config_file);
			}
			else
			{
				$config_file = false;
			}
		}
		else
		{
			$config_file = $filename;
		}

		if ($config_file && file_exists($path.$config_file))
		{
			$file_content = file_get_contents($path.$config_file);

			return ($decode)?json_decode($file_content):$file_content;
		}
	}
}

/* End of file Template.php */
/* Location: ./application/libraries/Template.php */