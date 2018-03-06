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

// verifica se é um cliente
$hasClient	= array_intersect($groups, $cfg['groupId']['client']); // se está na lista de administradores permitidos
// GET CLIENT ID
$client_id = 0;
if($hasClient) {
	$query = 'SELECT client_id FROM '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' WHERE user_id = '.$user->id.' AND state = 1';
	$db->setQuery($query);
	$client_id = $db->loadResult();
}
$cProj = $client_id ? 'client_id = '.$client_id.' AND ' : '';

?>

<script>
jQuery(function() {

	<?php // Default 'JS' Vars
	require(JPATH_CORE.DS.'apps/snippets/initVars.js.php');
	?>

	// STATUS CONTAINERS
	var typePopup	= jQuery('#modal-type-<?php echo $APPTAG?>');
	var formType	= jQuery('#form-type-<?php echo $APPTAG?>');

	// APP FIELDS
	var project_id			= jQuery('#<?php echo $APPTAG?>-project_id');
	var type				= mainForm.find('input[name=type]:radio'); // radio group
	var ctype				= jQuery('#<?php echo $APPTAG?>-ctype');
	var subject				= jQuery('#<?php echo $APPTAG?>-subject');
	var description			= jQuery('#<?php echo $APPTAG?>-description');
	var priority			= mainForm.find('input[name=priority]:radio'); // radio group
	var deadline			= jQuery('#<?php echo $APPTAG?>-deadline');
	var timePeriod			= jQuery('#<?php echo $APPTAG?>-timePeriod');
	var tags				= jQuery('#<?php echo $APPTAG?>-tags');

	// ALTER STATUS
	var typeId			= jQuery('#<?php echo $APPTAG?>-typeId');
	var typeOn			= jQuery('#<?php echo $APPTAG?>-typeOn');
	var typeDs			= jQuery('#<?php echo $APPTAG?>-typeDs');
	var new_type			= formType.find('input[name=new_type]:radio');

	// PARENT FIELD -> Select
	// informe, se houver, o campo que representa a chave estrangeira principal
	var parentFieldId		= project_id; // 'null', caso não exista...
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
			checkOption(type, '');
			ctype.val('');
			subject.val('');
			description.val('');
			checkOption(priority, 0);
			deadline.val('');
			timePeriod.selectUpdate('<?php echo JText::_('TEXT_AM'); ?>'); // select
			tags.selectUpdate(''); // select

			// TO DO LIST
			setHidden(jQuery('#<?php echo $APPTAG?>-alert-toDo'), false, jQuery('#<?php echo $APPTAG?>-btn-toDo'));

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

		// CUSTOM -> view ToDo list
		window.<?php echo $APPTAG?>_viewToDo = function() {
			<?php echo $APPTAG?>Todo_listReload(false, false, false, false, false, formId.val());
		};

		// CUSTOM -> Set Item View
		// Mostra a view em um modal
		var <?php echo $APPTAG?>ItemView = jQuery("#<?php echo $APPTAG?>-item-view");
		var <?php echo $APPTAG?>ItemViewContent = jQuery("#<?php echo $APPTAG?>-view-content");

		window.<?php echo $APPTAG?>_setItemView = function(itemID) {
			var urlView = "<?php echo JURI::root(true)?>/apps/<?php echo $APPPATH?>/view?vID="+itemID+"&tmpl=component";
			<?php echo $APPTAG?>ItemViewContent.attr('src', urlView);
		}
		// ON MODAL CLOSE -> Ações quando o modal da listagem é fechado
		<?php echo $APPTAG?>ItemView.on('hidden.bs.modal', function () {
			<?php echo $APPTAG?>ItemViewContent.attr('src', '');
			<?php echo $APPTAG?>_listReload(false, false, false);
		});

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

		// CUSTOM -> mostra o form para alterar o type
		window.<?php echo $APPTAG?>_setTypeModal = function(e) {
			var obj = jQuery(e);
			var id = obj.data('id');
			var val = obj.data('type');
			typePopup.modal();
			typeId.val(id);
			checkOption(new_type, val); // radio
			setFormDefinitions();
		};
		// On Modal Close -> Ações quando o modal é fechado
		typePopup.on('hidden.bs.modal', function () {
			typeId.val('');
			checkOption(new_type, 0); // radio
			setFormDefinitions();
		});

		// CUSTOM -> Confirm alter state (close task)
		window.<?php echo $APPTAG?>_confirmState = function(itemID, msgType) {
			var msg = msgType ? '<?php echo JText::_('MSG_CLOSE_ITEM_CONFIRM')?>' : '<?php echo JText::_('MSG_OPEN_ITEM_CONFIRM')?>';
			if(confirm(msg)) <?php echo $APPTAG?>_setState(itemID, null, false, 'base-icon-toggle-on', 'base-icon-toggle-on', 'text-success', 'text-muted');
		};

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
						project_id.selectUpdate(item.project_id); // select
						checkOption(type, item.type);
						ctype.val(item.type);
						subject.val(item.subject);
						description.val(item.description);
						checkOption(priority, item.priority);
						deadline.val(dateFormat(item.deadline)); // DATE -> conversão de data
						timePeriod.selectUpdate(item.timePeriod); // select
						tags.selectUpdate(item.tags); // select

						// TODO LIST
						setHidden(jQuery('#<?php echo $APPTAG?>-alert-toDo'), true, jQuery('#<?php echo $APPTAG?>-btn-toDo'));

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

		// CUSTOM -> Set Type
		// seta o valor do campo 'type' do registro
		window.<?php echo $APPTAG?>_setType = function(type) {
			var cod = '&id='+typeId.val();
			var st = '&st='+type;
			<?php echo $APPTAG?>_formExecute(true, false, true); // inicia o loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=type"+cod+st,
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data) {
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
					jQuery.map( data, function( res ) {
						if(res.type == 1) {
							if(res.newType == 0) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-type').attr('title', '<?php echo JText::_('TEXT_TYPE_0')?>').removeClass().addClass('base-icon-clock text-live hasTooltip');
							else if(res.newType == 1) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-type').attr('title', '<?php echo JText::_('TEXT_TYPE_1')?>').removeClass().addClass('base-icon-off text-live hasTooltip');
							else if(res.newType == 2) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-type').attr('title', '<?php echo JText::_('TEXT_TYPE_2')?>').removeClass().addClass('base-icon-off text-primary hasTooltip');
							else if(res.newType == 3) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-type').attr('title', '<?php echo JText::_('TEXT_TYPE_3')?>').removeClass().addClass('base-icon-ok text-success hasTooltip');
							jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-type').data('type', res.newType);
							setTips();
						}
					});
				},
				error: function(xhr, status, error) {
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
				},
				complete: function() {
					typePopup.modal('hide');
					<?php echo $APPTAG?>_listReload(false, false, false);
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
		$listContent = $cfg['listFull'] ? require($PATH_APP_FILE.'.list.php') : '';
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

	<div class="modal fade" id="<?php echo $APPTAG?>-item-view" tabindex="-1" role="dialog" aria-labelledby="task-view-<?php echo $APPTAG?>Label">
		<div class="modal-dialog mw-100 m-0" role="document">
			<div class="modal-content">
				<button type="button" class="close pos-absolute pos-right-gutter mt-2" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<iframe width="100%" id="<?php echo $APPTAG?>-view-content" class="set-height" data-offset="4"></iframe>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-type-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-type-<?php echo $APPTAG?>Label">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<?php require($PATH_APP_FILE.'.form.type.php'); ?>
			</div>
		</div>
	</div>
</div>
