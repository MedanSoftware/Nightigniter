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
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->config->set_item('compress_output',TRUE);
		$this->form_validation->set_data($this->{$this->request->method}());
		$this->lang->load(array('restful_api'));
	}
}

/* End of file RESTful_API.php */
/* Location: ./application/core/RESTful_API.php */