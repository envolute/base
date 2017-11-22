<?php
// DELETE
// Exclui o registro
// OBS: essa função não precisa de alteração
?>
window.<?php echo $APPTAG?>_del = function(itemID, isForm, recursive) {

	var msg = (itemID) ? '<?php echo JText::_('MSG_DELCONFIRM'); ?>' : '<?php echo JText::_('MSG_LISTDELCONFIRM'); ?>';
	var reCursive = (isSet(recursive) && recursive) ? true : false;
	if(!reCursive) {
		if(!confirm(msg)) return false;
		<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
	}

	var cod = dados = inputVars = '';
	if(itemID || (isForm && formId.val() != '')) {
		cod = '&id=' + (itemID ? itemID : formId.val());
	} else {
		dados = formList.serialize();
		inputVars	= formList.find('input[type="checkbox"]:checked, input[type="hidden"]').length;
	}

	jQuery.ajax({
		url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=del"+cod,
		dataType: 'json',
		type: 'POST',
		data:  dados,
		cache: false,
		success: function(data) {
			jQuery.map( data, function( res ) {
				if(res.status == 3) {
					if(isForm && !itemID) {
						<?php echo $APPTAG?>_formReset();
						<?php // SUCCESS STATUS -> Executa quando houver sucesso na requisição ajax
						require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxSuccess.js.php');
						?>
					}
					// remove parent field option
					if(res.parentField != '' && res.parentFieldVal != '') {
						jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
						jQuery(res.parentField).selectUpdate(); // atualiza o select
					}
					// remove as linhas referentes aos itens excluídos
					<?php echo $APPTAG?>_listReload(false, true, res.ids, <?php echo $APPTAG?>oCHL, <?php echo $APPTAG?>rNID, <?php echo $APPTAG?>rID);
					// item individual
					if(itemID) {
						<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
						$.baseNotify({ msg: res.msg, type: "success"});
					} else {
						// Tempo para que as linhas sejam excluídas...
						// evitando o reenvio dos itens já executados
						setTimeout(function() {
							// verifica quantos estão selecionados
							var listChecks	= formList.find('input[type="checkbox"]:checked').length;
							// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
							if(inputVars > maxInputVars && listChecks > 0) <?php echo $APPTAG?>_del(itemID, false, true); // executa novamente com os itens restantes
							else <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
						}, 300);
					}
				} else {
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
					$.baseNotify({ msg: res.msg, type: "danger"});
				}
			});
		},
		error: function(xhr, status, error) {
			<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
			<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
			require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
			?>
		}
	});
	return false;
};
