<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage File
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('file_manager'))
{
	/**
	 * file manager
	 * 
	 * @param  string $root   
	 * @param  array  $config
	 * @return output json
	 */
	function file_manager($root = FCPATH, $config = array())
	{
		$app = new RFM\Application();
		$local = new RFM\Repository\Local\Storage($config);
		$local->setRoot($root, true, false);
		$app->setStorage($local);
		$app->api = new RFM\Api\LocalApi();
		return $app;
	}
}

if (!function_exists('zip'))
{
	/**
	 * php zip
	 */
	function zip()
	{
		return new PhpZip\ZipFile;
	}
}

/* End of file MY_file_helper.php */
/* Location : ./application/helpers/MY_file_helper.php */