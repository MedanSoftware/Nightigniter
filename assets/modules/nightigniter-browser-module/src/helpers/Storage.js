/**
 * Has local storage
 * 
 * @return {Boolean}
 */
exports.has_local_storage = () => {
	let has = (typeof(Storage) !== 'undefined');

	if (!has) {
		console.error('local storage not supported');
	}

	return has;
}

/**
 * Local storage set data
 * 
 * @param  {String} key
 * @param  {String} data
 * @return {String}
 */
exports.local_storage_set_data = function(key, data) {
	if (this.has_local_storage()) {
		localStorage.setItem(key,data);
		return this.local_storage_get_data(key);
	}
}

/**
 * Local storage has data
 * 
 * @param  {String} key
 * @return {Boolean}
 */
exports.local_storage_has_data = function(key) {
	if (this.has_local_storage()) {
		return (typeof localStorage[key] !== 'undefined');
	}
}

/**
 * Local storage get data
 * 
 * @param  {String} key
 * @return {String}
 */
exports.local_storage_get_data = function(key) {
	if (this.has_local_storage()) {
		if (typeof localStorage[key] !== 'undefined') {
			return localStorage.getItem(key);
		}
	}
}

/**
 * Local storage get all
 * 
 * @return {Object}
 */
exports.local_storage_get_all = function() {
	if (this.has_local_storage()) {
		return localStorage;
	}
}

/**
 * Local storage delete
 * 
 * @param  {String} key
 * @return {Boolean}
 */
exports.local_storage_delete = function(key) {
	if (this.has_local_storage()) {
		localStorage.removeItem(key);
		return true;
	}
}

/**
 * Local storage delete all
 * 
 * @return {Boolean}
 */
exports.local_storage_delete_all = () => {
	localStorage.clear();
	return true;
}

/**
 * Local storage create or get
 * 
 * @param  {String} key
 * @param  {String} data
 * @return {String}
 */
exports.local_storage_create_or_get = function(key, data) {
	if (this.has_local_storage()) {
		if (typeof localStorage[key] !== 'undefined') {
			return localStorage.getItem(key);
		} else {
			localStorage.setItem(key,data);
		}
	}
}