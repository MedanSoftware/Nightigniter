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
	protected $header;

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

	/**
	 * Set header
	 * 
	 * @param  mixed $header
	 * @return RESTful_API
	 */
	public function set_header($header = RESTful_API::HTTP_OK)
	{
		$this->header = $header;
		return $this;
	}

	/**
	 * Send response
	 * 
	 * @param  string $status
	 * @param  array  $data
	 * @param  string $message
	 */
	public function send_response($status = 'success', $data = array(), $message = '')
	{
		$response[config_item('rest_status_field_name')] = $status;
		(!empty($message))?$response[config_item('rest_message_field_name')] = $message:FALSE;
		(!empty($data))?$response[config_item('rest_data_field_name')] = $data:FALSE;
		$this->response($response, $this->header);
	}
}

/* End of file RESTful_API.php */
/* Location: ./application/core/RESTful_API.php */