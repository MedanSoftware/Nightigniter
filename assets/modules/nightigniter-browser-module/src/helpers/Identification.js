var App_ = require('./App');
var URL_ = require('./URL');
var Array_ = require('./Array');
var String_ = require('./String');
var CryptoJS = require('crypto-js');
var UAParser = require('ua-parser-js');
var FingerPrintJs2 = require('fingerprintjs2');

/**
 * Is mobile device
 * 
 * @return {Boolean}
 */
exports.is_mobile = () => {
	return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
}

/**
 * Get device ID
 * 
 * @param  {Function} callback
 * @return {String}
 */
exports.get_device_id = function(callback) {
	let identify = {
		platform : navigator.platform,
		canvas : CryptoJS.SHA1(get_canvas_fingerprint({dontUseFakeFontInCanvas:true}).toString()).toString(),
		webgl : CryptoJS.SHA1(get_webgl_fingerprint().toString()).toString()
	};

	App_.callback(callback, CryptoJS.SHA1(JSON.stringify(identify)).toString());
}

/**
 * Get device type
 * 
 * @param  {Function} callback
 * @return {String}
 */
exports.get_device_type = (callback) => {
	if (typeof navigator.getBattery == 'function') {
		navigator.getBattery().then(function(battery) {
			if (battery.charging && battery.chargingTime === 0) {
				App_.callback(callback,'desktop')
			} else {
				App_.callback(callback,($.ua.device.type == undefined)?'laptop':$.ua.device.type);
			}
		});
	} else {
		App_.callback(callback,($.ua.device.type == undefined)?'desktop':$.ua.device.type);
	}
}

/**
 * Get device info
 * 
 * @param  {Function} callback
 * @return {Object}
 */
exports.get_device_info = function(callback) {
	var _this = this;
	async function get_info() {
		let data = {};
		let device_id = new Promise((resolve,reject) => { 
			_this.get_device_id(value => {
				resolve(value);
			});
		});

		let device_type = new Promise((resolve,reject) => { 
			_this.get_device_type(value => {
				resolve(value);
			});
		});

		let is_private = new Promise((resolve,reject) => { 
			_this.is_private_browser(value => {
				resolve(value);
			});
		});

		let browser_id = new Promise((resolve,reject) => { 
			_this.get_browser_id(value => {
				resolve(value);
			});
		});

		let public_ip = new Promise((resolve,reject) => { 
			_this.get_public_ip(value => {
				resolve(value);
			});
		});

		let coordinate = new Promise((resolve,reject) => { 
			_this.get_coordinate(value => {
				resolve(value);
			});
		});

		// device os
		data.os = {
			text : $.ua.os.name+' '+$.ua.os.version,
			name : $.ua.os.name,
			version : $.ua.os.version
		};

		// device info
		data.device = {
			id : await device_id,
			type : await device_type,
			model : $.ua.device.model,
			vendor : $.ua.device.vendor,
			platform : navigator.platform,
			is_browser : true
		};

		// browser info
		data.browser = {
			id : await browser_id,
			text : $.ua.browser.name+' '+$.ua.browser.version,
			name : $.ua.browser.name,
			version : $.ua.browser.version,
			is_private : await is_private
		}

		data.user_agent = $.ua.ua;
		data.public_ip = await public_ip;
		data.coordinate = await coordinate;

		return data;
	}

	get_info().then(data => {
		App_.callback(callback, data);
	});
}

/**
 * Get browser ID
 * 
 * @param  {Function} callback
 * @return {String}
 */
exports.get_browser_id = function(callback) {
	var _this = this;
	var options = {
		extraComponents : [
			{
				key : 'device_id',
				getData : (done,options) => {
					_this.get_device_id(data => {
						done(data);
					});
				}
			},
			{
				key : 'device_model',
				getData : (done,options) => {
					done($.ua.device.model)
				}
			},
			{
				key : 'device_vendor',
				getData : (done,options) => {
					done($.ua.device.vendor)
				}
			},
			{
				key : 'canvas_fingerprint',
				getData : (done,options) => {
					done(CryptoJS.SHA1(get_canvas_fingerprint({ dontUseFakeFontInCanvas:true }).toString()).toString());
				}
			},
			{
				key : 'is_private_browser',
				getData : (done,options) => {
					_this.is_private_browser(data => {
						done(data);
					});
				}
			},
		]
	}

	FingerPrintJs2.get(options,(components) => {
		var hash = CryptoJS.SHA1(JSON.stringify(components)).toString();
    	App_.callback(callback,hash);
	});
}

/**
 * Get browser info
 * 
 * @return {Object}
 */
exports.get_browser_info = () => {
	var browser = {};
	var pattern = navigator.userAgent.match(/(MSIE|Trident|(?!Gecko.+)Firefox|(?!AppleWebKit.+Chrome.+)Safari(?!.+Edge)|(?!AppleWebKit.+)Chrome(?!.+Edge)|(?!AppleWebKit.+Chrome.+Safari.+)Edge|AppleWebKit(?!.+Chrome|.+Safari)|Gecko(?!.+Firefox))(?: |\/)([\d\.apre]+)/);

	if (pattern.length > 0) {
		browser.name = (pattern.length > 1)?pattern[1]:'',
		browser.version = (pattern.length > 2)?pattern[2]:'',
		browser.text = pattern[0]
	}

	return browser;
}

/**
 * Get Coordinate
 * 
 * @param  {Function}
 * @return {Object}
 */
exports.get_coordinate = function(callback) {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition((position) => { App_.callback(callback, Array_.clone_as_object(position.coords))},(error => {
			if (error) {
				App_.callback(callback,error);
			}
		}),{
			enableHighAccuracy: true,
			timeout: 5000,
			maximumAge: 0
		});
	}
}

/**
 * Get local IP
 * 
 * @param  {Function} callback
 * @return {String}
 */
exports.get_local_ip = function(callback) {
	window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
	var pc = new RTCPeerConnection({iceServers:[]}), noop = function(){};
	pc.createDataChannel('');
	pc.createOffer(pc.setLocalDescription.bind(pc), noop);
	pc.onicecandidate = function(ice) {
		if (ice && ice.candidate && ice.candidate.candidate) {
			var local_ip = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(ice.candidate.candidate);
			pc.onicecandidate = noop;
			App_.callback(callback,(local_ip !== null)?local_ip[1]:'none');
		} else {
			App_.callback(callback,'none');
		}
	}
}

/**
 * Get public IP
 * 
 * @param  {Function} callback
 * @param  {String}   source
 * @return {String}
 */
exports.get_public_ip = function(callback, source) {
	if (navigator.onLine) {
		switch (source) {
			case 'seeip':
				$.get('https://ip.seeip.org/json').then(ip => {App_.callback(callback,ip.ip)},error => {
					if (error.statusText == 'error' && error.state() == 'rejected') {
						App_.callback(callback,'offline');
					} else {
						console.log(error);
					}

					App_.callback(callback,'error'); // set error callback
				});
			break;

			default:
				$.get('https://api.ipify.org?format=json').then(ip => {App_.callback(callback,ip.ip)},error => {
					if (error.statusText == 'error' && error.state() == 'rejected') {
						App_.callback(callback,'offline');
					} else {
						console.log(error);
					}

					App_.callback(callback,'error'); // set error callback
				});
			break;
		}
	}
	else {
		App_.callback(callback,'offline');
	}
}

/**
 * Get IP location
 * 
 * @param  {Function} callback
 * @param  {String}   ip
 * @param  {String}   source
 * @return {Object}
 */
exports.get_ip_location = function(callback, ip, source) {
	if (navigator.onLine) {
		ip = (ip !== '' || ip !== undefined)?ip:'';
		switch (source) {
			case 'freegeoip':
				$.get('https://freegeoip.app/json/'+ip).then(data => {App_.callback(callback,data)},error => {
					if (error.statusText == 'error' && error.state() == 'rejected') {
						App_.callback(callback,'offline');
					} else {
						console.log(error);
					}

					App_.callback(callback,'error');
				});
			break;

			default:
				$.get('https://geoip-db.com/json/'+ip).then(data => {App_.callback(callback,JSON.parse(data))},error => {
					if (error.statusText == 'error' && error.state() == 'rejected') {
						App_.callback(callback,'offline');
					} else {
						console.log(error);
					}

					App_.callback(callback,'error');
				});
			break;
		}
	} else {
		App_.callback(callback,'offline');
	}
}

/**
 * Is canvas supported
 * 
 * @return {Boolean}
 */
exports.is_canvas_supported = () => {
	var elem = document.createElement('canvas');
	return !!(elem.getContext && elem.getContext('2d'));
}

/**
 * Is private browser
 * 
 * @param  {Function} callback
 * @return {Boolean}
 */
exports.is_private_browser = function(callback) {
	var is_private;

	if (window.webkitRequestFileSystem) {
		window.webkitRequestFileSystem(window.TEMPORARY, 1,function() {
			is_private = false;
		},function(e) {
			is_private = true;
		});
	} else if (window.indexedDB && /Firefox/.test(window.navigator.userAgent)) {
		var db;
		try {
			db = window.indexedDB.open('test');
		} catch (e) {
			is_private = true;
		}

		if (typeof is_private === 'undefined') {
			retry_check_private_browser(
				function isDone() {
					return db.readyState === 'done' ? true : false;
				}, function next(is_timeout) {
					if (!is_timeout) {
						is_private = db.result ? false : true;
					}
				});
		}
	} else if (is_IE10_or_later(window.navigator.userAgent)) {
		is_private = false;
		try {
			if (!window.indexedDB) {
				is_private = true;
			}
		} catch (e) {
			is_private = true;
		}
	} else if (window.localStorage && /Safari/.test(window.navigator.userAgent)) {
		try {
			window.localStorage.setItem('test', 1);
		} catch (e) {
			is_private = true;
		}

		if (typeof is_private === 'undefined') {
			is_private = false;
			window.localStorage.removeItem('test');
		}
	}

	retry_check_private_browser(function isDone() {
		return typeof is_private !== 'undefined' ? true : false;
	}, function next(is_timeout) {
		App_.callback(callback,is_private)
	});
}

/**
 * Check IE 10 or later
 * 
 * @param  {String} user_agent
 * @return {Boolean}
 */
function is_IE10_or_later(user_agent) {
	var ua = user_agent.toLowerCase();
	if (ua.indexOf('msie') === 0 && ua.indexOf('trident') === 0) {
		return false;
	}
	
	var match = /(?:msie|rv:)\s?([\d\.]+)/.exec(ua);
	if (match && parseInt(match[1], 10) >= 10) {
		return true;
	}

	return false;
}

/**
 * Retry check private browser
 * 
 * @param {Boolean}  isDone
 * @param {Function} next
 */
function retry_check_private_browser(isDone, next) {
	var current_trial = 0,
	max_retry = 50,
	interval = 10,
	is_timeout = false;
	var id = window.setInterval(function() {
		if (isDone()) {
			window.clearInterval(id);
			next(is_timeout);
		}

		if (current_trial++ > max_retry) {
			window.clearInterval(id);
			is_timeout = true;
			next(is_timeout);
		}
	},10);
}

/**
 * Get canvas fingerprint
 * 
 * @param  {Object} options
 * @return {Array}
 */
function get_canvas_fingerprint(options) {
	if (exports.is_canvas_supported()) {
		var result = [];
		var canvas = document.createElement('canvas');
		
		canvas.width = 2000;
		canvas.height = 200;
		canvas.style.display = 'inline';
		
		var ctx = canvas.getContext('2d');
		ctx.rect(0, 0, 10, 10);
		ctx.rect(2, 2, 6, 6);

		result.push('canvas_winding:'+((ctx.isPointInPath(5, 5, 'evenodd') === false) ? 'yes' : 'no'));

		ctx.textBaseline = 'alphabetic';
		ctx.fillStyle = '#f60';
		ctx.fillRect(125, 1, 62, 20);
		ctx.fillStyle = '#069';
		if (options.dontUseFakeFontInCanvas) {
			ctx.font = '11pt Arial';
		} else {
			ctx.font = '11pt no-real-font-123';
		}

		ctx.fillText('Cwm fjordbank glyphs vext quiz, \ud83d\ude03', 2, 15);
		ctx.fillStyle = 'rgba(102, 204, 0, 0.2)';
		ctx.font = '18pt Arial';
		ctx.fillText('Cwm fjordbank glyphs vext quiz, \ud83d\ude03', 4, 45);
		ctx.globalCompositeOperation = 'multiply';
		ctx.fillStyle = 'rgb(255,0,255)';
		ctx.beginPath();
		ctx.arc(50, 50, 50, 0, Math.PI * 2, true);
		ctx.closePath();
		ctx.fill();
		ctx.fillStyle = 'rgb(0,255,255)';
		ctx.beginPath();
		ctx.arc(100, 50, 50, 0, Math.PI * 2, true);
		ctx.closePath();
		ctx.fill();
		ctx.fillStyle = 'rgb(255,255,0)';
		ctx.beginPath();
		ctx.arc(75, 100, 50, 0, Math.PI * 2, true);
		ctx.closePath();
		ctx.fill();
		ctx.fillStyle = 'rgb(255,0,255)';
		ctx.arc(75, 75, 75, 0, Math.PI * 2, true);
		ctx.arc(75, 75, 25, 0, Math.PI * 2, true);
		ctx.fill('evenodd');

		if (canvas.toDataURL) {
			result.push('canvas_fp:'+canvas.toDataURL());
		}

		return result;
	}
}

/**
 * Get WebGL canvas
 * 
 * @return {String}
 */
function get_webgl_canvas() {
	var canvas = document.createElement('canvas');
	var gl = null;

	try {
		gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
	} catch (e) {

	}

	if (!gl) {
		gl = null;
	}

	return gl;
}

/**
 * Get WebGL fingerprint
 * 
 * @return Array
 */
 function get_webgl_fingerprint() {
	var gl;
	var fa2s = function (fa) {
		gl.clearColor(0.0, 0.0, 0.0, 1.0);
		gl.enable(gl.DEPTH_TEST);
		gl.depthFunc(gl.LEQUAL);
		gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);

		return '[' + fa[0] + ', ' + fa[1] + ']';
	}

	var maxAnisotropy = function (gl) {
		var ext = gl.getExtension('EXT_texture_filter_anisotropic') || gl.getExtension('WEBKIT_EXT_texture_filter_anisotropic') || gl.getExtension('MOZ_EXT_texture_filter_anisotropic');
		if (ext) {
			var anisotropy = gl.getParameter(ext.MAX_TEXTURE_MAX_ANISOTROPY_EXT);
			
			if (anisotropy === 0) {
				anisotropy = 2
			}

			return anisotropy;
		} else {
			return null;
		}
	}

	gl = get_webgl_canvas();

	if (!gl) {
		return null;
	}

	var result = [];
	var vShaderTemplate = 'attribute vec2 attrVertex;varying vec2 varyinTexCoordinate;uniform vec2 uniformOffset;void main(){varyinTexCoordinate=attrVertex+uniformOffset;gl_Position=vec4(attrVertex,0,1);}';
	var fShaderTemplate = 'precision mediump float;varying vec2 varyinTexCoordinate;void main() {gl_FragColor=vec4(varyinTexCoordinate,0,1);}';
	var vertexPosBuffer = gl.createBuffer();
	gl.bindBuffer(gl.ARRAY_BUFFER, vertexPosBuffer);

	var vertices = new Float32Array([-0.2, -0.9, 0, 0.4, -0.26, 0, 0, 0.732134444, 0]);

	gl.bufferData(gl.ARRAY_BUFFER, vertices, gl.STATIC_DRAW);
	vertexPosBuffer.itemSize = 3;
	vertexPosBuffer.numItems = 3;

	var program = gl.createProgram();
	var vshader = gl.createShader(gl.VERTEX_SHADER);

	gl.shaderSource(vshader, vShaderTemplate)
	gl.compileShader(vshader)

	var fshader = gl.createShader(gl.FRAGMENT_SHADER);

	gl.shaderSource(fshader, fShaderTemplate);
	gl.compileShader(fshader);
	gl.attachShader(program, vshader);
	gl.attachShader(program, fshader);
	gl.linkProgram(program);
	gl.useProgram(program);
	program.vertexPosAttrib = gl.getAttribLocation(program, 'attrVertex');
	program.offsetUniform = gl.getUniformLocation(program, 'uniformOffset');
	gl.enableVertexAttribArray(program.vertexPosArray);
	gl.vertexAttribPointer(program.vertexPosAttrib, vertexPosBuffer.itemSize, gl.FLOAT, !1, 0, 0);
	gl.uniform2f(program.offsetUniform, 1, 1);
	gl.drawArrays(gl.TRIANGLE_STRIP, 0, vertexPosBuffer.numItems);

	try {
		result.push(gl.canvas.toDataURL());
	} catch (e) {
		/* .toDataURL may be absent or broken (blocked by extension) */
	}

	result.push('extensions:' + (gl.getSupportedExtensions() || []).join(';'));
	result.push('webgl aliased line width range:' + fa2s(gl.getParameter(gl.ALIASED_LINE_WIDTH_RANGE)));
	result.push('webgl aliased point size range:' + fa2s(gl.getParameter(gl.ALIASED_POINT_SIZE_RANGE)));
	result.push('webgl alpha bits:' + gl.getParameter(gl.ALPHA_BITS));
	result.push('webgl antialiasing:' + (gl.getContextAttributes().antialias ? 'yes' : 'no'));
	result.push('webgl blue bits:' + gl.getParameter(gl.BLUE_BITS));
	result.push('webgl depth bits:' + gl.getParameter(gl.DEPTH_BITS));
	result.push('webgl green bits:' + gl.getParameter(gl.GREEN_BITS));
	result.push('webgl max anisotropy:' + maxAnisotropy(gl));
	result.push('webgl max combined texture image units:' + gl.getParameter(gl.MAX_COMBINED_TEXTURE_IMAGE_UNITS));
	result.push('webgl max cube map texture size:' + gl.getParameter(gl.MAX_CUBE_MAP_TEXTURE_SIZE));
	result.push('webgl max fragment uniform vectors:' + gl.getParameter(gl.MAX_FRAGMENT_UNIFORM_VECTORS));
	result.push('webgl max render buffer size:' + gl.getParameter(gl.MAX_RENDERBUFFER_SIZE));
	result.push('webgl max texture image units:' + gl.getParameter(gl.MAX_TEXTURE_IMAGE_UNITS));
	result.push('webgl max texture size:' + gl.getParameter(gl.MAX_TEXTURE_SIZE));
	result.push('webgl max varying vectors:' + gl.getParameter(gl.MAX_VARYING_VECTORS));
	result.push('webgl max vertex attribs:' + gl.getParameter(gl.MAX_VERTEX_ATTRIBS));
	result.push('webgl max vertex texture image units:' + gl.getParameter(gl.MAX_VERTEX_TEXTURE_IMAGE_UNITS));
	result.push('webgl max vertex uniform vectors:' + gl.getParameter(gl.MAX_VERTEX_UNIFORM_VECTORS));
	result.push('webgl max viewport dims:' + fa2s(gl.getParameter(gl.MAX_VIEWPORT_DIMS)));
	result.push('webgl red bits:' + gl.getParameter(gl.RED_BITS));
	result.push('webgl renderer:' + gl.getParameter(gl.RENDERER));
	result.push('webgl shading language version:' + gl.getParameter(gl.SHADING_LANGUAGE_VERSION));
	result.push('webgl stencil bits:' + gl.getParameter(gl.STENCIL_BITS));
	result.push('webgl vendor:' + gl.getParameter(gl.VENDOR));
	result.push('webgl version:' + gl.getParameter(gl.VERSION));

	try {
		var extensionDebugRendererInfo = gl.getExtension('WEBGL_debug_renderer_info');
		if (extensionDebugRendererInfo) {
			result.push('webgl unmasked vendor:' + gl.getParameter(extensionDebugRendererInfo.UNMASKED_VENDOR_WEBGL));
			result.push('webgl unmasked renderer:' + gl.getParameter(extensionDebugRendererInfo.UNMASKED_RENDERER_WEBGL));
		}
	} catch (e) {

	}

	if (!gl.getShaderPrecisionFormat) {
		return result;
	}

	Array_.foreach(['FLOAT', 'INT'], function (numType) {
		Array_.foreach(['VERTEX', 'FRAGMENT'], function (shader) {
			Array_.foreach(['HIGH', 'MEDIUM', 'LOW'], function (numSize) {
				Array_.foreach(['precision', 'rangeMin', 'rangeMax'], function (key) {
					var format = gl.getShaderPrecisionFormat(gl[shader + '_SHADER'], gl[numSize + '_' + numType])[key];
					
					if (key !== 'precision') {
						key = 'precision ' + key;
					}

					var line = ['webgl ', shader.toLowerCase(), ' shader ', numSize.toLowerCase(), ' ', numType.toLowerCase(), ' ', key, ':', format].join('');
					result.push(line);
				});
			});
		});
	});

	return result;
}