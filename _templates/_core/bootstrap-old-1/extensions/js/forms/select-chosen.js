//JQUERY
jQuery(function() {

  // CHOSEN WIDTH
  // Define a largura do elemento chosen
  // Utiliza a biblioteca 'jquery.actual.js' para 'pegar' a largura de elementos 'hidden'
  window.setChosenWidth = function(select) {
    var sel = setElement(select, jQuery('select').not('.no-chosen'));
    if(elementExist(sel)) {

      // SET WIDTH
      // para resolver o problema da largura = 0 para selects 'hidden'
      // O chosen é atribuído a cada um 'select:hidden' separadamente
      // assim é possível setar a largura através do plugin 'jquery.actual.js'
      // pois ele consegue 'trazer' as dimensões dos elementos 'hidden'
      sel.next('.chosen-container:hidden').each(function() {
        var s = jQuery(this).prev('select');
        jQuery(this).width(s.actual('outerWidth'));
      });

      // INSIDE 'INPUT-GROUP'
      // Formata o elemento chosen dentro de um elemento 'input-group'
      // Seta uma largura fixa para validar o 'overflow' do select
      var chosensLen = jQuery('.input-group').find('.chosen-container').length;
      chosensLen += 2; // z-index mínimo do chosen-container é 3 ("2" acima do mínimo default => 1)
      jQuery('.input-group').find('.chosen-container').each(function(index, el) {
        var obj = jQuery(this);
        var dpl = obj.find('.chosen-single');
        var grp = obj.closest('.input-group');
        obj.css('position','absolute');
        var w = grp.actual('outerWidth');
        obj.css('position','');
        var wGroup = 0;
        grp.find('.input-group-btn > *, .input-group-addon').each(function() {
          wGroup += jQuery(this).actual('outerWidth');
        });
        w = w - wGroup;
        obj.width(w);
        // atribui uma largura para implementar o 'overflow' do select
        if(elementExist(dpl)) dpl.width(w - dpl.css('padding-left').replace('px', ''));
        // corrige o z-index do select
        obj.css('z-index', chosensLen - index);
      });

    }
  };

  // SET CHOSEN DEFAULT
  window.setChosenDefault = function() {

    // CHOSEN DEFAULT DEFINITIONS
    var chzSearch = 10;
    var chzNoResults = 'Sem resultados para';
    // Atribui o chosen default para todos os selects
    var sel = jQuery('select').not('.no-chosen');
    // SET CHOSEN
    if(elementExist(sel)) {
      sel.chosen({
          disable_search_threshold: chzSearch,
          no_results_text: chzNoResults,
          placeholder_text_single: " ",
          placeholder_text_multiple: " "
      }).addClass('has-chosen');
      setChosenWidth();
      // jquery.validation -> remove error class when chosen is change
      var chznError;
      sel.change(function() {
        jQuery(this).removeClass('error');
        chznError = jQuery(this).next('.chosen-container').next('.error');
        if(chznError.length) chznError.remove();
      });
    }

  };

});
