// SCRIPTS CUSTOMIZADOS DO GUIA DE ESTILO

//JQUERY
jQuery(function() {

  // MENU

    // AFFIX MENU

    var topSpace = 70; // space from top
    var bottomSpace = 20; // space from top
    var navSpace = topSpace + bottomSpace;
    var navAffix = jQuery('#guide-menu').offset().top - (navSpace);
    var footerAffix = jQuery('#footer').outerHeight(true) + bottomSpace;
    jQuery('#guide-menu').affix({ offset: { top: navAffix } });

    // VISIBLE HEIGHT

    var navguide	= jQuery('#guide-menu');
    if(navguide.length) {
      visibleHeight(navguide, navSpace);
      jQuery(window).resize(function() { visibleHeight(navguide, navSpace); });
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
