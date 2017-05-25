//JQUERY
jQuery(function() {

  // TOGGLE DISPLAY FIELD
  window.toggleDisplay = function (input, status) {
    if(!isSet(status) || status == false || status == 'false') status = false;
    else status = true;
    input.each(function() {
      var obj = (input.is('select') && input.next('.chosen-container').length) ? jQuery(this).next('.chosen-container') : jQuery(this);
      if(status) {
        obj.prop('hidden', false);
      } else {
        obj.prop('hidden', true);
      }
    });
    return status;
  };

  // TOGGLE DISABLED FIELD
  window.toggleDisabled = function (input, status) {
    if(!isSet(status) || status == false || status == 'false') status = false;
    else status = true;
    input.prop('disabled', status);
    if(input.is('select') && input.next('.chosen-container').length) input.trigger("chosen:updated"); // select
    return status;
  };

});
