// SCRIPTS CUSTOMIZADOS DO GUIA DE ESTILO

//JQUERY
jQuery(function() {

  // MENU

    // AFFIX MENU

    var topSpace = jQuery('#header').outerHeight(true) + jQuery('#content-toolbar').outerHeight(true); // space from top
    var bottomSpace = jQuery('#footer').outerHeight(true); // space from top
    // var navSpace = topSpace + bottomSpace;
    var navAffix = topSpace; //jQuery('#guide-menu').offset().top;
    // var footerAffix = jQuery('#footer').outerHeight(true) + bottomSpace;
    jQuery('#guide-menu').affix({ offset: { top: navAffix } }).css('top', topSpace);

    // VISIBLE HEIGHT

    var navguide	= jQuery('#guide-menu');
    if(navguide.length) {
      visibleHeight(navguide, 10, '#header, #content-toolbar, #user-info, #footer');
      jQuery(window).resize(function() { visibleHeight(navguide, 10, '#header, #content-toolbar, #user-info, #footer'); });
    }

    // SCROLLSPY

    if(jQuery('#guide-menu').length) {
      jQuery('body').scrollspy({
        target: '#guide-menu',
        offset: parseInt(jQuery(window).height() / 2) // 50% to bottom from top
      });
    }

    // SCROLL

    gotoElement('#guide-menu a', topSpace);

});
