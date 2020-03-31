import App_ from '../helpers/App';
import PeerJs from 'peerjs-client';

/**
 * Peer
 */
export default class Peer {

	constructor(host, port, path){
		this.host = host;
		this.port = port;
		this.path = path;
		return this;
	}

	/**
	 * Set host
	 * 
	 * @param {String} host
	 */
	setHost(host) {
		this.host = host;
		return this;
	}

	/**
	 * Set port
	 * 
	 * @param {String} port
	 */
	setPort(port) {
		this.port = port;
		return this;
	}

	/**
	 * Set path
	 * 
	 * @param {String} path
	 */
	setPath(path) {
		this.path = path;
		return this;
	}

	/**
	 * Connect to server
	 * 
	 * @param  {String}   id
	 * @param  {Function} callback
	 */
	connect(id, callback) {
		App_.callback(callback, PeerJs(id, {host : this.host, port : this.port, path : this.path}));
	}
}