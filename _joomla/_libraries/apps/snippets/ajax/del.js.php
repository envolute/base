<?php
// DELETE
// Exclui o registro
// OBS: essa função não precisa de alteração
?>
window.<?php echo $APPTAG?>_del = function(itemID, isForm) {
  var msg = (itemID) ? '<?php echo JText::_('MSG_DELCONFIRM'); ?>' : '<?php echo JText::_('MSG_LISTDELCONFIRM'); ?>';
  if(confirm(msg)) {
    var dados = cod = '';
    if(itemID || (isForm && formId.val() != '')) {
      cod = '&id=' + (itemID ? itemID : formId.val());
      if(isForm) { // delete action from form
        <?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
        mainForm.find('.set-success, .set-error').prop('hidden', true);
      }
    } else {
      dados = formList.serialize();
    }
    jQuery.ajax({
      url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=del"+cod,
      dataType: 'json',
      type: 'POST',
      data:  dados,
      cache: false,
      success: function(data){
        if(isForm) <?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
        jQuery.map( data, function( res ) {
          if(res.status == 3) {
            if(!itemID) {
              <?php echo $APPTAG?>_formReset();
              if(isForm) {
                <?php // SUCCESS STATUS -> Executa quando houver sucesso na requisição ajax
                require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxSuccess.js.php');
                ?>
              }
            }
            // remove parent field option
            if(res.parentField != '' && res.parentFieldVal != '') {
              jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
              jQuery(res.parentField).selectUpdate(); // atualiza o select
            }
            <?php echo $APPTAG?>_listReload(false, true, res.ids, <?php echo $APPTAG?>oCHL, <?php echo $APPTAG?>rNID, <?php echo $APPTAG?>rID);
          } else {
            if(!itemID) mainForm.find('.set-error').prop('hidden', false).text(res.msg);
          }
        });
      },
      error: function(xhr, status, error) {
        <?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
        require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
        ?>
        <?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
      }
    });
  }
  return false;
};
