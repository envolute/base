//JQUERY
jQuery(function() {

	// GET URL BASE FROM INPUT FIELD INTO TEMPLATE BASE
	window.URLBase = jQuery('#baseurl').val();

	// GET INFO ABOUT BROWSER
	window.nua = navigator.userAgent;

	// Essa função verifica se o parâmetro foi passado
	window.isSet = function (e) {
		return (e == null || typeof e === "null" || typeof e === "undefined") ? false : true;
	};

	// Essa função verifica se o parâmetro é uma string vazia
	window.isEmpty = function (e) {
	  return (e == "") ? true : false;
	};

	// Essa função é identica a 'setElement' em core.js
	// isso é para não haver dependência do core.js
	window.setElement = function (e, def) {
		var obj = e;
		if(!isSet(e) && isSet(def)) {
			obj = jQuery(def);
		} else if(typeof e === 'string') {
			e = e.replace('##', '#'); // evita erro no ID do elemento
			obj = jQuery(e);
		}
		return obj;
	};

	// Essa função verifica se o parâmetro foi passado
	window.elementExist = function (e) {
		if(isSet(e)) {
			var obj = setElement(e);
			return (obj.length) ? true : false;
		}
		return false;
	};

	// Essa função verifica a largura da janela
	window.getPageWidth = function () {
		var e = window, a = 'inner';
		if (!('innerWidth' in window )) {
			a = 'client';
			e = document.documentElement || document.body;
		}
		return e[ a+'Width' ];
	};

	// Tradução
	// text => A váriável a ser traduzida 'TEXT_LABEL'
	// lang => language (default is 'pt-BR')
	// arr  => Objeto com as variáveis e valores das traduções (default is 'jsLang')
	window.JSText_ = function (text, lang, obj) {
		var l = isSet(lang) ? lang : 'pt-BR';
		var a = isSet(obj) ? obj : jsLang;
		return isSet(a[l][text]) ? a[l][text] : text;
	};

	// BREAKPOINTS
	window._XS_ = 0;
	window._SM_ = 576;
	window._MD_ = 768;
	window._LG_ = 992;
	window._XL_ = 1200;

	// get current width and height
	window._WIDTH_ = getPageWidth();
	window._IS_WIDTH_CHANGE_ = false;
	window._HEIGHT_ = jQuery(window).height();
	window._IS_HEIGHT_CHANGE_ = false;

});
