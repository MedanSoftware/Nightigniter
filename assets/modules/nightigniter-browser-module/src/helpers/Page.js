var App_ = require('./App');

/**
 * Print page
 * 
 * @param  {String} element DOM element
 */
exports.print_page = (element) => {
	document.body.innerHTML = element;
	window.focus();
	window.print();
	window.location.reload();
}

/**
 * Scroll to top
 * 
 * @param  {Function} callback
 * @param  {Number}   speed
 */
exports.scroll_to_top = (callback, speed = 2280) => {
	$('html, body').animate({scrollTop:0},speed,() => {
		App_.callback(callback);
	});
}

/**
 * Trigger on bottom page
 * 
 * @param  {Function} callback
 */
exports.on_bottom = (callback) => {
	$(window).on('scroll', function(){
		var scrollHeight = $(document).height();
		var scrollPosition = $(window).height() + $(window).scrollTop();

		if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
    		App_.callback(callback);
		}
	});
}

/**
 * Full screen
 * 
 * @param  {Function} callback
 */
exports.full_screen = function(callback) {
	if ((document.fullScreenElement && document.fullScreenElement !== null) || (!document.mozFullScreen && !document.webkitIsFullScreen)) {
		if (document.documentElement.requestFullScreen) {
			document.documentElement.requestFullScreen();
		} else if (document.documentElement.mozRequestFullScreen) {
			document.documentElement.mozRequestFullScreen();
		} else if (document.documentElement.webkitRequestFullScreen) {
			document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
		}

		App_.callback(callback,'full_screen');
	} else {
		if (document.cancelFullScreen) {
			document.cancelFullScreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
		}

		App_.callback(callback,'exit_full_screen');
	}
}