// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// AFFIX ELEMENTS

		jQuery('#header').affix({ offset: { top: 10 } });
		jQuery('#toolbar-btns').affix({ offset: { top: 1 } });

	// TOGGLE WIDTH

		jQuery('#set-resolution').click(function() {
			jQuery('#content-body').toggleClass('fullScreen');
		});

});
