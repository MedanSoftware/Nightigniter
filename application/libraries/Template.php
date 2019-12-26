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

	public function __construct()
	{
		$this->ci =& get_instance();
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
}

/* End of file Template.php */
/* Location: ./application/libraries/Template.php */