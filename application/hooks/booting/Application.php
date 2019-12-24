<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Application
 * @category Hook
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

class Application
{
	/**
	 * Create default .htaccess file in root path
	 * 
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function htaccess()
	{
		if (!file_exists(FCPATH.'/.htaccess'))
		{
			$content = '';
			$content .= "#==============================================================\n";
			$content .= "#	DISABLE INDEX FOLDER \n";
			$content .= "#==============================================================\n";
			$content .= "\tOptions -Indexes\n";
			$content .= "#--------------------------------------------------------------#\n";

			$content .= "\n\n";
			$content .= "#==============================================================\n";
			$content .= "#\tCODEIGNITER HTACCESS RULE\n";
			$content .= "#==============================================================\n";
			$content .= "\tRewriteEngine on\n";
			$content .= "\tRewriteCond %{REQUEST_FILENAME} !-f\n";
			$content .= "\tRewriteCond %{REQUEST_FILENAME} !-d\n";
			$content .= "\tRewriteRule .* index.php/$0 [PT,L]\n";
			$content .= "#--------------------------------------------------------------#\n";

			file_put_contents(FCPATH.'/.htaccess',$content);
			log_message('info','application .htaccess file generated');
		}
	}

	/**
	 * Check application config files
	 *
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function config_files()
	{
		if (!file_exists(APP_CONFIG_FILE) && !file_exists(DATABASE_CONFIG_FILE))
		{
			if (!is_cli())
			{
				if (!preg_match('/(installation)/', CURRENT_URL))
				{
					exit (header('location:'.BASE_URL.'index.php/installation?code=start'));
				}
			}
			else
			{
				if (!strtolower($_SERVER['argv'][1]) == 'installation')
				{
					echo 'Plase complete the installation for use this application.'.PHP_EOL;
				}
			}
		}	
	}

	/**
	 * Create site_path in app.config file
	 * 
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function site_path()
	{
		$data = $this->read_config();

		if (!isset($data['site_path']))
		{
			$this->write_config(array('site_path' => (isset(parse_url(BASE_URL)['path']))?parse_url(BASE_URL)['path']:'/'), $data);
		}
	}

	/**
	 * Assets path
	 * 
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function assets_path()
	{
		$data = $this->read_config();

		$assets_uri = str_replace(FCPATH, '', ASSETS_PATH);

		if (!isset($data['assets']))
		{
			$this->write_config(array('assets' => ['sys' => ASSETS_PATH, 'uri' => $assets_uri]), $data);
		}
		else
		{
			if ($data['assets']['sys'] !== ASSETS_PATH)
			{
				$this->write_config(array('assets' => ['sys' => ASSETS_PATH, 'uri' => $assets_uri]), $data);
			}
		}
	}

	/**
	 * SSL certificate path
	 *
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function ssl_cert_path()
	{
		$data = $this->read_config();

		if (!isset($data['ssl_certificate_path']))
		{
			$this->write_config(array('ssl_certificate_path' => SSL_CERT_PATH), $data);
		}
	}

	/**
	 * Automatically generate site data folder
	 * 
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function site_data_folder()
	{
		$data = $this->read_config();

		if (!isset($data['site_data_folder']))
		{
			$SD = APP_INFO['name'].'_SiteData_'.time();
			(!is_dir(SITE_DATA_PATH.$SD))?mkdir(SITE_DATA_PATH.$SD ,0777 ,TRUE):FALSE;

			$this->write_config(array('site_data_folder' => $SD), $data);
			log_message('info','site data folder intialized');
		}
	}

	/**
	 * Generate site url for third-party application in APP_CONFIG_FILE
	 * 
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function site_url()
	{
		$data = $this->read_config();

		if (!isset($data['site_url']))
		{
			$this->write_config(array('site_url' => BASE_URL), $data);
		}
	}

	/**
	 * Application environment
	 * 
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function environment()
	{
		$data = $this->read_config();

		if (!isset($data['ENVIRONMENT']))
		{
			$this->write_config(array('ENVIRONMENT' => ENVIRONMENT), $data);
			log_message('info','application environment intialized');
		}
		else
		{
			if ($data['ENVIRONMENT'] !== ENVIRONMENT)
			{
				log_message('info','application environment changed from : '.APP_CONFIG['ENVIRONMENT'].' to '.ENVIRONMENT);
				$this->write_config(array('ENVIRONMENT' => ENVIRONMENT), $data);
			}
		}
	}

	/**
	 * Initialize language
	 *
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function language()
	{
		get_instance()->load->helper(['cookie','directory']);
		
		$language = (!empty(get_cookie('language')))?get_cookie('language'):get_instance()->input->get('language');

		if (preg_grep('/'.$language.'/',array_keys(directory_map(APPPATH.'language'))))
		{
			/* set current language to config */
			get_instance()->config->set_item('language', $language);
			log_message('info','site language intialized : '.$language);
		}
	}

	/**
	 * Themes
	 * 
	 * @author Agung Dirgantara <agungmasda29@gmail.com>
	 */
	public function themes()
	{	
		$data = array();

		foreach (directory_map(THEMES_PATH,3) as $module => $themes)
		{
			$module_name = str_replace('\\','',$module);

			if (preg_match('/(installation)/', $module))
			{
				continue;
			}

			if (is_dir(THEMES_PATH.'/'.$module))
			{
				$data = array_merge($data, array(
					$module_name => array(
						'themes' => array()
					)
				));

				foreach ($themes as $theme_folder => $theme_files)
				{
					if (in_array('theme.json', $theme_files))
					{
						$theme_json = json_decode(file_get_contents(THEMES_PATH.'/'.$module.$theme_folder.'theme.json'),TRUE);
						array_push(
							$data[$module_name]['themes'],
							array_merge((!empty($theme_json))?$theme_json:array(),array(
								'_path' => realpath(THEMES_PATH.'/'.$module.$theme_folder)
						)));
					}
				}
			}
		}

		$GLOBALS['module_themes'] = $data;
	}

	/**
	 * Read config
	 * 
	 * @return array
	 */
	private function read_config($config_file = APP_CONFIG_FILE)
	{
		return (file_exists($config_file))?json_decode(file_get_contents($config_file),TRUE):array();
	}

	/**
	 * Write config
	 */
	private function write_config($data = array(), $old_data = array(), $file = APP_CONFIG_FILE, $merge_data = true)
	{
		$content = $data;

		if ($merge_data)
		{
			if (!empty($old_data))
			{
				$content = array_merge($old_data, $data);
			}
		}

		file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT));
	}
}

/* End of file Application.php */
/* Location : ./application/hooks/booting/Application.php */