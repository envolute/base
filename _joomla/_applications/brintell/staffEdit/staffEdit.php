<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// Redireciona para o perfil do cliente
$hasClient = array_intersect($groups, $cfg['groupId']['client']); // se está na lista de grupos permitidos
if($hasClient) {
	$app->redirect(JURI::root(true).'/apps/clients/clientsstaff/edit-profile');
	exit();
}

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

// TYPE
$query = 'SELECT '.$db->quoteName('type').' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('user_id') .' = '.$user->id;
$db->setQuery($query);
$type = $db->loadResult();
$client = ($type == 2) ? true : false;

// Get request data
$uID = $user->id;

?>

<script>
jQuery(document).ready(function() {

	<?php // Default 'JS' Vars
	require(JPATH_CORE.DS.'apps/snippets/initVars.js.php');
	?>

	// APP FIELDS
	// var type				= jQuery('#<?php echo $APPTAG?>-type');
	// var role_id				= jQuery('#<?php echo $APPTAG?>-role_id');
	// var user_id				= jQuery('#<?php echo $APPTAG?>-user_id');
	var name 				= jQuery('#<?php echo $APPTAG?>-name');
	var nickname			= jQuery('#<?php echo $APPTAG?>-nickname');
	var email				= jQuery('#<?php echo $APPTAG?>-email');
	var cmail				= jQuery('#<?php echo $APPTAG?>-cmail');
	// var gender 				= mainForm.find('input[name=gender]:radio'); // radio group
	var birthday			= jQuery('#<?php echo $APPTAG?>-birthday');
	var marital_status		= jQuery('#<?php echo $APPTAG?>-marital_status');
	var children			= jQuery('#<?php echo $APPTAG?>-children');
	// Address
	var zip_code 			= jQuery('#<?php echo $APPTAG?>-zip_code');
	var address				= jQuery('#<?php echo $APPTAG?>-address');
	var address_number		= jQuery('#<?php echo $APPTAG?>-address_number');
	var address_info		= jQuery('#<?php echo $APPTAG?>-address_info');
	var address_district	= jQuery('#<?php echo $APPTAG?>-address_district');
	var address_city		= jQuery('#<?php echo $APPTAG?>-address_city');
	var address_state		= jQuery('#<?php echo $APPTAG?>-address_state');
	var address_country		= jQuery('#<?php echo $APPTAG?>-address_country');
	// phones
	var phone				= jQuery('#<?php echo $APPTAG?>-phone');
	var wapp				= jQuery('#<?php echo $APPTAG?>-wapp');
	var whatsapp			= jQuery('#<?php echo $APPTAG?>-whatsapp');
	var phone_desc			= jQuery('#<?php echo $APPTAG?>-phone_desc');
	var weblink_text		= jQuery('#<?php echo $APPTAG?>-weblink_text');
	var weblink_url			= jQuery('#<?php echo $APPTAG?>-weblink_url');
	var chat_name			= jQuery('#<?php echo $APPTAG?>-chat_name');
	var chat_user			= jQuery('#<?php echo $APPTAG?>-chat_user');
	// extra info
	var occupation			= jQuery('#<?php echo $APPTAG?>-occupation');
	var about_me			= jQuery('#<?php echo $APPTAG?>-about_me');
	var tags				= jQuery('#<?php echo $APPTAG?>-tags');
	// Joomla Registration
	// var access				= jQuery('#<?php echo $APPTAG?>-access');
	// var newUser				= jQuery('#<?php echo $APPTAG?>-newUser');
	// var usergroup 			= jQuery('#<?php echo $APPTAG?>-usergroup');
	// var username 			= jQuery('#<?php echo $APPTAG?>-username');
	var password			= jQuery('#<?php echo $APPTAG?>-password');
	var repassword			= jQuery('#<?php echo $APPTAG?>-repassword');
	// var emailConfirm		= jQuery('#<?php echo $APPTAG?>-emailConfirm');
	// var emailInfo			= jQuery('#<?php echo $APPTAG?>-emailInfo');
	// var reasonStatus		= jQuery('#<?php echo $APPTAG?>-reasonStatus');

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
			name.val('');
			nickname.val('');
			email.val('');
			cmail.val('');
			birthday.val('');
			marital_status.selectUpdate(0); // select
			children.selectUpdate(0); // select
			zip_code.val('');
			address.val('');
			address_number.val('');
			address_info.val('');
			address_district.val('');
			address_city.val('');
			address_state.val('');
			address_country.val('<?php echo $cfg['countryDef']?>');
			phone.phoneMaskUpdate('');
			checkOption(wapp, 0); // checkbox
			whatsapp.val('');
			phone_desc.val('');
			chat_name.val('');
			chat_user.val('');
			weblink_text.val('');
			weblink_url.val('');
			occupation.val('');
			about_me.val('');
			tags.selectUpdate(''); // select
			password.val('');
			repassword.val('');

			// CUSTOM -> Remove new fields
			jQuery('.newFieldsGroup').empty();

			// hide relations buttons
			setHidden('#<?php echo $APPTAG?>-buttons-relations', true, '#<?php echo $APPTAG?>-msg-relations');

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
			formGroup += '			<input type="text" name="chat_name[]" id="<?php echo $APPTAG?>-chat_name'+<?php echo $APPTAG?>ChatIndex+'" value="'+name+'" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_CHAT_NAME'); ?>" />';
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
			formGroup += '			<input type="text" name="weblink_text[]" id="<?php echo $APPTAG?>-weblink_text'+<?php echo $APPTAG?>LinkIndex+'" value="'+txt+'" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_WEBLINK_TEXT'); ?>" />';
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

	// AJAX CONTROLLERS
	// métodos controladores das ações referente ao banco de dados e envio de arquivos

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
						name.val(item.name);
						nickname.val(item.nickname);
						email.val(item.email);
						cmail.val(item.email);
						birthday.val(dateFormat(item.birthday)); // DATE -> conversão de data
						marital_status.selectUpdate(item.marital_status); // select
						children.selectUpdate(item.children); // select
						zip_code.val(item.zip_code);
						address.val(item.address);
						address_number.val(item.address_number);
						address_info.val(item.address_info);
						address_district.val(item.address_district);
						address_city.val(item.address_city);
						address_state.val(item.address_state);
						address_country.val(item.address_country);
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
						about_me.val(item.about_me);
						tags.selectUpdate(item.tags); // select
						password.val('');
						repassword.val('');

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

		<?php if($cfg['hasUpload']) : ?>

			<?php // DELETE FILES -> exclui o registro e deleta o arquivo
			require(JPATH_CORE.DS.'apps/snippets/ajax/delFile.js.php');
			?>

		<? endif; ?>

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

	<?php
	// FORM ACTION DEFINITION
	// Se for para editar, verifica se existe usuário é um associado
	// Senão, carrega o formulário em branco...
	$rID = 0;
	$showForm = true;
	$query = 'SELECT '. $db->quoteName('id') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('user_id') .' = '. $uID;
	$db->setQuery($query);
	$rID = $db->loadResult();
	if($rID) :
		echo 'setHidden("#'.$APPTAG.'-form-ajax", false, "#'.$APPTAG.'-form-loader");'; // Mostra o formulário
		echo $APPTAG.'_loadEditFields('.$rID.', true, true);';
	else :
		if($hasAdmin) :
			echo '<div class="alert alert-warning text-sm mx-2">'.JText::_('MSG_NOT_STAFF_PROFILE').'</div>';
		else :
			$app->enqueueMessage(JText::_('MSG_NOT_PERMISSION'), 'warning');
			$app->redirect(JURI::root(true));
			exit();
		endif;
	endif;
	?>

});

</script>

<div id="<?php echo $APPTAG?>-form-loader" class="text-center">
	<img src="<?php echo JURI::root()?>templates/base/images/core/loader-active.gif">
</div>
<div class="container pt-4">
	<div id="<?php echo $APPTAG?>-form-ajax" class="row clearfix" hidden>
		<form class="col" name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">
			<fieldset class="fieldset-embed">
				<legend class="base-icon-edit"> <?php echo JText::_('TEXT_EDIT_PROFILE')?></legend>
				<?php require_once($PATH_APP_FILE.'.form.php'); ?>
			</fieldset>
		</form>
	</div>
</div>
