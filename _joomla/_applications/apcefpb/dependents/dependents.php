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
	var client_id			= jQuery('#<?php echo $APPTAG?>-client_id');
	var group_id 			= jQuery('#<?php echo $APPTAG?>-group_id');
	var name 				= jQuery('#<?php echo $APPTAG?>-name');
		// Card
		var toggleName 		= jQuery('#<?php echo $APPTAG?>-toggleName');
		var card_name 		= jQuery('#<?php echo $APPTAG?>-card_name');
	var email				= jQuery('#<?php echo $APPTAG?>-email');
	var gender 				= mainForm.find('input[name=gender]:radio'); // radio group
	var birthday			= jQuery('#<?php echo $APPTAG?>-birthday');
	// phones
	var phone				= jQuery('#<?php echo $APPTAG?>-phone');
	var wapp				= jQuery('#<?php echo $APPTAG?>-wapp');
	var whatsapp			= jQuery('#<?php echo $APPTAG?>-whatsapp');
	var phone_desc			= jQuery('#<?php echo $APPTAG?>-phone_desc');
	var docs 				= mainForm.find('input[name=docs]:radio'); // radio group
	var note				= jQuery('#<?php echo $APPTAG?>-note');

	// PARENT FIELD -> Select
	// informe, se houver, o campo que representa a chave estrangeira principal
	var parentFieldId		= client_id; // 'null', caso não exista...
	var parentFieldGroup	= elementExist(parentFieldId) ? parentFieldId.closest('[class*="col-"]') : null;

	// GROUP RELATION'S BUTTONS -> grupo de botões de relacionamentos no form
	var groupRelations		= jQuery('#<?php echo $APPTAG?>-group-relation');

	// FORM CONTROLLERS
	// métodos controladores do formulário

		// ON FOCUS
		// campo que recebe o focus no carregamento
		var firstField		= name;

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
			group_id.selectUpdate(0);
			name.val('');
			email.val('');
			checkOption(gender, ''); // radio
			birthday.val('');
			phone.phoneMaskUpdate(''); // toggleMask
			checkOption(wapp, 0); // checkbox
			whatsapp.val('');
			phone_desc.val('');
			// CARD
			checkOption(toggleName, 0);
			card_name.val('');
			checkOption(docs, 0);
			note.val('');
			// esconde o botão para impressão
			setHidden('#<?php echo $APPTAG?>-group-btnPrint', true);

			// CUSTOM -> Remove new fields
			jQuery('.newFieldsGroup').empty();

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

		// PHONE ADD -> Adiciona novo campo para telefone
		window.<?php echo $APPTAG?>PhoneIndex = 1;
		window.<?php echo $APPTAG?>_phoneAdd = function(phone, whatsapp, description) {
			<?php echo $APPTAG?>PhoneIndex++;
			var p = (isSet(phone) && !isEmpty(phone)) ? phone : '';
			var w = (isSet(whatsapp) && !isEmpty(whatsapp)) ? whatsapp : '';
				var wState = (w == 1) ? ' active' : '';
				var wCheck = (w == 1) ? ' checked' : '';
			var d = (isSet(description) && !isEmpty(description)) ? description : '';

			var formGroup = '';
			formGroup += '<div id="<?php echo $APPTAG?>-newPhoneGroup'+<?php echo $APPTAG?>PhoneIndex+'">';
			formGroup += '	<div class="form-group row">';
			formGroup += '		<div class="col-sm-5 col-lg-4">';
			formGroup += '			<div class="input-group">';
			formGroup += '				<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone'+<?php echo $APPTAG?>PhoneIndex+'" value="'+p+'" class="form-control field-phone" />';
			formGroup += '				<span class="input-group-btn btn-group" data-toggle="buttons">';
			formGroup += '					<label class="btn btn-outline-success btn-block btn-active-success'+wState+' hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">';
			formGroup += '						<input type="checkbox" name="wapp[]" value="1"'+wCheck+' class="auto-tab" data-target="#<?php echo $APPTAG?>-whatsapp'+<?php echo $APPTAG?>PhoneIndex+'" data-target-value="1" data-target-value-reset="" data-tab-disabled="true" />';
			formGroup += '						<span class="base-icon-whatsapp icon-default"></span>';
			formGroup += '						<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp'+<?php echo $APPTAG?>PhoneIndex+'" value="'+w+'" />';
			formGroup += '					</label>';
			formGroup += '				</span>';
			formGroup += '			</div>';
			formGroup += '		</div>';
			formGroup += '		<div class="col-sm-7 col-lg-8 pt-2 pt-sm-0">';
			formGroup += '			<div class="input-group">';
			formGroup += '				<input type="text" name="phone_desc[]" id="<?php echo $APPTAG?>-phone_desc'+<?php echo $APPTAG?>PhoneIndex+'" value="'+d+'" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" maxlength="50" />';
			formGroup += '				<span class="input-group-btn">';
			formGroup += '					<button type="button" class="btn btn-danger base-icon-cancel" onclick="<?php echo $APPTAG?>_phoneRemove(\'#<?php echo $APPTAG?>-newPhoneGroup'+<?php echo $APPTAG?>PhoneIndex+'\')"></button>';
			formGroup += '				</span>';
			formGroup += '			</div>';
			formGroup += '		</div>';
			formGroup += '	</div>';
			formGroup += '</div>';

			jQuery('#<?php echo $APPTAG?>-phoneGroups').append(formGroup);
			setPhone();
			checkAutoTab();
		}
		// PHONE REMOVE -> Remove campo de telefone
		window.<?php echo $APPTAG?>_phoneRemove = function(id) {
			if(confirm('<?php echo JText::_('MSG_CONFIRM_REMOVE_PHONE')?>')) jQuery(id).remove();
		}

		// CUSTOM: Print Card
		window.<?php echo $APPTAG?>_printCard = function(itemID){
			var cID = (isSet(itemID) && itemID > 0) ? itemID : formId.val();
			var urlPrint = '<?php echo JURI::root()?>apps/dependents/<?php echo $APPTAG?>-card?uID='+cID+'&tmpl=component';
			jQuery('#<?php echo $APPTAG?>-card-iframe').attr("src", urlPrint);
			jQuery("#modal-card-<?php echo $APPTAG?>").modal();
		}
		window.<?php echo $APPTAG?>_setPrintCard = function(){
			document.getElementById("<?php echo $APPTAG?>-card-iframe").contentWindow.print();
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
						client_id.selectUpdate(item.client_id);
						group_id.selectUpdate(item.group_id);
						name.val(item.name);
						email.val(item.email);
						checkOption(gender, item.gender); // radio
						birthday.val(dateFormat(item.birthday)); // DATE -> conversão de data
						// phones
						var p = item.phone.split(";");
						var w = item.whatsapp.split(";");
						var d = item.phone_desc.split(";");
						for(i = 0; i < p.length; i++) {
							wCheck = (w[i] == 1 ? 1 : 0);
							if(i == 0) {
								phone.phoneMaskUpdate(p[0]);
								checkOption(wapp, wCheck); // checkbox
								whatsapp.val(w[0]);
								phone_desc.val(d[0]);
							} else {
								<?php echo $APPTAG?>_phoneAdd(p[i], w[i], d[i]);
							}
						}
						// CARD
						checkOption(toggleName, (!isEmpty(item.card_name) ? 1 : 0));
						card_name.val(item.card_name);
						// mostra o botão para impressão
						setHidden('#<?php echo $APPTAG?>-group-btnPrint', false);
						checkOption(docs, item.docs);
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
				<div class="modal-dialog" role="document">
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
	<?php endif; ?>

	<div class="modal fade" id="modal-card-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-card-<?php echo $APPTAG?>Label">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<iframe id="<?php echo $APPTAG?>-card-iframe" style="width:325px; height:205px; border:1px dashed #ddd"></iframe>
    				<button type="button" class="btn btn-lg btn-success mx-4 float-right hidden-print" style="height:80px" onclick="<?php echo $APPTAG?>_setPrintCard()"> <?php echo JText::_('TEXT_PRINT'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
