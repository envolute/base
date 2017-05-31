//JQUERY
jQuery(function() {

  // SELECT AUTO-TAB
  var field_selectAutoTab	= ".auto-tab select, select.auto-tab";

  window.selectAutoTab = function (input, target, group, noTab) {
    input = setElement(input, field_selectAutoTab);
    input.each(function() {

      var obj = jQuery(this);
      var objFocus = setElement((isSet(target) ? target : '#'+obj.data('target').replace('#','')));
      var objGroup = setElement((isSet(group) ? group : obj.data('targetGroup')));

      obj.change(function() {

        var option = obj.find('option:selected');
        var disable = option.data('targetDisabled');
        var display = option.data('targetDisplay');
        var value = option.data('targetValue');
        var newStatus;

        // set disable tab action
        var tabDisabled = isSet(noTab) ? noTab : (isSet(obj.data('tabDisabled')) ? obj.data('tabDisabled') : false);
        // Caso seja um select 'multiple', não realiza o 'auto-tab'
        tabDisabled = (obj.is('select[multiple]')) ? true : false;

        // IMPORTANT: this must go before autoTab
        // 'objFocus' corrige o focus do 'select.chosen'
        if(elementExist(objFocus)) {
          // set target disable status
          if(disable != null) {
            newStatus = toggleDisabled(objFocus, disable);
            if(isSet(objGroup)) toggleDisabled(objGroup, (newStatus ? false : true));
          }
          // set target status display
          if(display != null) {
            newStatus = toggleDisplay(objFocus, display);
            if(isSet(objGroup)) toggleDisplay(objGroup, (newStatus ? false : true));
          }
          // TAB TO TARGET
          var el = objFocus;
          // Verifica se o elemento é um 'campo' ou um 'elemento html'
          if(!objFocus.is('input') && !objFocus.is('textarea') && !objFocus.is('select')) {
            if(objFocus.find('input, textarea, select.no-chosen').filter(':visible:first').length) {
              el = objFocus.find('input, textarea, select').filter(':visible:first');
            } else if(objFocus.find('select').filter(':first').length) {
              el = objFocus.find('select').filter(':first');
            }
          }
          // Set Tab Action
          // newStatus: (false) habilitado|show / (true) desabilitado|hidden
          if(elementExist(el) && !newStatus && !tabDisabled) inputGetFocus(el);
          // set value
          if(value != null && value != "undefined") el.val(value);
          // Verifica se é um select 'chosen'
          if(el.is('select') && el.next('.chosen-container').length) el.trigger("chosen:updated"); // select
        } else {
          // auto tab
          obj.autoTab();
        }
      });
    });
  };

});
