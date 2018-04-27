//JQUERY
jQuery(function() {

	// LINK ACTIONS DEFAULT
	// Definições default para links
	window.linkActionsDef = function () {

		// LINK AVOID
		// desabilita ação quando link for apenas '#'
		jQuery('a[href$="#"]').click(function (e) { e.preventDefault(); });

		// LINK PADRÃO PARA IMPRESSÃO
		jQuery('#action-print').click(function (e) {
			print();
			e.preventDefault();
		});

	};

});
