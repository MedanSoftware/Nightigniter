<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Application
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('read_config_file'))
{
	/**
	 * Read config file
	 *
	 * @param  string $config_file
	 */
	function read_config_file(string $config_file = APP_CONFIG_FILE)
	{
		return (file_exists($config_file))?json_decode(file_get_contents($config_file),TRUE):array();
	}
}

if (!function_exists('write_config_file'))
{
	/**
	 * Write config file
	 * 
	 * @param  array   $data
	 * @param  array   $old_data
	 * @param  string  $config_file
	 * @param  boolean $merge_data
	 */
	function write_config_file(array $data = array(), array $old_data = array(), $config_file = APP_CONFIG_FILE, $merge_data = true)
	{
		$content = $data;

		if ($merge_data)
		{
			if (!empty($old_data))
			{
				$content = array_merge($old_data, $data);
			}
		}

		return file_put_contents($config_file, json_encode($content, JSON_PRETTY_PRINT));
	}
}

if (!function_exists('get_class_methods'))
{
	/**
	 * Get methods of class
	 * 
	 * @param  object $class class object
	 * @return array
	 */
	function get_class_methods($class)
	{
		$ReflectionClass = new ReflectionClass($class);
		$methods = array();

		foreach ($ReflectionClass->getMethods() as $method)
		{
			if ($method->class == $class)
			{
				$methods[] = $method->name;
			}
		}

		return $methods;
	}
}

if (!function_exists('has_nodeigniter'))
{
	/**
	 * Has nodeigniter
	 * 
	 * @return boolean
	 */
	function has_nodeigniter()
	{
		return (isset(APP_CONFIG['nodeigniter']));
	}
}

if (!function_exists('model'))
{
	/**
	 * Model
	 * 
	 * @param  string $name
	 * @return object
	 */
	function model($name)
	{
		$ci =& get_instance();
		$ci->load->model($name);

		return $ci->{$name};
	}
}

if (!function_exists('parsedown'))
{
	/**
	 * Parse markdown
	 * 
	 * @param  string $text markdown syntax
	 * @param  string $mode one of : line or text
	 * @return string
	 */
	function parsedown($text, $mode = 'line')
	{
		$Parsedown = new Parsedown();
		$Parsedown->setSafeMode(true);
		$Parsedown->setMarkupEscaped(true);

		if ($mode == 'line')
		{
			return $Parsedown->line($text);
		}
		else
		{
			return $Parsedown->text($text);
		}
	}
}

if (!function_exists('unused_hostname'))
{
	/**
	 * Check is not used hostname
	 * 
	 * @param  string $hostname
	 * @return boolean
	 */
	function unused_hostname($hostname = null)
	{
		if (!empty($hostname))
		{
			exec('ping '.$hostname, $output, $result);
			return ($result == 0);
		}

		return FALSE;
	}
}

if (!function_exists('return_if_not_empty'))
{
	/**
	 * Return if not empty
	 * 
	 * @param  string $value         value want to check
	 * @param  mixed $default_value  return default value on empty with string or callback function
	 * @return mixed
	 */
	function return_if_not_empty($value, $default_value = null)
	{
		if (!empty($value))
		{
			return $value;
		}
		else
		{
			if (is_callable($default_value))
			{
				return call_user_func($default_value, $value);
			}
			else
			{
				return $default_value;
			}
		}
	}
}

if (!function_exists('jsonp_decode'))
{
	/**
	 * JSONP decode
	 * 
	 * @param  string  $jsonp
	 * @param  boolean $assoc
	 * @return object
	 */
	function jsonp_decode($jsonp, $assoc = false)
	{
		if ($jsonp[0] !== '[' && $jsonp[0] !== '{')
		{
			$jsonp = substr($jsonp, strpos($jsonp, '('));
		}

		return json_decode(trim($jsonp,'();'), $assoc);
	}
}

/* End of file application_helper.php */
/* Location : ./application/helpers/application_helper.php */