//JQUERY
jQuery(function() {

	// COPY CONTENT TO CLIPBOARD
	// Basta passar o elemento que contém o texto ou o próprio texto
	window.copyToClipboard = function(e, message) {
		var $temp = jQuery("<input>");
		jQuery("body").append($temp);
		if(typeof e === 'string') {
			$temp.val(e).select();
		} else {
			$temp.val(jQuery(e).text()).select();
		}
		document.execCommand("copy");
		$temp.remove();
		if(isSet(message)) $.baseNotify({
			msg: message,
			alertTime: 3000
		});
	};

});
