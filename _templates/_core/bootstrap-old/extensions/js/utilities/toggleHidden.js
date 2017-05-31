// TOGGLE HIDDEN
// Alterna a propriedade [hidden] do elemento
// Ex: <div id="element" hidden>...</div>
// Ex: jQuery('#element').toggleHidden();
jQuery.fn.toggleHidden = function() {
  jQuery(this).prop('hidden', (jQuery(this).is(':hidden') ? false : true));
}
