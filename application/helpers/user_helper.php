<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage User
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('is_logged_in'))
{
	/**
	 * Check user is loggedin
	 * 
	 * @param  boolean $force_redirect
	 * @param  string  $redirect_to
	 * @return boolean
	 */
	function is_logged_in($force_redirect = false, $redirect_to = '')
	{
		$ci =& get_instance();

		if (user_session())
		{
			return TRUE;
		}
		else
		{
			if ($force_redirect)
			{
				if (preg_match('/https?:\/\//', $redirect_to))
				{
					redirect($redirect_to, 'refresh');
				}
				else
				{
					redirect(base_url($redirect_to), 'refresh');
				}
			}
		}

		return FALSE;
	}
}

if (!function_exists('user_session'))
{
	/**
	 * Get current user session
	 * 
	 * @param  string|array $fields
	 */
	function user_session($fields = null)
	{
		$ci =& get_instance();
		$user = false;
		$user_token = $ci->input->get_request_header('user-token', TRUE);
		$user_session = $ci->session->userdata('user-session');

		if (!empty($user_token))
		{
			$session = Nightigniter\Model\User_session::where('token', $user_token)->where('status', TRUE)->first();

			if (!empty($session))
			{
				$user = Nightigniter\Model\User::with('profile_photo')->find($session['user_id'], $fields);
			}
		}
		elseif (!empty($user_session))
		{
			$user = (!empty($user_session))?Nightigniter\Model\User::with('profile_photo')->find($user_session['id'], $fields):FALSE;
		}

		return $user;
	}
}

/* End of file user_helper.php */
/* Location : ./application/helpers/user_helper.php */