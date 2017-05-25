<?php
// CLEAR VALIDATION DEFAULT
// Ações default para limpar a validação dos campos após o reset do form
?>
formElement.find('input, select, textarea').each(function(){
  jQuery(this).removeClass('error'); //remove as error from fields
  <?php echo $APPTAG?>_validator.successList.push(this); //mark as error free
  <?php echo $APPTAG?>_validator.showErrors(); //remove error messages if present
});
<?php echo $APPTAG?>_validator.resetForm(); //remove error class on name elements and clear history
<?php echo $APPTAG?>_validator.reset(); //remove all error and success data
// Reset 'input/btn-group'
formElement.find('.has-error').removeClass('has-error');
