<?php

// load Scripts
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'templates/base/css/style.base.app.css');
$doc->addScript(JURI::base().'templates/base/js/forms.js');
$doc->addScript(JURI::base().'templates/base/js/validate.js');
// carrega Jquery.ui datepicker;
if($cfg['dateConvert'] || $cfg['load_UI']) {
	$doc->addStyleSheet(JURI::base().'templates/base/core/libs/jquery/jquery-ui.min.css');
	$doc->addScript(JURI::base().'templates/base/core/libs/jquery/jquery-ui.min.js');
}
if($cfg['htmlEditor']) {
	$doc->addStyleSheet(JURI::base().'templates/base/core/libs/editor/ui/trumbowyg.min.css');
	$doc->addScript(JURI::base().'templates/base/core/libs/editor/trumbowyg.min.js');
	$doc->addScript(JURI::base().'templates/base/core/libs/editor/langs/pt.min.js');
	if($cfg['htmlEditorFull']) {
		// base64
		$doc->addScript(JURI::base().'templates/base/core/libs/editor/plugins/base64/trumbowyg.base64.min.js');
		// colors
		$doc->addScript(JURI::base().'templates/base/core/libs/editor/plugins/colors/trumbowyg.colors.min.js');
		$doc->addStyleSheet(JURI::base().'templates/base/core/libs/editor/plugins/colors/ui/trumbowyg.colors.min.css');
		// noembed
		$doc->addScript(JURI::base().'templates/base/core/libs/editor/plugins/noembed/trumbowyg.noembed.min.js');
	}
}

?>
