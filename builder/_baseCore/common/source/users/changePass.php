<?php
/* CRUD SIMPLES EM AJAX PARA O SISTEMA BASE
 * AUTOR: IVO JUNIOR
 * EM: 18/01/2016
*/
defined('_JEXEC') or die;

// App Define
$APPNAME  = 'changePass';

// load Scripts
$doc = JFactory::getDocument();
$doc->addScript(JURI::base().'templates/base/js/validate.js');

// carrega o arquivo de tradução
$lang = JFactory::getLanguage();
$langDef = 'lang='.$lang->getTag();
$lang->load('base_'.$APPNAME, JPATH_BASE, $lang->getTag(), true);

$app = JFactory::getApplication('site');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

// if user is not logged
if($user->guest) return;

// database connect
$db = JFactory::getDbo();
?>

<script>
jQuery(function() {

	// VIEWS
	var mainForm		= jQuery('#form-<?php echo $APPNAME?>');
	window.mainForm_<?php echo $APPNAME?> = mainForm;
	var popup		= jQuery('#modal-<?php echo $APPNAME?>');

	//FIELDS
	var newpass			= jQuery('#<?php echo $APPNAME?>-newpass');
	var repass			= jQuery('#<?php echo $APPNAME?>-repass');

	// FORM CONTROLLERS
	// métodos controladores do formulário

		var firstField = newpass; // campo que recebe o focu no carregamento
		// On Modal Open -> Ações quando o modal é aberto
		popup.on('shown.bs.modal', function () {
			// seta o focus no carregamento do formulário
			newpass.focus();
		});

		// On Modal Close -> Ações quando o modal é fechado
		popup.on('hidden.bs.modal', function () {
			// limpa a validação quando o formulário é fechado
			<?php echo $APPNAME?>_clearValidation(mainForm);
			// reseta o form
			<?php echo $APPNAME?>_formReset();
		});

		// Executing -> processo de execução de ação do form
		window.<?php echo $APPNAME?>_formExecute = function(loader, disabled, e) {
			// mostra/esconde o loader
			if(loader) mainForm.find('.ajax-loader').toggleClass('hide');
			// habilita/desabilita o form
			if(disabled) mainForm.find('fieldset').prop('disabled', e);
	 	};

		// Reset -> Reseta o form e limpa as mensagens de validação
		window.<?php echo $APPNAME?>_formReset = function() {
			newpass.val('');
			repass.val('');

			// limpa as classes e mensagens de validação caso sejam setadas...
			<?php echo $APPNAME?>_clearValidation(mainForm);

			// esconde as mensagens de erro e sucesso 'set-success, set-error'
			mainForm.find('.set-success, .set-error').addClass('hide');
			// reabre o form caso uma ação anterior tenha sido executada com sucesso
			mainForm.find('fieldset, #btn-<?php echo $APPNAME?>-save, #btn-<?php echo $APPNAME?>-cancel').removeClass('hide');
			mainForm.find('#btn-<?php echo $APPNAME?>-close').addClass('hide');


		};

		// Clear Validation ->  limpa os erros de validação
		window.<?php echo $APPNAME?>_clearValidation = function(formElement){
			//Iterate through named elements inside of the form, and mark them as error free
			formElement.find('input').each(function(){
				jQuery(this).removeClass('error'); //remove as error from fields
				<?php echo $APPNAME?>_validator.successList.push(this); //mark as error free
				<?php echo $APPNAME?>_validator.showErrors(); //remove error messages if present
			});
			<?php echo $APPNAME?>_validator.resetForm(); //remove error class on name elements and clear history
			<?php echo $APPNAME?>_validator.reset(); //remove all error and success data
		}

	// AJAX CONTROLLERS
	// métodos controladores das ações referente ao banco de dados e envio de arquivos

		// Save -> executa a ação de inserção ou atualização dos dados no banco
		window.<?php echo $APPNAME?>_save = function() {
			// valida o formulário antes do envio -> 'jquery validation'
			if(mainForm.valid()) {
				// pega os dados enviados pelo form
				var dados = mainForm.serialize();

				// LOADER
			 	mainForm.find('.set-success, .set-error').addClass('hide'); // esconde as mensagens de 'erro' ou 'sucesso'
				<?php echo $APPNAME?>_formExecute(true, true, true); // inicia o loader

				jQuery.ajax({
					url: "<?php echo JURI::root().'templates/base/source/users/changePass.model.php?'.$langDef?>",
					dataType: 'json',
					type: 'POST',
					method: "POST",
					data:  dados,
					cache: false,
					success: function(data){
						<?php echo $APPNAME?>_formExecute(true, true, false); // encerra o loader
						jQuery.map( data, function( res ) {
							if(res.status == 1) { // changed
								// esconde o form
								mainForm.find('fieldset, #btn-<?php echo $APPNAME?>-save, #btn-<?php echo $APPNAME?>-cancel').addClass('hide');
								mainForm.find('#btn-<?php echo $APPNAME?>-close').removeClass('hide');
								// MENSAGENS: mostra a mensagem de sucesso/erro
								mainForm.find('.set-success').removeClass('hide');
							} else {
								// caso ocorra um erro na ação, mostra a mensagem de erro
								mainForm.find('.set-error').removeClass('hide').text((res.status == 2 ? '<?php echo JText::_('MSG_PASS_NOT_EQUAL'); ?>' : res.msg));
								firstField.focus(); // seta novamente o focus no primeiro campo
							}
						});
					},
					error: function(xhr, status, error) {
						console.log(xhr);
						console.log(status);
						console.log(error);
						<?php echo $APPNAME?>_formExecute(true, true, false); // encerra o loader
					}
				});
			}
		};

}); // CLOSE JQUERY->READY

jQuery(window).load(function() {
	// Jquery Validation
	window.<?php echo $APPNAME?>_validator = mainForm_<?php echo $APPNAME?>.validate({
		rules: {
			newpass : {
				minlength : 6
			},
			repass: {
				equalTo: "#<?php echo $APPNAME?>-newpass"
			}
		},
		messages: {
			repass: {
				equalTo: "<?php echo JText::_('MSG_PASS_NOT_EQUAL')?>"
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

});

</script>

<div class="modal fade" id="modal-<?php echo $APPNAME?>" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPNAME?>Label">
	<div class="modal-dialog modal-sm" role="document" style="max-width:220px">
		<div class="modal-content text-left">

      <form name="form-<?php echo $APPNAME?>" id="form-<?php echo $APPNAME?>" method="post" enctype="multipart/form-data">

      	<div class="modal-header">
      		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      		<h4 class="modal-title">
						<span class="base-icon-lock-open-alt"></span>
      			<?php echo JText::_('FORM_TITLE'); ?>
      		</h4>
      	</div>
      	<div class="modal-body">
					<div class="base-icon-ok-circled2 set-success alert alert-success no-margin hide"><?php echo JText::_('MSG_SAVED');?></div>
					<div class="base-icon-cancel-circled set-error alert alert-danger bottom-space-sm hide"><?php echo JText::_('MSG_ERROR');?></div>
      		<fieldset>
      			<div class="row">
      				<div class="col-sm-12">
      					<div class="form-group">
									<div class="input-group">
	      						<input type="password" name="newpass" id="<?php echo $APPNAME?>-newpass" class="form-control input-required" placeholder="<?php echo JText::_('FIELD_LABEL_NEWPASS'); ?>" />
										<span class="input-group-addon">
											<span class="base-icon-help-circled cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NEWPASS'); ?>"></span>
										</span>
									</div>
      					</div>
      				</div>
      				<div class="col-sm-12">
      					<div class="form-group no-margin">
									<div class="input-group">
										<input type="password" name="repass" id="<?php echo $APPNAME?>-repass" class="form-control input-required" placeholder="<?php echo JText::_('FIELD_LABEL_REPASS'); ?>" />
										<span class="input-group-addon">
											<span class="base-icon-help-circled cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_REPASS'); ?>"></span>
										</span>
									</div>
      					</div>
      				</div>
            </div>
      		</fieldset>
      	</div>
      	<div class="modal-footer">
      		<div class="pull-left bottom-space-xs">
      			<span class="ajax-loader hide"></span>
      		</div>
      		<div class="pull-right">
      			<button name="btn-<?php echo $APPNAME?>-save" id="btn-<?php echo $APPNAME?>-save" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPNAME?>_save();"> <?php echo JText::_('TEXT_SAVE'); ?></button>
      			<button name="btn-<?php echo $APPNAME?>-cancel" id="btn-<?php echo $APPNAME?>-cancel" class="base-icon-cancel btn btn-default btn-sm" data-dismiss="modal"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
      			<button name="btn-<?php echo $APPNAME?>-close" id="btn-<?php echo $APPNAME?>-close" class="base-icon-cancel btn btn-danger btn-sm hide" data-dismiss="modal"> <?php echo JText::_('TEXT_CLOSE'); ?></button>
      		</div>
      	</div>

      </form>

		</div>
	</div>
</div>
