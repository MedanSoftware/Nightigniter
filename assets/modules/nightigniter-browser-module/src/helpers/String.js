/**
 * Random string
 * 
 * @param  {Number} length
 * @return {String}
 */
exports.random_string = (length) => {
	var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
    if (!length) {
    	length = Math.floor(Math.random() * chars.length);
    }

    var str = '';
    for (var i = 0; i < length; i++) {
        str += chars[Math.floor(Math.random() * chars.length)];
    }

    return str;
}

/**
 * Uppercase first character
 * 
 * @param  {String} string
 * @return {String}
 */
exports.ucfirst = (string) => {
	return (string !== null)?string.charAt(0).toUpperCase()+string.slice(1):string;
}

/**
 * Backslash to slash
 * 
 * @param  {String} string
 * @return {String}
 */
exports.backslash_to_slash = (string) => {
	return string.replace('\\','/');
}

/**
 * Base64 to int8 array
 * 
 * @param  {String} base64String
 * @return {Array}
 */
exports.base64_to_int8_array = (base64String) => {
	const padding = '='.repeat((4 - base64String.length % 4) % 4);
	const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
	const rawData = window.atob(base64);
	const outputArray = new Uint8Array(rawData.length);

	for (let i = 0; i < rawData.length; ++i) {
		outputArray[i] = rawData.charCodeAt(i);
	}

	return outputArray;
}