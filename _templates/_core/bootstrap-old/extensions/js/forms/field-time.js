//JQUERY
jQuery(function() {

  // TIME
  var field_time 		= ".field-time input, input.field-time";
  
  window.setTime = function (input, seconds, autotab) {
    input = setElement(input, field_time);
    input.each(function() {
      var obj = jQuery(this);
      var width = isSet(obj.data('width')) ? obj.data('width') : false;
      // seconds param
      var sec = isSet(seconds) ? seconds : false;
      sec = isSet(obj.data('seconds')) ? obj.data('seconds') : sec;
      // autotab param
      var tab = isSet(autotab) ? autotab : true;
      tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;
      if(sec) {
        obj.inputmask("h:s:s",{ oncomplete: function(){ if(tab) obj.autoTab(); } });
        w = '6em';
      } else {
        obj.inputmask("h:s",{ oncomplete: function(){ if(tab) obj.autoTab(); } });
        w = '4.5em';
      }
      if(width) obj.css('width', width);
      obj.css({'min-width':w, 'max-width':'100%'});
    });
  };

});
