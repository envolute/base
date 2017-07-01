//JQUERY
jQuery(function() {

  // BUTTON TOGGLE STATE
  // Alterna, no clique do botão, a classe 'active'
  // Essa é uma opção para ser utilizada através da classe ou da chamada da função
  // O Bootstrap também possui essa funcionalidade através da propriedade 'data-toggle'
  // Ex: <button data-toggle="button">
  // targetInactive: Marca o 'target' como desativado (remove a classe 'active')
  // Essa opção possibilita 'desativar' um elemento a partir do click no botão
  // Um exemplo é quando o botão 'collapse-all' (target) está ativo e
  // outro altera um dos elementos selecionados. Assim desativa o target 'collapse-all'

  window.btnToggleState = function(button, targetInactive) {
    var btn = setElement(button, '.btn.toggle-state');
    btn.each(function() {
      var obj = jQuery(this);
      obj.on('click',function(e) {
        obj.not(':disabled').not('.disabled').toggleClass('active');
        // DISABLE STATE OF THE TARGET
        var target = isSet(targetInactive) ? targetInactive : false;
        target = isSet(obj.data('targetInactive')) ? setElement(obj.data('targetInactive')) : target;
        if(target && target.hasClass('active')) {
          target.removeClass('active');
          // Caso o 'target' execute a funcionalidade 'toggleIcon'
          toggleIcon(target);
        }
      });
    });
  };

});
