// CUSTOMIZAÇÕES, ADAPTAÇÕES E CORREÇÕES DO CMS

jQuery(function() {

  // define a class 'btn-default' para elementos '.btn' legado do bootstrap 2
  jQuery('.btn:not([class*="btn-"])').each(function() {
    jQuery(this).addClass('btn-default');
  });

  // Chosen -> campos chosen com largura de 100%
  jQuery('#jform_tags').next('.chosen-container').width('100%');


});
