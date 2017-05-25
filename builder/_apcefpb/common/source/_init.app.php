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

?>
