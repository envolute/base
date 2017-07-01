//JQUERY
jQuery(function() {

  // SMOOTH SCROLL
  window.scrollTo = function(obj, offSet){
    var e;
    var topSpace = (isSet(offSet)) ? offSet : 0;
    if(obj == null) {
      e = jQuery('body');
    } else {
      var object = (jQuery.type(obj) === "string") ? jQuery(obj) : obj;
      e = (elementExist(object)) ? jQuery(obj) : jQuery('body');
    }
    jQuery('html,body').animate({scrollTop: e.offset().top - topSpace},'slow');
  };

  // #GOTO -> SMOOTH ANCHOR
  window.gotoElement = function(elem, offset) {
    // Basta utilizar a classe "go-to".
    // IMPORTANTE: Apenas para navegação na mesma página;
    // -> Não utilizar em links de menu, pois não será acessado de outra página
    var e = setElement(elem, 'a.go-to');
    if(elementExist(e)) {
      e.each(function() {
        jQuery(this).click(function(e) {
          var obj = jQuery(this);
          if(location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var offSet = (offset != null) ? offset : (obj.data('offset') != null ? obj.data('offset') : 0);
            var target = this.hash;
            target = elementExist(target) ? target : jQuery('[name=' + target.slice(1) +']');
            if(elementExist(target)) {
							scrollTo(target, offSet);
							return false;
						}
          }
        });
      });
    }
  };

});
