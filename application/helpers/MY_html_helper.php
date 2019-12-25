<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package Codeigniter
 * @subpackage HTML
 * @category Helper
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

if (!function_exists('datatable'))
{
	/**
	 * DataTable HTML Generator
	 * 
	 * @param  array  	$columns          	columns name
	 * @param  boolean 	$search_by_column 	search by column in <tfoot>
	 * @return string
	 */
	function datatable($columns = array(), $search_by_column = true)
	{
		get_instance()->load->library('table');
		
		$table = array(
			'table_open' => '<table class="table datatable_server_side table-striped" cellspacing="0" width="100%">',
			'table_close' => ($search_by_column === true)?'<tfoot><tr><th>'.implode('</th><th>', $columns).'</th></tr></tfoot></table>':'</table>'
		);

		get_instance()->table->set_template($table);
		get_instance()->table->set_empty("&nbsp;");
		get_instance()->table->set_heading($columns);

		return get_instance()->table->generate();
	}
}

/* End of file MY_html_helper.php */
/* Location : ./application/helpers/MY_html_helper.php */