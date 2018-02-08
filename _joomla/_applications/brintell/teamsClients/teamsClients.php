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
	var type				= mainForm.find('input[name=type]:radio'); // radio group
	var user_id				= jQuery('#<?php echo $APPTAG?>-user_id');
	var name 				= jQuery('#<?php echo $APPTAG?>-name');
	var nickname			= jQuery('#<?php echo $APPTAG?>-nickname');
	var email				= jQuery('#<?php echo $APPTAG?>-email');
	var cmail				= jQuery('#<?php echo $APPTAG?>-cmail');
	var gender 				= mainForm.find('input[name=gender]:radio'); // radio group
	var birthday			= jQuery('#<?php echo $APPTAG?>-birthday');
	// phones
	var phone				= jQuery('#<?php echo $APPTAG?>-phone');
	var wapp				= jQuery('#<?php echo $APPTAG?>-wapp');
	var whatsapp			= jQuery('#<?php echo $APPTAG?>-whatsapp');
	var phone_desc			= jQuery('#<?php echo $APPTAG?>-phone_desc');
	var weblink_text		= jQuery('#<?php echo $APPTAG?>-weblink_text');
	var weblink_url			= jQuery('#<?php echo $APPTAG?>-weblink_url');
	var chat_name			= jQuery('#<?php echo $APPTAG?>-chat_name');
	var chat_user			= jQuery('#<?php echo $APPTAG?>-chat_user');
	var chat_user			= jQuery('#<?php echo $APPTAG?>-chat_user');
	// Joomla Registration
	var access				= mainForm.find('input[name=access]:radio'); // radio group
	var newUser				= jQuery('#<?php echo $APPTAG?>-newUser');
	var usergroup 			= jQuery('#<?php echo $APPTAG?>-usergroup');
	var username 			= jQuery('#<?php echo $APPTAG?>-username');
	var password			= jQuery('#<?php echo $APPTAG?>-password');
	var repassword			= jQuery('#<?php echo $APPTAG?>-repassword');
	var emailConfirm		= jQuery('#<?php echo $APPTAG?>-emailConfirm');
	var emailInfo			= jQuery('#<?php echo $APPTAG?>-emailInfo');
	var reasonStatus		= jQuery('#<?php echo $APPTAG?>-reasonStatus');
	// Client Integration
	var role				= jQuery('#<?php echo $APPTAG?>-role');

	// PARENT FIELD
	// informe, se houver, o campo que representa a chave estrangeira principal
	var parentFieldId		= null; // 'null', caso não exista...
	var parentFieldGroup	= elementExist(parentFieldId) ? parentFieldId.closest('[class*="col"]') : null;

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
			type.val(2);
			client_id.val(<?php echo $cfg['clientID']?>);
			user_id.val('');
			name.val('');
			nickname.val('');
			email.val('');
			cmail.val('');
			checkOption(gender, ''); // radio
			birthday.val('');
			phone.phoneMaskUpdate('');
			checkOption(wapp, 0); // checkbox
			whatsapp.val('');
			phone_desc.val('');
			chat_name.val('');
			chat_user.val('');
			weblink_text.val('');
			weblink_url.val('');
			usergroup.selectUpdate(0); // select
			checkOption(access, 0);
			reasonStatus.val('');
			role.val('');

			// CUSTOM -> Remove new fields
			jQuery('.newFieldsGroup').empty();

			// hide relations buttons
			setHidden('#<?php echo $APPTAG?>-buttons-relations', true, '#<?php echo $APPTAG?>-msg-relations');

			<?php // Closure Actions
			require(JPATH_CORE.DS.'apps/snippets/form/formReset.end.js.php');
			?>

		};

		// CUSTOM -> Reset Registration Fields
		window.<?php echo $APPTAG?>_accessForm = function(val) {
			var isUser = (user_id.val() == 0) ? false : true;
			newUser.selectUpdate(0); // select
			username.val('');
			password.val('');
			repassword.val('');
			emailInfo.val('');
			var mailConfirm = 0;
			checkOption(emailConfirm, mailConfirm);
			jQuery('#accessFields').collapse((val ? 'show' : 'hide'));
			jQuery('#<?php echo $APPTAG?>-reasonStatus-group').collapse((!val ? 'show' : 'hide'));
			setHidden('.new-user-data', (val && isUser), '.edit-user-data');
			setHidden('.<?php echo $APPTAG?>-no-user', isUser, '.<?php echo $APPTAG?>-is-user');
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
			formGroup += '		<div class="col-sm-6 col-lg-4">';
			formGroup += '			<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone'+<?php echo $APPTAG?>PhoneIndex+'" value="'+p+'" class="form-control field-phone" data-toggle-mask="true" />';
			formGroup += '		</div>';
			formGroup += '		<div class="col-sm-6 col-lg-8">';
			formGroup += '			<div class="input-group">';
			formGroup += '				<span class="input-group-btn btn-group" data-toggle="buttons">';
			formGroup += '					<label class="btn btn-outline-success btn-active-success'+wState+' hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">';
			formGroup += '						<input type="checkbox" name="wapp[]" value="1"'+wCheck+' class="auto-tab" data-target="#<?php echo $APPTAG?>-whatsapp'+<?php echo $APPTAG?>PhoneIndex+'" data-target-value="1" data-target-value-reset="" data-tab-disabled="true" />';
			formGroup += '						<span class="base-icon-whatsapp icon-default"></span>';
			formGroup += '						<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp'+<?php echo $APPTAG?>PhoneIndex+'" value="'+w+'" />';
			formGroup += '					</label>';
			formGroup += '				</span>';
			formGroup += '				<input type="text" name="phone_desc[]" id="<?php echo $APPTAG?>-phone_desc'+<?php echo $APPTAG?>PhoneIndex+'" value="'+d+'" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" maxlength="50" />';
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

		// CHAT ADD -> Adiciona novo campo de chat
		window.<?php echo $APPTAG?>ChatIndex = 1;
		window.<?php echo $APPTAG?>_chatAdd = function(chatName, chatUser) {
			<?php echo $APPTAG?>ChatIndex++;
			var name = (isSet(chatName) && !isEmpty(chatName)) ? chatName : '';
			var user = (isSet(chatUser) && !isEmpty(chatUser)) ? chatUser : '';
			var formGroup = '';
			formGroup += '<div id="<?php echo $APPTAG?>-newChatGroup'+<?php echo $APPTAG?>ChatIndex+'" class="form-group">';
			formGroup += '	<div class="row">';
			formGroup += '		<div class="col-sm-4">';
			formGroup += '			<input type="text" name="chat_name[]" id="<?php echo $APPTAG?>-chat_name'+<?php echo $APPTAG?>ChatIndex+'" value="'+name+'" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_CHAT_NAME'); ?>" />';
			formGroup += '		</div>';
			formGroup += '		<div class="col-sm-8">';
			formGroup += '			<div class="input-group">';
			formGroup += '				<input type="text" name="chat_user[]" id="<?php echo $APPTAG?>-chat_user'+<?php echo $APPTAG?>ChatIndex+'" value="'+user+'" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_CHAT_USER'); ?>" />';
			formGroup += '				<span class="input-group-btn">';
			formGroup += '					<button type="button" class="btn btn-danger base-icon-cancel" onclick="<?php echo $APPTAG?>_chatRemove(\'#<?php echo $APPTAG?>-newChatGroup'+<?php echo $APPTAG?>ChatIndex+'\')"></button>';
			formGroup += '				</span>';
			formGroup += '			</div>';
			formGroup += '		</div>';
			formGroup += '	</div>';
			formGroup += '</div>';
			jQuery('#<?php echo $APPTAG?>-chatGroups').append(formGroup);
		}
		// CHAT REMOVE -> Remove campo de chat
		window.<?php echo $APPTAG?>_chatRemove = function(id) {
			if(confirm('<?php echo JText::_('MSG_CONFIRM_REMOVE_CHAT')?>')) jQuery(id).remove();
		}

		// WEBLINK ADD -> Adiciona novo campo de weblink
		window.<?php echo $APPTAG?>LinkIndex = 1;
		window.<?php echo $APPTAG?>_linkAdd = function(text, urlPath) {
			<?php echo $APPTAG?>LinkIndex++;
			var txt = (isSet(text) && !isEmpty(text)) ? text : '';
			var url = (isSet(urlPath) && !isEmpty(urlPath)) ? urlPath : '';
			var formGroup = '';
			formGroup += '<div id="<?php echo $APPTAG?>-newLinkGroup'+<?php echo $APPTAG?>LinkIndex+'" class="form-group">';
			formGroup += '	<div class="row">';
			formGroup += '		<div class="col-sm-4">';
			formGroup += '			<input type="text" name="weblink_text[]" id="<?php echo $APPTAG?>-weblink_text'+<?php echo $APPTAG?>LinkIndex+'" value="'+txt+'" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_WEBLINK_TEXT'); ?>" />';
			formGroup += '		</div>';
			formGroup += '		<div class="col-sm-8">';
			formGroup += '			<div class="input-group">';
			formGroup += '				<input type="text" name="weblink_url[]" id="<?php echo $APPTAG?>-weblink_url'+<?php echo $APPTAG?>LinkIndex+'" value="'+url+'" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_WEBLINK_URL'); ?>" />';
			formGroup += '				<span class="input-group-btn">';
			formGroup += '					<button type="button" class="btn btn-danger base-icon-cancel" onclick="<?php echo $APPTAG?>_linkRemove(\'#<?php echo $APPTAG?>-newLinkGroup'+<?php echo $APPTAG?>LinkIndex+'\')"></button>';
			formGroup += '				</span>';
			formGroup += '			</div>';
			formGroup += '		</div>';
			formGroup += '	</div>';
			formGroup += '</div>';
			jQuery('#<?php echo $APPTAG?>-linkGroups').append(formGroup);
		}
		// WEBLINK REMOVE -> Remove campo de weblink
		window.<?php echo $APPTAG?>_linkRemove = function(id) {
			if(confirm('<?php echo JText::_('MSG_CONFIRM_REMOVE_WEBLINK')?>')) jQuery(id).remove();
		}

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

		// LOAD EDIT
		// Prepara o formulário para a edição dos dados
		window.<?php echo $APPTAG?>_loadEditFields = function(appID, reload, formDisable) {
			var id = (appID ? appID : displayId.val());
			if(isEmpty(id) || id == 0) {
				<?php echo $APPTAG?>_formReset();
				return false;
			}

			// CUSTOM -> Remove new fields
			jQuery('.newFieldsGroup').empty();

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
						type.val(item.type);
						user_id.val(item.user_id);
						usergroup.selectUpdate(item.usergroup); // select
						name.val(item.name);
						nickname.val(item.nickname);
						email.val(item.email);
						cmail.val(item.email);
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
						// chats
						var cName = item.chat_name.split(";");
						var cUser = item.chat_user.split(";");
						for(i = 0; i < cName.length; i++) {
							if(i == 0) {
								chat_name.val(cName[0]);
								chat_user.val(cUser[0]);
							} else {
								<?php echo $APPTAG?>_chatAdd(cName[i], cUser[i]);
							}
						}
						// weblinks
						var wTxt = item.weblink_text.split(";");
						var wUrl = item.weblink_url.split(";");
						for(i = 0; i < wUrl.length; i++) {
							if(i == 0) {
								weblink_text.val(wTxt[0]);
								weblink_url.val(wUrl[0]);
							} else {
								<?php echo $APPTAG?>_linkAdd(wTxt[i], wUrl[i]);
							}
						}
						occupation.val(item.occupation);
						extra_info.val(item.extra_info);
						tags.selectUpdate(item.tags); // select
						note.val(item.note);
						checkOption(access, item.access);
						reasonStatus.val(item.reasonStatus);
						role.val(item.reasonStatus);

						// show relations buttons
						setHidden('#<?php echo $APPTAG?>-buttons-relations', false, '#<?php echo $APPTAG?>-msg-relations');

						<?php // Closure Actions
						require(JPATH_CORE.DS.'apps/snippets/form/loadEdit.end.js.php');
						?>

					});
					// mostra dos botões 'salvar & novo' e 'delete'
					setHidden('#btn-<?php echo $APPTAG?>-delete', false);
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

		// CUSTOM -> Sincroniza com os contatos
		// Essa sincronização apenas verifica se os usuários atribuídos ainda existem e se estão ativos
		window.<?php echo $APPTAG?>_userSync = function() {
			<?php echo $APPTAG?>_formExecute(true, true, false); // inicia o loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=userSync",
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data) {
					<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					jQuery.map( data, function( res ) {
						setTimeout(function() {
							<?php $redir = baseHelper::setUrlParam(JURI::current(), 'sync=1'); ?>
							window.location.href = "<?php echo $redir?>";
						}, 1000);
					});
				},
				error: function(xhr, status, error) {
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
					<?php echo $APPTAG?>_formExecute(true, formDisable, false); // encerra o loader
				}
			});
			return false;
		};

		// CUSTOM
		// Set Group List
		// seta a lista de grupos de acordo com o tipo selecionado
		window.<?php echo $APPTAG?>_getGroupList = function(grps, gID) {
			<?php echo $APPTAG?>_formExecute(true, false, false); // inicia o loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?task=gList&gIDs="+grps,
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data){
					jQuery.map( data, function( res, i ) {
						if(res.status == 1) {
							// remove all options
							if(i == 0) {
								usergroup.find('option').remove();
								if(res.total != 1) usergroup.append('<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>');
							}
							usergroup.append('<option value="'+res.id+'">'+res.title+'</option>');
							if(isSet(gID) && gID) usergroup.val(gID);
							usergroup.selectUpdate(); // atualiza o select
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
					<?php echo $APPTAG?>_formExecute(true, false, false); // encerra o loader
				}
			});
			return false;
		};

}); // CLOSE JQUERY->READY

jQuery(window).on('load', function() {

	// JQUERY VALIDATION
	window.<?php echo $APPTAG?>_validator = mainForm_<?php echo $APPTAG?>.validate({
		rules: {
			email: {
				remote: {
					url: '<?php echo _CORE_?>helpers/users/checkEmail.php',
					type: 'post',
					data: {
						cmail: function() {
							return jQuery('#<?php echo $APPTAG?>-email').val();
						},
						cmail: function() {
							return jQuery('#<?php echo $APPTAG?>-cmail').val();
						}
					}
				},
				required: function(el) {
					return (jQuery('#<?php echo $APPTAG?>-access-1').is(':checked'));
				}
			},
			username: {
				remote: {
					url: '<?php echo _CORE_?>helpers/users/checkEmail.php',
					type: 'post'
				},
				required: function(el) {
					return (jQuery('#<?php echo $APPTAG?>-access-1').is(':checked'));
				}
			},
			password : {
				minlength : 6
			},
			repassword: {
				equalTo: '#<?php echo $APPTAG?>-password'
			}
		},
		messages: {
			email: {
				remote: '<?php echo JText::_('MSG_EMAIL_EXISTS')?>'
			},
			username : {
				remote: '<?php echo JText::_('MSG_USERNAME_EXISTS')?>'
			},
			repassword: {
				equalTo: '<?php echo JText::_('MSG_PASS_NOT_EQUAL')?>'
			}
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
					<button type="button" class="btn btn-sm btn-success hasTooltip" onclick="<?php echo $APPTAG?>_userSync()" title="<?php echo JText::_('TEXT_USER_SYNC_DESC')?>">
						<span class="base-icon-arrows-cw"></span> <?php echo JText::_('TEXT_USER_SYNC')?>
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
	<?php endif; ?>
</div>
