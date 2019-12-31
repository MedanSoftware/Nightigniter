<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage User
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('current_user_session'))
{
	function current_user_session($field = null)
	{
		$ci =& get_instance();
		$ci->load->model('user_model');
		return $ci->user_model->current_session(array(), $field);
	}
}

/* End of file user_helper.php */
/* Location : ./application/helpers/user_helper.php */