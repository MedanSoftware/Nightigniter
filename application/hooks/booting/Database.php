<?php
/**
 * @package Codeigniter
 * @subpackage Database
 * @category Hook
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

namespace Booting;

class Database
{
	/**
	 * Check database connection
	 */
	public function connection()
	{
		log_message('info','Check the database connection in from hook');

		if (!file_exists($database_config = APPPATH.'config/'.ENVIRONMENT.'/database.php') && !file_exists($database_config = APPPATH.'config/database.php'))
		{
			show_error('The configuration file database.php does not exist.');
		}

		include ($database_config);

		if (!isset($db[ACTIVE_DATABASE_GROUP]))
		{
			if (!is_cli())
			{
				if (!preg_match('/(installation|api)\/(database|sign_in)/', CURRENT_URL))
				{
					if (!preg_match('/sqlite/', $db[ACTIVE_DATABASE_GROUP]['dbdriver']))
					{
						log_message('error','Database connection failed');
						header('location:'.BASE_URL.'index.php/installation/database?code=database_connection_failed');
					}
					else
					{
						log_message('info','Initialize SQLITE database');
						header('location:'.BASE_URL.'index.php/installation/database?code=sqlite_database');
					}

					exit;
				}
			}
			else
			{
				log_message('error','Database connection failed');

				echo 'Database connection failed'.PHP_EOL;
				echo 'Please check active database group in : '.APP_CONFIG_FILE.PHP_EOL;
				echo 'OR'.PHP_EOL;
				echo 'Please check database config file in : '.DATABASE_CONFIG_FILE.PHP_EOL;
			}
		}
		else
		{
			if (class_exists('\Illuminate\Database\Capsule\Manager'))
			{
				// initialize Eloquent ORM
				$capsule = new \Illuminate\Database\Capsule\Manager;

				foreach ($db as $db_group => $config)
				{
					// SQLITE DB
					if (preg_match('/sqlite/', $config['dbdriver']))
			        {
			            $capsule->addConnection([
			                'driver'    => 'sqlite',
			                'database'  => $config['database'],
			                'prefix'    => $config['dbprefix']
			            ],$db_group);
			        }
			        // MySQL or Other DB
			        else
			        {
			            $capsule->addConnection([
			                'driver'    => (preg_match('/mysql/',$config['dbdriver']))?'mysql':$config['dbdriver'],
			                'host'      => $config['hostname'],
			                'database'  => $config['database'],
			                'username'  => $config['username'],
			                'password'  => $config['password'],
			                'charset'   => $config['char_set'],
			                'collation' => $config['dbcollat'],
			                'prefix'    => $config['dbprefix']
			            ],$db_group);
			        }
				}

				$capsule->setAsGlobal();
				$capsule->bootEloquent();

				if (!preg_match('/sqlite/', $db[ACTIVE_DATABASE_GROUP]['dbdriver']))
				{
					try
					{
						$capsule->getConnection(ACTIVE_DATABASE_GROUP)->getPdo();
						$GLOBALS['database_initialized'] = TRUE;

						log_message('info','Database connection success');
					}
					catch (\Exception $e)
					{
						$GLOBALS['database_initialized'] = FALSE;

						if (!is_cli())
						{
							if (!preg_match('/(installation|api)\/(database|sign_in)/', CURRENT_URL))
							{
								if (!preg_match('/sqlite/', $db[ACTIVE_DATABASE_GROUP]['dbdriver']))
								{
									log_message('error','Database connection failed');
									header('location:'.BASE_URL.'index.php/installation/database?code=database_connection_failed');
								}
								else
								{
									log_message('info','Initialize database connection');
									header('location:'.BASE_URL.'index.php/installation/database?code=sqlite_database');
								}

								exit;
							}
						}
						else
						{
							log_message('error','Database connection failed');

							echo 'Database connection failed'.PHP_EOL;
							echo 'Please check active database group in : '.APP_CONFIG_FILE.PHP_EOL;
							echo 'OR'.PHP_EOL;
							echo 'Please check database config file in : '.DATABASE_CONFIG_FILE.PHP_EOL;
						}
					}
				}
				else
				{
					$GLOBALS['database_initialized'] = TRUE;
					log_message('info','Database using SQLITE');
				}
			}
			else
			{
				require_once (BASEPATH.'database/DB.php');
				DB(ACTIVE_DATABASE_GROUP);
			}
		}
	}

	/**
	 * Load models
	 */
	public function models()
	{
		require_once(APPPATH.'core/Eloquent_Model.php');

		foreach (ELOQUENT_MODEL_LOCATIONS as $location)
		{
			$this->require_eloquent_models($location);
		}

		log_message('info','Load eloquent model');
	}

	/**
	 * Load file model
	 * 
	 * @param  string  $dir
	 * @param  integer $depth
	 */
	private function require_eloquent_models($dir, $depth = 1)
	{
		$scan = glob("$dir/*");

		foreach ($scan as $path)
		{
			if (preg_match('/\.php$/', $path))
			{
				require_once($path);
			}
			elseif (is_dir($path))
			{
				$this->require_eloquent_models($path, $depth+1);
			}
		}
	}
}

/* End of file Database.php */
/* Location : ./application/hooks/booting/Database.php */