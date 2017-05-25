<?php
// JQUERY VALIDATION DEFAULT
// Validação básica para elementos obrigatórios
?>
window.<?php echo $APPTAG?>_validator = mainForm_<?php echo $APPTAG?>.validate({
  //don't remove this
  invalidHandler: function(event, validator) {
    //if there is error,
    //set custom preferences
  },
  submitHandler: function(form){
    return false;
  }
});
