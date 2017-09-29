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
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// if user is not logged
if($user->guest) return;

// database connect
$db = JFactory::getDbo();

?>

<script>
jQuery(function() {

	// VIEWS
	var mainForm		= jQuery('#form-<?php echo $APPTAG?>');
	window.mainForm_<?php echo $APPTAG?> = mainForm;
	var popup			= jQuery('#modal-<?php echo $APPTAG?>');

	//FIELDS
	var newpass			= jQuery('#<?php echo $APPTAG?>-newpass');
	var repass			= jQuery('#<?php echo $APPTAG?>-repass');

	// FORM CONTROLLERS
	// métodos controladores do formulário

		var firstField = newpass; // campo que recebe o focu no carregamento

		<?php if($isModal) :?>
			// On Modal Open -> Ações quando o modal é aberto
			popup.on('shown.bs.modal', function () {
				// seta o focus no carregamento do formulário
				firstField.focus();
			});
		<?php endif;?>

		// Executing -> processo de execução de ação do form
		window.<?php echo $APPTAG?>_formExecute = function(loader, disabled, e) {
			// mostra/esconde o loader
			if(loader) toggleLoader();
			// habilita/desabilita o form
			if(disabled) mainForm.find('fieldset').prop('disabled', e);
	 	};

		// Reset -> Reseta o form e limpa as mensagens de validação
		window.<?php echo $APPTAG?>_formReset = function() {

			newpass.val('');
			repass.val('');

			// limpa as classes e mensagens de validação caso sejam setadas...
			<?php echo $APPTAG?>_clearValidation(mainForm);

			<?php if($isModal) :?>
			// Seta o focus no carregamento do formulário
			if(elementExist(firstField)) setTimeout(function() { inputGetFocus(firstField) }, 10);
			<?php endif;?>

		};

		// CLEAR VALIDATION ->  Limpa os erros de validação
		window.<?php echo $APPTAG?>_clearValidation = function(formElement){
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/clearValidation.def.js.php');
			?>
		}

	// AJAX CONTROLLERS
	// métodos controladores das ações referente ao banco de dados e envio de arquivos

		// Save -> executa a ação de inserção ou atualização dos dados no banco
		window.<?php echo $APPTAG?>_save = function() {
			// valida o formulário antes do envio -> 'jquery validation'
			if(mainForm.valid()) {
				// pega os dados enviados pelo form
				var dados = mainForm.serialize();
				// LOADER
			 	<?php echo $APPTAG?>_formExecute(true, true, true); // inicia o loader
				jQuery.ajax({
					url: "<?php echo $URL_APP_FILE ?>.model.php<?php echo $langDef?>",
					dataType: 'json',
					type: 'POST',
					method: "POST",
					data:  dados,
					cache: false,
					success: function(data){
						<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
						jQuery.map( data, function( res ) {
							if(res.status == 1) { // changed

								<?php // SUCCESS STATUS -> Executa quando houver sucesso na requisição ajax
								require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxSuccess.js.php');
								?>
								// limpa a validação quando o formulário é fechado
								<?php echo $APPTAG?>_clearValidation(mainForm);
								// reseta o form
								<?php echo $APPTAG?>_formReset();
								<?php
								// Fecha o modal
								if($isModal) echo "popup.modal('hide');";
								?>

							} else {

								// caso ocorra um erro na ação, mostra a mensagem de erro
								$.baseNotify({ msg: res.msg, type: "danger"});

								// recarrega os scripts de formulário para os campos
								// necessário após um procedimento ajax que envolve os elementos
								setFormDefinitions();
							}
						});
					},
					error: function(xhr, status, error) {
						<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
						require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
						?>
						<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
					}
				});
			}
		};

}); // CLOSE JQUERY->READY

jQuery(window).on('load', function() {
	// Jquery Validation
	window.<?php echo $APPTAG?>_validator = mainForm_<?php echo $APPTAG?>.validate({
		rules: {
			newpass : {
				minlength : 6
			},
			repass: {
				equalTo: "#<?php echo $APPTAG?>-newpass"
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

<?php if($isModal) :?>
<div class="modal fade" id="modal-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>Label">
	<div class="modal-dialog modal-sm" role="document" style="max-width:220px">
		<div class="modal-content text-left">
			<div class="modal-header">
				<h5 class="modal-title">
					<span class="base-icon-lock-open-alt"></span>
					<?php echo JText::_('FORM_TITLE'); ?>
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
<?php endif;?>
				<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">
					<fieldset style="max-width: 280px;">
						<div class="form-group">
							<div class="input-group">
								<input type="password" name="newpass" id="<?php echo $APPTAG?>-newpass" class="form-control input-required" placeholder="<?php echo JText::_('FIELD_LABEL_NEWPASS'); ?>" />
								<span class="input-group-addon">
									<span class="base-icon-help-circled cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NEWPASS'); ?>"></span>
								</span>
							</div>
						</div>
						<div class="form-group m-0">
							<div class="input-group">
								<input type="password" name="repass" id="<?php echo $APPTAG?>-repass" class="form-control input-required" placeholder="<?php echo JText::_('FIELD_LABEL_REPASS'); ?>" />
								<span class="input-group-addon">
									<span class="base-icon-help-circled cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_REPASS'); ?>"></span>
								</span>
							</div>
						</div>
						<div class="form-actions d-flex justify-content-between">
							<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="base-icon-ok btn btn-success" onclick="<?php echo $APPTAG?>_save();"> <?php echo JText::_('TEXT_SAVE'); ?></button>
							<?php if($isModal) :?>
								<button name="btn-<?php echo $APPTAG?>-cancel" id="btn-<?php echo $APPTAG?>-cancel" class="base-icon-cancel btn btn-sm btn-default" data-dismiss="modal"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
							<?php endif;?>
						</div>
					</fieldset>
				</form>
<?php if($isModal) :?>
			</div>
		</div>
	</div>
</div>
<?php endif;?>
