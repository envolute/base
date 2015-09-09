// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// AFFIX ELEMENTS

		jQuery('#header').affix({ offset: { top: 10 } });
		jQuery('#toolbar-btns').affix({ offset: { top: 1 } });

	// SHOW/HIDE SCROLL-TO-TOP BUTTON

		window.scrollToTop = function() {
			var obj = jQuery('#scroll-to-top');
			var pos = jQuery(window).scrollTop();
			if(pos > 200) obj.fadeIn();
			else obj.fadeOut();
		};
		scrollToTop();
		jQuery(window).scroll(function(){ scrollToTop() });

	// SET DEFAULT VALIDATION CONFIGURATIONS

		jQuery.validator.setDefaults({
			debug: true,
			success: "valid",
			ignore: ":hidden:not(select)",
			ignore: ".chosen-choices input, .chosen-search input"
		});
		jQuery.extend(jQuery.validator.messages, {
			required: "Campo Obrigatório",
			remote: "Por favor, verifique esse campo",
			email: "Informe uma e-mail válido",
			url: "Informe uma URL válida",
			date: "Informe uma data válida",
			dateISO: "Informe uma data válida (ISO).",
			number: "Informe um número válido",
			digits: "Informe apenas digitos",
			creditcard: "Informe um número de cartão de crédito válido",
			equalTo: "Campos com valores diferentes",
			accept: "Extensão Inválida!",
			maxlength: jQuery.validator.format("Informe no máximo {0} caracteres."),
			minlength: jQuery.validator.format("Informe ao menos {0} caracteres."),
			rangelength: jQuery.validator.format("Informe um valor de {0} à {1} caracteres."),
			range: jQuery.validator.format("Informe um valor entre {0} e {1}."),
			max: jQuery.validator.format("Informe um valor menor igual à {0}."),
			min: jQuery.validator.format("Informe um valor maior igual à {0}.")
		});

	// MENU

	// TOGGLE WIDTH

		jQuery('#set-resolution').click(function() {
			jQuery('#content-body').toggleClass('fullScreen');
		});

});
