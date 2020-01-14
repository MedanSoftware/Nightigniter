<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage Eloquent Model 
 * @category Library
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

namespace Nightigniter\Model;

abstract class Eloquent_Model extends \Illuminate\Database\Eloquent\Model
{
	/**
	 * Created by
	 * 
	 * @return Object
	 */
	public function created_by()
	{
		return $this->belongsTo('Nightigniter\Model\User', 'created_by');
	}

	/**
	 * Updated by
	 * 
	 * @return Object
	 */
	public function updated_by()
	{
		return $this->belongsTo('Nightigniter\Model\User', 'updated_by');
	}

	/**
	 * Deleted by
	 * 
	 * @return Object
	 */
	public function deleted_by()
	{
		return $this->belongsTo('Nightigniter\Model\User', 'deleted_by');
	}
}

/* End of file Eloquent_Model.php */
/* Location : ./application/core/Eloquent_Model.php */