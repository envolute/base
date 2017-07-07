//JQUERY
jQuery(function() {

  // NAV MENU DEFINITIONS
  // Definições padrão do elemento '.nav.menu'
  window.navMenu = function(){

    // NAV MENU -> menu horizontal & vertical
    // Atribui a class parent se não houver
    var navChild = jQuery('.nav.menu .nav-child');
    if(elementExist(navChild)) {
        navChild.each(function() {
          if(!jQuery(this).closest('li').hasClass('parent')) jQuery(this).closest('li').addClass('parent');
        });
    }
    // Indicador de sub-item
    if(elementExist(jQuery('.nav.menu'))) {
      jQuery('.nav.menu li.parent > a').each(function() {
        if(!elementExist(jQuery(this).find('.child-reveal'))) { // verifica se já foi 'appended'
          if(jQuery(this).attr('href') == '#') {
            jQuery(this).append('<span class="child-reveal"></span>');
          } else {
            jQuery(this).append('<a href="#" class="child-reveal"></a>');
          }
        }
      });
      // Abre o sub-menu no 'click'
      jQuery('.nav.menu li.parent').each(function() {
        var obj = jQuery(this);
        var a = obj.children('a[href="#"]');
        var b = obj.children('a').find('a.child-reveal');
        a.add(b).off().on('click focus', function(e) {
          e.preventDefault();
          obj.toggleClass('opened');
          // remove também dos sub-itens
          if(!obj.hasClass('opened')) obj.find('li.opened').removeClass('opened');
        });
      });
      // seta o item pai como ativo quando o filho estiver
      jQuery('.nav.menu li a:hover, .nav.menu li.active').parents('li').addClass('active');
    }

  };

  // NAV STACKED -> Seta o menu como vertical
  // É necessário setar a class 'stacked-*' para que o menu 'quebre' no ponto deteminado
  // 'stacked-lg': 'quebra' a partir da resolução 'lg'
  // 'stacked-md': 'quebra' a partir da resolução 'md'
  // 'stacked-sm': 'quebra' a partir da resolução 'sm'
  // 'stacked-xs': 'quebra' a partir da resolução 'xs'
  // Obs: para que o menu seja vertical sempre basta adicionar as classes 'nav-stacked stacked'
  window.navStacked = function() {
    var menu = jQuery('.nav.menu[class*="stacked-"]');
    if(elementExist(menu)) {
      menu.each(function() {
        var obj = jQuery(this);
        if(!obj.hasClass('nav-stacked') || obj.hasClass('stacked')) {
          if(obj.hasClass('stacked-lg') && _WIDTH_ < _XL_) obj.addClass('nav-stacked stacked');
          else if(obj.hasClass('stacked-md') && _WIDTH_ < _LG_) obj.addClass('nav-stacked stacked');
          else if(obj.hasClass('stacked-sm') && _WIDTH_ < _MD_) obj.addClass('nav-stacked stacked');
          else if(obj.hasClass('stacked-xs') && _WIDTH_ < _SM_) obj.addClass('nav-stacked stacked');
          else obj.removeClass('nav-stacked stacked'); // reseta
        }
      });
    }
  };

});
