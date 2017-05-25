<?php
  // Deleta o Arquivo -> exclui o registro e deleta o arquivo
  // OBS: essa função não precisa de alteração
?>
window.<?php echo $APPTAG?>_delFile = function(btn, fileName, itemID) {
  if(confirm('<?php echo JText::_('MSG_FILE_DELCONFIRM'); ?>')) {
    var cod = fname = '';
    cod 	= '&id=' + formId.val();
    fname	= '&fname=' + fileName;
    <?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
    mainForm.find('.set-success, .set-error').prop('hidden', true);
    jQuery.ajax({
      url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=delFile"+cod+fname,
      dataType: 'json',
      cache: false,
      success: function(data){
        <?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
        jQuery.map( data, function( res ) {
          if(res.status == 5) {
            // remove as informações do arquivo no campo
            <?php echo $APPTAG?>_resetFiles(jQuery(btn).closest('.btn-group').next('input:file'), true);

            // MENSAGENS: mostra a mensagem de sucesso/erro
            mainForm.find('.set-success').prop('hidden', false).text(res.msg);

            <?php // SUCCESS STATUS -> Executa quando houver sucesso na requisição ajax
            require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxSuccess.js.php');
            ?>

            <?php
            // recarrega a página quando fechar o form para atualizar a lista
            echo ($cfg['listFull'] ? 'fReload = true;' : $APPTAG.'_listReload(false, false, false, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);');
            ?>
          } else {
            mainForm.find('.set-error').prop('hidden', false).text(res.msg);
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
