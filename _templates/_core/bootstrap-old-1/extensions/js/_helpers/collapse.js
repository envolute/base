//JQUERY
jQuery(function() {

  // MODAL HELPERS
  window.collapseAll = function(grp, btnGrp) {

    var btn = jQuery('.collapse-all');
    // Botão do item individual
    // Obs: ele deve conter a classe 'collapse-group-btn' para ter seu estado alterado de acordo
    // No botão 'collapse-all' essa classe pode ser customizada através o atributo 'data-collapse-buttons'
    var btnGroup = setElement(btnGrp, '.collapse-group-btn');
    // Grupo de itens que serão 'collapsed'
    // Obs: eles deverão ter a classe 'collapse-group' para receber a ação
    // No botão 'collapse-all' essa classe pode ser customizada através o atributo 'data-collapse-group'
    var group = setElement(grp, '.collapse-group');

    btn.each(function() {
      var obj = jQuery(this);
      // Get 'data' attr from 'btn.collapse-all'
      group = isSet(obj.data('collapseGroup')) ? setElement(obj.data('collapseGroup')) : group;
      btnGroup = isSet(obj.data('collapseButtons')) ? setElement(obj.data('collapseButtons')) : btnGroup;
      if(!group.length) return;
      // COLLAPSE ALL GROUP
      // Mostra/esconde todos os itens de um grupo
      obj.click(function(e) {
        if(obj.hasClass('active')) {
          group.collapse('hide');
          // Remove o estado 'ativo' em todos os botões
          obj.add(btnGroup).removeClass('active');
        } else {
          group.collapse('show');
          // Seta o estado 'ativo' em todos os botões
          obj.add(btnGroup).addClass('active');
        }
        // Caso o grupo de botões executem a função 'toggleIcon'
        // Executa a ação sem a necessidade do 'click'
        toggleIcon(btnGroup);
      });
    });

  };

});
