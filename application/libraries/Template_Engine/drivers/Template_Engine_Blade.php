<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Template Engine Blade
 * @category Library
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

require (__DIR__.'/../class/Blade.php');

class Template_Engine_Blade
{
	private $ci;

	private $engine;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
	}

	/**
	 * Render View
	 * 
	 * @param  string $page View File
	 * @param  array  $data Content Data
	 * @return Output View
	 */
	public function render($page, $data = array())
	{
		$this->ci->output->_display($this->engine->render($page, $data));
	}

	/**
	 * Decorate
	 */
	public function decorate($parent)
	{
		$this->engine = new CiMS\TemplateEngine\Blade($parent->view_paths, $parent->cache_path);
	}
}

/* End of file Template_Engine_Blade.php */
/* Location : ./application/libraries/Template_engine/drivers/Template_Engine_Blade.php */