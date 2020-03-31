import App_ from '../helpers/App';
import CryptoJS from 'crypto-js';

/**
 * Encryption
 */
export default class Encryption {

	/**
	 * Encrypt method length
	 * @return Integer
	 */
	encryptMethodLength() {
		var encryptMethod = this.encryptMethod();
		var aesNumber = encryptMethod.match(/\d+/)[0];
		return parseInt(aesNumber);
	}

	/**
	 * Encrypt size
	 * @return Integer
	 */
	encryptKeySize() {
		var aesNumber = this.encryptMethodLength();
		return parseInt(aesNumber / 8);
	}

	/**
	 * Encrypt method
	 * @return String
	 */
	encryptMethod() {
		return 'AES-256-CBC';
	}

	/**
	 * Encrypt key
	 * @return string
	 */
	getKey() {
		if (App_.get_app_config().config !== undefined) {
			return App_.get_app_config().config.encryption_key;
		} else {
			return 'Nightigniter';
		}
	}

	/**
	 * Encrypt
	 * 
	 * @param  {String} string
	 * @param  {String} key
	 * @return {String}
	 */
	encrypt(string, key) {
		var key = (key == undefined)?this.getKey():key;
		var iv = CryptoJS.lib.WordArray.random(16);
		var salt = CryptoJS.lib.WordArray.random(256);
		var iterations = 999;
		var encryptMethodLength = (this.encryptMethodLength()/4);
		var hashKey = CryptoJS.PBKDF2(key, salt, {'hasher': CryptoJS.algo.SHA512, 'keySize': (encryptMethodLength/8), 'iterations': iterations});
		var encrypted = CryptoJS.AES.encrypt(string, hashKey, {'mode': CryptoJS.mode.CBC, 'iv': iv});
		var encryptedString = CryptoJS.enc.Base64.stringify(encrypted.ciphertext);
		var output = {
			'ciphertext': encryptedString,
			'iv': CryptoJS.enc.Hex.stringify(iv),
			'salt': CryptoJS.enc.Hex.stringify(salt),
			'iterations': iterations
		};

		return CryptoJS.enc.Base64.stringify(CryptoJS.enc.Utf8.parse(JSON.stringify(output)));
	}

	/**
	 * Decrypt
	 * 
	 * @param  {String} encryptedString
	 * @param  {String} key
	 * @return {Mixed}
	 */
	decrypt(encryptedString, key) {
		var key = (key == undefined)?this.getKey():key;
		var json = CryptoJS.enc.Utf8.stringify(CryptoJS.enc.Base64.parse(encryptedString));
		
		if (json.length > 0) {
			json = JSON.parse(json);
			var salt = CryptoJS.enc.Hex.parse(json.salt);
			var iv = CryptoJS.enc.Hex.parse(json.iv);
			var encrypted = json.ciphertext;
			var iterations = parseInt(json.iterations);
			
			if (iterations <= 0) {
				iterations = 999;
			}

			var encryptMethodLength = (this.encryptMethodLength()/4);
			var hashKey = CryptoJS.PBKDF2(key, salt, {'hasher': CryptoJS.algo.SHA512, 'keySize': (encryptMethodLength/8), 'iterations': iterations});
			var decrypted = CryptoJS.AES.decrypt(encrypted, hashKey, {'mode': CryptoJS.mode.CBC, 'iv': iv});
			
			return decrypted.toString(CryptoJS.enc.Utf8);
		} else {
			return false;
		}
	}
}