<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage CLI_request
 * @category MX Controller
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

class CLI_request extends MX_Controller
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if (!$this->input->is_cli_request())
		{
			show_error('<b>CLI <small>(Command Line Interface) Request Only</small></b>', 405, 'Method Not Allowed');
		}
	}
}

/* End of file CLI_request.php */
/* Location : ./application/core/CLI_request.php */