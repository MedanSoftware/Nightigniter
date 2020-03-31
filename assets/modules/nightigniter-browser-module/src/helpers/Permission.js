var App_ = require('./App');
var Identification_ = require('./Identification');

/**
 * Get notification permisssion
 * 
 * @param  {Function} callback
 */
exports.get_notification_permission = function(callback) {
	Identification_.is_private_browser(private_browser => {
		if (!private_browser) {
			if (!('Notification' in window)) {
				App_.callback(callback,'not_supported');
			} else if (Notification.permission === 'granted') {
				App_.callback(callback,'granted');
			} else if (Notification.permission !== 'denied') {
				Notification.requestPermission().then((permission) => {
					if (permission === 'granted') {
						App_.callback(callback,'granted');
					} else {
						App_.callback(callback,Notification.permission);
					}
				})
			} else {
				App_.callback(callback,Notification.permission);
			}
		} else {
			App_.callback(callback,'private_browser');
			console.error('cannot request notification permission in private browser');
		}
	});
}

/**
 * Register push notification
 * 
 * @param  {Function} callback
 */
exports.register_push_notification = function(callback) {
	Identification_.is_private_browser(private_browser => {
		if (!private_browser) {
			if (typeof App_.get_app_config().webpush !== 'undefined') {
				if ('serviceWorker' in navigator) {
					run().catch(error => {
						console.log('error')
						App_.callback(callback,error);
					});
				}

				async function run() {
					const serviceWorkerFile = URL_.base_url('/service-worker.js');
					const register = await navigator.serviceWorker.register(serviceWorkerFile, {
						scope: App_.site_path()
					});

					register.addEventListener('updatefound',() => {
						const newWorker = register.installing;
						console.log('service worker has update');
						newWorker.addEventListener('statechange', () => {
							console.log(newWorker.state+' service worker updated successfully');
						});
					});

					const subscription = await register.pushManager.subscribe({
						userVisibleOnly: true,
						applicationServerKey: String_.base64_to_int8_array(App_.get_app_config().webpush.public_vapid_key)
					});

					App_.callback(callback,subscription);
				}
			} else {
				console.error('webpush not configured correctly');
				App_.callback(callback, {code:1, message:'web push not configured'});
			}
		} else {
			console.error('cannot request notification permission in private browser');
		}
	});
}