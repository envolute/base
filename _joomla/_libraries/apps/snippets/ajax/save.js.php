<?php
// SAVE
// executa a ação de inserção ou atualização dos dados no banco
?>
window.<?php echo $APPTAG?>_save = function(trigger) {
  // valida o formulário antes do envio -> 'jquery validation'
  if(mainForm.valid()) {

    <?php
      // FORMAT VALUES -> Formatação de valores para inclusão no banco
  		require(JPATH_CORE.DS.'apps/snippets/form/formatValues.js.php');
    ?>

    // pega os dados enviados pelo form
    var dados = <?php echo ($cfg['hasUpload'] ? 'new FormData(mainForm[0])' : 'mainForm.serialize()') ?>;

    // executando...
    <?php echo $APPTAG?>_formExecute(true, true, true); // inicia o loader
    mainForm.find('.set-success, .set-error').prop('hidden', true); // esconde as mensagens de 'erro' ou 'sucesso'

    jQuery.ajax({
      url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=save&id="+formId.val(),
      dataType: 'json',
      type: 'POST',
      method: "POST",
      data:  dados,
      cache: false,
      <?php if($cfg['hasUpload']): // quando houver upload ?>
        processData: false,
        contentType: false,
      <?php endif; ?>
      success: function(data){
        <?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
        jQuery.map( data, function( res ) {
          if(res.status > 0) { // se alguma ação for realizada

            if(res.status == 1 || isSet(trigger)) {
              if(isSet(trigger)) {
                <?php echo $APPTAG?>_formReset();
                if(trigger == 'close') popup.modal('hide');
              } else {
                <?php echo $APPTAG?>_loadEditFields(res.regID, true, false); // recarrega os dados do form
              }
            } else { // 'atualizado'
              <?php echo $APPTAG?>_loadEditFields(formId.val(), true, false); // recarrega os dados do form
            }

            // Update Parent field
            if(res.parentField != '' && res.parentFieldVal != '') {
              // remove if option exist
              if(jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').length) jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
              // add option if is active (state = 1)
              if(res.parentFieldLabel != '' && res.parentFieldLabel != null) {
                jQuery(res.parentField).append('<option value='+res.parentFieldVal+'>'+res.parentFieldLabel+'</option>'); // add valor à lista
                jQuery(res.parentField).val(res.parentFieldVal).selectUpdate(); // atualiza o select
              } else {
                jQuery(res.parentField).selectUpdate();
              }
            }

            <?php // SUCCESS STATUS -> Executa quando houver sucesso na requisição ajax
            require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxSuccess.js.php');
            ?>

            <?php // recarrega a página quando fechar o form para atualizar a lista
            echo ($cfg['listFull'] ? 'fReload = true;' : $APPTAG.'_listReload(false, false, false, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);');
            ?>

            if(firstField.length) setTimeout(function() { firstField.focus() }, 10); // seta novamente o focus no primeiro campo

          } else {

            // caso ocorra um erro na ação, mostra a mensagem de erro
            mainForm.find('.set-error').prop('hidden', false).text(res.msg);

            // recarrega os scripts de formulário para os campos
            // necessário após um procedimento ajax que envolve os elementos
            setFormDefinitions();

          }
        });
      },
      error: function(xhr, status, error) {
        <?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
        require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
        ?>
        <?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
      }
    });
  }
};
