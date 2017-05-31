//JQUERY
jQuery(function() {

  // SELECT TO SELECT
  // Preenche um select a partir de outro
	// Obs: 'element' é referente ao container
	window.selectToSelect = function (element) {
    var field	= ".select-order";
		el = setElement(element, field);
	  el.each(function() {
		  var obj = jQuery(this);
			var selFrom = obj.find('.select-from');
			var selTo = obj.find('.select-to');
			h = isSet(obj.data('height')) ? obj.data('height') : 100;
			selFrom.add(selTo).css('min-height', h);
			obj.find('.btn-add').click(function(){
				selFrom.find('option:selected').each( function() {
					selTo.append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
          jQuery(this).remove();
        });
	    });
	    obj.find('.btn-remove').off('click').on('click', function(){
        selTo.find('option:selected').each( function() {
          selFrom.append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
          jQuery(this).remove();
        });
	    });
	    obj.find('.btn-up').off('click').on('click', function() {
        selTo.find('option:selected').each( function() {
          var newPos = selTo.find('option').index(this) - 1;
          if (newPos > -1) {
            selTo.find('option').eq(newPos).before("<option value='"+jQuery(this).val()+"' selected='selected'>"+jQuery(this).text()+"</option>");
            jQuery(this).remove();
          }
        });
	    });
	    obj.find('.btn-down').off('click').on('click', function() {
        var countOptions = obj.find('.select-to option').size();
				var countSelected = obj.find('.select-to option:selected').size();
        selTo.find('option:selected').each( function() {
          var newPos = selTo.find('option').index(this) + countSelected;
          if (newPos < countOptions) {
            selTo.find('option').eq(newPos).after("<option value='"+jQuery(this).val()+"' selected='selected'>"+jQuery(this).text()+"</option>");
            jQuery(this).remove();
          }
        });
	    });
	  });
	};

	// Seleciona todas as opções de um select multiple
	window.selectAllOptions = function (input) {
		if(isSet(input)) {
			input = setElement(input);
			if(elementExist(input)) {
			  input.each(function() {
				  jQuery(this).find('option').prop('selected', true);
				});
			}
		}
	};

});
