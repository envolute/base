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

// verifica o acesso
$hasAuthor	= array_intersect($groups, $cfg['groupId']['author']); // se está na lista de administradores permitidos

// DATABASE CONNECT
$db = JFactory::getDbo();

?>

<script>
jQuery(function() {

	<?php // Default 'JS' Vars
	require(JPATH_CORE.DS.'apps/snippets/initVars.js.php');
	?>

	// STATUS CONTAINERS
	var statusPopup	= jQuery('#modal-status-<?php echo $APPTAG?>');
	var formStatus	= jQuery('#form-status-<?php echo $APPTAG?>');

	// APP FIELDS
	var project_id			= jQuery('#<?php echo $APPTAG?>-project_id');
	var type				= mainForm.find('input[name=type]:radio'); // radio group
	var subject				= jQuery('#<?php echo $APPTAG?>-subject');
	var description			= jQuery('#<?php echo $APPTAG?>-description');
	var priority			= mainForm.find('input[name=priority]:radio'); // radio group
	var deadline			= jQuery('#<?php echo $APPTAG?>-deadline');
	var timePeriod			= jQuery('#<?php echo $APPTAG?>-timePeriod');
	var tags				= jQuery('#<?php echo $APPTAG?>-tags');
	<?php if($hasAuthor) :?>
		var setClose		= jQuery('#<?php echo $APPTAG?>-setClose');
		var status			= jQuery('#<?php echo $APPTAG?>-status');
	<?php else :?>
		var status			= mainForm.find('input[name=status]:radio'); // radio group
	<?php endif;?>
	var cstatus				= jQuery('#<?php echo $APPTAG?>-cstatus');
	var status_desc			= jQuery('#<?php echo $APPTAG?>-status_desc');

	// ALTER STATUS
	var statusId			= jQuery('#<?php echo $APPTAG?>-statusId');
	var statusOn			= jQuery('#<?php echo $APPTAG?>-statusOn');
	var statusDs			= jQuery('#<?php echo $APPTAG?>-statusDs');
	var new_status			= formStatus.find('input[name=new_status]:radio');

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
			checkOption(type, 0);
			subject.val('');
			description.val('');
			checkOption(priority, 0);
			deadline.val('');
			timePeriod.selectUpdate('<?php echo JText::_('TEXT_AM'); ?>'); // select
			tags.selectUpdate(''); // select
			<?php if($hasAuthor) :?>
				setHidden(setClose, true);
				checkOption(setClose, 0);
				status.val(0);
			<?php else :?>
				checkOption(status, 0);
			<?php endif;?>
			cstatus.val('');
			status_desc.val('');

			// TODO LIST
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

		// CUSTOM -> Set Type
		// implementa ações de acordo com o tipo de tarefa
		window.<?php echo $APPTAG?>_setType = function(e) {
			setHidden(jQuery('[class*="<?php echo $APPTAG?>-groupType"]'), true);
			// show type fields
			if(e !== 0) {
				setHidden(jQuery('.<?php echo $APPTAG?>-groupType-'+e), false);
				if(e == 1) setHidden(jQuery('#<?php echo $APPTAG?>-component-label'), false, jQuery('#<?php echo $APPTAG?>-subject-label'));
			} else {
				setHidden(jQuery('#<?php echo $APPTAG?>-subject-label'), false);
			}
		}

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

		// CUSTOM -> mostra o form para alterar o status
		window.<?php echo $APPTAG?>_setStatusModal = function(e) {
			var obj = jQuery(e);
			var id = obj.data('id');
			var val = obj.data('status');
			statusPopup.modal();
			statusId.val(id);
			checkOption(new_status, val); // radio
			setFormDefinitions();
		};
		// On Modal Close -> Ações quando o modal é fechado
		statusPopup.on('hidden.bs.modal', function () {
			statusId.val('');
			checkOption(new_status, 0); // radio
			setFormDefinitions();
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
						project_id.selectUpdate(item.project_id); // select
						checkOption(type, item.type);
						subject.val(item.subject);
						description.val(item.description);
						checkOption(priority, item.priority);
						deadline.val(dateFormat(item.deadline)); // DATE -> conversão de data
						timePeriod.selectUpdate(item.timePeriod); // select
						tags.selectUpdate(item.tags); // select
						<?php if($hasAuthor) :?>
							setHidden(setClose, true);
							checkOption(setClose, (item.status == 3 ? 1 : 0));
							status.val(item.status);
						<?php else :?>
							checkOption(status, item.status);
						<?php endif;?>
						cstatus.val(item.status);
						status_desc.val(item.status_desc);

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

		// CUSTOM -> Set Status
		// seta o valor do campo 'status' do registro
		window.<?php echo $APPTAG?>_setStatus = function(status) {
			var cod = '&id='+statusId.val();
			var st = '&st='+status;
			<?php echo $APPTAG?>_formExecute(true, false, true); // inicia o loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=status"+cod+st,
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data) {
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							if(res.newStatus == 0) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-status').attr('title', '<?php echo JText::_('TEXT_STATUS_0')?>').removeClass().addClass('base-icon-clock text-live hasPopover');
							else if(res.newStatus == 1) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-status').attr('title', '<?php echo JText::_('TEXT_STATUS_1')?>').removeClass().addClass('base-icon-off text-live hasPopover');
							else if(res.newStatus == 2) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-status').attr('title', '<?php echo JText::_('TEXT_STATUS_2')?>').removeClass().addClass('base-icon-off text-primary hasPopover');
							else if(res.newStatus == 3) jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-status').attr('title', '<?php echo JText::_('TEXT_STATUS_3')?>').removeClass().addClass('base-icon-ok text-success hasPopover');
							jQuery('#<?php echo $APPTAG?>-item-'+res.id+'-status').data('status', res.newStatus);
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
					statusPopup.modal('hide');
					<?php echo $APPTAG?>_listReload(false, false, false);
				}
			});
			return false;
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
			if($cfg['showAddBtn'] && ($hasAdmin || $hasAuthor)) echo $addBtn;
			if($cfg['showList']) :
				if($cfg['listFull']) :
					if($hasAdmin) : ?>
						<button class="btn btn-sm btn-success <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 1)">
							<span class="base-icon-ok-circled"></span> <?php echo JText::_('TEXT_ACTIVE'); ?>
						</button>
						<button class="btn btn-sm btn-warning <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 0)">
							<span class="base-icon-cancel"></span> <?php echo JText::_('TEXT_INACTIVE'); ?>
						</button>
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

	<?php if($hasAdmin || $hasAuthor) : ?>
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

	<div class="modal fade" id="modal-status-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-status-<?php echo $APPTAG?>Label">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<?php require($PATH_APP_FILE.'.form.status.php'); ?>
			</div>
		</div>
	</div>
</div>
