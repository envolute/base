//JQUERY
jQuery(function() {

	// MODAL HELPERS
	window.modalHelpers = function() {

		// FIX SCROLL NESTED MODALS
		// Corrige o scroll quando há mais de uma modal aberta
		jQuery(document).on('hidden.bs.modal', '.modal', function () {
		jQuery('.modal:visible').length && jQuery(document.body).addClass('modal-open');
		});

	};

	// SET BASE MODAL
	// Chama a modal padrão...
	window.setBaseModal = function() {

		jQuery('.set-base-modal').each(function() {
			var trg = isSet(jQuery(this).data('target')) ? jQuery(this).data('target') : jQuery(this).attr('href');
			var obj = setElement(trg);
			var input = jQuery(this).data('inputFocus');
			if(isSet(obj)) {
				jQuery(this).click(function() {
					obj.modal('show');
				});
				obj.on('shown.bs.modal', function () {
					jQuery(this).find(input).focus();
				});
			} else {
				console.log('SetBaseModal: O elemento "'+obj+'" não está definido no código!');
			}
		});

	};

});
