<?php
// CHECK ALL
// Seleciona todas as linhas (checkboxes) da listagem
?>
var chk = jQuery('#<?php echo $APPTAG?>_checkAll');
chk.click(function() {
  var checked = (jQuery(this).is(':checked')) ? true : false;
  jQuery('.<?php echo $APPTAG?>-chk').each(function() {
    jQuery(this).prop("checked", checked);
  });
  <?php echo $APPTAG?>_setBtnStatus();
});
// desmarca checkAll caso um checkbox da lista seja alterado individualmente
jQuery('.<?php echo $APPTAG?>-chk').click(function() {
  chk.prop("checked", false);
  <?php echo $APPTAG?>_setBtnStatus();
});
