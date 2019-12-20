<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage MY_Security 
 * @category Libraries
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

class MY_Security extends CI_Security
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Set password
	 * 
	 * @param string $password
	 */
	public function set_password(string $password)
	{
		return sha1($password);
	}

	/**
	 * Match password
	 * 
	 * @param  string $password       password given
	 * @param  string $saved_password password saved
	 * @return boolean
	 */
	public function match_password(string $password, string $saved_password)
	{
		return ($this->set_password($password) === $saved_password);
	}
}

/* End of file MY_Security.php */
/* Location : ./application/core/MY_Security.php */