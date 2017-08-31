<?php
// Load Files
// Carrega os campos 'file' gerados dinâmicamente
?>
window.<?php echo $APPTAG?>_loadFiles = function(files) {
	var obj;
	var html = del = path = '';
	var root = '<?php echo JURI::root(true)?>';
	var len = (files.length > 0) ? parseInt(files[(files.length - 1)]['index']) : 0; // ultimo 'index'
	var f = Array();
	for(a = 0; (a < (len + 1) && files.length > 0); a++) { // len + 1, pois conta com o zero!
		<?php
		// load dinamic files
		if($cfg['dinamicFiles']) :
			echo '
			if(a > ('.$APPTAG.'IndexFileInit - 1) && a <= len) {
				for(i = 0; i < files.length; i++) {
					if(files[i]["index"] == a && files[i]["group"] != "")
					'.$APPTAG.'_setNewFile(files[i]["group"], files[i]["groupType"], files[i]["class"], files[i]["label"]);
				}
			}
			';
		endif;
		?>
		obj = jQuery('input:file[name="file['+a+']"]');

		// Define a sequencia dos itens
		for(i = 0; i < files.length; i++) {
			if(files[i]['index'] == a) {

				desc = files[i]['filename']+'<br />'+(parseFloat(Math.round(files[i]['filesize'] / 1024)).toFixed(2))+'kb';
				// Gera os links
				if(files[i]['mimetype'].indexOf('image') == -1) {
					path = root + '/get-file?fn='+files[i]['fn']+'&mt='+files[i]['mt']+'&tag=<?php echo base64_encode($APPTAG)?>';
					html += '	<a href="'+path+'" class="base-icon-attach btn btn-default hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_DOWNLOAD'); ?><br />'+desc+'"></a>';
				} else {
					path = root + '/images/apps/<?php echo $APPPATH?>/'+files[i]['filename'];
					html += '	<a href="#" class="base-icon-eye btn btn-default hasTooltip" data-animation="false" title="<img src=\''+path+'\' style=\'width:100px;max-height:100px\' /><br />'+desc+'"></a>';
				}

				// Se for um campo obrigatório não permite a exclusão
				if(!obj.hasClass('input-required')) {
					del = '	<a href="#" class="base-icon-cancel btn btn-danger hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_DELETE').' '.JText::_('TEXT_FILE'); ?>" onclick="<?php echo $APPTAG?>_delFile(this, \''+files[i]['filename']+'\')"></a>';
				}

				// Atribui os 'botões' ao elemento
				btnFile = obj.closest('.btn-file');
				imgFile = obj.closest('.image-file');

				// => btn file
				if(btnFile.length) {
					// Remove o estado 'ativo' do botão de upload
					btnFileDefault(obj);
					// gera os botões de ação
					btnFile.find('.btn-group').append(html + del);
				// => image file
				} else if(imgFile.length) {
					// Remove o estado 'ativo' do botão de upload
					fieldImageDefault(obj);
					// mostra a imagem
					imgFile.find('.image-file-label').addClass('hasImg').css('background-image', 'url("' + path + '")');
					// gera o botão de exclusão
					imgFile.find('.btn-group').append(del);
					// mostra o ícone de editar
					imgFile.find('.image-file-edit').prop('hidden', false);
					// esconde o label 'default' quando não há imagem
					imgFile.find('.image-file-off').prop('hidden', true);
				}
			}
		}

	    html = del = path = '';
	}

	setCoreDefinitions(); // core
};
