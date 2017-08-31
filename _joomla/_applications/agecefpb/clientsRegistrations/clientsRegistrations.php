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

// Get request data
$uID = $app->input->get('uID', 0, 'int');
$uID = ($hasAdmin && $uID > 0) ? $uID : $user->id;

// DATABASE CONNECT
$db = JFactory::getDbo();

?>

<script>
jQuery(function() {

	<?php // Default 'JS' Vars
	require(JPATH_CORE.DS.'apps/snippets/initVars.js.php');
	?>

	// APP FIELDS
	var user_id				= jQuery('#<?php echo $APPTAG?>-user_id');
	var usergroup 			= jQuery('#<?php echo $APPTAG?>-usergroup');
	var username 			= jQuery('#<?php echo $APPTAG?>-username');
	var cusername 			= jQuery('#<?php echo $APPTAG?>-cusername');
	var name 				= jQuery('#<?php echo $APPTAG?>-name');
	var email				= jQuery('#<?php echo $APPTAG?>-email');
	var cmail				= jQuery('#<?php echo $APPTAG?>-cmail');
	var cpf					= jQuery('#<?php echo $APPTAG?>-cpf');
	var rg					= jQuery('#<?php echo $APPTAG?>-rg');
	var rg_orgao			= jQuery('#<?php echo $APPTAG?>-rg_orgao');
	var gender 				= mainForm.find('input[name=gender]:radio'); // radio group
	var birthday			= jQuery('#<?php echo $APPTAG?>-birthday');
	var marital_status		= jQuery('#<?php echo $APPTAG?>-marital_status');
	var partner				= jQuery('#<?php echo $APPTAG?>-partner');
	var children			= jQuery('#<?php echo $APPTAG?>-children');
	// Company data
	var cx_status			= mainForm.find('input[name=cx_status]:radio'); // status "efetivo/aposentado"
	var cx_code				= jQuery('#<?php echo $APPTAG?>-cx_code'); // matrícula
	var cx_email			= jQuery('#<?php echo $APPTAG?>-cx_email');
	var cx_role				= jQuery('#<?php echo $APPTAG?>-cx_role'); // cargo
	var cx_situated			= jQuery('#<?php echo $APPTAG?>-cx_situated'); // lotação
	var cx_date				= jQuery('#<?php echo $APPTAG?>-cx_date'); // data de admissão
	// Address
	var zip_code 			= jQuery('#<?php echo $APPTAG?>-zip_code');
	var address				= jQuery('#<?php echo $APPTAG?>-address');
	var address_number		= jQuery('#<?php echo $APPTAG?>-address_number');
	var address_info		= jQuery('#<?php echo $APPTAG?>-address_info');
	var address_district	= jQuery('#<?php echo $APPTAG?>-address_district');
	var address_city		= jQuery('#<?php echo $APPTAG?>-address_city');
	// contact
	var phone0				= jQuery('#<?php echo $APPTAG?>-phone0');
	var phone1				= jQuery('#<?php echo $APPTAG?>-phone1');
	var phone2				= jQuery('#<?php echo $APPTAG?>-phone2');
	// Billing data
	var agency				= jQuery('#<?php echo $APPTAG?>-agency');
	var account				= jQuery('#<?php echo $APPTAG?>-account');
	var operation			= jQuery('#<?php echo $APPTAG?>-operation');

	var disableEdit = [name, cpf, rg, rg_orgao, gender, birthday, cx_code, cx_date, agency, account, operation];

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
			user_id.val(0);
			usergroup.val(<?php echo $_SESSION[$APPTAG.'newUsertype']?>);
			username.val('');
			cusername.val('');
			name.val('');
			email.val('');
			cmail.val('');
			cpf.val('');
			rg.val('');
			rg_orgao.val('');
			checkOption(gender, ''); // radio
			birthday.val('');
			marital_status.selectUpdate(0); // select
			partner.val('');
			children.selectUpdate(0); // select
			checkOption(cx_status, 0); // radio
			cx_code.val('');
			cx_email.val('');
			cx_role.val('');
			cx_situated.val('');
			cx_date.val('');
			zip_code.val('');
			address.val('');
			address_number.val('');
			address_info.val('');
			address_district.val('');
			address_city.val('');
			phone0.val('');
			phone1.val('');
			phone2.val('');
			agency.val('');
			account.val('');
			operation.val('');

			// Habilita campos não editaveis
			for (i = 0; i < disableEdit.length; i++) {
				<?php echo $APPTAG?>_noEditableField(disableEdit[i]);
			}

			// Recarrega o form e esconde a mensagem de successo
			setHidden('#<?php echo $APPTAG?>-form-ajax', false, '#<?php echo $APPTAG?>-msg-success');

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
		window.<?php echo $APPTAG?>_clearValidation = function(formElement) {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/clearValidation.def.js.php');
			?>
		};

		// CONFIRM SUCCESS -> Ação após a confirmação do cadastro
		window.<?php echo $APPTAG?>_confirmSuccess = function(regID) {
			setHidden('#<?php echo $APPTAG?>-form-ajax', true, '#<?php echo $APPTAG?>-msg-success');
			var urlPrint = '<?php echo JURI::root()?>associe-se/ficha?rID='+regID+'&tmpl=modal';
			jQuery('#<?php echo $APPTAG?>-registration-data').attr("src", urlPrint);
			scrollTo('#header');
		};

		// EDIT SUCCESS -> Ações após editar um registro
		window.<?php echo $APPTAG?>_editSuccess = function() {
			<?php $p = ($uID != $user->id) ? '?uID='.$uID : '' ?>
			location.href = '<?php echo JURI::root()?>user/profile'.$p;
		};

		// SELECT USER -> Selecionar um usuário no formulário de edição
		window.<?php echo $APPTAG?>_selectUser = function(el) {
			var val = jQuery(el).val();
			location.href = '<?php echo JURI::current()?>'+((!isEmpty(val) && val != 0) ? '?uID='+val : '');
		};

	// AJAX CONTROLLERS
	// métodos controladores das ações referente ao banco de dados e envio de arquivos

		// LOAD EDIT
		// Prepara o formulário para a edição dos dados
		window.<?php echo $APPTAG?>_loadEditFields = function(appID, reload, formDisable) {
			var id = appID;
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
						user_id.val(item.user_id);
						usergroup.val(item.usergroup);
						username.val(item.username);
						cusername.val(item.username);
						name.val(item.name);
						email.val(item.email);
						cmail.val(item.email);
						cpf.val(item.cpf);
						rg.val(item.rg);
						rg_orgao.val(item.rg_orgao);
						checkOption(gender, item.gender); // radio
						birthday.val(dateFormat(item.birthday)); // DATE -> conversão de data
						marital_status.selectUpdate(item.marital_status); // select
						partner.val(item.partner);
						children.selectUpdate(item.children); // select
						checkOption(cx_status, item.cx_status); // radio
						cx_code.val(item.cx_code);
						cx_email.val(item.cx_email);
						cx_role.val(item.cx_role);
						cx_situated.val(item.cx_situated);
						cx_date.val(dateFormat(item.cx_date)); // DATE -> conversão de data
						zip_code.val(item.zip_code);
						address.val(item.address);
						address_number.val(item.address_number);
						address_info.val(item.address_info);
						address_district.val(item.address_district);
						address_city.val(item.address_city);
							// phones
							var p = item.phones.split(",");
							phone0.val(p[0]);
							phone1.val(p[1]);
							phone2.val(p[2]);
						agency.val(item.agency);
						account.val(item.account);
						operation.val(item.operation);

						// Desabilita campos não editáveis
						for (i = 0; i < disableEdit.length; i++) {
							<?php echo $APPTAG?>_noEditableField(disableEdit[i]);
						}

						<?php // Closure Actions
						require(JPATH_CORE.DS.'apps/snippets/form/loadEdit.end.js.php');
						?>

					});

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

		// NO EDITABLE FIELDS
		window.<?php echo $APPTAG?>_noEditableField = function(field) {
			var val = field.val();
			if(!isEmpty(val) && val != 0) field.prop('disabled', true);
			else field.prop('disabled', false);
			// Atualiza se for select 'chosen'
			if(field.is('select')) field.trigger('chosen:updated');
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

jQuery(window).load(function() {

	// JQUERY VALIDATION
	window.<?php echo $APPTAG?>_validator = mainForm_<?php echo $APPTAG?>.validate({
		rules: {
			email: {
				remote: {
					url: '<?php echo _CORE_?>helpers/users/checkEmail.php',
					type: 'post',
					data: {
						cmail: function() {
							return jQuery('#<?php echo $APPTAG?>-cmail').val();
						}
					}
				}
			},
			cpf : {
				remote: {
					url: '<?php echo _CORE_?>helpers/users/checkUsername.php',
					type: 'post',
					data: {
						username: function() {
							return jQuery('#<?php echo $APPTAG?>-cpf').val().replace(/[^\d]+/g,'');
						},
						cusername: function() {
							return jQuery('#<?php echo $APPTAG?>-cusername').val();
						}
					}
				}
			},
			partner: {
				required: function(el) {
					return jQuery('#<?php echo $APPTAG?>-marital_status option:selected').data('targetDisplay');
				}
			},
			repassword: {
				equalTo: '#<?php echo $APPTAG?>-password'
			}
		},
		messages: {
			email: {
				remote: '<?php echo JText::_('MSG_EMAIL_EXISTS')?>'
			},
			cpf : {
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

	<?php
	// FORM ACTION DEFINITION
	// Se for para editar, verifica se existe usuário é um associado
	// Senão, carrega o formulário em branco...
	$rID = 0;
	$showForm = true;
	if($cfg['isEdit']) :
		$query = 'SELECT '. $db->quoteName('id') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('user_id') .' = '. $uID;
		$db->setQuery($query);
		$rID = $db->loadResult();
		if(!$rID) :
			// SEM DADOS
			$showForm = false;
			if($hasAdmin) :
				// O perfil é visualizado apenas por associados.
				// Usuários administradores "$hasAdmin" (não associados) só podem
				// visualizar seus dados ou editar seu perfil, na administração...
				// => Mostra a mensagem...
				echo 'console.log("'.$query.'");';
				echo 'setHidden("#'.$APPTAG.'-is-admin", false, "#'.$APPTAG.'-form-loader");';
			else :
				$app->enqueueMessage(JText::_('MSG_NOT_PERMISSION'), 'warning');
				$app->redirect(JURI::root(true));
				exit();
			endif;
		endif;
	endif;
	// LOAD FORM
	if($showForm) :
		echo 'setHidden("#'.$APPTAG.'-form-ajax", false, "#'.$APPTAG.'-form-loader");'; // Mostra o formulário
		echo $APPTAG.'_loadEditFields('.$rID.', true, true);';
	endif;
	?>



});

</script>

<?
if($hasAdmin) :
	// CLIENTS
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE user_id <> 0 AND state = 1 ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
?>

	<div class="row">
		<div class="col-md-4">
			<h5 class="text-info"><?php echo JText::_('MSG_ADMIN_EDIT'); ?></h5>
		</div>
		<div class="col-md-8">
			<fieldset class="fieldset-embed fieldset-sm">
				<legend><?php echo JText::_('FIELD_LABEL_CLIENT_SELECT'); ?></legend>
				<select name="uID" id="<?php echo $APPTAG?>-uID" onchange="<?php echo $APPTAG?>_selectUser(this)">
					<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
					<?php
						foreach ($clients as $obj) {
							echo '<option value="'.$obj->user_id.'"'.($uID == $obj->user_id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
			</fieldset>
		</div>
	</div>
	<hr class="mt-0" />
<?php endif;?>

<div id="<?php echo $APPTAG?>-form-loader" class="text-center">
	<img src="<?php echo JURI::root()?>templates/base/images/core/loader-active.gif">
</div>
<div id="<?php echo $APPTAG?>-is-admin" class="alert alert-warning base-icon-attention" hidden>
	 <?php echo JText::_('MSG_IS_ADMIN')?>
 </div>
<div id="<?php echo $APPTAG?>-msg-success" class="clearfix" hidden>
	<h4 class="alert alert-success base-icon-ok"> <?php echo JText::_('MSG_RESGISTRATION_SUCCESS')?></h4>
	<?php echo JText::_('MSG_PRINT_DATA')?>
	<iframe id="<?php echo $APPTAG?>-registration-data" style="width:100%; height:1000px; border:none"></iframe>
</div>
<div id="<?php echo $APPTAG?>-form-ajax" class="base-app clearfix" hidden>
	<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" id="<?php echo $APPTAG?>-id" />
		<?php require_once($PATH_APP_FILE.'.form.php');?>
	</form>
</div>
