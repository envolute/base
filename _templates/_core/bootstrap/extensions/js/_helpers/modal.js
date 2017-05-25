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

});
