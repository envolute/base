//JQUERY
jQuery(function() {

  // RESPONSIVE DEFINITIONS

  window.responsive = function (width) {

    // RESPONSIVE CLASSES
    // "media" define o formato mínimo
    jQuery('html').addClass('media-xs');
    if (width >= _XL_) jQuery('html').addClass('media-xl');
    else jQuery('html').removeClass('media-xl');
    if (width >= _LG_) jQuery('html').addClass('media-lg');
    else jQuery('html').removeClass('media-lg');
    if (width >= _MD_) jQuery('html').addClass('media-md');
    else jQuery('html').removeClass('media-md');
    if (width >= _SM_) jQuery('html').addClass('media-sm');
    else jQuery('html').removeClass('media-sm');

  };

});
