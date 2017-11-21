<?php
// Set State
// seta o valor do campo 'state' do registro
?>
window.<?php echo $APPTAG?>_setState = function(itemID, state, recursive) {

	var dados = cod = st = e = '';
	var msg = '<?php echo JText::_('MSG_LIST0CONFIRM'); ?>';
	if(state === 1) msg = '<?php echo JText::_('MSG_LIST1CONFIRM'); ?>';
	if(typeof state !== "null" && typeof state !== "undefined") st = '&st='+state;

	if(itemID) {

		cod = '&id='+itemID;
		<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader

	} else {

		var reCursive = (isSet(recursive) && recursive) ? true : false;
		if(!reCursive) {
			if(!confirm(msg)) return false;
			<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
		}

		dados		= formList.serialize();
		inputVars	= formList.find('input[type="checkbox"]:checked, input[type="hidden"]').length;

	}
	console.log('setState');

	jQuery.ajax({
		url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=state"+cod+st,
		dataType: 'json',
		type: 'POST',
		data:  dados,
		cache: false,
		success: function(data){
			jQuery.map( data, function( res ) {
				console.log('Status: '+res.status);
				if(res.status == 4) {
					for(i = 0; i < res.ids.length; i++) {
						console.log('desmarca ID: '+res.ids[i]);
						// desmarca as linhas referentes aos itens excluídos
						jQuery('input[name="<?php echo $APPTAG?>_ids[]"][value="'+res.ids[i]+'"]').prop('checked', false);
						// formata as linhas da listagem
						e = list.find('#<?php echo $APPTAG?>-state-'+res.ids[i]+' > span');
						if((res.state == 2 && e.hasClass('base-icon-ok')) || res.state == 0) {
							e.removeClass('base-icon-ok text-success').addClass('base-icon-cancel text-danger');
							<?php
							if($cfg['listFull']) echo 'e.parents("tr").addClass("table-danger");';
							else echo 'e.parents("li").addClass("list-danger");';
							?>
							// remove parent field option
							if(res.parentField != '' && res.parentFieldVal != '') {
								jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
								jQuery(res.parentField).selectUpdate(); // atualiza o select
							}
						} else {
							e.removeClass('base-icon-cancel text-danger').addClass('base-icon-ok text-success');
							<?php
							if($cfg['listFull']) echo 'e.parents("tr").removeClass("table-danger");';
							else echo 'e.parents("li").removeClass("list-danger");';
							?>
							// add parent field option
							if(res.parentField != '' && res.parentFieldVal != '') {
								jQuery(res.parentField).append('<option value='+res.parentFieldVal+'>'+res.parentFieldLabel+'</option>');
								jQuery(res.parentField).val(res.parentFieldVal).selectUpdate(); // atualiza o select
							}
						}
					}
					<?php if(!$cfg['listFull']) echo $APPTAG.'_listReload(false, false, false, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);'; ?>
					// Tempo para que as linhas sejam desmarcadas...
					// evitando o reenvio dos itens já executados
					setTimeout(function() {
						// verifica quantos estão selecionados
						var listChecks	= formList.find('input[type="checkbox"]:checked').length;
						console.log('listChecks: '+listChecks);
						// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
						if(inputVars > maxInputVars && listChecks > 0) {
							console.log('recall');
							<?php echo $APPTAG?>_setState(itemID, state, true); // executa novamente com os itens restantes
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "success"});
							console.log('finish');
						}
					}, 300);
				} else {
					$.baseNotify({ msg: res.msg, type: "danger"});
				}
			});
		},
		error: function(xhr, status, error) {
			<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
			require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
			?>
			<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
		},
		complete: function() {
			hideTips(); // force tooltip close
		}
	});
	return false;
};
