var Storage_ = require('./Storage');
var Howler = require('howler');
var app_config = JSON.parse(require('nightigniter/application/app.config').default); // app.config

/**
 * Get application assets
 * 
 * @type {Object}
 */
exports.assets = function() {
	return app_config.assets;
}

/**
 * Get application environment
 * 
 * @type {String}
 */
exports.environment = function() {
	return app_config.environment;
}

/**
 * Site path
 * 
 * @type {Object}
 */
exports.site_path = function(path) {
	return (path !== undefined)?app_config.site_path+path:app_config.site_path;
}

/**
 * Callback function
 * 
 * @param  {Function} callback
 * @return {Function}
 */
exports.callback = function(callback) {
	if (callback !== undefined) {
		if (typeof callback == 'function') {
			callback(...Array.prototype.slice.call(arguments, 1));
		} else {
			eval(callback+'(...Array.prototype.slice.call(arguments, 1))');
		}
	}
}

/**
 * Get app config
 * 
 * @return {Object}
 */
exports.get_app_config = () => {
	return app_config;
}

/**
 * Ajax request
 * 
 * @param  {Object} 	ajax_param
 * @param  {Function} 	callbackOnSuccess
 * @param  {Function} 	callbackOnFail
 */
exports.ajax_request = function(ajax_param, callbackOnSuccess, callbackOnFail) {
	var _this = this;
	var identify = {
		user_token : Storage_.local_storage_get_data('user_token')
	}

	if (typeof ajax_param.headers == 'undefined') {
		ajax_param.headers = identify;
	} else {
		$.extend(ajax_param.headers, identify);
	}

	$.ajax(ajax_param).done(function(data) {
		_this.callback(callbackOnSuccess, ajax_param, data);
	}).fail(function(response) {

		if (_this.environment() !== 'production') {
			console.error({
				error : 'ajax request failed!',
				debug : {
					url : ajax_param.url,
					method : (typeof ajax_param.type == 'undefined')?'GET':ajax_param.type,
					response : response
				}
			});
		}

		_this.callback(callbackOnFail, ajax_param, response);
	})
}

/**
 * Save JSON from browser console
 * 
 * @param  {Object} data     JSON data
 * @param  {String} filename Save as File (default : console.json)
 */
exports.save_json_console = function (data, filename) {
	if (!data) {
		console.error('save_console : no data');
		return;
	}

	if (!filename) {
		filename = 'console.json';
	}

	if (typeof data === 'object') {
		data = JSON.stringify(data, undefined, 4);
	}

	var blob = new Blob([data], {type: 'text/json'}), e = document.createEvent('MouseEvents'), a = document.createElement('a');

	a.download = filename;
	a.href = window.URL.createObjectURL(blob);
	a.dataset.downloadurl = ['text/json', a.download, a.href].join(':');
	e.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
	a.dispatchEvent(e);
}