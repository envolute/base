//JQUERY
jQuery(function() {

	// COPY CONTENT TO CLIPBOARD
	// Basta passar o elemento que contém o texto ou o próprio texto
	window.copyToClipboard = function(e) {
		var $temp = jQuery("<input>");
		jQuery("body").append($temp);
		if(typeof e === 'string') {
			$temp.val(e).select();
		} else {
			$temp.val(jQuery(e).text()).select();
		}
		document.execCommand("copy");
		$temp.remove();
	};

});
