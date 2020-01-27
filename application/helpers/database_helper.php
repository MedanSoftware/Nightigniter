<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Database
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('db_version'))
{
	/**
	 * Database version
	 * 
	 * @param  string $connection Database Group Connection
	 * @return string
	 */
	function db_version($connection = ACTIVE_DATABASE_GROUP)
	{
		$db = get_instance()->load->database($connection ,TRUE);
		return $db->version();
	}
}

if (!function_exists('db_connection'))
{
	/**
	 * Check database connection
	 * 
	 * @param  string $connection Database Group Connection
	 * @return boolean
	 */
	function db_connection($connection = ACTIVE_DATABASE_GROUP)
	{
		$db = get_instance()->load->database($connection ,TRUE);
		return $db->initialize();
	}
}

if (!function_exists('db_has_table'))
{
	/**
	 * Check database table
	 * 
	 * @param  string $table      Table Name
	 * @param  string $connection Database Group Connection
	 * @return boolean
	 */
	function db_has_table($table, $connection = ACTIVE_DATABASE_GROUP)
	{
		$db = get_instance()->load->database($connection ,TRUE);
		return $db->table_exists($table);
	}
}

if (!function_exists('db_show_create_table'))
{
	/**
	 * Show create table
	 * 
	 * @param  string $table      Table Name
	 * @param  string $connection Database Group Connection
	 * @return array
	 */
	function db_show_create_table($table, $connection = ACTIVE_DATABASE_GROUP)
	{
		$db = get_instance()->load->database($connection ,TRUE);
		if (db_has_table($table, $connection))
		{
			return $db->query('SHOW CREATE TABLE '.TABLE_PREFIX.$table)->result()[0]->{'Create Table'};
		}
	}
}

if (!function_exists('db_show_tables'))
{
	/**
	 * Show tables
	 * 
	 * @param  string $connection Database Group Connection
	 * @return array
	 */
	function db_show_tables($connection = ACTIVE_DATABASE_GROUP)
	{
		$db = get_instance()->load->database($connection ,TRUE);
		return $db->list_tables();
	}
}

if (!function_exists('db_show_columns'))
{
	/**
	 * Show columns
	 * 
	 * @param  string $table      Table Name
	 * @param  string $connection Database Group Connection
	 * @return array
	 */
	function db_show_columns($table, $field_data = false, $connection = ACTIVE_DATABASE_GROUP)
	{
		$db = get_instance()->load->database($connection ,TRUE);
		if (db_has_table($table, $connection))
		{
			if (!filter_var($field_data,FILTER_VALIDATE_BOOLEAN))
			{
				return $db->list_fields($table);
			}
			else
			{
				return $db->field_data($table);
			}
		}
	}
}

if (!function_exists('table_count_data'))
{
	/**
	 * Count table data
	 * 
	 * @param  string $table
	 * @param  string $connection
	 * @return integer
	 */
	function table_count_data($table = null, $connection = ACTIVE_DATABASE_GROUP)
	{
		return $this->db->get($table)->num_rows();
	}
}

if (!function_exists('db_install_tables'))
{
	/**
	 * Database install tables
	 * 
	 * @param  string 	$source     One of : array, model, json
	 * @param  mixed 	$data       Model Name or Array or Path to JSON file
	 * @param  string 	$connection Database Group Connection
	 * @return array
	 */
	function db_install_tables($source = 'model', $data, $connection = ACTIVE_DATABASE_GROUP)
	{
		$db = get_instance()->load->database($connection, TRUE);
		$dbforge = get_instance()->load->dbforge($db, TRUE);

		$installation_done = array();
		$installation_fail = array();

		switch ($source)
		{
			case 'array':
				if (is_array($data) && !empty($data))
				{
					foreach ($data as $table => $attributes)
					{
						/**
						 * table not exists
						 */
						if (!$db->table_exists($table))
						{
							$dbforge->add_field($attributes['fields']);

							foreach ($attributes['primary_keys'] as $table_key)
							{
								$dbforge->add_key($table_key, TRUE);
							}

							foreach ($attributes['keys'] as $table_key)
							{
								$dbforge->add_key($table_key);
							}

							($dbforge->create_table($table))?array_push($installation_done, $table):array_push($installation_fail, $table);
						}
						/**
						 * table exists
						 */
						else
						{
							$diff_fields = array_diff(array_keys($attributes['fields']), $db->list_fields($table)); // check diff fields

							if ($diff_fields)
							{
								$fields = array_intersect_key($attributes['fields'],array_flip($diff_fields)); // get new fields
								$dbforge->add_column($table, $fields); // add new fields
								array_push($installation_done,$table); // push as done
							}
							else
							{
								array_push($installation_done,$table); // push as done
							}
						}
					}

					return array('tables' => array_keys($data),'done' => $installation_done, 'fail' => $installation_fail);
				}
			break;

			case 'model':
				get_instance()->load->model($data,'_install_tables');

				foreach (get_instance()->_install_tables->tables as $table)
				{
					$attributes = get_instance()->_install_tables->$table();
					/**
					 * table not exists
					 */
					if (!$db->table_exists($table))
					{
						$dbforge->add_field($attributes['fields']);

						foreach ($attributes['primary_keys'] as $table_key)
						{
							$dbforge->add_key($table_key, TRUE);
						}

						foreach ($attributes['keys'] as $table_key)
						{
							$dbforge->add_key($table_key);
						}

						($dbforge->create_table($table))?array_push($installation_done, $table):array_push($installation_fail, $table);
					}
					/**
					 * table exists
					 */
					else
					{
						$diff_fields = array_diff(array_keys($attributes['fields']), $db->list_fields($table)); // check diff fields

						if ($diff_fields)
						{
							$fields = array_intersect_key($attributes['fields'],array_flip($diff_fields)); // get new fields
							$dbforge->add_column($table, $fields); // add new fields
							array_push($installation_done,$table); // push as done
						}
						else
						{
							array_push($installation_done,$table); // push as done
						}
					}
				}

				return array('tables' => array_values(get_instance()->_install_tables->tables), 'done' => $installation_done, 'fail' => $installation_fail);
			break;

			case 'json':
				$data = (file_exists($data))?json_decode(file_get_contents($data),TRUE):array();

				if (is_array($data) && !empty($data))
				{
					foreach ($data as $table => $attributes)
					{
						/**
						 * table not exists
						 */
						if (!$db->table_exists($table))
						{
							$dbforge->add_field($attributes['fields']);

							foreach ($attributes['primary_keys'] as $table_key)
							{
								$dbforge->add_key($table_key, TRUE);
							}

							foreach ($attributes['keys'] as $table_key)
							{
								$dbforge->add_key($table_key);
							}

							($dbforge->create_table($table))?array_push($installation_done, $table):array_push($installation_fail, $table);
						}
						/**
						 * table exists
						 */
						else
						{
							$diff_fields = array_diff(array_keys($attributes['fields']), $db->list_fields($table)); // check diff fields

							if ($diff_fields)
							{
								$fields = array_intersect_key($attributes['fields'],array_flip($diff_fields)); // get new fields
								$dbforge->add_column($table, $fields); // add new fields
								array_push($installation_done,$table); // push as done
							}
							else
							{
								array_push($installation_done,$table); // push as done
							}
						}
					}

					return array('tables' => array_keys($data),'done' => $installation_done, 'fail' => $installation_fail);
				}
			break;

			default:
				log_message('error','invalid source option in database_helper.php');
				return FALSE;
			break;
		}
	}
}

if (!function_exists('db_available_groups'))
{
	/**
	 * Available Database Groups
	 * 
	 * @return array
	 */
	function db_available_groups()
	{
		if (!file_exists($database_config = APPPATH.'config/'.ENVIRONMENT.'/database.php') && !file_exists($database_config = APPPATH.'config/database.php'))
		{
			show_error('The configuration file database.php does not exist.');
		}

		include($database_config);
		unset($db['system']);

		return $db;
	}
}

if (!function_exists('db_backup'))
{
	/**
	 * Database Backup
	 * 
	 * @param  string  $database Database Group Connection
	 * @param  boolean $download Download Backup File
	 */
	function db_backup($database = ACTIVE_DATABASE_GROUP, $download = false, $config = array())
	{
		$config['tables'] = (isset($config['tables']))?(array_diff($config['tables'], db_show_tables())<1)?$config['tables']:db_show_tables($database):array();
		$config['ignore'] = (isset($config['ignore']))?(array_diff($config['ignore'], db_show_tables())<1)?$config['ignore']:db_show_tables($database):array();
		$config['format'] = (isset($config['format']))?(in_array($config['format'], ['gzip','zip','txt']))?$config['format']:'zip':'zip';
		$config['filename'] = (isset($config['filename']))?$config['filename']:'backup';
		$config['add_drop'] = (isset($config['add_drop']))?filter_var($config['add_drop'],FILTER_VALIDATE_BOOLEAN):TRUE;
		$config['add_insert	'] = (isset($config['add_insert	']))?filter_var($config['add_insert	'],FILTER_VALIDATE_BOOLEAN):TRUE;
		$config['newline'] = (isset($config['newline']))?$config['newline']:"\n";
		$config['foreign_key_checks'] = (isset($config['foreign_key_checks']))?filter_var($config['foreign_key_checks'],FILTER_VALIDATE_BOOLEAN):TRUE;

		/**
		 * load database
		 * @var object
		 */
		$database = get_instance()->load->database($database, TRUE);

		/**
		 * load database utility
		 * @var object
		 */
		$database_utility = get_instance()->load->dbutil($database, TRUE);

		/**
		 * database utility backup
		 * @var blob
		 */
		$database_backup = $database_utility->backup($config);

		/**
		 * database backup path
		 * @var string
		 */
		$backup_path = validate_directory(FCPATH.'backup/database/');

		/**
		 * database backup file name
		 * @var string
		 */
		$backup_file = $database->database.'-'.nice_date(unix_to_human(now()),'Y-m-d').'.'.$config['format'];

		/**
		 * write backup file
		 */
		write_file($backup_path.'/'.$backup_file,$database_backup);

		if (filter_var($download,FILTER_VALIDATE_BOOLEAN))
		{
			get_instance()->load->helper('download');
			force_download($backup_file, $database_backup);
		}
		else
		{
			return realpath($backup_path.'/'.$backup_file);
		}
	}
}


/* End of file database_helper.php */
/* Location : ./application/helpers/database_helper.php */