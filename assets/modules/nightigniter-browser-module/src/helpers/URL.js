var App_ = require('./App');

/**
 * Application site URL
 * 
 * @param  {String} path URI Path (append path)
 * @return {String}      URL
 */
exports.site_url = function(path) {
	return (path !== undefined)?App_.get_app_config().site_url+path:App_.get_app_config().site_url;
}

/**
 * Application base URL
 * 
 * @param  {String} path URI Path (append path)
 * @return {String}      URL
 */
exports.base_url = function(path) {
	var url = new URL(window.location);
	var base_url = url.protocol+'//'+url.hostname+App_.site_path();
	return (path !== undefined)?base_url+path:base_url;
}

/**
 * Application API URL
 * 
 * @param  {String} path URI Path (append path)
 * @return {String}      URL
 */
exports.api_url = function(path) {
	if (App_.get_app_config().api_url === undefined) {
		var url = new URL(window.location);
		var api_url = url.protocol+'//'+url.hostname+App_.site_path()+'api/';
		return (path !== undefined)?api_url+path:api_url;
	} else {
		return App_.get_app_config().api_url;
	}
}

/**
 * Get URL parameters
 * 
 * @param  {String}
 * @return {Object}
 */
exports.get_parameters = (search_string) => {
	var search_string = (search_string == undefined)?window.location.search:search_string;
	var parse = function(params, pairs) {
        var pair = pairs[0];
        var parts = pair.split('=');
        var key = decodeURIComponent(parts[0]);
        var value = decodeURIComponent(parts.slice(1).join('='));
        
        if (typeof params[key] === 'undefined') {
            params[key] = value;
        } else {
            params[key] = [].concat(params[key], value);
        }

        return pairs.length == 1 ? params : parse(params, pairs.slice(1));
    }

    return search_string.length == 0 ? {} : parse({}, search_string.substr(1).split('&'));
}