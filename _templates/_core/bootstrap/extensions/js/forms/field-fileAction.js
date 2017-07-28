//JQUERY
jQuery(function() {

	// BUTTON FILE ACTION
	// Botão para 'substituir' um campo do tipo 'input:file'
	var btn_fileAction	= ".btn.file-action";
	var activeClass		= 'active base-icon-ok btn-icon';

	// Seta a ação em um campo do tipo file
	window.btnFileAction = function (btn, target, width, height) {
		btn = setElement(btn, btn_fileAction);
		btn.each(function() {
			var obj = jQuery(this);
			var input = isSet(target) ? target : jQuery(obj.data('target'));
			if(!elementExist(input)) input = obj.closest('.btn-file').find('input:file');
			// If input 'target' exist...
			if(elementExist(input)) {
				obj.off('click').on('click',function() { input.click() });
				// seta a mudança de estado do botão no evento 'change' do 'input:file'
				btnFileActive(obj, input);
			}
		});
	};

	// BTN FILE ACTIVE
	// seta a mudança de estado do botão no evento 'change' do 'input:file'
	window.btnFileActive = function (button, input) {
		var btn = setElement(button, btn_fileAction);
		if(elementExist(btn)) {
			var file = setElement(input, btn.closest('.btn-file').find('input:file'));
			file.change(function() {
				var obj = jQuery(this);
				if(obj.val() == '') {
					// remove o estado 'ativo' do botão
					btn.removeClass(activeClass).addClass('.btn-default').attr('title', '').attr('data-original-title', '');
				} else if(obj.val() != '') {
					// mostra o indicador
					btn.addClass(activeClass).removeClass('.btn-default').attr('title', obj.val().replace(/.*[\/\\]/, ''));
					// remove error message if field is 'required'
					obj.next('.error').addClass('valid').empty();
				}
				setTips();
			});
		}
	};

	// BTN FILE STATE DEFAULT
	// Desabilita o estado ativo do "botão" referente ao 'input:file' informado
	window.btnFileDefault = function (fileInput) {
		if(isSet(fileInput) && elementExist(fileInput)) {
			var obj = setElement(fileInput);
			var btn = obj.closest('.btn-file').find(btn_fileAction);
			if(elementExist(btn)) btn.removeClass(activeClass);
		}
	};

});
