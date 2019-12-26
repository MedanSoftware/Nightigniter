<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Mysql Import
 * @category Library
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

class Mysql_import
{
	/**
	 * ci instance
	 * @var object
	 */
	private $ci;

	/**
	 * load database
	 * @var object
	 */
	private $db;

	/**
	 * database group name
	 * @var string
	 */
	private $db_group = 'default';

	/**
	 * path to sql file
	 * @var string
	 */
	private $sql_file;

	/**
	 * errors
	 * @var array
	 */
	private $errors = array();

	public function __construct(array $config = array())
	{
		$this->ci =& get_instance();
		$this->initialize($config);
	}

	/**
	 * Initialize
	 * 
	 * @param  array  $config
	 * @return Mysql_import
	 */
	public function initialize(array $config=array())
	{
		foreach ($config as $key => $value) 
		{
			$method = 'set_'.$key;

			if (method_exists($this, $method))
			{
				$this->$method($value);
			}
		}

		return $this;
	}

	/**
	 * Clear
	 * 
	 * @return Mysql_import
	 */
	public function clear()
	{
		$this->sql_file = '';
		$this->db_group = '';
		$this->errors = array();

		return $this;
	}
	
	/**
	 * Set path to sql file
	 * 
	 * @param string $file path to *.sql file
	 * @return Mysql_import
	 */
	public function set_sql_file($file)
	{
		if (file_exists($file))
		{
			$this->sql_file = $file;
		}
		else
		{
			array_push($this->errors,'file not found : '.$file);
		}

		return $this;
	}

	/**
	 * Set database target
	 * 
	 * @param string $db_group database group name
	 * @return Mysql_import
	 */
	public function set_db_group($db_group)
	{
		if (!file_exists($database_config = APPPATH.'config/'.ENVIRONMENT.'/database.php') && !file_exists($database_config = APPPATH.'config/database.php'))
		{
			show_error('The configuration file database.php does not exist.');
		}

		include($database_config);

		if (isset($db[$db_group]))
		{
			$this->db_group = $db_group;
		}

		$this->db = $this->ci->load->database($this->db_group, TRUE);

		return $this;
	}

	/**
	 * Import sql file
	 * 
	 * @return boolean|array boolean on success & array on error
	 */
	public function run()
	{
		if (count($this->errors) < 1)
		{
			if (!preg_match('/sqlite/', $this->db->platform()))
			{
				$fp = fopen($this->sql_file, 'r');
				$templine = '';

				while (($line = fgets($fp)) !== false)
				{
					if (substr($line, 0, 2) == '--' || $line == '')
					{
						continue;
					}

					$templine .= $line;

					if (substr(trim($line), -1, 1) == ';')
					{
						$this->query($templine);
						$templine = '';
					}
				}

				fclose($fp);

				return TRUE;
			}
			else
			{
				array_push($this->errors, ['sqlite database not supported']);
			}
		}
		else
		{
			return $this->errors;
		}
	}

	/**
	 * Execute query
	 * 
	 * @param  string $query query string
	 * @return boolean
	 */
	private function query($query)
	{
		if (!$this->db->query($query))
		{
			array_push($this->errors,'query error : '.$query);
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Get errors
	 * 
	 * @return array
	 */
	public function get_errors()
	{
		return $this->errors;
	}
}

/* End of file Mysql_import.php */
/* Location: ./application/libraries/Mysql_import.php */