var App_ = require('./App');

/**
 * Image URL to base64
 *
 * convert image to base64 encoding (source from url)
 * 
 * @param  {Object}   options
 * @param  {Function} callback
 * @return {String}
 */
exports.image_url_to_base64 = function(options, callback) {
	// var xhr = new XMLHttpRequest();
	// xhr.onload = function(){
	// 	var reader = new FileReader();
	// 	reader.onloadend = function() {
	// 		App_.callback(callback,reader.result);
	// 	}

	// 	reader.readAsDataURL(xhr.response);
	// };

	// xhr.open((options.method !== undefined)?options.method:'GET', options.url);
	// xhr.responseType = 'blob';
	// xhr.send();

	$.ajax({
		url: options.url,
		headers: {
			'Accept': '*/*',
			'Access-Control-Allow-Origin': '*',
			// 'Access-Control-Request-Method': 'GET',
			'Cache-Control': 'no-cache',
			// 'Accept-Encoding': 'gzip, deflate, br',
			// 'Connection': 'keep-alive'
			// 'Origin': '*'
		},
		xhrFields: {
			withCredentials: false
		},
		crossDomain: true,
		// type: (options.method !== undefined)?options.method:'GET',
		type: 'GET',
		dataType: 'blob',
		// xhr: function(xhr){
		// 	console.log(xhr)
		// },
		// data: {param1: 'value1'},
		success: function(response){
			console.log(response)
		},
		error: function(error){
			console.log(error)
		}
	})
	
}