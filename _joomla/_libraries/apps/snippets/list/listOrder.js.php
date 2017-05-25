<?php
// LIST ORDER
// Seta a ação de ordenamento da listagem
?>
window.<?php echo $APPTAG?>_setListOrder = function(col, type) {
  if(col) {
    formOrder.find('input#<?php echo $APPTAG?>oF').val(col);
    formOrder.find('input#<?php echo $APPTAG?>oT').val(type);
  }
  formOrder.submit();
};
