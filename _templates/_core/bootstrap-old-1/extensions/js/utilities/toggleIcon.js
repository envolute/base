//JQUERY
jQuery(function() {

  // TOGGLE ICON
  // Alterna o ícone de um determinado elemento
  window.toggleIcon = function(elem, iconDefault, iconActive) {

    var el = setElement(elem, '.toggle-icon');
    el.each(function() {

      var obj = jQuery(this);
      // Icon Default -> Target
      var iconDef = isSet(iconDefault) ? iconDefault : false;
      iconDef = isSet(obj.data('iconDefault')) ? obj.data('iconDefault') : iconDef;
      // Icon Active
      var iconAct = isSet(iconActive) ? iconActive : false;
      iconAct = isSet(obj.data('iconActive')) ? obj.data('iconActive') : iconAct;
      // Localiza o elemento 'alvo'
      var target = obj.hasClass(iconDef) ? obj : obj.find('.'+iconDef);
      // Caso o icone 'default' do elemento tenha sido alternado, ele não será encontrado
      // Assim, verifica se o elemento tem o ícone 'ativo'
      if(!target.length) target = obj.hasClass(iconAct) ? obj : obj.find('.'+iconAct);
      if(!target.length) target = obj;

      if(elementExist(target) && iconDef && iconAct) {
        // INICIALIZA
        // Atribui o ícone de acordo com o estado atual do botão 'default/active'
        if(obj.hasClass('active')) target.removeClass(iconDef).addClass(iconAct);
        else target.removeClass(iconAct).addClass(iconDef);
        // TOGGLE
        // Alterna o ícone no 'click'
        obj.on('click',function(e) {
          setTimeout(function() {
            if(obj.hasClass('active')) target.removeClass(iconDef).addClass(iconAct);
            else target.removeClass(iconAct).addClass(iconDef);
          }, 100);
        });
      }

    });

  };

});
