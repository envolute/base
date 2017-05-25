<?php
/* CRUD SIMPLES EM AJAX PARA O SISTEMA BASE
 * AUTOR: IVO JUNIOR
 * EM: 18/01/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');

$app = JFactory::getApplication('site');

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// verifica o acesso
$hasGroup = array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
$hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos
if(!$cfg['isPublic']) :
	if($user->guest) :
		$app->redirect(JURI::root(true).'/login?return='.urlencode(base64_encode(JURI::current())));
		exit();
	elseif(!$hasGroup && !$hasAdmin) :
		$app->enqueueMessage(JText::_('MSG_NOT_PERMISSION'), 'warning');
		$app->redirect(JURI::root(true));
		exit();
	endif;
endif;

// database connect
$db = JFactory::getDbo();
?>

<script>
jQuery(function() {

	// VIEWS
	var mainForm	= jQuery('#form-<?php echo $APPTAG?>');
	window.mainForm_<?php echo $APPTAG?> = mainForm;
		// form paginator
		var fPager	= jQuery('#<?php echo $APPTAG?>-formPaginator');
		var fPrev		= jQuery('#<?php echo $APPTAG?>-prev');
		var fNext		= jQuery('#<?php echo $APPTAG?>-next');
		var fRest		= jQuery('#<?php echo $APPTAG?>-restart');
		var btnPrev	= jQuery('#btn-<?php echo $APPTAG?>-prev');
		var btnNext	= jQuery('#btn-<?php echo $APPTAG?>-next');
		var btnRest	= jQuery('#btn-<?php echo $APPTAG?>-restart');
	var popup			= jQuery('#modal-<?php echo $APPTAG?>');
	var fReload		= false;
	var list			= jQuery('#list-<?php echo $APPTAG?>');
	var listPopup	= jQuery('#modal-list-<?php echo $APPTAG?>');
		// lista completa
		var formFilter	= jQuery('#filter-<?php echo $APPTAG?>');
		var formList		= jQuery('#form-list-<?php echo $APPTAG?>');
		var formLimit		= jQuery('#form-limit-<?php echo $APPTAG?>');
		var formOrder		= jQuery('#form-order-<?php echo $APPTAG?>');
		// relacionamento
		window.<?php echo $APPTAG?>oCHL	= 0;
		window.<?php echo $APPTAG?>rNID	= '';
		window.<?php echo $APPTAG?>rID	= 0;

	// DEFAULT FIELDS
	var formId	 			= jQuery('#<?php echo $APPTAG?>-id');
	window.formId_<?php echo $APPTAG?> = formId;
	var relationId		= jQuery('#<?php echo $APPTAG?>-relationId');
	// state is default
	var state					= mainForm.find('input[name=state]:radio');
		var active			= mainForm.find('#<?php echo $APPTAG?>-state-1');
		var inactive		= mainForm.find('#<?php echo $APPTAG?>-state-0');
	// se houver upload
	<?php if($cfg['hasUpload']) :?>
		var files 			= mainForm.find("input:file");
		<?php if($cfg['dinamicFiles']) :?>
			// valor inicial do index do arquivo... considerando '0' o campo estático
			window.<?php echo $APPTAG?>IndexFile = window.<?php echo $APPTAG?>IndexFileInit = <?php echo $cfg['indexFileInit']?>;
			// container para campos dinâmicos de arquivos
			// Obs: não colocar campos 'file' estáticos dentro do container
			var filesGroup	= jQuery('#<?php echo $APPTAG?>-files-group');
		<?php endif;?>
	<?php endif;?>

	// APP FIELDS
	var title					= jQuery('#<?php echo $APPTAG?>-title');
	var description		= jQuery('#<?php echo $APPTAG?>-description');
	var coverType			= mainForm.find('input[name=coverType]:radio');
	var videoEmbed		= jQuery('#<?php echo $APPTAG?>-videoEmbed');
	var date					= jQuery('#<?php echo $APPTAG?>-date');
	var month 				= jQuery('#<?php echo $APPTAG?>-month');
	var year					= jQuery('#<?php echo $APPTAG?>-year');
	var contentType		= mainForm.find('input[name=contentType]:radio');
	var url						= jQuery('#<?php echo $APPTAG?>-url');
	var target				= jQuery('#<?php echo $APPTAG?>-target');
	var content				= jQuery('#<?php echo $APPTAG?>-content');
	var tag						= jQuery('#<?php echo $APPTAG?>-tag');
	var ordering			= jQuery('#<?php echo $APPTAG?>-ordering');
	var cordering			= jQuery('#<?php echo $APPTAG?>-cordering'); // guarda a ordem atual -> current order
	var urlList				= jQuery('#<?php echo $APPTAG?>-urlList');
	var urlContent		= jQuery('#<?php echo $APPTAG?>-urlContent');
	// CONFIG FIELDS
	var element_class						= jQuery('#<?php echo $APPTAG?>-element_class');
	var element_dateFormat			= jQuery('#<?php echo $APPTAG?>-element_dateFormat');
	var element_layout					= jQuery('#<?php echo $APPTAG?>-element_layout');
	var element_imageWidth			= jQuery('#<?php echo $APPTAG?>-element_imageWidth');
	var element_imageHeight			= jQuery('#<?php echo $APPTAG?>-element_imageHeight');
	var element_imageClass			= jQuery('#<?php echo $APPTAG?>-element_imageClass');
	var element_downloadLabel		= jQuery('#<?php echo $APPTAG?>-element_downloadLabel');
	var element_downloadClass		= jQuery('#<?php echo $APPTAG?>-element_downloadClass');
	var element_contentModal		= mainForm.find('input[name=element_contentModal]:radio');
	var element_modalHeader			= mainForm.find('input[name=element_modalHeader]:radio');
	var element_modalSize				= jQuery('#<?php echo $APPTAG?>-element_modalSize');
	var content_galleryGrid			= jQuery('#<?php echo $APPTAG?>-content_galleryGrid');
	var content_galleryCaption	= mainForm.find('input[name=content_galleryCaption]:radio');
	var content_galImageWidth		= jQuery('#<?php echo $APPTAG?>-content_galImageWidth');
	var content_galImageHeight	= jQuery('#<?php echo $APPTAG?>-content_galImageHeight');
	var content_galImageClass		= jQuery('#<?php echo $APPTAG?>-content_galImageClass');
	var content_layout					= jQuery('#<?php echo $APPTAG?>-content_layout');
	var content_imageWidth			= jQuery('#<?php echo $APPTAG?>-content_imageWidth');
	var content_imageHeight			= jQuery('#<?php echo $APPTAG?>-content_imageHeight');
	var content_imageClass			= jQuery('#<?php echo $APPTAG?>-content_imageClass');

	// PARENT FIELD -> Select
	// informe, se houver, o campo que representa a chave estrangeira principal
	var parentFieldId			= null;
	var parentFieldGroup	= (parentFieldId) ? parentFieldId.closest('[class*="col-"]') : null;

	// GROUP RELATION'S BUTTONS -> grupo de botões de relacionamentos no form
	var groupRelations		= jQuery('#<?php echo $APPTAG?>-group-relation');

	// FORM CONTROLLERS
	// métodos controladores do formulário

		// On Set Modal Open -> Ações durante a abertura do modal
		popup.on('show.bs.modal', function () {

		});

		var firstField = title; // campo que recebe o focus no carregamento
		// On Modal Open -> Ações quando o modal é aberto
		popup.on('shown.bs.modal', function () {
			// init form default values
			if(formId.val() == '') <?php echo $APPTAG?>_formReset();
			// seta o focus no carregamento do formulário
			if(firstField.length) setTimeout(function() { firstField.focus() }, 10);
		});

		// On Modal Close -> Ações quando o modal é fechado
		popup.on('hidden.bs.modal', function () {
			// limpa a validação quando o formulário é fechado
			<?php echo $APPTAG?>_clearValidation(mainForm);
			// reseta o form
			<?php echo $APPTAG?>_formReset();
			// reseta o relacionamento
			relationId.val('<?php echo $_SESSION[$RTAG.'RelId']?>');
			// reseta o parent
			if(parentFieldId != null) {
				parentFieldId.val(0).trigger("chosen:updated"); // select
				parentFieldGroup.prop('hidden', false);
			}
			<?php
			// recarrega a listagem
			if($cfg['listFull']) echo $APPTAG.'_listReload(fReload, false);';
			?>
		});

		// On List Modal Close -> Ações quando o modal da listagem é fechado
		listPopup.on('hidden.bs.modal', function () {
			<?php echo $APPTAG?>_listReload(fReload);
		});

		// Set Form Paginator -> implementa os botões de paginação do formulário
		window.<?php echo $APPTAG?>_formPaginator = function(id, prev, next) {
			if(id != 0) {
				fPager.prop('hidden', false);
				fRest.prop('hidden', false);
				if(prev != 0) {
					btnPrev.prop('disabled', false);
					fPrev.val(prev);
				} else {
					btnPrev.prop('disabled', true);
					fPrev.val('');
				}
				if(next != 0) {
					btnNext.prop('disabled', false);
					fNext.val(next);
				} else {
					btnNext.prop('disabled', true);
					fNext.val('');
				}
			} else {
				fPager.prop('hidden', true);
				fRest.prop('hidden', true);
				fPrev.val('');
				fNext.val('');
			}
		};
		btnPrev.click(function() { <?php echo $APPTAG?>_loadEditFields(fPrev.val(), true, true) });
		btnNext.click(function() { <?php echo $APPTAG?>_loadEditFields(fNext.val(), true, true) });
		btnRest.click(function() { <?php echo $APPTAG?>_loadEditFields(formId.val(), true, true) });

		// Set State Action in Form -> seta o valor do campo 'state'
		active.parent('label.btn').off('click').on('click', function() { if(formId.val() != 0) <?php echo $APPTAG?>_setState(formId.val(), 1) });
		inactive.parent('label.btn').off('click').on('click', function() { if(formId.val() != 0) <?php echo $APPTAG?>_setState(formId.val(), 0) });

		// Reset -> Reseta o form e limpa as mensagens de validação
		window.<?php echo $APPTAG?>_formExecute = function(loader, disabled, e) {
			// mostra/esconde o loader
			if(loader) toggleLoader();
			// habilita/desabilita o form
			if(disabled) mainForm.find('fieldset').prop('disabled', e);
	 	};

		// Reset -> Reseta o form e limpa as mensagens de validação
		window.<?php echo $APPTAG?>_formReset = function() {
			// Default Fields
			formId.val('');
			// se houver upload
			<?php if($cfg['hasUpload']) :?>
				<?php echo $APPTAG?>_resetFiles(files);
			<?php endif;?>

			// hidden relation's buttons
			if(groupRelations.length) groupRelations.prop('hidden', true);

			// App Fields
			// IMPORTANTE:
			// => SE HOUVER UM CAMPO INDICADO NA VARIÁVEL 'parentFieldId', NÃO RESETÁ-LO NA LISTA ABAIXO
			title.val('');
			description.val('');
			checkOption(coverType, <?php echo $_SESSION[$RTAG.'CoverTypeDef']?>);
			videoEmbed.val('');
			date.val('');
			month.val('0').trigger("chosen:updated"); // select
			year.val('<?php echo date('Y')?>');
			checkOption(contentType, <?php echo $_SESSION[$RTAG.'ContentTypeDef']?>);
			url.val('');
			target.prop('checked', <?php echo ($_SESSION[$RTAG.'TargetDef'] ? 'true' : 'false')?>);
			content.val('');
			tag.val('<?php echo $APPTAG?>');
			ordering.val('0');
			cordering.val('0');
			urlList.val('<?php echo $_SESSION[$RTAG.'UrlListView']?>');
			urlContent.val('');
			element_class.val('');
			element_dateFormat.val('');
			element_layout.val('');
			element_imageWidth.val('');
			element_imageHeight.val('');
			element_imageClass.val('');
			element_downloadLabel.val('');
			element_downloadClass.val('');
			checkOption(element_contentModal, '');
			checkOption(element_modalHeader, '');
			element_modalSize.val('').trigger("chosen:updated"); // select
			content_galleryGrid.val('');
			content_galImageWidth.val('');
			content_galImageHeight.val('');
			content_galImageClass.val('');
			checkOption(content_galleryCaption, 0);
			content_layout.val('');
			content_imageWidth.val('');
			content_imageHeight.val('');
			content_imageClass.val('');

			<?php // set content in html editor
			if($cfg['htmlEditor']) echo 'setContentEditor();';
			?>

			// state -> radio: default = 1
			checkOption(state, 1);
			// limpa as classes e mensagens de validação caso sejam setadas...
			<?php echo $APPTAG?>_clearValidation(mainForm);
			// remove a paginação do form
			<?php echo $APPTAG?>_formPaginator(0);

			// esconde as mensagens de erro e sucesso 'set-success, set-error'
			// esconde botão Salvar & Novo e deletar 'btn-FORM-save-new, btn-FORM-delete'
			mainForm.find('.set-success, .set-error, #btn-<?php echo $APPTAG?>-delete').prop('hidden', true);

		};

		<?php if($cfg['hasUpload']) :?>
			// Load Files -> Carrega os campos de arquivos dinâmicos
			window.<?php echo $APPTAG?>_loadFiles = function(files) {
				var obj;
				var html = path = '';
				var root = '<?php echo JURI::root(true)?>';
				var len = (files.length > 0) ? parseInt(files[(files.length - 1)]['index']) : 0; // ultimo 'index'
				var f = Array();
				for(a = 0; (a < (len + 1) && files.length > 0); a++) { // len + 1, pois conta com o zero!
					<?php
					// load dinamic files
					if($cfg['dinamicFiles']) echo 'if(a >= ('.$APPTAG.'IndexFileInit - 1) && a < len) '.$APPTAG.'_setNewFile();';
					?>
					obj = jQuery('input:file[name="file['+a+']"]');
					// define a sequencia dos itens
					for(i = 0; i < files.length; i++) {
						if(files[i]['index'] == a) {
							desc = files[i]['filename']+'<br />'+(parseFloat(Math.round(files[i]['filesize'] / 1024)).toFixed(2))+'kb';
							// gera os links
							if(files[i]['mimetype'].indexOf('image') == -1) {
								path = root + '/get-file?fn='+files[i]['fn']+'&mt='+files[i]['mt']+'&tag=<?php echo base64_encode($APPTAG)?>';
								html += '	<a href="'+path+'" class="base-icon-attach btn btn-default hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_DOWNLOAD'); ?><br />'+desc+'"></a>';
							} else {
								path = root + '/images/apps/<?php echo $APPNAME?>/'+files[i]['filename'];
								html += '	<a href="#" class="base-icon-eye btn btn-default hasTooltip" data-animation="false" title="<img src=\''+path+'\' style=\'width:100px;max-height:100px\' /><br />'+desc+'"></a>';
							}
							if(!obj.hasClass('required')) { // se for um campo obrigatório não permite a exclusão
								html += '	<a href="#" class="base-icon-cancel btn btn-danger hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_DELETE').' '.JText::_('TEXT_FILE'); ?>" onclick="<?php echo $APPTAG?>_delFile(this, \''+files[i]['filename']+'\')"></a>';
							}
							// atribui os 'botões' ao elemento
							obj.prev('.btn-group').append(html);
						}
					}
					html = path = '';
				}

				setCoreDefinitions(); // core
				btnFileActive();
			};

			// Reset Files -> Reseta os campos 'file'
			window.<?php echo $APPTAG?>_resetFiles = function(inputFiles, single) {
				if(inputFiles.length) {
					var s = (typeof single !== "null" && typeof single !== "undefined") ? single : false;
					<?php
					// remove dinamic files
					if($cfg['dinamicFiles']) :
						echo '
						if(!s) {
							'.$APPTAG.'IndexFile = '.$APPTAG.'IndexFileInit;
							filesGroup.empty();
						}
						';
					endif;
					?>
					// reset selected button
					inputFiles.val('').prev('.btn-group').find('.file-action').removeClass('btn-success').addClass('btn-default');
					// remove file info/action buttons
					inputFiles.val('').prev('.btn-group').find('a').remove();
				}
		 	};

			// SET NEW FILE -> Gera um novo campo para envio de arquivo
			window.<?php echo $APPTAG?>_setNewFile = function() {
				var fileField = '';
				fileField += '<div class="col-6 col-sm-3">';
				fileField += '	<div class="form-group">';
				fileField += '		<span class="btn-group">';
				fileField += '			<button type="button" class="base-icon-search btn btn-default file-action"></button>';
				fileField += '		</span>';
				fileField += '		<input type="file" name="<?php echo $cfg['fileField']?>['+<?php echo $APPTAG?>IndexFile+']" id="<?php echo $APPTAG?>-<?php echo $cfg['fileField']?>'+<?php echo $APPTAG?>IndexFile+'" class="form-control field-image" hidden />';
				fileField += '	</div>';
				fileField += '</div>';
				filesGroup.append(fileField);
				btnFileAction(); // seta a ação no botão 'serch file'
				btnFileActive(); // seta o botão 'ativo' quando o arquivo é selecionado
				<?php echo $APPTAG?>IndexFile++;
			};
		<?php endif;?>

		// Clear Validation ->  limpa os erros de validação
		window.<?php echo $APPTAG?>_clearValidation = function(formElement){
			//Iterate through named elements inside of the form, and mark them as error free
			formElement.find('input, select, textarea').each(function(){
				jQuery(this).removeClass('error'); //remove as error from fields
				<?php echo $APPTAG?>_validator.successList.push(this); //mark as error free
				<?php echo $APPTAG?>_validator.showErrors(); //remove error messages if present
			});
			<?php echo $APPTAG?>_validator.resetForm(); //remove error class on name elements and clear history
			<?php echo $APPTAG?>_validator.reset(); //remove all error and success data
		}

		// SET RELATION -> Atribui valor ao ID de relacionamento
		window.<?php echo $APPTAG?>_setRelation = function(id) {
			if(typeof id !== "null" && typeof id !== "undefined" && id != 0) {
				relationId.val(id);
				btnPrev.remove();
				btnNext.remove();
			}
		};

		// SET PARENT -> seta o valor do elemento pai (foreign key) do relacionamento
		window.<?php echo $APPTAG?>_setParent = function(id) {
			if(typeof id !== "null" && typeof id !== "undefined" && id != 0) {
				if(parentFieldId != null) {
					parentFieldId.val(id).trigger("chosen:updated"); // selects
					parentFieldId.trigger('change');
					// hide 'parentFieldId'
					if(parentFieldGroup && <?php echo $_SESSION[$RTAG.'HideParentField']?> && parentFieldId.find('option[value="'+id+'"]').length) {
						parentFieldGroup.prop('hidden', true);
					}
				}
				btnPrev.remove();
				btnNext.remove();
			}
		};

	// LIST CONTROLLERS
	// ações & métodos controladores da listagem

		// Check all -> seleciona todas as linhas (checkboxes) da listagem
		var chk = jQuery('#<?php echo $APPTAG?>_checkAll');
		chk.click(function() {
			var checked = (jQuery(this).is(':checked')) ? true : false;
			jQuery('.<?php echo $APPTAG?>-chk').each(function() {
				jQuery(this).prop("checked", checked);
			});
			<?php echo $APPTAG?>_setBtnStatus();
		});
		// desmarca checkAll caso um checkbox da lista seja alterado individualmente
		jQuery('.<?php echo $APPTAG?>-chk').click(function() {
			chk.prop("checked", false);
			<?php echo $APPTAG?>_setBtnStatus();
		});

		// Set Btn status
		// habilita/desabilita os botões de ação se houver, ou não, checkboxes marcados na Listagem
		window.<?php echo $APPTAG?>_setBtnStatus = function() {
			var disable = true;
			var btn = jQuery('.<?php echo $APPTAG?>-btn-action');
			jQuery('.<?php echo $APPTAG?>-chk').each(function() {
				if(jQuery(this).is(':checked')) disable = false;
			});
			btn.prop('disabled', disable);
		};

		// Set Filter -> submit o filtro no evento 'onchange'
		// usado em campos do tipo 'select'
		formFilter.find('.set-filter').change(function() {
			setTimeout(function() { formFilter.submit() }, 100);
		});

		// On Submit Filter
		// usado em campos do tipo 'select'
		formFilter.on('submit', function() {
			<?php // conversão de data e preço para inclusão no banco
			if($cfg['dateConvert']) echo 'dateConvert();';
			if($cfg['priceDecimal']) echo 'priceDecimal();';
			if($cfg['htmlEditor']) echo 'getContentEditor();';
			?>
		});

		// Set Order
		// define a ordem na listagem
		window.<?php echo $APPTAG?>_setListOrder = function(col, type) {
			if(col) {
				formOrder.find('input#<?php echo $APPTAG?>oF').val(col);
				formOrder.find('input#<?php echo $APPTAG?>oT').val(type);
			}
			formOrder.submit();
		};

		// Set Limit
		// define o limite de itens na listagem
		window.<?php echo $APPTAG?>_setListLimit = function() {
			formLimit.submit();
		};

	// AJAX CONTROLLERS
	// métodos controladores das ações referente ao banco de dados e envio de arquivos

		// List Reload -> (Re)carrega a listagem AJAX dos dados
		window.<?php echo $APPTAG?>_listReload = function(reload, remove, ids, onlyChilds, relNameId, relId) {
			<?php if(!$cfg['showList']) echo 'return;' ?>
			<?php if($cfg['listFull']) : ?>
				if(reload) {
					<?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '' ?>
					location.href = '<?php echo JURI::current().$qs?>';
				} else if(remove && ids.length > 0) {
					for(i = 0; i < ids.length; i++) {
						jQuery('#<?php echo $APPTAG?>-item-'+ids[i]).remove();
					}
				}
				fReload = false;
				return;
			<?php else : ?>
				// inicia o loader
				list.find('.ajax-loader').prop('hidden', false);
				// relation
				<?php echo $APPTAG?>oCHL = (typeof onlyChilds !== "null" && typeof onlyChilds !== "undefined" && onlyChilds == true) ? 1 : 0;
				<?php echo $APPTAG?>rNID = (typeof relNameId !== "null" && typeof relNameId !== "undefined") ? relNameId : '';
				<?php echo $APPTAG?>rID = (typeof relId !== "null" && typeof relId !== "undefined" && relId !== 0) ? relId : 0;
				<?php if(!empty($_SESSION[$RTAG.'RelTable'])) echo $APPTAG.'_setRelation('.$APPTAG.'rID);'; ?>
				jQuery.ajax({
					url: "<?php echo $URL_APP_FILE ?>.list.ajax.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&oCHL="+<?php echo $APPTAG?>oCHL+"&rNID="+<?php echo $APPTAG?>rNID+"&rID="+<?php echo $APPTAG?>rID,
					type: 'POST',
					cache: false,
					success: function(data) {
						// encerra o loader
						list.find('.ajax-loader').prop('hidden', true);
						// load content
						list.html(data);
					},
					error: function(xhr, status, error) {
						console.log(xhr);
						console.log(status);
						console.log(error);
						// encerra o loader
						list.find('.ajax-loader').prop('hidden', true);
					},
					complete: function() {
						// Reload Javascript Base
						// como o ajax carrega 'novos elementos'
						// é necessário recarrega o DOM para atribuir o JS default à esses elementos
						setCoreDefinitions(); // core
						setCustomDefinitions(); // custom
						if(<?php echo $_SESSION[$RTAG.'Enable_slide']?> && typeof <?php echo $pageItensAppTag.${$pageItensAppTag.'RelTag'}?>BxSlider !== 'undefined') {
							<?php echo $pageItensAppTag.${$pageItensAppTag.'RelTag'}?>BxSlider.destroySlider();
							<?php echo $pageItensAppTag.${$pageItensAppTag.'RelTag'}?>Slider();
						}
					}
				});
			<?php endif; ?>
		};
		<?php
		// init list
		if(!$cfg['listFull']) echo $APPTAG.'_listReload(false, false);';
		?>

		// Load Edit Data -> Prepara o formulário para a edição dos dados
		window.<?php echo $APPTAG?>_loadEditFields = function(appID, reload, formDisable) {
			var id = (appID ? appID : formId.val());
			<?php echo $APPTAG?>_formExecute(false, formDisable, true); // inicia o loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=get&id="+id,
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data) {
					jQuery.map( data, function( item ) {
						if(!reload) popup.modal({backdrop: 'static', keyboard: false});
						<?php echo $APPTAG?>_formExecute(false, formDisable, false); // encerra o loader

						// Default Fields
						formId.val(item.id);
						// state
						checkOption(state, item.state);
						// se houver upload
						<?php if($cfg['hasUpload']) :?>
							<?php echo $APPTAG?>_resetFiles(files);
							<?php echo $APPTAG?>_loadFiles(item.files);
						<?php endif;?>

						// App Fieldstitle.val('');
						title.val(item.title);
						description.val(item.description);
						checkOption(coverType, item.coverType);
						videoEmbed.val(item.videoEmbed);
						date.val(dateFormat(item.date)); // DATE -> conversão de data
						month.val(item.month).trigger("chosen:updated"); // selects
						year.val(item.year);
						checkOption(contentType, item.contentType);
						url.val(item.url);
						target.prop('checked', (item.target == 0 ? false : true));
						content.val(item.content);
						tag.val(item.tag);
						ordering.val(item.ordering);
						cordering.val(item.ordering);
						urlList.val(item.urlList);
						urlContent.val(item.urlContent);
						element_class.val(item.element_class);
						element_dateFormat.val(item.element_dateFormat);
						element_layout.val(item.element_layout);
						element_imageWidth.val(item.element_imageWidth);
						element_imageHeight.val(item.element_imageHeight);
						element_imageClass.val(item.element_imageClass);
						element_downloadLabel.val(item.element_downloadLabel);
						element_downloadClass.val(item.element_downloadClass);
						checkOption(element_contentModal, item.element_contentModal);
						checkOption(element_modalHeader, item.element_modalHeader);
						element_modalSize.val(item.element_modalSize).trigger("chosen:updated"); // select
						content_galleryGrid.val(item.content_galleryGrid);
						content_galImageWidth.val(item.content_galImageWidth);
						content_galImageHeight.val(item.content_galImageHeight);
						content_galImageClass.val(item.content_galImageClass);
						checkOption(content_galleryCaption, item.content_galleryCaption);
						content_layout.val(item.content_layout);
						content_imageWidth.val(item.content_imageWidth);
						content_imageHeight.val(item.content_imageHeight);
						content_imageClass.val(item.content_imageClass);

						<?php // set content in html editor
						if($cfg['htmlEditor']) echo 'setContentEditor();';
						?>

						// show relation's buttons
						if(groupRelations.length) groupRelations.prop('hidden', false);

						// set form's paginator
						<?php echo $APPTAG?>_formPaginator(item.id, item.prev, item.next);
						// recarrega os scripts de formulário para os campos
						// necessário após um procedimento ajax que envolve os elementos
						setFormDefinitions();
					});
					// mostra dos botões 'salvar & novo' e 'delete'
					jQuery('#btn-<?php echo $APPTAG?>-delete').prop('hidden', false);
					// limpa as mensagens de erro de validação
					<?php echo $APPTAG?>_clearValidation(mainForm);
				},
				error: function(xhr, status, error) {
					console.log(xhr);
					console.log(status);
					console.log(error);
					<?php echo $APPTAG?>_formExecute(false, formDisable, false); // encerra o loader
				}
			});
		};

		// Save -> executa a ação de inserção ou atualização dos dados no banco
		window.<?php echo $APPTAG?>_save = function(trigger) {
			// valida o formulário antes do envio -> 'jquery validation'
			if(mainForm.valid()) {

				<?php // conversão de data e preço para inclusão no banco
				if($cfg['dateConvert']) echo 'dateConvert();';
				if($cfg['priceDecimal']) echo 'priceDecimal();';
				if($cfg['htmlEditor']) echo 'getContentEditor();';
				?>

				// pega os dados enviados pelo form
				var dados = <?php echo ($cfg['hasUpload'] ? 'new FormData(mainForm[0])' : 'mainForm.serialize()') ?>;

				// executando...
				<?php echo $APPTAG?>_formExecute(true, true, true); // inicia o loader
				mainForm.find('.set-success, .set-error').prop('hidden', true); // esconde as mensagens de 'erro' ou 'sucesso'

				jQuery.ajax({
					url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=save&id="+formId.val(),
					dataType: 'json',
					type: 'POST',
					method: "POST",
					data:  dados,
					cache: false,
					<?php if($cfg['hasUpload']): // quando houver upload ?>
						processData: false,
						contentType: false,
					<?php endif; ?>
					success: function(data){
						<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
						jQuery.map( data, function( res ) {
							if(res.status > 0) { // se alguma ação for realizada

								if(res.status == 1 || isSet(trigger)) {
									if(isSet(trigger)) {
										<?php echo $APPTAG?>_formReset();
										if(trigger == 'close') popup.modal('hide');
									} else {
										<?php echo $APPTAG?>_loadEditFields(res.regID, true, false); // recarrega os dados do form
									}
								} else { // 'atualizado'
									<?php echo $APPTAG?>_loadEditFields(formId.val(), true, false); // recarrega os dados do form
								}

								// Update Parent field
								if(res.parentField != '' && res.parentFieldVal != '') {
									// remove if option exist
									if(jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').length) jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
									// add option if is active (state = 1)
									if(res.parentFieldLabel != '' && res.parentFieldLabel != null) {
										jQuery(res.parentField).append('<option value='+res.parentFieldVal+'>'+res.parentFieldLabel+'</option>'); // add valor à lista
										jQuery(res.parentField).val(res.parentFieldVal).trigger("chosen:updated").change(); // atualiza o select
									} else {
										jQuery(res.parentField).trigger("chosen:updated");
									}
								}

								// MENSAGENS: mostra a mensagem de sucesso/erro
								mainForm.find('.set-success').prop('hidden', false).text(res.msg);
								if(res.uploadError) // mensagem de erro no envio do arquivo
								mainForm.find('.set-error').prop('hidden', false).text(res.uploadError);

								<?php
								// recarrega a página quando fechar o form para atualizar a lista
								echo ($cfg['listFull'] ? 'fReload = true;' : $APPTAG.'_listReload(false, false, false, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);');
								?>
								setTimeout(function() {
									if(firstField.length) firstField.focus(); // seta novamente o focus no primeiro campo
								}, 10);

							} else {

								// caso ocorra um erro na ação, mostra a mensagem de erro
								mainForm.find('.set-error').prop('hidden', false).text(res.msg);
								// recarrega os scripts de formulário para os campos
								// necessário após um procedimento ajax que envolve os elementos
								setFormDefinitions();

							}
						});
					},
					error: function(xhr, status, error) {
						console.log(xhr);
						console.log(status);
						console.log(error);
						<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					}
				});
			}
		};

		// Set State
		// seta o valor do campo 'state' do registro
		window.<?php echo $APPTAG?>_setState = function(itemID, state) {

			var dados = cod = st = e = '';
			var msg = '<?php echo JText::_('MSG_LIST0CONFIRM'); ?>';
			if(state === 1) msg = '<?php echo JText::_('MSG_LIST1CONFIRM'); ?>';
			if(typeof state !== "null" && typeof state !== "undefined") st = '&st='+state;
			if(itemID) {
				cod = '&id='+itemID;
			} else {
				if(!confirm(msg)) return false;
				dados = formList.serialize();
			}

			<?php echo $APPTAG?>_formExecute(true, true, true); // inicia o loader

			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=state"+cod+st,
				dataType: 'json',
				type: 'POST',
				data:  dados,
				cache: false,
				success: function(data){
					<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					jQuery.map( data, function( res ) {
						if(res.status == 4) {
							for(i = 0; i < res.ids.length; i++) {
								e = list.find('#<?php echo $APPTAG?>-state-'+res.ids[i]+' > span');
								if((res.state == 2 && e.hasClass('base-icon-ok')) || res.state == 0) {
										e.removeClass('base-icon-ok text-success').addClass('base-icon-cancel text-danger');
										<?php if($cfg['listFull']) echo 'e.closest(".pageItens-item").addClass("bg-danger");'?>
										// remove parent field option
										if(res.parentField != '' && res.parentFieldVal != '') {
											jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
											jQuery(res.parentField).trigger("chosen:updated").change(); // atualiza o select
										}
								} else {
									e.removeClass('base-icon-cancel text-danger').addClass('base-icon-ok text-success');
									<?php if($cfg['listFull']) echo 'e.closest(".pageItens-item").removeClass("bg-danger");'?>
									// add parent field option
									if(res.parentField != '' && res.parentFieldVal != '') {
										jQuery(res.parentField).append('<option value='+res.parentFieldVal+'>'+res.parentFieldLabel+'</option>');
										jQuery(res.parentField).val(res.parentFieldVal).trigger("chosen:updated").change(); // atualiza o select
									}
								}
							}
							<?php if(!$cfg['listFull']) echo $APPTAG.'_listReload(false, false, false, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);'; ?>
						} else {
							if(!itemID) mainForm.find('.set-error').prop('hidden', false).text(res.msg);
						}
					});
				},
				error: function(xhr, status, error) {
					console.log(xhr);
					console.log(status);
					console.log(error);
					<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
				},
				complete: function() {
					hideTips(); // force tooltip close
				}
			});
			return false;
		};

		// Deleta -> Exclui o registro
		// OBS: essa função não precisa de alteração
		window.<?php echo $APPTAG?>_del = function(itemID, isForm) {
			var msg = (itemID) ? '<?php echo JText::_('MSG_DELCONFIRM'); ?>' : '<?php echo JText::_('MSG_LISTDELCONFIRM'); ?>';
			if(confirm(msg)) {
				var dados = cod = '';
				if(itemID || (isForm && formId.val() != '')) {
					cod = '&id=' + (itemID ? itemID : formId.val());
					if(isForm) { // delete action from form
						<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
						mainForm.find('.set-success, .set-error').prop('hidden', true);
					}
				} else {
					dados = formList.serialize();
				}
				jQuery.ajax({
					url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=del"+cod,
					dataType: 'json',
					type: 'POST',
					data:  dados,
					cache: false,
					success: function(data){
						if(isForm) <?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
						jQuery.map( data, function( res ) {
							if(res.status == 3) {
								if(!itemID) {
									<?php echo $APPTAG?>_formReset();
									if(isForm) {
										// MENSAGENS: mostra a mensagem de sucesso/erro
										mainForm.find('.set-success').prop('hidden', false).text(res.msg);
										if(res.uploadError) // mensagem de erro no envio do arquivo
										mainForm.find('.set-error').prop('hidden', false).text(res.uploadError);
									}
								}
								// remove parent field option
								if(res.parentField != '' && res.parentFieldVal != '') {
									jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
									jQuery(res.parentField).trigger("chosen:updated").change(); // atualiza o select
								}
								<?php echo $APPTAG?>_listReload(false, true, res.ids, <?php echo $APPTAG?>oCHL, <?php echo $APPTAG?>rNID, <?php echo $APPTAG?>rID);
							} else {
								if(!itemID) mainForm.find('.set-error').prop('hidden', false).text(res.msg);
							}
						});
					},
					error: function(xhr, status, error) {
						console.log(xhr);
						console.log(status);
						console.log(error);
						<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
					}
				});
			}
			return false;
		};

		<?php if($cfg['hasUpload']) :?>
			// Deleta o Arquivo -> exclui o registro e deleta o arquivo
			// OBS: essa função não precisa de alteração
			window.<?php echo $APPTAG?>_delFile = function(btn, fileName, itemID) {
				if(confirm('<?php echo JText::_('MSG_FILE_DELCONFIRM'); ?>')) {
					var cod = fname = '';
					cod 	= '&id=' + formId.val();
					fname	= '&fname=' + fileName;
					<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
					mainForm.find('.set-success, .set-error').prop('hidden', true);
					jQuery.ajax({
						url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=delFile"+cod+fname,
						dataType: 'json',
						cache: false,
						success: function(data){
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
							jQuery.map( data, function( res ) {
								if(res.status == 5) {
									// remove as informações do arquivo no campo
									<?php echo $APPTAG?>_resetFiles(jQuery(btn).closest('.btn-group').next('input:file'), true);

									// MENSAGENS: mostra a mensagem de sucesso/erro
									mainForm.find('.set-success').prop('hidden', false).text(res.msg);
									if(res.uploadError) // mensagem de erro no envio do arquivo
									mainForm.find('.set-error').prop('hidden', false).text(res.uploadError);

									<?php
									// recarrega a página quando fechar o form para atualizar a lista
									echo ($cfg['listFull'] ? 'fReload = true;' : $APPTAG.'_listReload(false, false, false, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);');
									?>
								} else {
									mainForm.find('.set-error').prop('hidden', false).text(res.msg);
								}
							});
						},
						error: function(xhr, status, error) {
							console.log(xhr);
							console.log(status);
							console.log(error);
							<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
						}
					});
				}
				return false;
			};
		<?php endif;?>

}); // CLOSE JQUERY->READY

jQuery(window).load(function() {
	// Jquery Validation
	window.<?php echo $APPTAG?>_validator = mainForm_<?php echo $APPTAG?>.validate({
		rules: {
			// rules
		},
		//don't remove this
		invalidHandler: function(event, validator) {
			//if there is error,
			//set custom preferences
		},
		submitHandler: function(form){
			return false;
		}
	});
	<?php if($cfg['hasUpload']): ?>
		mainForm_<?php echo $APPTAG?>.find("input:file").each(function() {
			var obj = jQuery(this);
			if(obj.hasClass('field-image')) { // apenas imagens
				obj.rules("add", {
					required: function(element) {
						// só é obrigatório se o ID não for informado, ou seja, um novo item
						return (obj.hasClass('input-required') && formId_<?php echo $APPTAG?>.val() == '') ? true : false;
					},
					accept: "<?php echo implode(',', $cfg['fileTypes']['image'])?>",
					messages: {
						required: "<?php echo JText::_('FIELD_REQUIRED')?>",
						accept:"<?php echo JText::_('MSG_FILETYPE')?>"
					}
				});
			} else if(obj.hasClass('field-file')) { // não permite images
				obj.rules("add", {
					required: function(element) {
						// só é obrigatório se o ID não for informado, ou seja, um novo item
						return (obj.hasClass('input-required') && formId_<?php echo $APPTAG?>.val() == '') ? true : false;
					},
					accept: "<?php echo implode(',', $cfg['fileTypes']['file'])?>",
					messages: {
						required: "<?php echo JText::_('FIELD_REQUIRED')?>",
						accept:"<?php echo JText::_('MSG_FILETYPE')?>"
					}
				});
			} else { // todos os tipos permitidos
				obj.rules("add", {
					required: function(element) {
						// só é obrigatório se o ID não for informado, ou seja, um novo item
						return (obj.hasClass('input-required') && formId_<?php echo $APPTAG?>.val() == '') ? true : false;
					},
					accept: "<?php echo implode(',', array_merge($cfg['fileTypes']['image'], $cfg['fileTypes']['file']))?>",
					messages: {
						required: "<?php echo JText::_('FIELD_REQUIRED')?>",
						accept:"<?php echo JText::_('MSG_FILETYPE')?>"
					}
				});
			}
		});
	<?php endif; ?>

});

</script>

<div class="base-app<?php echo ' base-list-'.($cfg['listFull'] ? 'full' : 'ajax')?> clearfix">
	<?php
	$addText = $cfg['showList'] ? JText::_('TEXT_ADD') : JText::_('TEXT_ADD_UNLIST');
	$tipText = $cfg['addText'] ? '' : $addText;
	$relAdd	= !empty($_SESSION[$RTAG.'RelTable']) ? $APPTAG.'_setRelation('.$APPTAG.'rID);' : $APPTAG.'_setParent('.$APPTAG.'rID);';
	$addBtn = '
		<button class="base-icon-plus btn-add btn btn-sm btn-success hasTooltip" data-animation="false" title="'.$tipText.'" onclick="'.$relAdd.'" data-toggle="modal" data-target="#modal-'.$APPTAG.'" data-backdrop="static" data-keyboard="false">
			'.($cfg['addText'] ? '<span class="text-add">'.$addText.'</span>': '').'
		</button>
	';
	$admBtn = (!$cfg['listFull'] && !empty($_SESSION[$RTAG.'UrlListAdmin'])) ? '<a href="'.$_SESSION[$RTAG.'UrlListAdmin'].'" class="base-icon-cog btn btn-sm btn-warning hasTooltip" data-animation="false" title="'.JText::_('TEXT_MANAGE').'"></a>' : '';
	?>
	<?php if($cfg['showApp'] && !$cfg['itemContent'] && (($cfg['showAddBtn'] && $hasAdmin) || ($cfg['showList'] && $cfg['listFull']))) :?>
		<div class="list-toolbar<?php echo ($cfg['staticToolbar'] ? '' : ' floating')?> hidden-print">
			<?php
			if($cfg['showAddBtn'] && $hasAdmin) echo $addBtn.$admBtn;
			if($cfg['showList'] && $cfg['listFull']) :
			?>
				<?php if($hasAdmin) : ?>
					<button class="btn btn-sm btn-success <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 1)">
						<span class="base-icon-ok-circled"></span> <?php echo JText::_('TEXT_ACTIVE'); ?>
					</button>
					<button class="btn btn-sm btn-warning <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 0)">
						<span class="base-icon-cancel"></span> <?php echo JText::_('TEXT_INACTIVE'); ?>
					</button>
					<button class="btn btn-sm btn-danger <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_del(0)">
						<span class="base-icon-trash"></span> <?php echo JText::_('TEXT_DELETE'); ?>
					</button>
				<?php endif; ?>
				<button class="btn btn-sm btn-default <?php echo ((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? 'active' : '')?>" onclick="toggleFieldsetEmbed(this, '#filter-<?php echo $APPTAG?> .fieldset-embed')">
					<span class="base-icon-filter"></span> <?php echo JText::_('TEXT_FILTER'); ?> <span class="base-icon-sort"></span>
				</button>
			<?php endif; ?>
		</div>
	<?php endif; // showApp ?>

	<?php
	// Content Type
	if($cfg['itemContent']) :

		$itemContent = require($PATH_APP_FILE.'.content.php');
		echo '<div id="item-content-'.$APPTAG.'" class="base-app-content">'.$itemContent.'</div>';

	else :

		$list = '';
		if($cfg['showList']) :
			$listContent = ($cfg['listFull']) ? require($PATH_APP_FILE.'.list.php') : '';
			if($cfg['showListDesc']) $list .= '<div class="base-list-description">'.JText::_('LIST_DESCRIPTION').'</div>';
			$sliderClass = $_SESSION[$RTAG.'Slider_RemoveContainer'] ? ' no-border' : '';
			$sliderClass .= $_SESSION[$RTAG.'Slider_fullScreen'] ? ' slider-fullScreen' : '';
			$list .= '<div id="list-'.$APPTAG.'" class="base-app-list'.$sliderClass.'">'.$listContent.'</div>';
		endif; // end noList

		if($cfg['listModal']) :
			if($cfg['showAddBtn'] && !$cfg['showApp']) $addBtn = '<div class="modal-list-toolbar">'.$addBtn.'</div>';
			echo '
				<div class="modal fade" id="modal-list-'.$APPTAG.'" tabindex="-1" role="dialog" aria-labelledby="modal-list-'.$APPTAG.'Label">
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">'.JText::_('LIST_TITLE').'</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body">
							'.$addBtn.$list.'
							</div>
						</div>
					</div>
				</div>
			';
		else :
			if($cfg['showApp']) echo $list;
		endif; // end list modal

	endif; // end item content
	?>
	<?php if($hasAdmin) : ?>
		<div class="modal fade" id="modal-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>Label">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<?php
					if($newInstance) require($PATH_APP_FILE.'.form.php');
					else require_once($PATH_APP_FILE.'.form.php');
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
