//JQUERY
jQuery(function() {

	window.setFormDefinitions = function () {

		// CHAMADA GERAL DOS MÉTODOS
		// -------------------------------------------------------------------------------

			// BUTTONS

				// CHECK STATE
				btnCheckState();

			// FILE

				// BUTTON FILE ACTION
				btnFileAction();

				// IMAGE FILE ACTION
				imgFileAction();

			// INPUTS
			// -> CONTROLADORES DE EVENTOS

				// GET FOCUS
				inputGetFocus();

				// NO DROP
				inputNoDrop();

				// NO PASTE
				inputNoPaste();

				// AUTO TAB

					// Initialize
					jQuery.fn.autoTab = function() {
						return this.each(function() {
							var fields = jQuery(this).parents("form:eq(0),body").find("button,input,textarea,select");
							var index = fields.index( this );
							if ( index > -1 && ( index + 1 ) < fields.length ) {
								var obj = fields.eq( index + 1 );
								obj.focus();
								// CHOSEN
								// verifica se é um 'select' chosen
								if(obj.next('.chosen-container').length) {
									// seta o foco no elemento 'chosen' após o evento autoTab
									obj.next('.chosen-container').find('a.chosen-single').focus();
								}
							}
							return false;
						});
					};

					// SELECT AUTO-TAB
					selectAutoTab();

					// CHECK AUTO-TAB
					checkAutoTab();

			// -> CAMPOS PRE-DEFINIDOS

				// FIXED LENGTH
				inputFixedLength();

				// UPPER CASE
				inputUppercase();

				// LOWER CASE
				inputLowercase();

				// ALPHANUMERIC
				inputAlphanumeric();

				// NÃO PERMITE NÚMEROS
				inputNoNumber();

				// DESABILITA ESPAÇOS EM BRANCO
				inputNoBlankSpace();

				// SEM ACENTUAÇÃO
				inputNoAccents();

				// VALORES NÚMERICOS
				inputNumeric();

				// COM MÁSCARAS

					// INPUTMASK
				  // mascaras pré-definidas. jQuery Input Mask plugin
				  // -> http://robinherbots.github.io/jquery.inputmask
				  // set defaults
				  Inputmask.extendDefaults({
				    showMaskOnHover: false
				  });

					// DATA
					setDate();

					// HORA
					setTime();

					// TELEFONES
					setPhone();

					// IP NUMBER
					setIP();

					// PREÇO
					setPrice();

					// CEP
					setCEP();

					// CPF
					setCPF();

					// CNPJ
					setCNPJ();

				// UTILITÁRIOS

					// SELECT TO SELECT
					selectToSelect();

					// HTML EDITOR
					setEditor();

			// VALIDAÇÃO

				// REQUIRED FIELD
				setFieldRequired();

				// RESET REQUIRED FIELD
				resetFieldRequired();

		// FUNCIONALIDADES ESPECÍFICAS
		// -------------------------------------------------------------------------------

			// CORREÇÕES DO BOOTSTRAP PARA O ANDROID
			var isAndroid = (nua.indexOf('Mozilla/5.0') > -1 && nua.indexOf('Android ') > -1 && nua.indexOf('AppleWebKit') > -1 && nua.indexOf('Chrome') === -1);
			if (isAndroid) jQuery('select.form-control').removeClass('form-control').css('width', '100%');

			// DESABILITA O CLICK EM LABEL "disabled"
			jQuery('.btn.disabled, .btn[disabled]').each(function() {
				jQuery(this).click(function(e) {
					e.preventDefault();
				});
			});

			// CAMPOS COM MASCARA QUE APRESENTARAM PROBLEMAS QUANDO TENTA 'COLAR' UM VALOR
			// alguns campos com mascara não estavam colando o valor corretamente, apenas quando a mascara toda é selecionada
			// sendo assim, a função abaixo seleciona todo o valor do campo quando o evento 'paste'(copiar) é chamado...
			var field_fixPaste	= "input[class^='field-'], input[class*=' field-']";
			jQuery(field_fixPaste).bind({
				paste : function(){ jQuery(this).select().trigger('change'); }
			});

	};
	// END FORM DEFINITIONS ------------------------------------------------------------

});

// CHAMADA GERAL DAS PREDEFINIÇÕES DE FORMULÁRIO
// -----------------------------------------------------------------------------------
jQuery(window).on('load', function() {
	setFormDefinitions();
});
