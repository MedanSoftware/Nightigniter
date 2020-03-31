/**
 * Nightigniter Service Worker
 *
 * @version 1.0.0
 * @package Nightigniter Browser Module
 * @subpackage Service Worker
 * @author Agung Dirgantara <agungmasda29@gmail.com>
 */

'use strict';

let app = {
	name :'Nightigniter',
	version : '1.0.0',
	api_url : REPLACE_API_URL
}

/**
 * install service worker
 * 
 * @param  {String} event
 */
self.addEventListener('install', event => {
	console.log('installing '+app.name+' Service Worker Version : '+app.version);
	self.skipWaiting();
});

/**
 * listen webpush
 * 
 * @param  {String} event event name (push tracker)
 */
self.addEventListener('push', event => {
	let data = JSON.parse(event.data.json());

	if (data.data !== undefined) {
		if (typeof data.data.campaign_uid !== 'undefined') {
			let campaign_uid = (typeof data.data.campaign_uid !== 'undefined')?data.data.campaign_uid:false;
			let subscriber_id = (typeof data.data.subscriber_id !== 'undefined')?data.data.subscriber_id:false;
			// push_tracker(campaign_uid, {subscriber_id:subscriber_id, status:'delivered'});
		}
	}

	self.registration.showNotification(data.title, data);
});

/**
 * push notification - onclick
 */
self.addEventListener('notificationclick', function(event) {
	event.notification.close();
	event.waitUntil(
		clients.matchAll({
			type: 'window'
	}).then(function(clientList){
		for (var i=0;i<clientList.length;i++) {
			var client = clientList[i];
			if (client.url == '/' && 'focus' in client) {
				return client.focus();
			}
		}

		if (clients.openWindow) {
			if (typeof event.notification.data.url !== 'undefined') {
				if (typeof event.notification.data.campaign_uid !== 'undefined') {
					let campaign_uid = (typeof event.notification.data.campaign_uid !== 'undefined')?event.notification.data.campaign_uid:false;
					let subscriber_id = (typeof event.notification.data.subscriber_id !== 'undefined')?event.notification.data.subscriber_id:false;
					// push_tracker(campaign_uid, {subscriber_id:subscriber_id,status:'opened'});
				}

				return clients.openWindow(event.notification.data.url);
			}
		}
	}));
});

/**
 * push notification - onclose
 */
self.addEventListener('notificationclose', function(event) {
	if (typeof event.notification.data.campaign_uid !== 'undefined') {
		let campaign_uid = (typeof event.notification.data.campaign_uid !== 'undefined')?event.notification.data.campaign_uid:false;
		let subscriber_id = (typeof event.notification.data.subscriber_id !== 'undefined')?event.notification.data.subscriber_id:false;
		// push_tracker(campaign_uid,{subscriber_id:subscriber_id,status:'closed'});
	}
});