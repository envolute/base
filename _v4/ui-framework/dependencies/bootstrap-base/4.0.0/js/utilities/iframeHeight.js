//JQUERY
jQuery(function() {

	// SET IFRAME HEIGHT
	// Seta a altura do iframe com base na altura total do conteúdo do iframe
	window.iframeHeight = function(elem) {
		var e = setElement(elem, '.set-iframe-height');
		if(elementExist(e)) {
			e.each(function() {
				obj = jQuery(this);
				// The Iframe's child page BODY element.
				var iframe_content = obj.contents().find('body');
				iframe_content.css('height','auto');

				// Bind the resize event. When the iframe's size changes, update its height as
				// well as the corresponding info div.
				iframe_content.resize(function(){
					var elem = jQuery(this);
					// Resize the IFrame.
					obj.css({ height: elem.outerHeight(true) });
				});

				// Resize the Iframe and update the info div immediately.
				iframe_content.resize();
			});
		}
	};


	// redimensiona os iframes após a página ser carregada
	window.setIframeHeight = function(elem) {
		var e = setElement(elem, '.set-iframe-height');
		iframeHeight(e);
		e.on("load", function () {
			iframeHeight(e);
		});
	};

});
