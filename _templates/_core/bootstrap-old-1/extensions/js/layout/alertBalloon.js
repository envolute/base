//JQUERY
jQuery(function() {

  // SET ALERT
  // Mostra/esconde mensagem de alerta
  var field_setAlert = '.set-alert'

  window.setAlertBalloon = function(elem, seconds, offsetTop, offsetRight, show) {
    var e = setElement(elem, field_setAlert);
    if(elementExist(e)) {
      e.each(function() {
        var obj = jQuery(this);
        var sec = isSet(seconds) ? seconds : (isSet(obj.data('seconds')) ? obj.data('seconds') : 0);
        var offTop = isSet(offsetTop) ? offsetTop : (isSet(obj.data('offsetTop')) ? obj.data('offsetTop') : 15);
        var offRight = isSet(offsetRight) ? offsetRight : (isSet(obj.data('offsetRight')) ? obj.data('offsetRight') : 15);
        var showOn = isSet(show) && show ? show : ((isSet(obj.data('show')) && obj.data('show') == 'true') ? obj.data('show') : true);
        obj.css({'top' : offTop, 'right' : offRight});
        // show object
        if(showOn) setTimeout(function() { obj.fadeIn() }, 500);
        if(sec > 0) setTimeout(function() { obj.fadeOut() }, (sec * 1000));
        jQuery(document).on('keydown', function (e) {
            if (e.keyCode === 27) obj.fadeOut();
        });
        obj.find('.close').click(function() { obj.fadeOut() });
      });
    }
  };

});
