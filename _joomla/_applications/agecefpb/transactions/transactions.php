<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// DATABASE CONNECT
$db = JFactory::getDbo();

?>

<script>
jQuery(function() {

	<?php // Default 'JS' Vars
	require(JPATH_CORE.DS.'apps/snippets/initVars.js.php');
	?>

	// APP FIELDS
	var provider_id			= jQuery('#<?php echo $APPTAG?>-provider_id');
	var client_id			= jQuery('#<?php echo $APPTAG?>-client_id');
	var invoice_id			= jQuery('#<?php echo $APPTAG?>-invoice_id');
	var description			= jQuery('#<?php echo $APPTAG?>-description');
	var fixed				= mainForm.find('input[name=fixed]:radio'); // radio group
	var date 				= jQuery('#<?php echo $APPTAG?>-date');
	var price 				= jQuery('#<?php echo $APPTAG?>-price');
	var total 				= jQuery('#<?php echo $APPTAG?>-total');
	var totalDesc 			= jQuery('#<?php echo $APPTAG?>-totalDesc'); // Na edição -> resultado do parcelamento
	var doc_number			= jQuery('#<?php echo $APPTAG?>-doc_number');
	var note 				= jQuery('#<?php echo $APPTAG?>-note');

	// PARENT FIELD -> Select
	// informe, se houver, o campo que representa a chave estrangeira principal
	var parentFieldId		= provider_id; // 'null', caso não exista...
	var parentFieldGroup	= elementExist(parentFieldId) ? parentFieldId.closest('[class*="col-"]') : null;

	// GROUP RELATION'S BUTTONS -> grupo de botões de relacionamentos no form
	var groupRelations		= jQuery('#<?php echo $APPTAG?>-group-relation');

	// FORM CONTROLLERS
	// métodos controladores do formulário

		// ON FOCUS
		// campo que recebe o focus no carregamento
		var firstField		= '';

		// ON MODAL OPEN -> Ações quando o modal do form é aberto
		popup.on('shown.bs.modal', function () {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/onModalOpen.def.js.php');
			?>
		});

		// ON MODAL CLOSE -> Ações quando o modal do form é fechado
		popup.on('hidden.bs.modal', function () {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/onModalClose.def.js.php');
			?>
		});

		<?php // FORM PAGINATOR -> Implementa os botões de paginação do formulário
		require(JPATH_CORE.DS.'apps/snippets/form/formPaginator.js.php');
		?>

		<?php // CHANGE STATE -> Seta o valor do campo 'state' no form
		require(JPATH_CORE.DS.'apps/snippets/form/changeState.js.php');
		?>

		// FORM EXECUTE -> Indicadores de execução do form
		window.<?php echo $APPTAG?>_formExecute = function(loader, disabled, e) {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/formExecute.def.js.php');
			?>
		};

		// FORM RESET -> Reseta o form e limpa as mensagens de validação
		window.<?php echo $APPTAG?>_formReset = function() {

			<?php // Init Actions
			require(JPATH_CORE.DS.'apps/snippets/form/formReset.init.js.php');
			?>

			// App Fields
			// IMPORTANTE:
			// => SE HOUVER UM CAMPO INDICADO NA VARIÁVEL 'parentFieldId', NÃO RESETÁ-LO NA LISTA ABAIXO
			client_id.selectUpdate(0); // select
			invoice_id.selectUpdate(0); // select
			description.val(''); // select
			checkOption(fixed, 0);
			// mostra campos da movimentação normal
			setHidden('.<?php echo $APPTAG?>-no-fixed', false);
			date.val('');
			price.val('');
			<?php echo $APPTAG?>_setInstallments();
			totalDesc.val('');
			// oculta os campos não editáveis
			setHidden('.<?php echo $APPTAG?>-no-edit', false, '.<?php echo $APPTAG?>-only-edit');
			doc_number.val('');
			note.val('');

			<?php // Closure Actions
			require(JPATH_CORE.DS.'apps/snippets/form/formReset.end.js.php');
			?>

		};

		<?php if($cfg['hasUpload']) : ?>

			<?php // LOAD FILES -> Carrega os campos 'file' gerados dinâmicamente
			require(JPATH_CORE.DS.'apps/snippets/form/filesLoad.js.php');
			?>

			<?php // ADD NEW FILE -> Gera um novo campo para envio de arquivo
			require(JPATH_CORE.DS.'apps/snippets/form/fileAdd.js.php');
			?>

			// RESET FILES -> Reseta os campos 'file'
			window.<?php echo $APPTAG?>_resetFiles = function(inputFiles, single) {
				if(inputFiles.length) {
					<?php // Default Actions
					require(JPATH_CORE.DS.'apps/snippets/form/filesReset.def.js.php');
					?>
				}
			};

		<?php endif; ?>

		// CLEAR VALIDATION ->  Limpa os erros de validação
		window.<?php echo $APPTAG?>_clearValidation = function(formElement){
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/clearValidation.def.js.php');
			?>
		}

		// SET RELATION -> Atribui valor ao ID de relacionamento
		window.<?php echo $APPTAG?>_setRelation = function(id) {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/setRelation.def.js.php');
			?>
		};

		// SET PARENT -> Seta o valor do elemento pai (foreign key) do relacionamento
		window.<?php echo $APPTAG?>_setParent = function(id) {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/setParent.def.js.php');
			?>
		};

		// CUSTOM -> edit from select
		window.<?php echo $APPTAG?>_editInvoice = function() {
			var itemID = jQuery('#<?php echo $APPTAG?>-invoiceID').val();
			if(itemID != '' && itemID != 0) transactionsInvoices_loadEditFields(itemID, false, false);
			else alert('<?php echo JText::_('MSG_SELECT_ITEM_FROM_LIST')?>');
		};

		// CUSTOM -> Set fixed transaction
		window.<?php echo $APPTAG?>_setFixed = function(val) {
			setHidden('.<?php echo $APPTAG?>-no-fixed', val);
			if(val == 1) {
				invoice_id.selectUpdate(0);
			}
		};

		// CUSTOM -> set installments
		window.<?php echo $APPTAG?>_setInstallments = function() {
			total.empty();
			var limit = 90;
			for(i = 1; i <= limit; i++) {
				total.append('<option value="'+i+'">'+i+'</option>');
			}
			// O valor só pode ser selecionado na hora de criar a movimentação
			// O item editado é sempre referente a 1 parcela do total
			// Dessa forma, o valor, tanto na criação quanto na edição será sempre 1
			total.prop('disabled',(formId_<?php echo $APPTAG?>.val() == 0 ? false : true)).selectUpdate(1); // select
		};

	// LIST CONTROLLERS
	// ações & métodos controladores da listagem

		// ON MODAL CLOSE -> Ações quando o modal da listagem é fechado
		listPopup.on('hidden.bs.modal', function () {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/list/onModalClose.def.js.php');
			?>
		});

		<?php // CHECK ALL -> Seleciona todas as linhas (checkboxes) da listagem
		require(JPATH_CORE.DS.'apps/snippets/list/checkAll.js.php');
		?>

		<?php // BTN STATUS -> habilita/desabilita botões se houver, ou não, checkboxes marcados na Listagem
		require(JPATH_CORE.DS.'apps/snippets/list/btnStatus.js.php');
		?>

		<?php // SET FILTER -> Submit o filtro no evento 'onchange'
		require(JPATH_CORE.DS.'apps/snippets/list/setFilter.js.php');
		?>

		<?php // LIST ORDER -> Seta a ação de ordenamento da listagem
		require(JPATH_CORE.DS.'apps/snippets/list/listOrder.js.php');
		?>

		<?php // LIST LIMIT -> Altera o limite de itens visualizados na listagem
		require(JPATH_CORE.DS.'apps/snippets/list/listLimit.js.php');
		?>

		// FILTER SUBMIT ACTIONS -> Seta ações no submit do filtro
		formFilter.on('submit', function() {
		  <?php
		    // FORMAT VALUES -> Formatação de valores para inclusão no banco
		    require(JPATH_CORE.DS.'apps/snippets/form/formatValues.js.php');
		  ?>
		});

	// AJAX CONTROLLERS
	// métodos controladores das ações referente ao banco de dados e envio de arquivos

		<?php // LIST RELOAD -> (Re)carrega a listagem AJAX dos dados
		require(JPATH_CORE.DS.'apps/snippets/ajax/listReload.js.php');
		?>

		// Load Edit Data -> Prepara o formulário para a edição dos dados
		window.<?php echo $APPTAG?>_loadEditFields = function(appID, reload, formDisable) {
			var id = (appID ? appID : displayId.val());
			if(isEmpty(id) || id == 0) {
				<?php echo $APPTAG?>_formReset();
				return false;
			}
			<?php echo $APPTAG?>_formExecute(true, formDisable, true); // inicia o loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=get&id="+id,
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data) {
					if(!data.length) {
						<?php echo $APPTAG?>_formReset();
						<?php echo $APPTAG?>_formExecute(true, formDisable, false); // encerra o loader
						return false;
					}
					jQuery.map( data, function( item ) {

						<?php // Init Actions
						require(JPATH_CORE.DS.'apps/snippets/form/loadEdit.init.js.php');
						?>

						// App Fields
						provider_id.selectUpdate(item.provider_id); // select
						client_id.selectUpdate(item.client_id); // select
						invoice_id.selectUpdate(item.invoice_id); // select
						description.val(item.description);
						checkOption(fixed, (item.fixed == 0 ? 0 : 1));
						// esconde campos da movimentação normal
						setHidden('.<?php echo $APPTAG?>-no-fixed', item.fixed);
						date.val(dateFormat(item.date));
						price.val(item.price);
						totalDesc.val(item.totalDesc);
						// oculta os campos não editáveis
						setHidden('.<?php echo $APPTAG?>-no-edit', true, '.<?php echo $APPTAG?>-only-edit');
						doc_number.val(item.doc_number);
						note.val(item.note);

						<?php // Closure Actions
						require(JPATH_CORE.DS.'apps/snippets/form/loadEdit.end.js.php');
						?>

					});
					// mostra dos botões 'salvar & novo' e 'delete'
					jQuery('#btn-<?php echo $APPTAG?>-delete').prop('hidden', false);
					// limpa as mensagens de erro de validação
					<?php echo $APPTAG?>_clearValidation(mainForm);
				},
				error: function(xhr, status, error) {
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
					<?php echo $APPTAG?>_formExecute(true, formDisable, false); // encerra o loader
				}
			});
		};

		<?php // SAVE -> executa a ação de inserção ou atualização dos dados no banco
		require(JPATH_CORE.DS.'apps/snippets/ajax/save.js.php');
		?>

		<?php // SET STATE -> seta o valor do campo 'state' do registro
		require(JPATH_CORE.DS.'apps/snippets/ajax/setState.js.php');
		?>

		<?php // DELETE -> Exclui o registro
		require(JPATH_CORE.DS.'apps/snippets/ajax/del.js.php');
		?>

		<?php if($cfg['hasUpload']) : ?>

			<?php // DELETE FILES -> exclui o registro e deleta o arquivo
			require(JPATH_CORE.DS.'apps/snippets/ajax/delFile.js.php');
			?>

		<? endif; ?>



		// CUSTOM -> atribui a fatura
		window.<?php echo $APPTAG?>_invoice = function(recursive) {

			var form = jQuery('#form-<?php echo $APPTAG?>-invoice');
			var invID = form.find('#<?php echo $APPTAG?>-invoiceID').val();
			if(invID == 0) {
				alert('<?php echo JText::_('MSG_SELECT_ITEM_FROM_LIST'); ?>');
				return false;
			}

			var reCursive = (isSet(recursive) && recursive) ? true : false;
			if(!reCursive) {
				if(!confirm('<?php echo JText::_('MSG_INVOICED_CONFIRM'); ?>')) return false;
				<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
			}

			var dados		= formList.serialize();
			var inputVars	= formList.find('input[type="checkbox"]:checked, input[type="hidden"]').length;

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=invoice&st="+invID,
				dataType: 'json',
				type: 'POST',
				data:  dados,
				cache: false,
				success: function(data){
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							// remove os itens executados
							if(res.ids.length > 0) {
								for(i = 0; i < res.ids.length; i++) {
									// remove as linhas referentes aos itens executados
									jQuery('#<?php echo $APPTAG?>-item-'+res.ids[i]).remove();
								}
							}
							// Tempo para que as linhas sejam removidas...
							// evitando o reenvio dos itens já executados
							setTimeout(function() {
								// verifica quantos estão selecionados
								var listChecks	= formList.find('input[type="checkbox"]:checked').length;
								// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
								if(inputVars > maxInputVars && listChecks > 0) <?php echo $APPTAG?>_invoice(true); // executa novamente com os itens restantes
								else <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
							}, 300);
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "danger" });
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

		// CUSTOM -> remover a fatura
		window.<?php echo $APPTAG?>_removeInvoice = function(recursive) {

			var reCursive = (isSet(recursive) && recursive) ? true : false;
			if(!reCursive) {
				if(!confirm('<?php echo JText::_('MSG_REMOVE_INVOICED_CONFIRM'); ?>')) return false;
				<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
			}

			var dados		= formList.serialize();
			var inputVars	= formList.find('input[type="checkbox"]:checked, input[type="hidden"]').length;

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=removeInvoice",
				dataType: 'json',
				type: 'POST',
				data:  dados,
				cache: false,
				success: function(data){
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							// remove os itens executados
							if(res.ids.length > 0) {
								for(i = 0; i < res.ids.length; i++) {
									// remove as linhas referentes aos itens executados
									jQuery('#<?php echo $APPTAG?>-item-'+res.ids[i]).remove();
								}
							}
							// Tempo para que as linhas sejam removidas...
							// evitando o reenvio dos itens já executados
							setTimeout(function() {
								// verifica quantos estão selecionados
								var listChecks	= formList.find('input[type="checkbox"]:checked').length;
								// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
								if(inputVars > maxInputVars && listChecks > 0) <?php echo $APPTAG?>_removeInvoice(true); // executa novamente com os itens restantes
								else <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
							}, 300);
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "danger" });
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

		// CUSTOM -> gera cópia das movimentações fixas sem fatura
		window.<?php echo $APPTAG?>_addFixed = function() {

			var form = jQuery('#form-<?php echo $APPTAG?>-addFixed');
			var groupIDs = [];
			form.find('input[name=<?php echo $APPTAG?>GroupID]:checked').map(function() {
				groupIDs.push(jQuery(this).val());
			});
			if(groupIDs == "") {
				$.baseNotify({ msg: '<?php echo JText::_('MSG_SELECT_ITEM_FROM_LIST'); ?>', type: "danger" });
				return false;
			}
			if(!confirm('<?php echo JText::_('MSG_ADDFIXED_CONFIRM'); ?>')) return false;
			<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=addFixed&arr="+groupIDs,
				dataType: 'json',
				cache: false,
				success: function(data){
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							<?php echo $APPTAG?>_listReload(true, false); // recarrega a página
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "danger" });
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

		// Add Selected Fixeds
		// Importa apenas as movimentações recorrentes selecionadas
		window.<?php echo $APPTAG?>_addSelectedFixed = function(recursive) {

			var reCursive = (isSet(recursive) && recursive) ? true : false;
			if(!reCursive) {
				if(!confirm('<?php echo JText::_('MSG_ADDFIXED_CONFIRM'); ?>')) return false;
				<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
			}

			var form = jQuery('#form-<?php echo $APPTAG?>-addSelectedFixed');
			var inv = form.find('#<?php echo $APPTAG?>-invID');
			var cod = '&invID='+inv.val();

			var dados		= formList.serialize();
			var inputVars	= formList.find('input[type="checkbox"]:checked, input[type="hidden"]').length;

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=addSelectedFixed"+cod,
				dataType: 'json',
				type: 'POST',
				data:  dados,
				cache: false,
				success: function(data){
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							// remove os itens executados
							if(res.ids.length > 0) {
								for(i = 0; i < res.ids.length; i++) {
									// remove as linhas referentes aos itens executados
									jQuery('#<?php echo $APPTAG?>-item-'+res.ids[i]).remove();
								}
							}
							// Tempo para que as linhas sejam desmarcadas...
							// evitando o reenvio dos itens já executados
							setTimeout(function() {
								// verifica quantos estão selecionados
								var listChecks	= formList.find('input[type="checkbox"]:checked').length;
								// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
								if(inputVars > maxInputVars && listChecks > 0) <?php echo $APPTAG?>_addSelectedFixed(true); // executa novamente com os itens restantes
								else <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
							}, 300);
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "danger" });
						}
					});
				},
				error: function(xhr, status, error) {
					<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
				}
			});
			return false;
		};

		// CUSTOM -> Disponibiliza as próximas parcelas para serem faturadas
		window.<?php echo $APPTAG?>_chargeInstallments = function() {

			var form = jQuery('#form-<?php echo $APPTAG?>-chargeInstallments');
			var groupIDs= [];
			form.find('input[name=<?php echo $APPTAG?>GroupID]:checked').map(function() {
				groupIDs.push(jQuery(this).val());
			});
			if(groupIDs == "") {
				$.baseNotify({ msg: '<?php echo JText::_('MSG_SELECT_ITEM_FROM_LIST'); ?>', type: "danger" });
				return false;
			}
			if(!confirm('<?php echo JText::_('MSG_CHARGE_CONFIRM'); ?>')) return false;
			if(formList.find('.isInstallment').length) {
				if(!confirm('<?php echo JText::_('MSG_ALERT_INSTALLMENT'); ?>')) return false;
			}

			<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=chargeInstallments&arr="+groupIDs,
				dataType: 'json',
				cache: false,
				success: function(data){
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							<?php echo $APPTAG?>_listReload(true, false); // recarrega a página
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "danger" });
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

		// CUSTOM -> Importa apenas as próximas parcelas selecionadas
		window.<?php echo $APPTAG?>_chargeSelected = function(recursive) {

			var reCursive = (isSet(recursive) && recursive) ? true : false;
			if(!reCursive) {
				if(!confirm('<?php echo JText::_('MSG_CHARGE_SELECTED_CONFIRM'); ?>')) return false;
				<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
			}

			var form = jQuery('#form-<?php echo $APPTAG?>-chargeSelected');
			var inv = form.find('#<?php echo $APPTAG?>-invID');
			var cod = '&invID='+inv.val();

			var dados		= formList.serialize();
			var inputVars	= formList.find('input[type="checkbox"]:checked, input[type="hidden"]').length;

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=chargeSelected"+cod,
				dataType: 'json',
				type: 'POST',
				data:  dados,
				cache: false,
				success: function(data){
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							// remove os itens executados
							if(res.ids.length > 0) {
								for(i = 0; i < res.ids.length; i++) {
									// remove as linhas referentes aos itens executados
									jQuery('#<?php echo $APPTAG?>-item-'+res.ids[i]).remove();
								}
							}
							// Tempo para que as linhas sejam removidas...
							// evitando o reenvio dos itens já executados
							setTimeout(function() {
								// verifica quantos estão selecionados
								var listChecks	= formList.find('input[type="checkbox"]:checked').length;
								// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
								if(inputVars > maxInputVars && listChecks > 0) <?php echo $APPTAG?>_chargeSelected(true); // executa novamente com os itens restantes
								else <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
							}, 300);
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "danger" });
						}
					});
				},
				error: function(xhr, status, error) {
					<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
				}
			});
			return false;
		};

		// CUSTOM -> remove parcelas selecionadas da lista de movimentações disponíveis para faturar
		window.<?php echo $APPTAG?>_unchargeSelected = function(recursive) {

			var reCursive = (isSet(recursive) && recursive) ? true : false;
			if(!reCursive) {
				if(!confirm('<?php echo JText::_('MSG_UNCHARGE_SELECTED_CONFIRM'); ?>')) return false;
				<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
			}

			var dados		= formList.serialize();
			var inputVars	= formList.find('input[type="checkbox"]:checked, input[type="hidden"]').length;

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=unchargeSelected",
				dataType: 'json',
				type: 'POST',
				data:  dados,
				cache: false,
				success: function(data){
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							// remove os itens executados
							if(res.ids.length > 0) {
								for(i = 0; i < res.ids.length; i++) {
									// remove as linhas referentes aos itens executados
									jQuery('#<?php echo $APPTAG?>-item-'+res.ids[i]).remove();
								}
							}
							// Tempo para que as linhas sejam removidas...
							// evitando o reenvio dos itens já executados
							setTimeout(function() {
								// verifica quantos estão selecionados
								var listChecks	= formList.find('input[type="checkbox"]:checked').length;
								// Verifica se o envio excede o limite de 1000 para o parâmetro 'max_input_vars' do PHP
								if(inputVars > maxInputVars && listChecks > 0) <?php echo $APPTAG?>_unchargeSelected(true); // executa novamente com os itens restantes
								else <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
							}, 300);
						} else {
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							$.baseNotify({ msg: res.msg, type: "danger" });
						}
					});
				},
				error: function(xhr, status, error) {
					<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
				}
			});
			return false;
		};

		// CUSTOM -> importa a fatura telefônica
		window.<?php echo $APPTAG?>_phoneInvoice = function() {

			var form = jQuery('#form-<?php echo $APPTAG?>-phoneInvoice');
			var invID = form.find('#<?php echo $APPTAG?>-phoneInvoiceID').val();
			if(invID == 0) {
				alert('<?php echo JText::_('MSG_SELECT_ITEM_FROM_LIST'); ?>');
				return false;
			}
			if(!confirm('<?php echo JText::_('MSG_PHONE_INVOICED_CONFIRM'); ?>')) return false;
			<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=phoneInvoice&st="+invID,
				dataType: 'json',
				cache: false,
				success: function(data){
					<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
					jQuery.map( data, function( res ) {
						if(res.status == 1) <?php echo $APPTAG?>_listReload(true, false); // recarrega a página
						else $.baseNotify({ msg: res.msg, type: "danger" });
					});
				},
				error: function(xhr, status, error) {
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
				}
			});
			return false;
		};

		// CUSTOM -> gera arquivo de débito
		window.<?php echo $APPTAG?>_getInvoiceFile = function(invID) {

			var seq = formFilter.find('#<?php echo $APPTAG?>-seq').val();
			if(invID == 0) {
				$.baseNotify({ msg: '<?php echo JText::_('MSG_INVOICE_NOT_SELECTED'); ?>', type: "danger" });
				return false;
			}
			if(seq == "") {
				$.baseNotify({ msg: '<?php echo JText::_('MSG_INVOICE_NOT_SEQUENTIAL'); ?>', type: "danger" });
				return false;
			}
			if(!confirm('<?php echo JText::_('MSG_INVOICE_FILE_CONFIRM'); ?>')) return false;
			<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=invoiceFile&st="+invID+"&sq="+seq,
				dataType: 'json',
				cache: false,
				success: function(data) {
					<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							var path = "<?php echo $URL_APP_FILE ?>.getFile.php?fn="+res.file;
							location.href = path;
						} else {
							$.baseNotify({ msg: res.msg, type: "danger" });
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
			return false;
		};

		// CUSTOM -> seta o sequencial anterior atribuído à fatura
		window.<?php echo $APPTAG?>_setSequencial = function(val) {
			formFilter.find('#<?php echo $APPTAG?>-seq').val(val);
		};

}); // CLOSE JQUERY->READY

jQuery(window).on('load', function() {

	// Jquery Validation
	window.<?php echo $APPTAG?>_validator = mainForm_<?php echo $APPTAG?>.validate({
		//don't remove this
		invalidHandler: function(event, validator) {
			//if there is error,
			//set custom preferences
		},
		submitHandler: function(form){
			return false;
		}
	});

	<?php
	// JQUERY VALIDATION DEFAULT FOR INPUT FILES
	// Validação básica para campos de envio de arquivo
	require(JPATH_CORE.DS.'apps/snippets/form/validationFile.def.js.php');
	?>

});

</script>

<div class="base-app<?php echo ' base-list-'.($cfg['listFull'] ? 'full' : 'ajax')?> clearfix">
	<?php
	$addText = $cfg['showList'] ? JText::_('TEXT_ADD') : JText::_('TEXT_ADD_UNLIST');
	$tipText = $cfg['addText'] ? '' : $addText;
	$relAdd	= !empty($_SESSION[$RTAG.'RelTable']) ? $APPTAG.'_setRelation('.$APPTAG.'rID);' : $APPTAG.'_setParent('.$APPTAG.'rID);';
	$addBtn = '
		<button class="base-icon-plus btn-add btn btn-sm btn-success hasTooltip '.$cfg['addClass'].'" title="'.$tipText.'" onclick="'.$relAdd.'" data-toggle="modal" data-target="#modal-'.$APPTAG.'" data-backdrop="static" data-keyboard="false">
			'.($cfg['addText'] ? ' <span class="text-add"> '.$addText.'</span>': '').'
		</button>
	';
	?>
	<?php if($cfg['showApp']) :?>
		<div class="list-toolbar<?php echo ($cfg['staticToolbar'] ? '' : ' floating')?> hidden-print">
			<?php
			if($cfg['showAddBtn'] && $hasAdmin) echo $addBtn;
			if($cfg['showList'] && $cfg['listFull']) :
			?>
				<?php if($hasAdmin) : ?>
					<button class="btn btn-sm btn-success <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 1)">
						<span class="base-icon-ok-circled"></span> <?php echo JText::_('TEXT_ACTIVE'); ?>
					</button>
					<button class="btn btn-sm btn-warning <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 0)">
						<span class="base-icon-cancel"></span> <?php echo JText::_('TEXT_INACTIVE'); ?>
					</button>
					<button class="btn btn-sm btn-danger <?php echo $APPTAG?>-btn-action d-none d-sm-inline-block" disabled onclick="<?php echo $APPTAG?>_del(0)">
						<span class="base-icon-trash"></span> <?php echo JText::_('TEXT_DELETE'); ?>
					</button>
				<?php endif; ?>
			<?php endif; ?>
			<?php if($cfg['listFull'] || $cfg['ajaxFilter']) :?>
				<button class="btn btn-sm btn-default toggle-state <?php echo ((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'active' : '')?>" data-toggle="collapse" data-target="<?php echo '#filter-'.$APPTAG?>" aria-expanded="<?php echo ((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'true' : '')?>" aria-controls="<?php echo 'filter'.$APPTAG?>">
					<span class="base-icon-filter"></span> <?php echo JText::_('TEXT_FILTER'); ?> <span class="base-icon-sort"></span>
				</button>
			<?php endif; ?>
		</div>
	<?php endif; // showApp ?>

	<?php
	$list = '';
	if($cfg['showList']) :
		// LOAD FILTER
		$htmlFilter = $where = $orderList = '';
		if($cfg['listFull'] || $cfg['ajaxFilter']) require($PATH_APP_FILE.'.filter.php');
		$where = $where;
		$orderList = $orderList;
		$listContent = $cfg['listFull'] ? require($PATH_APP_FILE.'.list.php') : '';
		if($cfg['showListDesc']) $list .= '<div class="base-list-description">'.JText::_('LIST_DESCRIPTION').'</div>';
		$list .= '<div id="list-'.$APPTAG.'" class="base-app-list">'.$listContent.'</div>';
	endif; // end noList

	if($cfg['listModal']) :
		if($cfg['showAddBtn'] && !$cfg['showApp']) $addBtn = '<div class="modal-list-toolbar">'.$addBtn.'</div>';
	?>
			<div class="modal fade" id="modal-list-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-list-<?php echo $APPTAG?>Label">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<?php require(JPATH_CORE.DS.'apps/layout/list/modal.header.php'); ?>
						<div class="modal-body">
							<?php echo $addBtn.$list; ?>
						</div>
					</div>
				</div>
			</div>
	<?php
	else :
		// SHOW LIST
		if($cfg['showApp']) echo $htmlFilter.$list;
	endif;
	?>

	<?php if($hasAdmin) : ?>
		<div class="modal fade" id="modal-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>Label">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">
						<?php if($cfg['showFormHeader']) require(JPATH_CORE.DS.'apps/layout/form/modal.header.php'); ?>
						<div class="modal-body">
							<fieldset>
								<?php
								require(JPATH_CORE.DS.'apps/layout/form/toolbar.php');
								if($newInstance) require($PATH_APP_FILE.'.form.php');
								else require_once($PATH_APP_FILE.'.form.php');
								?>
							</fieldset>
							<?php require(JPATH_CORE.DS.'apps/layout/form/alert.error.php'); ?>
						</div>
						<?php require(JPATH_CORE.DS.'apps/layout/form/modal.footer.php'); ?>
					</form>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-<?php echo $APPTAG?>-invoice" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>-invoiceLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php require($APPTAG.'.invoice.form.php'); ?>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-<?php echo $APPTAG?>-addFixed" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>-addFixedLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php require($APPTAG.'.addFixed.form.php'); ?>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-<?php echo $APPTAG?>-addSelectedFixed" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>-addSelectedFixedLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php require($APPTAG.'.addSelectedFixed.form.php'); ?>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-<?php echo $APPTAG?>-chargeInstallments" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>-chargeInstallmentsLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php require($APPTAG.'.chargeInstallments.form.php'); ?>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-<?php echo $APPTAG?>-chargeSelected" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>-chargeSelectedLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php require($APPTAG.'.chargeSelected.form.php'); ?>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modal-<?php echo $APPTAG?>-phoneInvoice" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>-phoneInvoiceLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php require($APPTAG.'.phoneInvoice.form.php'); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
