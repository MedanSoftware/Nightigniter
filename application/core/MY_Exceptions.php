<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage MY_Exceptions
 * @category Libraries
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

require_once (APPPATH.'libraries/Template_Engine/class/Twig.php');

class MY_Exceptions extends CI_Exceptions
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * General Error Page
	 * 
	 * @param  string  $heading     Page heading
	 * @param  string  $message     Error message
	 * @param  string  $template    Template name
	 * @param  integer $status_code (default : 500)
	 * @return string  Error page output
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		if (class_exists('MX_Controller') OR class_exists('CI_Controller'))
		{
			$module = null;

			$ci =& get_instance();

			$ci->load->library('template');

			if (!empty($ci->router->fetch_module()))
			{
				$module = ($ci->router->fetch_module() !== 'api')?$ci->router->fetch_module():$ci->router->default_controller;
			}
			else
			{
				$module = $ci->router->default_controller;
			}

			$theme_config = $ci->template->config_file($module);

			if (isset($theme_config->error_view_path))
			{
				set_status_header($status_code);
				$data['status_code'] 	= $status_code;
				$data['heading']		= $heading;
				$data['message'] 		= (is_cli()?"\t":"<p>").(is_array($message)?implode((is_cli()?"\n\t":"</p><p>"),$message):$message).(is_cli()?"":"</p>");

				$view_engine = new \Nightigniter\TemplateEngine\Twig(array(
					THEMES_PATH.'/'.$module.'/'.active_theme($module).'/views' => THEMES_PATH.'/'.$module.'/'.active_theme($module).'/views'
				), APPPATH.'cache');

				try 
				{
					exit($view_engine->render($theme_config->error_view_path.DIRECTORY_SEPARATOR.(is_cli()?'cli':'html').DIRECTORY_SEPARATOR.$template, $data));
				}
				catch (Exception $e)
				{
					return CI_Exceptions::show_error($heading, $message, $template, $status_code);
				}
			}
			else
			{
				return CI_Exceptions::show_error($heading, $message, $template, $status_code);
			}
		}
		else
		{
			return CI_Exceptions::show_error($heading, $message, $template, $status_code);
		}
	}

	/**
	 * Show Exception
	 * 
	 * @param  object $exception exception class
	 * @return string Error page output
	 */
	public function show_exception($exception)
	{
		if (class_exists('MX_Controller') OR class_exists('CI_Controller'))
		{
			$module = null;

			$ci =& get_instance();

			$ci->load->library('template');

			if (!empty($ci->router->fetch_module()))
			{
				$module = ($ci->router->fetch_module() !== 'api')?$ci->router->fetch_module():$ci->router->default_controller;
			}
			else
			{
				$module = $ci->router->default_controller;
			}

			$theme_config = $ci->template->config_file($module);

			if (isset($theme_config->error_view_path))
			{
				$data['exception'] = $exception;

				$view_engine = new \Nightigniter\TemplateEngine\Twig(array(
					THEMES_PATH.'/'.$module.'/'.active_theme($module).'/views' => THEMES_PATH.'/'.$module.'/'.active_theme($module).'/views'
				), APPPATH.'cache');

				try 
				{
					exit($view_engine->render($theme_config->error_view_path.DIRECTORY_SEPARATOR.(is_cli()?'cli':'html').DIRECTORY_SEPARATOR.'error_exception', $data));
				}
				catch (Exception $e)
				{
					return CI_Exceptions::show_exception($exception);
				}
			}
			else
			{
				return CI_Exceptions::show_exception($exception);
			}
		}
		else
		{
			return CI_Exceptions::show_exception($exception);
		}
	}

	/**
	 * Native PHP Error Handler
	 * 
	 * @param  integer 	$severity Error level
	 * @param  string 	$message  Error message
	 * @param  string 	$filepath File path
	 * @param  integer 	$line     Line number
	 * @return void
	 */
	public function show_php_error($severity, $message, $filepath, $line)
	{
		if (class_exists('MX_Controller') OR class_exists('CI_Controller'))
		{
			$module = null;

			$ci =& get_instance();

			$ci->load->library('template');

			if (!empty($ci->router->fetch_module()))
			{
				$module = ($ci->router->fetch_module() !== 'api')?$ci->router->fetch_module():$ci->router->default_controller;
			}
			else
			{
				$module = $ci->router->default_controller;
			}

			$theme_config = $ci->template->config_file($module);

			if (isset($theme_config->error_view_path))
			{
				$filepath = str_replace('\\', '/', $filepath);

				if (FALSE !== strpos($filepath, '/'))
				{
					$x = explode('/', $filepath);
					$filepath = $x[count($x)-2].'/'.end($x);
				}

				$data['heading'] = 'A PHP Error was encountered';
				$data['message'] = $message;
				$data['severity'] = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
				$data['filepath'] = $filepath;
				$data['line'] = $line;

				$view_engine = new \Nightigniter\TemplateEngine\Twig(array(
					THEMES_PATH.'/'.$module.'/'.active_theme($module).'/views' => THEMES_PATH.'/'.$module.'/'.active_theme($module).'/views'
				), APPPATH.'cache');

				try 
				{
					exit($view_engine->render($theme_config->error_view_path.DIRECTORY_SEPARATOR.(is_cli()?'cli':'html').DIRECTORY_SEPARATOR.'error_php', $data));
				}
				catch (Exception $e)
				{
					return CI_Exceptions::show_php_error($severity, $message, $filepath, $line);
				}
			}
			else
			{
				return CI_Exceptions::show_php_error($severity, $message, $filepath, $line);
			}
		}
		else
		{
			return CI_Exceptions::show_php_error($severity, $message, $filepath, $line);
		}
	}
}

/* End of file MY_Exceptions.php */
/* Location: ./application/core/MY_Exceptions.php */