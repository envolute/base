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
	$portaria = in_array($group_portaria, $groups) ? true : false;
	?>

	window.depIds = Array();

	// APP FIELDS
	var client_id			= jQuery('#<?php echo $APPTAG?>-client_id');
	var client_desc			= jQuery('#<?php echo $APPTAG?>-client_desc');
	var presence			= jQuery('#<?php echo $APPTAG?>-presence');
	var cExam				= jQuery('#<?php echo $APPTAG?>-cExam');
	var cForbidden			= jQuery('#<?php echo $APPTAG?>-cForbidden');
	var cReason				= jQuery('#<?php echo $APPTAG?>-cReason');
	var deps				= jQuery('#<?php echo $APPTAG?>-deps');
	var dependent			= jQuery('#<?php echo $APPTAG?>-dependent');
	var exm					= jQuery('#<?php echo $APPTAG?>-exm');
	var exam				= jQuery('#<?php echo $APPTAG?>-exam');
	var locker				= jQuery('#<?php echo $APPTAG?>-locker');
	var forbidden			= jQuery('#<?php echo $APPTAG?>-forbidden');
	var reason				= jQuery('#<?php echo $APPTAG?>-reason');
	var guestName			= mainForm.find('input[name="guestName[]"]');
	var guestAge			= mainForm.find('input[name="guestAge[]"]');
	var guestNote			= mainForm.find('input[name="guestNote[]"]');
	var tax_price			= jQuery('#<?php echo $APPTAG?>-tax_price');
	var guestTax			= jQuery('#<?php echo $APPTAG?>-guestTax');
	var accessDate			= jQuery('#<?php echo $APPTAG?>-accessDate');

	// PARENT FIELD -> Select
	// informe, se houver, o campo que representa a chave estrangeira principal
	var parentFieldId		= null; // 'null', caso não exista...
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
			client_desc.val('');
			checkOption(presence, 0); // checkbox
			checkOption(cExam, 0); // checkbox
			checkOption(cForbidden, 0); // checkbox
			cReason.val('');
			// limpa a lista de dependentes
			jQuery('#<?php echo $APPTAG?>-dependentGroups').empty();
			// limpa a lista de convidados
			jQuery('#<?php echo $APPTAG?>-guestGroups').empty();
			tax_price.val('<?php echo $cfg['tax']?>');
			accessDate.val('<?php echo date('d/m/Y H:i')?>'); // DATE -> conversão de data

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

		// CUSTOM -> DEPENDENT lista dependentes de acordo com o associado
		client_id.on('change', function() {
			jQuery('#<?php echo $APPTAG?>-clientInfo').prop('hidden', (jQuery(this).val() == 0 ? true : false));
			checkOption(presence, 0); // checkbox
			checkOption(cExam, 0); // checkbox
			checkOption(cForbidden, 0); // checkbox
			cReason.val('');
			jQuery('#<?php echo $APPTAG?>-dependentGroups, #<?php echo $APPTAG?>-guestGroups').empty();
			<?php echo $APPTAG?>_getDependentList(jQuery(this).val());
		});
		// Reset client info
		window.<?php echo $APPTAG?>_resetClient = function(group) {
			if(group == 'all' && !presence.is(':checked')) {
				checkOption(cExam, 0); // checkbox
				checkOption(cForbidden, 0); // checkbox
				cReason.val('');
			} else if(group == 'exam' && !cExam.is(':checked')) {
				// forbidden and reason
				checkOption(cForbidden, 0); // checkbox
				cReason.val('');
			} else if(group == 'forbidden' && !cForbidden.is(':checked')) {
				// only reason
				cReason.val('');
			}
		};

		window.<?php echo $APPTAG?>_checkDependent = function(index, depID, Exam, Forbidden, Reason) {
			if(!isEmpty(depID) && depID != 0) {
				var dCheck = jQuery('#<?php echo $APPTAG?>-deps'+index);
				var d = jQuery('#<?php echo $APPTAG?>-dependent'+index);
				var eCheck = jQuery('#<?php echo $APPTAG?>-exm'+index);
				var e = jQuery('#<?php echo $APPTAG?>-exam'+index);
				var fCheck = jQuery('#<?php echo $APPTAG?>-locker'+index);
				var f = jQuery('#<?php echo $APPTAG?>-forbidden'+index);
				var r = jQuery('#<?php echo $APPTAG?>-reason'+index);
				checkOption(dCheck, 1); // checkbox
				d.val(depID);
				checkOption(eCheck, (!isEmpty(Exam) ? 1 : 0)); // checkbox
				e.val(Exam);
				checkOption(fCheck, (!isEmpty(Forbidden) ? 1 : 0)); // checkbox
				f.val(Forbidden);
				r.val(Reason);
			}
		}
		// Reset dependent info
		window.<?php echo $APPTAG?>_resetDependent = function(index, group) {
			var dCheck = jQuery('#<?php echo $APPTAG?>-deps'+index);
			var eCheck = jQuery('#<?php echo $APPTAG?>-exm'+index);
			var e = jQuery('#<?php echo $APPTAG?>-exam'+index);
			var fCheck = jQuery('#<?php echo $APPTAG?>-locker'+index);
			var f = jQuery('#<?php echo $APPTAG?>-forbidden'+index);
			var r = jQuery('#<?php echo $APPTAG?>-reason'+index);

			if(group == 'all' && !dCheck.is(':checked')) {
				checkOption(eCheck, 0); // checkbox
				e.val('');
				checkOption(fCheck, 0); // checkbox
				f.val('');
				r.val('');
			} else if(group == 'exam' && !eCheck.is(':checked')) {
				// forbidden and reason
				checkOption(fCheck, 0); // checkbox
				f.val('');
				r.val('');
			} else if(group == 'forbidden' && !fCheck.is(':checked')) {
				// only reason
				r.val('');
			}
		};

		// GUEST ADD -> Adiciona novos campos para convidado
		window.<?php echo $APPTAG?>GuestIndex = 1;
		window.<?php echo $APPTAG?>_guestAdd = function(name, age, note, tax) {
			<?php echo $APPTAG?>GuestIndex++;
			var gName = isSet(name) ? name : '';
			var gAge = isSet(age) ? age : '';
			var gNote = isSet(note) ? note : '';
			var gTax = isSet(tax) ? tax : '0';
			var tCheck = (gTax == 1) ? ' checked' : '';

			var formGroup = '';
			formGroup += '<div id="<?php echo $APPTAG?>-newGuestGroup'+<?php echo $APPTAG?>GuestIndex+'" class="pt-3 b-top b-top-dashed">';
			formGroup += '	<div class="row">';
			formGroup += '		<div class="col-10">';
			formGroup += '			<div class="row">';
			formGroup += '				<div class="col-12">';
			formGroup += '					<div class="form-group">';
			formGroup += '						<div class="input-group">';
			formGroup += '							<input type="text" name="guestName[]" id="<?php echo $APPTAG?>-guestName'+<?php echo $APPTAG?>GuestIndex+'" value="'+gName+'" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_NAME'); ?>" />';
			<?php if(!$portaria) :?>
				formGroup += '							<label class="input-group-addon text-success bg-white">';
				formGroup += '								<input type="checkbox" name="tax[]" value="1"'+tCheck+' class="mr-1 auto-tab" data-target="#<?php echo $APPTAG?>-guestTax'+<?php echo $APPTAG?>GuestIndex+'" data-target-value="1" data-target-value-reset="" data-tab-disabled="true" />';
				formGroup += '								<span class="base-icon-dollar icon-default"></span> <?php echo JText::_('TEXT_TAX'); ?>';
				formGroup += '								<input type="hidden" name="guestTax[]" id="<?php echo $APPTAG?>-guestTax'+<?php echo $APPTAG?>GuestIndex+'" value="'+gTax+'" />';
				formGroup += '							</label>';
			<?php endif;?>
			formGroup += '						</div>';
			formGroup += '					</div>';
			formGroup += '				</div>';
			formGroup += '				<div class="col-lg-4">';
			formGroup += '					<div class="form-group">';
			formGroup += '						<div class="input-group input-group-sm">';
			formGroup += '							<input type="text" name="guestAge[]" id="<?php echo $APPTAG?>-guestAge'+<?php echo $APPTAG?>GuestIndex+'" value="'+gAge+'" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_AGE'); ?>" maxlength="3" />';
			formGroup += '							<span class="input-group-addon">';
			formGroup += '								<?php echo JText::_('TEXT_YEARS'); ?>';
			formGroup += '							</span>';
			formGroup += '						</div>';
			formGroup += '					</div>';
			formGroup += '				</div>';
			formGroup += '				<div class="col-lg-8">';
			formGroup += '					<div class="form-group">';
			formGroup += '						<input type="text" name="guestNote[]" id="<?php echo $APPTAG?>-guestNote'+<?php echo $APPTAG?>GuestIndex+'" value="'+gNote+'" class="form-control form-control-sm" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE'); ?>" maxlength="100" />';
			formGroup += '					</div>';
			formGroup += '				</div>';
			formGroup += '			</div>';
			formGroup += '		</div>';
			formGroup += '		<div class="col-2">';
			formGroup += '			<button type="button" class="btn btn-danger base-icon-cancel" onclick="<?php echo $APPTAG?>_guestRemove(\'#<?php echo $APPTAG?>-newGuestGroup'+<?php echo $APPTAG?>GuestIndex+'\')"></button>';
			formGroup += '		</div>';
			formGroup += '	</div>';
			formGroup += '</div>';

			jQuery('#<?php echo $APPTAG?>-guestGroups').append(formGroup);
			setFormDefinitions();
			jQuery('#<?php echo $APPTAG?>-guestName'+<?php echo $APPTAG?>GuestIndex).focus();

		}
		// GUEST REMOVE -> Remove campos de convidado
		window.<?php echo $APPTAG?>_guestRemove = function(id) {
			if(confirm('<?php echo JText::_('MSG_CONFIRM_REMOVE_GUEST')?>')) jQuery(id).remove();
		}

		// CUSTOM: Print Payment
		window.<?php echo $APPTAG?>_printPayment = function(itemID, execute) {
			var hasGuest = mainForm_access.find('input[name="guestName[]"]').length;
			var tax = 0;
			mainForm_access.find('input[name="guestTax[]"]').each(function() {
				if(jQuery(this).val() == 1) tax = 1;
			});
			var exec = isSet(execute) ? execute : false;
			var pID = (isSet(itemID) && itemID > 0) ? itemID : false;
			setTimeout(function() {
				if(pID && ((hasGuest && tax) || exec)) {
					var urlPrint = '<?php echo JURI::root()?>apps/access/<?php echo $APPTAG?>-payment?uID='+pID+'&tmpl=component';
					jQuery('#<?php echo $APPTAG?>-payment-iframe').attr("src", urlPrint);
					jQuery("#modal-payment-<?php echo $APPTAG?>").modal();
				}
			}, 100);
		}
		window.<?php echo $APPTAG?>_setPrintPayment = function(){
			document.getElementById("<?php echo $APPTAG?>-payment-iframe").contentWindow.print();
		}

	// LIST CONTROLLERS
	// ações & métodos controladores da listagem

		// ON MODAL CLOSE -> Ações quando o modal da listagem é fechado
		listPopup.on('hidden.bs.modal', function () {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/list/onModalClose.def.js.php');
			?>
		});

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
						client_id.selectUpdate(item.client_id); // select
						// mostra/esconde 'client_desc'
						if(!isEmpty(item.client_desc)) jQuery('#<?php echo $APPTAG?>-clientDescGroup').prop('hidden', false);
						setTimeout(function() {
							client_desc.val(item.client_desc); // select
						}, 100);
						checkOption(presence, item.presence); // checkbox
						checkOption(cExam, item.cExam); // checkbox
						checkOption(cForbidden, item.cForbidden); // checkbox
						cReason.val(item.cReason);
						// Dependents
						var dp = item.dependent.split(";");
						var ex = item.exam.split(";");
						var fb = item.forbidden.split(";");
						var rs = item.reason.split(";");
						setTimeout(function() {
							for(i = 0; i < dp.length; i++) {
								<?php echo $APPTAG?>_checkDependent(i, dp[i], ex[i], fb[i], rs[i]);
							}
						}, 2000);

						// Convidados
						var gName = item.guestName.split(";");
						var gAge = item.guestAge.split(";");
						var gNote = item.guestNote.split(";");
						var gTax = item.guestTax.split(";");
						for(i = 0; i < gName.length; i++) {
							if(!isEmpty(gName[i])) <?php echo $APPTAG?>_guestAdd(gName[i], gAge[i], gNote[i], gTax[i]);
						}
						tax_price.val(item.tax_price);
						accessDate.val(dateFormat(item.accessDate, 'd/m/Y H:i:s')); // DATE -> conversão de data

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

		// CUSTOM
		// Set dependents List
		// seta a lista de dependentes de acordo com o associado selecionado
		window.<?php echo $APPTAG?>_getDependentList = function(itemID) {
			<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
			dateConvert();
			var dt = accessDate.val();
			var keepOn = 1;
			var upDate = (!isEmpty(displayId.val()) && displayId.val() > 0) ? true : false;
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?task=cList&cID="+itemID+"&aDate="+dt,
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data){
					jQuery.map( data, function( res, i ) {
						if(res.status != 0) {
							// remove all options
							if(i == 0) jQuery('#<?php echo $APPTAG?>-dependentGroups').empty();
							if(res.status == 1) {
								if(!upDate && res.exist == 1 && i == 0) {
									if(!confirm('<?php echo JText::_('MSG_CLIENT_DATE_EXIST')?>')) {
										<?php echo $APPTAG?>_formReset();
										keepOn = 0;
									}
								}
								if(keepOn) {
									depIds = res.id;
									<?php echo $APPTAG?>DepIndex = i;
									var formGroup = '';
									if(i == 0) {
										formGroup += '<hr class="hr-tag" /><span class="badge badge-primary"><?php echo JText::_('TEXT_DEPENDENTS')?></span>';
									} else {
										formGroup += '<hr class="b-top-dashed" />';
									}

									<?php $setExam = ($portaria ? 'style="display:none!important"' : '');?>
									formGroup += '<div id="<?php echo $APPTAG?>-newDepGroup'+<?php echo $APPTAG?>DepIndex+'">';
									formGroup += '	<div class="form-check">';
									formGroup += '		<h5 class="form-check-label">';
									formGroup += '			<input type="checkbox" name="deps[]" value="1" onChange="<?php echo $APPTAG?>_resetDependent('+<?php echo $APPTAG?>DepIndex+', \'all\')" id="<?php echo $APPTAG?>-deps'+<?php echo $APPTAG?>DepIndex+'" class="form-check-input auto-tab" data-target="#<?php echo $APPTAG?>-examGroup'+<?php echo $APPTAG?>DepIndex+'" data-target-display="true" data-target-field="#<?php echo $APPTAG?>-dependent'+<?php echo $APPTAG?>DepIndex+'" data-target-value="'+res.id+'" data-target-value-reset="" data-tab-disabled="true">';
									formGroup += '			'+res.name+'';
									formGroup += '		</h5>';
									formGroup += '		<input type="hidden" name="dependent[]" id="<?php echo $APPTAG?>-dependent'+<?php echo $APPTAG?>DepIndex+'" />';
									formGroup += '	</div>';
									formGroup += '	<div id="<?php echo $APPTAG?>-examGroup'+<?php echo $APPTAG?>DepIndex+'" hidden <?php echo $setExam?>>';
									formGroup += '		<div class="form-check form-check-inline">';
									formGroup += '			<label class="form-check-label">';
									formGroup += '				<input type="checkbox" name="exm[]" value="1" onChange="<?php echo $APPTAG?>_resetDependent('+<?php echo $APPTAG?>DepIndex+', \'exam\')" id="<?php echo $APPTAG?>-exm'+<?php echo $APPTAG?>DepIndex+'" class="form-check-input auto-tab" data-target=".examDescGroup'+<?php echo $APPTAG?>DepIndex+'" data-target-field="<?php echo $APPTAG?>-exam'+<?php echo $APPTAG?>DepIndex+'" data-target-value="1" data-target-value-reset="" data-target-display="true">';
									formGroup += '				<?php echo JText::_('FIELD_LABEL_EXAM')?>';
									formGroup += '			</label>';
									formGroup += '			<input type="hidden" name="exam[]" id="<?php echo $APPTAG?>-exam'+<?php echo $APPTAG?>DepIndex+'" />';
									formGroup += '		</div>';
									formGroup += '		<span class="base-icon-right-big mx-2 examDescGroup'+<?php echo $APPTAG?>DepIndex+'" hidden></span>';
									formGroup += '		<div class="form-check form-check-inline examDescGroup'+<?php echo $APPTAG?>DepIndex+'" hidden>';
									formGroup += '			<label class="form-check-label text-danger">';
									formGroup += '				<input type="checkbox" name="locker[]" value="1" onChange="<?php echo $APPTAG?>_resetDependent('+<?php echo $APPTAG?>DepIndex+', \'forbidden\')" id="<?php echo $APPTAG?>-locker'+<?php echo $APPTAG?>DepIndex+'" class="form-check-input auto-tab" data-target="#<?php echo $APPTAG?>-reason'+<?php echo $APPTAG?>DepIndex+'" data-target-field="<?php echo $APPTAG?>-forbidden'+<?php echo $APPTAG?>DepIndex+'" data-target-value="1" data-target-value-reset="" data-target-display="true">';
									formGroup += '				<?php echo JText::_('FIELD_LABEL_FORBIDDEN')?>';
									formGroup += '				<input type="hidden" name="forbidden[]" id="<?php echo $APPTAG?>-forbidden'+<?php echo $APPTAG?>DepIndex+'" />';
									formGroup += '			</label>';
									formGroup += '		</div>';
									formGroup += '	</div>';
									formGroup += '</div>';
									formGroup += '<div class="examDescGroup'+<?php echo $APPTAG?>DepIndex+'" hidden>';
									formGroup += '	<input type="text" name="reason[]" class="form-control form-control-sm" id="<?php echo $APPTAG?>-reason'+<?php echo $APPTAG?>DepIndex+'" placeholder="Motivo" hidden />';
									formGroup += '</div>';
									jQuery('#<?php echo $APPTAG?>-dependentGroups').append(formGroup);
									//if(displayId.val() > 0) checkDependents(<?php echo $APPTAG?>DepIndex);
									setFormDefinitions();
								}
							}
						} else {
							$.baseNotify({ msg: res.msg, type: "danger"});
						}
					});
				},
				error: function(xhr, status, error) {
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
				},
				complete: function() {
					// Seleciona os dependentes marcados
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
				}
			});
			return false;
		};

	// JQUERY VALIDATION
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
			if($cfg['showAddBtn'] && $cfg['canAdd']) echo $addBtn;
			if($cfg['showList']) :
				if($cfg['listFull']) :
					if($cfg['canEdit']) : ?>
						<button class="btn btn-sm btn-success <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 1)">
							<span class="base-icon-ok-circled"></span> <?php echo JText::_('TEXT_ACTIVE'); ?>
						</button>
						<button class="btn btn-sm btn-warning <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 0)">
							<span class="base-icon-cancel"></span> <?php echo JText::_('TEXT_INACTIVE'); ?>
						</button>
					<?php endif;?>
					<?php if($cfg['canDelete']) :?>
						<button class="btn btn-sm btn-danger <?php echo $APPTAG?>-btn-action d-none d-sm-inline-block" disabled onclick="<?php echo $APPTAG?>_del(0)">
							<span class="base-icon-trash"></span> <?php echo JText::_('TEXT_DELETE'); ?>
						</button>
					<?php endif;?>
				<?php else :?>
					<?php if(!$cfg['listModal'] && !$cfg['listFull'] && $cfg['ajaxReload']) :?>
						<a href="#" class="btn btn-sm btn-info base-icon-arrows-cw" onclick="<?php echo $APPNAME?>_listReload(false, false, false)"></a>
					<?php endif;?>
				<?php endif;?>
				<?php if($cfg['listFull'] || $cfg['ajaxFilter']) :?>
					<button class="btn btn-sm btn-default toggle-state <?php echo ((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'active' : '')?>" data-toggle="collapse" data-target="<?php echo '#filter-'.$APPTAG?>" aria-expanded="<?php echo ((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'true' : '')?>" aria-controls="<?php echo 'filter'.$APPTAG?>">
						<span class="base-icon-filter"></span> <?php echo JText::_('TEXT_FILTER'); ?> <span class="base-icon-sort"></span>
					</button>
				<?php endif;?>
			<?php endif;?>
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
		$listContent = $cfg['listFull'] ? require($PATH_APP_FILE.'.'.(!empty($cfg['listCustom']) ? $cfg['listCustom'] : 'list.php')) : '';
		if($cfg['showListDesc']) $list .= '<div class="base-list-description">'.JText::_('LIST_DESCRIPTION').'</div>';
		$list .= '<div id="list-'.$APPTAG.'" class="base-app-list">'.$listContent.'</div>';
	endif; // end noList

	if($cfg['listModal']) :
		$addBtn = $cfg['showAddBtn'] ? '<div class="modal-list-toolbar">'.$addBtn.'</div>' : '';
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

	<?php if($cfg['canAdd'] || $cfg['canEdit']) : ?>
		<div class="modal fade" id="modal-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>Label">
			<div class="modal-dialog" role="document">
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
	<?php endif; ?>

	<div class="modal fade" id="modal-payment-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-payment-<?php echo $APPTAG?>Label">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<iframe id="<?php echo $APPTAG?>-payment-iframe" style="width:325px; height:600px; border:1px dashed #ddd"></iframe>
    				<button type="button" class="btn btn-lg btn-success mx-4 float-right hidden-print" style="height:80px" onclick="<?php echo $APPTAG?>_setPrintPayment()"> <?php echo JText::_('TEXT_PRINT'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
