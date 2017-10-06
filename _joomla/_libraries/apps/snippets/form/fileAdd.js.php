<?php
// ADD NEW FILE
// Gera um novo campo para envio de arquivo
?>

// Gera campos dinâmicos do tipo 'File'
// fGroup => ID do container dos campos dinâmicos
// fGtype => Tipo do campo ('file', 'image');
// fClass => classe opcional para um container de cada novo item
window.<?php echo $APPTAG?>_setNewFile = function(fGroup, fGtype, fClass, fLabel) {

	var gType	= isSet(fGtype) ? fGtype : 0;
	if(gType == 'image') {

		// container para campos dinâmicos de arquivos
		var gName	= isSet(fGroup) ? fGroup : '#<?php echo $APPTAG?>-images-group';
		var group	= jQuery(gName);

		var fileField = isSet(fClass) ? '<div class="'+fClass+'">' : '';
		fileField += '<div class="image-file">';
		fileField += '	<a href="#" class="image-action">';
		fileField += '		<div class="image-file-label">';
		fileField += '			<span class="image-file-off base-icon-file-image"></span>';
		fileField += '			<span class="image-file-on text-sm base-icon-ok" hidden></span>';
		fileField += '			<span class="image-file-edit base-icon-pencil" hidden></span>';
		fileField += '		</div>';
		fileField += '	</a>';
		fileField += '	<span class="btn-group mt-2"></span>';
		fileField += '	<input type="file" name="<?php echo $cfg['fileField']?>['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>'+<?php echo $APPTAG?>IndexFile+'" class="field-image" hidden />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Group['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-group'+<?php echo $APPTAG?>IndexFile+'" value="'+gName+'" />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Gtype['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-gtype'+<?php echo $APPTAG?>IndexFile+'" value="image" />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Class['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-class'+<?php echo $APPTAG?>IndexFile+'" value="'+fClass+'" />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Label['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-label'+<?php echo $APPTAG?>IndexFile+'" />';
		fileField += '</div>';
		fileField += isSet(fClass) ? '</div>' : '';
		// Adiciona a class 'imagesGroup' para definir como um grupo de campos
		group.addClass('<?php echo $APPTAG?>-imgFileGroup');
		// Gera o campo
		group.append(fileField);
		// Seta a funcionalidade
		imgFileAction(); // seta a ação no botão 'serch file'
		<?php echo $APPTAG?>IndexFile++;

	} else {

		// container para campos dinâmicos de arquivos
		var gName	= isSet(fGroup) ? fGroup : '#<?php echo $APPTAG?>-files-group';
		var group	= jQuery(gName);
		var label	= (isSet(fLabel) && !isEmpty(fLabel)) ? fLabel : '';

		var fileField = isSet(fClass) ? '<div class="'+fClass+'">' : '';
		fileField += '<div class="form-group btn-file">';
		fileField += '	<span class="btn-group">';
		fileField += '		<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>';
		fileField += '	</span>';
		fileField += '	<input type="file" name="<?php echo $cfg['fileField']?>['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>'+<?php echo $APPTAG?>IndexFile+'" hidden />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Group['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-group'+<?php echo $APPTAG?>IndexFile+'" value="'+gName+'" />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Gtype['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-gtype'+<?php echo $APPTAG?>IndexFile+'" value="file" />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Class['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-class'+<?php echo $APPTAG?>IndexFile+'" value="'+fClass+'" />';
		fileField += '	<input type="hidden" name="<?php echo $cfg['fileField']?>Label['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>-label'+<?php echo $APPTAG?>IndexFile+'" />';
		fileField += '</div>';
		fileField += isSet(fClass) ? '</div>' : '';
		// Adiciona a class 'filesGroup' para definir como um grupo de campos
		group.addClass('<?php echo $APPTAG?>-btnFileGroup');
		// Gera o campo
		group.append(fileField);
		// Seta a funcionalidade
		btnFileAction(); // seta a ação no botão 'serch file'
		<?php echo $APPTAG?>IndexFile++;
	}

};
