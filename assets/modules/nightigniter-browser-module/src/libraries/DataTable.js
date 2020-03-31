import App_ from '../helpers/App';
import Swal from 'sweetalert2';

/**
 * Data Table
 */
export default class DataTable {

	constructor(){
		var _this = this;

		// Data table DOM
		this.datatable_dom =
		"<'row'<'col-sm-4 dt_length'l>>"+
		"<'row'<'col-sm-8'B> <'col-sm-4'f>>"+
		"<'row'<'col-sm-12'tr>>"+
		"<'row'<'col-sm-6 col-md-6 col-lg-4'i><'col-sm-6 col-md-6 col-lg-8'>>"+
		"<'row'<'col-sm-12 col-lg-7'<'pull-right'p>>>";

		// Data table export options
		this.export_options = {
			exportOptions : {
				modifier : {
					selected : true,
				},
				columns : ':visible',
			}
		}

		// Data table buttons
		this.datatable_buttons = [
			// Button print
			$.extend(true, {}, this.export_options,{
				extend : 'print',
				text : 'print <i class="fa fa-print"></i>',
				exportOptions : {
					columns : ':visible'
				}
			}),

			// Button copy
			$.extend(true, {}, this.export_options,{
				extend : 'copy',
				text : 'copy <i class="fa fa-copy"></i>',
				exportOptions : {
					columns : ':visible'
				}
			}),

			// Button pdf
			$.extend(true, {}, this.export_options,{
				extend : 'pdfHtml5',
				text : 'pdf <i class="fa fa-file-pdf-o"></i>',
				exportOptions : {
					columns : ':visible'
				},
				action : function(e, dt, node, config) {
					_this.SetExportFileName(this,e, dt, node, config, 'pdfHtml5');
				}
			}),

			// Button excel
			$.extend(true, {}, this.export_options,{
				extend : 'excelHtml5',
				text : 'excel <i class="fa fa-file-excel-o"></i>',
				exportOptions : {
					columns : ':visible'
				},
				action : function(e, dt, node, config) {
					_this.SetExportFileName(this,e, dt, node, config, 'excelHtml5');
				}
			}),

			// Button csv
			$.extend(true, {}, this.export_options,{
				extend : 'csv',
				text : 'csv',
				exportOptions : {
					columns : ':visible'
				},
				action : function(e, dt, node, config) {
					_this.SetExportFileName(this,e, dt, node, config, 'csvHtml5');
				}
			}),

			// Button column visible
			{
				extend : 'colvis',
				text : 'column <i class="fa fa-columns"></i>'
			}
		]
	}

	/**
	 * Destroy data table
	 * 
	 * @param {String} selector
	 */
	destroy(selector = '.datatable_server_side') {
		if ($.fn.DataTable.isDataTable(selector)) {
			$(selector).empty();
			$(selector).DataTable().clear().destroy();
		}
	}

	/**
	 * Set export file name
	 * 
	 * @param {Object} that
	 * @param {Object} e
	 * @param {Object} dt
	 * @param {Object} node
	 * @param {Object} config
	 * @param {String} extend
	 */
	setExportFileName(that, e, dt, node, config, extend) {
		if (extend) {
			Swal.fire({
				title : 'file name',
				input : 'text',
				inputPlaceholder : 'enter the output file name',
				showCancelButton : true,
				confirmButtonText : 'Submit',
				showLoaderOnConfirm : true,
			}).then(function(result) {
				if (result) {
					config.filename = result
					$.fn.DataTable.ext.buttons[extend].action.call(that, e, dt, node, config)
				}
			},function(dismiss){});
		}
	}

	/**
	 * Data table server side
	 * 
	 * @param {String} selector
	 * @param {Object} ajax
	 * @param {Object} datatable_param
	 * @param {Function} drawCallback
	 */
	serverSide(selector = '.datatable_server_side', ajax, datatable_param, drawCallback) {
		var _this = this;
		ajax.dataFilter = (data) => {
			var json = jQuery.parseJSON(data);
			json.recordsTotal = json.record_total;
			json.recordsFiltered = json.record_filtered;
			return JSON.stringify(json);
	    }

	    $(selector).DataTable({
			lengthMenu : (typeof datatable_param.lengthMenu !== 'undefined')?datatable_param.lengthMenu:[[10, 20, 50, 100, -1], [10, 20, 50, 100, 'All']],
			processing : true,
			fixedHeader : (typeof datatable_param.fixedHeader !== 'undefined')?datatable_param.fixedHeader:true,
			serverSide : (typeof datatable_param.serverSide !== 'undefined')?datatable_param.serverSide:true,
			responsive : (typeof datatable_param.responsive !== 'undefined')?datatable_param.responsive:true,
			searchable : (typeof datatable_param.searchable !== 'undefined')?datatable_param.searchable:true,
			ajax : ajax,
			columns : (typeof datatable_param.columns !== 'undefined')?datatable_param.columns:[],
			dom : _this.datatable_dom,
			columnDefs : (typeof datatable_param.columnDefs !== 'undefined')?datatable_param.columnDefs:[],
			buttons : _this.datatable_buttons,
			initComplete : function() {
				var api = this.api();
				api.columns().every(function() {
					var that = this;
					$('.footer_search', this.footer()).on('keyup change', function() {
						if (that.search() !== this.value) {
							that.search(this.value).draw();
						}
					});
				});
			},
			drawCallback : function(dcparam) {
				App_.callback(drawCallback, ajax, this.api());
			}
		});
	}
}