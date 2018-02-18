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
if(!$hasClient) {
	$app->redirect(JURI::root(true).'/apps/staff/edit-profile');
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
	var client_id			= jQuery('#<?php echo $APPTAG?>-client_id');
	var user_id				= jQuery('#<?php echo $APPTAG?>-user_id');
	var name 				= jQuery('#<?php echo $APPTAG?>-name');
	var email				= jQuery('#<?php echo $APPTAG?>-email');
	var cmail				= jQuery('#<?php echo $APPTAG?>-cmail');
	var gender 				= mainForm.find('input[name=gender]:radio'); // radio group
	var role				= jQuery('#<?php echo $APPTAG?>-role');
	// Joomla Registration
	// var access				= mainForm.find('input[name=access]:radio'); // radio group
	// var usergroup 			= mainForm.find('input[name=usergroup]:radio'); // radio group
	// var cusergroup			= jQuery('#<?php echo $APPTAG?>-cusergroup');
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
			// user_id.val('');
			name.val('');
			email.val('');
			cmail.val('');
			checkOption(gender, ''); // radio
			role.val('');
			// checkOption(access, 1);
			// setHidden(jQuery('#<?php echo $APPTAG?>-access-group'), true);
			// checkOption(usergroup, 16);
			// cusergroup.val(0);
			// reasonStatus.val('');
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
						// client_id.selectUpdate(item.client_id);
						// user_id.val(item.user_id);
						name.val(item.name);
						email.val(item.email);
						cmail.val(item.email);
						checkOption(gender, item.gender); // radio
						role.val(item.role);
						// checkOption(access, item.access);
						// setHidden(jQuery('#<?php echo $APPTAG?>-access-group'), false);
						// checkOption(usergroup, item.usergroup); // radio
						// cusergroup.val(item.usergroup);
						// reasonStatus.val(item.reasonStatus);
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
	// verifica se existe usuário é um associado
	$rID = 0;
	$showForm = true;
	$query = 'SELECT '. $db->quoteName('id') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('user_id') .' = '. $uID;
	$db->setQuery($query);
	$rID = $db->loadResult();
	if($rID) :
		echo 'setHidden("#'.$APPTAG.'-form-ajax", false, "#'.$APPTAG.'-form-loader");'; // Mostra o formulário
		echo $APPTAG.'_loadEditFields('.$rID.', true, true);';
	else :
		if($isAdmin) :
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
