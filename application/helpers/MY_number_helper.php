<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Number
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('random_port'))
{
	/**
	 * Generate random port
	 * 
	 * @return integer
	 */
	function random_port()
	{
		return rand(0,65535);
	}
}

/* End of file MY_number_helper.php */
/* Location : ./application/helpers/MY_number_helper.php */