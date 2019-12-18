<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage RESTful_API
 * @category Libraries
 * @author Agung Dirgantara <agungmasda29@gmail.com>
*/

require_once(APPPATH.'libraries/RESTful/REST_Controller.php');

class RESTful_API extends REST_Controller
{
	protected $ci;

	public function __construct()
	{
		parent::__construct();
		$this->ci =& get_instance();
	}
}

/* End of file RESTful_API.php */
/* Location: ./application/core/RESTful_API.php */