<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Template Engine
 * @category Library
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

require ('class/SEO.php');
require ('vendor/autoload.php');

class Template_Engine extends CI_Driver_Library
{
	public $adapter;

	public $view_paths;

	public $cache_path;

	public $include_seo;

	public $valid_drivers;

	/**
	 * constructor
	 */
	public function __construct($config = array())
	{
		get_instance()->load->helper('url');
		
		$this->adapter = 'twig'; // default adapter

		$this->view_paths = array(APPPATH.'views' => APPPATH.'views'); // default view paths
		
		$this->cache_path = APPPATH.'cache'; // default cache path

		$this->include_seo = TRUE; // include SEO library

		$this->valid_drivers = array('twig', 'blade'); // valid drivers

		$this->initialize($config);
	}

	/**
	 * load child class
	 * 
	 * @param  string $child
	 */
	public function __get($child)
	{
		if (in_array($child, $this->valid_drivers))
		{
			return $this->load_driver($child);
		}
	}

	/**
	 * Initialize Template Engine
	 * 
	 * @param  array  $params
	 * @return Template_engine
	 */
	public function initialize(array $params = array())
	{
		$this->clear();

		foreach ($params as $key => $value)
		{
			if (isset($this->$key))
			{
				$method = 'set_'.$key;

				if (method_exists($this, $method))
				{
					$this->$method($value);
				}
				else
				{
					$this->$key = $value;
				}
			}
		}

		return $this;
	}

	/**
	 * Initialize Template Engine
	 * 
	 * @return Template_Engine
	 */
	public function clear()
	{
		$this->adapter = ''; // clear adapter

		$this->view_paths = array(APPPATH.'views' => APPPATH.'views'); // default view paths

		$this->cache_path = APPPATH.'cache';

		$this->include_seo = TRUE;

		return $this;
	}

	/**
	 * Set Adapter
	 * 
	 * @param string $adapter
	 */
	public function set_adapter($adapter = 'twig')
	{
		if (in_array($adapter, $this->valid_drivers))
		{
			$this->adapter = $adapter;
		}

		return $this;
	}

	/**
	 * Get Adapter
	 * 
	 * @return object
	 */
	public function get_adapter()
	{
		return $this->{$this->adapter};
	}

	/**
	 * Set View Paths
	 * 
	 * @param array $view_paths
	 */
	public function set_view_paths(array $view_paths)
	{
		$this->view_paths = $view_paths;

		return $this;
	}

	/**
	 * Add View Paths
	 * 
	 * @param array $view_paths
	 */
	public function add_view_paths(array $view_paths)
	{
		array_merge($this->view_paths, $view_paths);

		return $this;
	}

	/**
	 * Set Cache Path
	 * 
	 * @param string $cache_path
	 */
	public function set_cache_path(string $cache_path)
	{
		$this->cache_path = $cache_path;

		return $this;
	}

	/**
	 * Build SEO
	 * 
	 * @return SEO
	 */
	public function build_seo()
	{
		if ($this->include_seo)
		{
			$seo = new SEO;

			return $seo
				->meta_tag('http-equiv','Pragma','no-cache')
				->meta_tag('http-equiv','Cache-Control','no-cache')
				->meta_tag('http-equiv','X-UA-Compatible','IE=edge')
				->meta_tag('name','viewport','width=device-width, initial-scale=1.0')
				->meta_tag('name','robots','index,follow')
				->meta_tag('name','distribution','Global')
				->meta_tag('name','rating','General')
				->meta_tag('name','revisit-after','7 days')
				->meta_tag('name','url',current_url())
				->meta_tag('property','og:type','website');
		}
	}

	/**
	 * Render template
	 * 
	 * @param  string  $page   [description]
	 * @param  array   $data   [description]
	 * @return mixed
	 */
	public function render($page, $data = array())
	{
		$data = array_merge_recursive($data, array(
			'themes' => array(
				'seo' => $this->build_seo()
			)
		));

		$this->{$this->adapter}->render($page, $data);
	}
}

/* End of file Template_Engine.php */
/* Location : ./application/libraries/Template_Engine/Template_Engine.php */