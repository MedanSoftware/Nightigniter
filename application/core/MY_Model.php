<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage MY_Model
 * @category Libraries
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

namespace Nightigniter;

class MY_Model extends \CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database(ACTIVE_DATABASE_GROUP);
	}

	/**
	 * Set database
	 */
	public function set_db($db_group = ACTIVE_DATABASE_GROUP)
	{
		$this->db = $this->load->database($db_group, TRUE);
	}

	/**
	 * Get database tables
	 * 
	 * @return array
	 */
	public function get_tables()
	{
		return $this->db->list_tables();
	}

	/**
	 * Table exists
	 * 
	 * @param  string $table
	 * @return boolean
	 */
	public function table_exists($table)
	{
		return $this->db->table_exists($table);
	}

	/**
	 * Get fields
	 * 
	 * @param  string $table
	 * @return array
	 */
	public function get_fields($table)
	{
		if ($this->db->table_exists($table))
		{
			return $this->db->list_fields($table);
		}
	}

	/**
	 * Field exists
	 * 
	 * @param  string $table
	 * @param  string $field
	 * @return boolean
	 */
	public function field_exists($table, $field)
	{
		if ($this->table_exists($table))
		{
			return $this->db->field_exists($field, $table);
		}
	}
}

/* End of file MY_Model.php */
/* Location : ./application/core/MY_Model.php */