//JQUERY
jQuery(function() {

  // DATE
  var field_date 		= ".field-date input, input.field-date";

  window.setDate = function (input, setTime, seconds, autotab) {
    input = setElement(input, field_date);
    input.each(function() {
      var obj = jQuery(this);
      // evita erros com data zerada '00/00/0000' ou em branco
      // Obs: quando a vem em branco, obj.val() = '0_/__/____'
      if(obj.val().indexOf('0_/') >= 0 || isEmpty(obj.val())) obj.val('');
      // formata o valor
      else obj.val(dateFormat(obj.val()));
      // setTime param
      var time = isSet(setTime) ? setTime : false;
      time = isSet(obj.data('time')) ? obj.data('time') : time;
      // seconds param
      var sec = isSet(seconds) ? seconds : true;
      sec = isSet(obj.data('seconds')) ? obj.data('seconds') : sec;
      // autotab param
      var tab = isSet(autotab) ? autotab : true;
      tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

      var mask = 'd/m/y';
      var hold = '__/__/____';
      if(time == true) {
        if(sec == true){
          mask = mask+' h:s:s';
          hold = hold+' __:__:__';

        } else {
          mask = mask+' h:s';
          hold = hold+' __:__';
        }
      }
      var mindate = obj.data('mindate');
      var maxdate = obj.data('maxdate');
      var yrange  = obj.data('yearRange');
      var width = isSet(obj.data('width')) ? obj.css('width', obj.data('width')) : '';

      // mask date
      obj.inputmask(mask, {
        placeholder: hold,
        showMaskOnHover: true,
        oncomplete: function(){
          obj.datepicker("hide");
          obj.change();
          if(tab) obj.autoTab();
        },
        onKeyDown: function(){
          obj.datepicker("hide");
        }
      });
      // open datepicker on click
      obj.off('click').on('click', function() { obj.focus(); })
      // define datapicker format
      var _dateFormat = "dd/mm/yy";
      var _dayNames = ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado","Domingo"];
      var _dayNamesMin = ["D","S","T","Q","Q","S","S","D"];
      var _dayNamesShort = ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb","Dom"];
      var _monthNames = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
      var _monthNamesShort = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
      var _nextText = "Próximo";
      var _prevText = "Anterior";
      var _currDate;
      obj.datepicker({
        dateFormat: _dateFormat,
        dayNames: _dayNames,
        dayNamesMin: _dayNamesMin,
        dayNamesShort: _dayNamesShort,
        monthNames: _monthNames,
        monthNamesShort: _monthNamesShort,
        nextText: _nextText,
        prevText: _prevText,
        changeMonth: true,
        changeYear: true,
        beforeShow: function(dateText, inst){
          if(time) {
            _currDate = obj.val().split(' ');
          }
        },
        onSelect: function(dateText, inst){
          if(time) {
            dateText = dateText + " " + _currDate[1];
            obj.val(dateText);
            if(dateText.replace(/[^0-9]/g, '').length == 14) {
              if(tab) setTimeout(function(){ obj.autoTab(); }, 100);
            } else {
              obj.focus();
            }
          } else {
            if(tab) setTimeout(function(){ obj.autoTab(); }, 100);
          }
        }
      });
      // tipos de calendário
      if(mindate != null) obj.datepicker("option", "minDate", mindate );
      if(maxdate != null) obj.datepicker("option", "maxDate", maxdate);
      if(yrange  != null) obj.datepicker("option", "yearRange", yrange );

    });
  };

  // Converte do formato de banco (0000-00-00) para o formato padrão do 'field-date' (00-00-0000)
  window.dateFormat = function (val) {
    if(val.indexOf('-') == 4) {
      var dh = val.split(' ');
      var dt = dh[0].split('-');
      var time = isSet(dh[1]) ? dh[1] : '00:00:00';
      return dt[2]+'/'+dt[1]+'/'+dt[0]+' '+time;
    } else {
      return val;
    }
  };

  // Formata o 'field-date' para o formato de banco (0000-00-00)
  window.dateConvert = function () {
    jQuery('.field-date').each(function() {
      var obj, dh, dt, d, m, y, t, setTime, seconds;
      obj = jQuery(this);
      if(isSet(obj.data('convert')) && obj.data('convert') && !isEmpty(obj.val())) {
        dh = obj.val().split(' ');
        dt = dh[0].split('/');
        d = (isSet(dt[0]) && dt[0].length == 2) ? dt[0] : '';
        m = (isSet(dt[1]) && dt[1].length == 2) ? dt[1] : '';
        y = (isSet(dt[2]) && dt[2].length == 4) ? dt[2] : '';
        if(!isEmpty(d) && !isEmpty(m) && !isEmpty(y)) {
          t = (isSet(dh[1]) && dh[1].length > 0) ? ' '+dh[1] : '';
          // remove mask to enable converted value
          if(obj.inputmask) obj.inputmask('remove');
          // set converted value
          obj.val(y+'-'+m+'-'+d+t);
          // reset mask after 3 seconds
          setTime = (isSet(obj.data('time')) && obj.data('time')) ? true : false;
          seconds = (isSet(obj.data('seconds')) && !obj.data('seconds')) ? false : true;
          setTimeout(function() {
            setDate(obj, setTime, seconds);
          } , 1000 );
        }
      }
    });
  };

});
