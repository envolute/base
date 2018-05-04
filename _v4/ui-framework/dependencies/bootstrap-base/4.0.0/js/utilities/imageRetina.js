//JQUERY
jQuery(function() {

	window.replaceIfRetinaExists = function(obj, url) {
		var img = new Image();
		img.onerror = function() { return false; };
		img.onload = function() {
			// As propriedades da imagem "normal" (obj) só ficam disponíveis
			// após o carregamento da imagem. Porém, como a imagem "retina"
			// é maior que a imagem normal, entende-se que quando ela carregar
			// a imagem "normal" já tenha sido carregada antes. Assim,
			// as propriedade 'width e height' já devem estar disponíveis
			w = obj.width();
			h = obj.height();
			obj.attr('src',url).css({'width':w,'height':h});
		};
		img.src = url;
	}

	// IMAGE RETINA
	// Detecta se o monitor é de alta resolução e atribui às
	// substitui as imagens com a class "img-retina" pela imagem "retina"
	// IMPORTANTE:
	// A imagem "retina" segue o padrão de nomenclatura "@2x"
	// Ex: image.png => image@2x.png {no mesmo diretório}
	window.imageRetina = function() {
		var query = '(-webkit-min-device-pixel-ratio: 2), (min-device-pixel-ratio: 2), (min-resolution: 192dpi)';
		if(!matchMedia(query).matches) { // RETINA -> high-dpi stuff
			jQuery('img.img-retina').each(function() {
				obj = jQuery(this);
				src = jQuery(this).attr('src');
				ext = src.split('.').pop();
				img = src.replace('.'+ext, '@2x.'+ext);
				replaceIfRetinaExists(obj, img);
			});
		}
	};

});
