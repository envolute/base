<?php

// 'Core' da Base
define('JPATH_CORE', JPATH_BASE.'/libraries/envolute');

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

// LOAD SCRIPTS
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base(true).'/templates/base/css/style.app.css');
$doc->addScript(JURI::base(true).'/templates/base/js/forms.js');
$doc->addScript(JURI::base(true).'/templates/base/js/validate.js');

?>

<script type="text/javascript">

jQuery(function() {

	window.formContact	= jQuery('#get-in-touch form');

	// CUSTOM -> Set Status
	// seta o valor do campo 'status' do registro
	window.getInTouch = function() {
		// valida o formulário antes do envio -> 'jquery validation'
		if(formContact.valid()) {
			var dados	= formContact.serialize();
			// inicia o loader
			toggleLoader();
			jQuery.ajax({
				url: "sendMail.php?task=mail",
				dataType: 'json',
				type: 'POST',
				data:  dados,
				cache: false,
				success: function(data) {
					jQuery.map( data, function( res ) {
						if(res.status == 1) {
							// Mensagem de sucesso
							jQuery.baseNotify({ msg: '<h4 class="base-icon-ok-circled"> Thank you for your contact!</h4>Soon we will contact you', alertTime: 5000 });
							formContact.find('input').val('');
						} else {
							// Mensagem de erro
							jQuery.baseNotify({ msg: "Sending error!<br />Please try again.", type: "danger"});
						}
					});
				},
				error: function(xhr, status, error) {
					jQuery.baseNotify({ msg: "Sending error!", type: "danger"});
				},
				complete: function() {
					toggleLoader(); // encerra o loader
				}
			});
			return false;
		}
	};

	// JQUERY VALIDATION
	window.form_validator = formContact.validate({
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

<div id="get-in-touch" class="row">
	<div class="col-12">
		<div class="form-container p-3 p-lg-5 bg-white set-shadow-lg">
			<div class="row">
				<div class="col-md-6 px-3 px-lg-5">
					<form onsubmit="return false">
						<p class="mb-md-5">Do you have a project you'd like to discuss? We're here to help.
Leave your contact information, and we'll get in touch with you.</p>
						<div class="form-group field-required">
							<input type="text" name="name" id="contact-name" class="form-control" />
							<label>Name:</label>
						</div>
						<div class="form-group field-required">
							<input type="email" name="email" id="contact-email" class="form-control" />
							<label>Email:</label>
						</div>
						<div class="form-group field-required">
							<input type="phone" name="phone" id="contact-phone" class="form-control" />
							<label>Phone:</label>
						</div>
						<div class="form-actions pt-5 text-right">
							<button type="button" class="btn btn-outline-primary" onclick="getInTouch()">Let’s talk!</button>
						</div>
					</form>
				</div>
				<div class="col-md-6">
					<h1 class="display-3 font-serif pt-5 mb-4">We’re ready when you are</h1>
				</div>
			</div>
		</div>
	</div>
</div>
