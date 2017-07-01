//JQUERY
jQuery(function() {

  // HTML EDITOR
  // Implementa um editor de html a partir de um textarea
  // utilizando o script 'trumbowyg' => 'https://alex-d.github.io/Trumbowyg/'
  var field_editor	= "textarea.field-editor, textarea.field-html";

  window.setEditor = function (input, isFull, isDisable, height) {
    // $.trumbowyg.svgPath = false:
    input = setElement(input, field_editor);
    input.each(function() {
      var obj = jQuery(this);
      // set full
      var full = isSet(isFull) ? isFull : false;
      full = isSet(obj.data('editorFull')) ? obj.data('editorFull') : full;
      // set height
      var height = isSet(height) ? height : 300;
      height = isSet(obj.data('editorHeight')) ? obj.data('editorHeight') : height;
      // set disabled
      var disable = isSet(isDisable) ? isDisable : false;
      disable = isSet(obj.data('editorDisabled')) ? obj.data('editorDisabled') : disable;

      var basicOptions = ['btnGrp-semantic',['formatting'],'btnGrp-justify','btnGrp-lists',['horizontalRule'],['link'],['removeformat'],['fullscreen']];
      var fullOptions = [['viewHTML'],'btnGrp-semantic','superscript',['formatting'],['foreColor'],'btnGrp-justify','btnGrp-lists',['horizontalRule'],['link'],'image',['noembed'],['removeformat'],['fullscreen']]
      if(full) {
        var options = fullOptions;
        var imageGroup = { dropdown: ['insertImage', 'base64'], ico: 'insertImage' };
      } else {
        var options = basicOptions;
        var imageGroup = null;
      }

      obj.css('height', height);
      obj.trumbowyg({
        lang: 'pt',
        removeformatPasted: true,
        disabled: disable,
        btnsDef: { image: imageGroup },
        btns: options
      });
    });
  };

  // Carrega o valor recebido no editor
  window.setContentEditor = function (input, value) {
    input = setElement(input, field_editor);
    input.each(function() {
      var obj = jQuery(this);
      var val = isEmpty(obj.val()) ? '' : obj.val();
      val = isSet(value) ? value : val;
      obj.trumbowyg('html', val); // html editor
    });
  };

  // Pega o valor gerado pelo editor
  window.getContentEditor = function (input) {
    input = setElement(input, field_editor);
    input.each(function() {
      jQuery(this).val(jQuery(this).trumbowyg('html'));
    });
  };

});
