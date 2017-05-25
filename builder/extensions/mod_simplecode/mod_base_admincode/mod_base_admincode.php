<?php
/*@module mod_admincode */
defined('_JEXEC') or die;
$doc = JFactory::getDocument();

// custom code
if($params->get('css')) $doc->addStyleDeclaration($params->get('css'));
if($params->get('script')) $doc->addScriptDeclaration($params->get('script'));
$code = $params->get('code');
if($params->get('code')) :
	if($params->get('type') == 1):
		$code=ltrim($code,'<?php');
		$code=rtrim($code,'?>');
		echo eval($code);
	else:
		echo $code;
	endif;
endif;

// custom file included
if($params->get('phpFile')) :
	if(strpos($params->get('phpFile'), 'http') === false) :
		require(JPATH_BASE.'/'.$params->get('phpFile'));
	else :
		echo '<p class="alert alert-danger">'.Jtext::_('MOD_ADMINCODE_INCLUDE_ALERT').'</p>';
	endif;
endif;
