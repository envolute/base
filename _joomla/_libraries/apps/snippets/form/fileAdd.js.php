<?php
// ADD NEW FILE
// Gera um novo campo para envio de arquivo
?>
var filesGroup	= jQuery('#<?php echo $APPTAG?>-files-group');
var imagesGroup	= jQuery('#<?php echo $APPTAG?>-images-group');

// Gera campos do tipo 'Button File'
// fGroup => ID do container dos campos dinâmicos
// fClass => classe opcional para um container de cada novo item
window.<?php echo $APPTAG?>_setNewFile = function(fGroup, fClass) {

	// container para campos dinâmicos de arquivos
	var grp	= setElement(fGroup, filesGroup);

	var fileField = isSet(fClass) ? '<div class="'+fClass+'">' : '';
	fileField += '<div class="form-group btn-file">';
	fileField += '	<span class="btn-group">';
	fileField += '		<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>';
	fileField += '	</span>';
	fileField += '	<input type="file" name="<?php echo $cfg['fileField']?>['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>'+<?php echo $APPTAG?>IndexFile+'" hidden />';
	fileField += '</div>';
	fileField += isSet(fClass) ? '</div>' : '';
	// Adiciona a class 'filesGroup' para definir como um grupo de campos
	grp.addClass('<?php echo $APPTAG?>-btnFileGroup');
	// Gera o campo
	grp.append(fileField);
	// Seta a funcionalidade
	btnFileAction(); // seta a ação no botão 'serch file'
	<?php echo $APPTAG?>IndexFile++;
};

// Gera campos do tipo 'Image File'
// fGroup => ID do container dos campos dinâmicos
// fClass => classe opcional para um container de cada novo item
window.<?php echo $APPTAG?>_setNewImageFile = function(fGroup, fClass) {

	// container para campos dinâmicos de arquivos
	var grp	= setElement(fGroup, imagesGroup);

	var fileField = isSet(fClass) ? '<div class="'+fClass+'">' : '';
	fileField += '<div class="image-file">';
	fileField += '	<a href="#" class="image-action">';
	fileField += '		<div class="image-file-label">';
	fileField += '			<span class="image-file-off base-icon-file-image"><small>200 x 200</small></span>';
	fileField += '			<span class="image-file-on text-sm base-icon-ok" hidden></span>';
	fileField += '			<span class="image-file-edit base-icon-pencil" hidden></span>';
	fileField += '		</div>';
	fileField += '	</a>';
	fileField += '	<span class="btn-group mt-2"></span>';
	fileField += '	<input type="file" name="<?php echo $cfg['fileField']?>['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>'+<?php echo $APPTAG?>IndexFile+'" class="field-image" hidden />';
	fileField += '</div>';
	fileField += isSet(fClass) ? '</div>' : '';
	// Adiciona a class 'imagesGroup' para definir como um grupo de campos
	grp.addClass('<?php echo $APPTAG?>-imgFileGroup');
	// Gera o campo
	grp.append(fileField);
	// Seta a funcionalidade
	imgFileAction(); // seta a ação no botão 'serch file'
	<?php echo $APPTAG?>IndexFile++;
};
