<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Array
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('array_object_find_value'))
{
	/**
	 * Find array|object value
	 * 
	 * @param  array   $array
	 * @param  string  $key_name
	 * @param  string  $key_value
	 * @param  boolean $force_equal
	 * @return mixed
	 */
	function array_object_find_value($array = array(), $key_name = null, $key_value = null, $force_equal = false)
	{
		foreach ($array as $key => $value)
		{
			if ($force_equal)
			{
				if (is_array($value))
				{
					if (isset($value[$key_name]))
					{
						if ($value[$key_name] === $key_value)
						{
							return $key;
						}
					}
					else
					{
						continue;
					}
				}
				elseif (is_object($value))
				{
					if (isset($value->{$key_name}))
					{
						if ($value->{$key_name} === $key_value)
						{
							return $key;
						}
						else
						{
							continue;
						}
					}
				}
				else
				{
					continue;
				}
			}
			else
			{
				if (is_array($value))
				{
					if (isset($value[$key_name]))
					{
						if ($value[$key_name] == $key_value)
						{
							return $key;
						}
					}
					else
					{
						continue;
					}
				}
				elseif (is_object($value))
				{
					if (isset($value->{$key_name}))
					{
						if ($value->{$key_name} == $key_value)
						{
							return $key;
						}
						else
						{
							continue;
						}
					}
				}
				else
				{
					continue;
				}
			}
		}

		return FALSE;
	}
}

if (!function_exists('return_if_exists'))
{
	/**
	 * Return if exists
	 * 
	 * @param  array|object $data   array or object
	 * @param  string $key      	key of array or object
	 * @param  mixed $onEmpty  		return default value with string or callback function
	 * @param  mixed $onExists 		return custom value with string or callback function
	 * @return mixed
	 */
	function return_if_exists($data, $key = '', $onEmpty = null, $onExists = null, $checkEmptyValue = false)
	{
		if (in_array(gettype($data), ['array','object']) && !empty($data))
		{
			$data = (array) $data;

			if (stripos($key, '.') === FALSE)
			{
				if (isset($data[$key]))
				{
					if ($checkEmptyValue)
					{
						if (!empty($data[$key]))
						{
							return (is_callable($onExists))?call_user_func($onExists,$data[$key]):$data[$key];
						}
						else
						{
							return (is_callable($onEmpty))?call_user_func($onEmpty,$data):$onEmpty;
						}
					}
					else
					{
						return (is_callable($onExists))?call_user_func($onExists,$data[$key]):$data[$key];
					}
				}
				else
				{
					return (is_callable($onEmpty))?call_user_func($onEmpty,$data):$onEmpty;
				}
			}
			else
			{
				if (isset($data[$key]))
				{
					return return_if_exists($data,$key,$onExists,$onEmpty,$checkEmptyValue);
				}
				else
				{
					$_keys = explode('.',$key);
					$_data = $data;

					for ($i=0;$i<count($_keys);$i++)
					{
						if (isset($_data[$_keys[$i]]))
						{
							if ($i == (count($_keys)-1))
							{
								return (is_callable($onExists))?call_user_func($onExists,$_data[$_keys[$i]]):$_data[$_keys[$i]];
							}
							else
							{
								$_data = (array) $_data[$_keys[$i]];
								continue;
							}
						}
						else
						{
							return (is_callable($onEmpty))?call_user_func($onEmpty,$_data):$onEmpty;
						}
					}

				}
			}
		}
	}
}

/* End of file MY_array_helper.php */
/* Location : ./application/helpers/MY_array_helper.php */