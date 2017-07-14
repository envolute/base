//JQUERY
jQuery(function() {

	// NAV MENU DEFINITIONS
	// Definições padrão do elemento '.nav.menu'
	window.navMenu = function(){

		window.itemFocusOn = false;

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
				a.add(b).off().on('mousedown', function(e) { // menu 'opened' no 'clique'
					e.preventDefault();
					setOpened(obj);
				}).on('focus', function(e) { // menu 'opened' no 'focus' (navega com a tecla TAB)
					e.preventDefault();
					setOpened(obj);
				});
			});
			// Navegação com 'focus'
			// Fecha o submenu quando o foco sai do elemento e/ou seus 'filhos'
			jQuery('.nav.menu li').each(function() {
				var obj = jQuery(this);
				var a = obj.children('a');
				var b = obj.children('a').find('a.child-reveal');
				a.on('focus', function(e) {
					var s = jQuery(this).parent('li');
					// No 'focus' seta a classe 'focused' para indicar que
					// existe um elemento com o foco. Isso faz com que o menu
					// não se feche quando estiver navegando em um sub menu.
					jQuery(this).addClass('focused');
					// fecha todos os submenus, com exceção do que está com o foco
					jQuery(this).closest('ul').find('li.parent').not(s).removeClass('opened');
				});
				// Atribui a classe 'focused' também no link '.child-reveal'
				b.on('focus', function(e) { jQuery(this).addClass('focused'); });
				a.add(b).on('blur', function(e) {
					// Quando o foco sai do objeto, a classe 'focused' é removida
					// para indicar que o mesmo não está mais com o foco
					jQuery(this).removeClass('focused');
					var p = jQuery(this).parents('ul').last();
					// setTimeout define um tempo para verificar se ainda existe
					// algum elemento com o foco no menu. Caso não exista,
					// todos os submenus são fechados.
					setTimeout(function() {
						if(!p.find('a.focused').length) p.find('li.opened').removeClass('opened');
					}, 100);
				});
			});
			// seta o item pai como ativo quando o filho estiver ativo
			jQuery('.nav.menu li a:hover, .nav.menu li.active').parents('li').addClass('active');
		}
		// Função para atribuir a classe 'opened' ao objeto
		function setOpened(obj) {
			obj.toggleClass('opened');
			// remove também dos sub-itens
			if(!obj.hasClass('opened')) obj.find('li.opened').removeClass('opened');
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
    var menu = jQuery('.nav.menu[class*=" stacked-"]');
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
