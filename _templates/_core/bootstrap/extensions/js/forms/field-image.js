//JQUERY
jQuery(function() {

	// BUTTON FILE ACTION
	// Botão para 'substituir' um campo do tipo 'input:file'
	var field_image	= ".image-action";

	// Seta a ação em um campo do tipo file
	window.imageFileAction = function (field, target, width, height) {
		field = setElement(field, field_image);
		field.each(function() {
			var obj = jQuery(this);
			var input = isSet(target) ? target : jQuery(obj.data('target'));
			if(!elementExist(input)) input = obj.closest('.image-file').find('input:file');
			// If input 'target' exist...
			if(elementExist(input)) {
				obj.off('click').on('click',function(e) {
					e.preventDefault();
					input.click();
				});
				// seta a mudança de estado do botão no evento 'change' do 'input:file'
				fieldImageActive(obj, input);
			}
		});
	};

	// IMAGE FILE ACTIVE
	// seta a mudança de estado do botão no evento 'change' do 'input:file'
	window.fieldImageActive = function (field, input) {
		var field = setElement(field, field_image);
		if(elementExist(field)) {
			var file = setElement(input, field.closest('.image-file').find('input:file'));
			file.change(function() {
				var obj = jQuery(this);
				if(obj.val() == '') {
					// remove o estado 'ativo' do botão
					field.removeClass('active');
					var hasImg = field.find('.image-file-label').hasClass('hasImg') ? true : false;
					field.find('.image-file-off').prop('hidden', hasImg);
					field.find('.image-file-on').prop('hidden', true);
				} else if(obj.val() != '') {
					// mostra o indicador
					field.addClass('active');
					field.find('.image-file-off, .image-file-edit').prop('hidden', true);
					field.find('.image-file-on').prop('hidden', false).text(obj.val().replace(/.*[\/\\]/, ''));
					// remove error message if field is 'required'
					obj.next('.error').addClass('valid').empty();
				}
			});
		}
	};

	// IMAGE FILE STATE DEFAULT
	// Desabilita o estado ativo do "botão" referente ao 'input:file' informado
	window.fieldImageDefault = function (fileInput) {
		if(isSet(fileInput) && elementExist(fileInput)) {
			var obj = setElement(fileInput);
			var img = obj.closest('.image-file').find(field_image);
			if(elementExist(img)) {
				img.removeClass('active');
				img.find('.image-file-off').prop('hidden', false);
				img.find('.image-file-on, .image-file-edit').prop('hidden', true);
				img.find('.image-file-label').removeClass('hasImg');
				img.find('.image-file-label').css({'background-image': 'none'});
			}
		}
	};

});
