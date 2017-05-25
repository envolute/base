<?php
// ADD NEW FILE
// Gera um novo campo para envio de arquivo
?>
window.<?php echo $APPTAG?>_setNewFile = function() {
  var fileField = '';
  fileField += '<div class="form-group btn-file">';
  fileField += '	<span class="btn-group">';
  fileField += '		<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>';
  fileField += '	</span>';
  fileField += '	<input type="file" name="<?php echo $cfg['fileField']?>['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>'+<?php echo $APPTAG?>IndexFile+'" hidden />';
  fileField += '</div>';
  filesGroup.append(fileField);
  btnFileAction(); // seta a ação no botão 'serch file'
  <?php echo $APPTAG?>IndexFile++;
};
