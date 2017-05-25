<?php
// JQUERY VALIDATION DEFAULT FOR INPUT FILES
// Validação básica para campos de envio de arquivo
?>
<?php if($cfg['hasUpload']): ?>
  mainForm_<?php echo $APPTAG?>.find("input:file").each(function() {
    var obj = jQuery(this);
    obj.rules("add", {
      required: function(element) {
        // só é obrigatório se o ID não for informado, ou seja, um novo item
        return (obj.hasClass('input-required') && formId_<?php echo $APPTAG?>.val() == '') ? true : false;
      },
      messages: {
        required: "<?php echo JText::_('FIELD_REQUIRED')?>"
      }
    });
    if(obj.hasClass('field-image')) { // apenas imagens
      obj.rules("add", {
        accept: "<?php echo implode(',', $cfg['fileTypes']['image'])?>",
        messages: {
          accept:"<?php echo JText::_('MSG_FILETYPE')?>"
        }
      });
    } else if(obj.hasClass('field-file')) { // não permite images
      obj.rules("add", {
        accept: "<?php echo implode(',', $cfg['fileTypes']['file'])?>",
        messages: {
          accept:"<?php echo JText::_('MSG_FILETYPE')?>"
        }
      });
    } else {
      // SEM VALIDAÇÃO NO JAVASCRIPT
      // Devido a alguns bugs na validação de alguns tipos de arquivos como "xls, csv..."
      // caso o campo não possua nenhuma das classes 'field-image' ou 'field-file'
      // não será feita a validação no form. Mas continua sendo feita no servidor 'PHP'
    }
  });
<?php endif; ?>
