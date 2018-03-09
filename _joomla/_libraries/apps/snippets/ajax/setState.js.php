<?php
// Set State
// seta o valor do campo 'state' do registro
?>
window.<?php echo $APPTAG?>_setState = function(itemID, state, recursive, iconOn, iconOff, colorOn, colorOff) {

	var dados = cod = st = e = '';
	var msg = '<?php echo JText::_('MSG_LIST0CONFIRM'); ?>';
	if(state === 1) msg = '<?php echo JText::_('MSG_LIST1CONFIRM'); ?>';
	if(isSet(state)) st = '&st='+state;
	var icon = (isSet(iconOn) && !isEmpty(iconOn)) ? iconOn : 'base-icon-ok';
	var icoff = (isSet(iconOff) && !isEmpty(iconOff)) ? iconOff : 'base-icon-cancel';
	var colon = (isSet(colorOn) && !isEmpty(colorOn)) ? colorOn : 'text-success';
	var coloff = (isSet(colorOff) && !isEmpty(colorOff)) ? colorOff : 'text-danger';

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

	jQuery.ajax({
		url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=state"+cod+st,
		dataType: 'json',
		type: 'POST',
		data:  dados,
		cache: false,
		success: function(data){
			jQuery.map( data, function( res ) {
				if(res.status == 4) {
					for(i = 0; i < res.ids.length; i++) {
						// item individual
						if(itemID) {
							// formata as linhas da listagem
							e = jQuery('#<?php echo $APPTAG?>-state-'+res.ids[i]+' > span');
							if((res.state == 2 && e.hasClass(icon+' '+colon)) || res.state == 0) {
								e.removeClass(icon+' '+colon).addClass(icoff+' '+coloff);
								<?php
								if($cfg['listFull']) echo 'e.closest("tr").addClass("table-danger");';
								else echo 'e.closest("li").addClass("list-danger");';
								?>
								// remove parent field option
								if(res.parentField != '' && res.parentFieldVal != '') {
									jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
									jQuery(res.parentField).selectUpdate(); // atualiza o select
								}
							} else {
								e.removeClass(icoff+' '+coloff).addClass(icon+' '+colon);
								<?php
								if($cfg['listFull']) echo 'e.closest("tr").removeClass("table-danger");';
								else echo 'e.closest("li").removeClass("list-danger");';
								?>
								// add parent field option
								if(res.parentField != '' && res.parentFieldVal != '') {
									jQuery(res.parentField).append('<option value='+res.parentFieldVal+'>'+res.parentFieldLabel+'</option>');
									jQuery(res.parentField).val(res.parentFieldVal).selectUpdate(); // atualiza o select
								}
							}
						// multiplos itens
						} else {
							// remove as linhas referentes aos itens executados
							jQuery('#<?php echo $APPTAG?>-item-'+res.ids[i]).remove();
						}
					}
					<?php if(!$cfg['listFull']) :?>
						<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
						<?php echo $APPTAG?>_listReload(false, false, false, <?php echo $APPTAG?>oCHL, <?php echo $APPTAG?>rNID, <?php echo $APPTAG?>rID);
					<?php else :?>
						// item individual
						if(itemID) {
							<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
						} else {
							// Tempo para que as linhas sejam removidas...
							// evitando o reenvio dos itens já executados
							setTimeout(function() {
								// verifica quantos estão selecionados
								var listChecks	= formList.find('input[type="checkbox"]:checked').length;
								// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
								if(inputVars > maxInputVars && listChecks > 0) <?php echo $APPTAG?>_setState(itemID, state, true, icon, icoff, colon, coloff); // executa novamente com os itens restantes
								else <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
							}, 300);
						}
					<?php endif;?>
				} else {
					<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					$.baseNotify({ msg: res.msg, type: "danger"});
				}
			});
		},
		error: function(xhr, status, error) {
			<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
			<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
			require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
			?>
		},
		complete: function() {
			hideTips(); // force tooltip close
		}
	});
	return false;
};
