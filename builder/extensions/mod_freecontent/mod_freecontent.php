<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base Free Content
* Version:	1.0.0
* Date:		13/12/2016
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;
?>

<div class="mod-freecontent">
	<?php

	// custom code
	if($params->get('css')) $doc->addStyleDeclaration($params->get('css'));
	if($params->get('script')) $doc->addScriptDeclaration($params->get('script'));
	$code = $params->get('code');
	if(!empty($code)) :
		$code=ltrim($code,'<?php');
		$code=rtrim($code,'?>');
		echo eval($code);
	endif;

	// custom file included
	$file = $params->get('include');
	if(!empty($file)) :
		if(strpos($file, 'http') === false) :
			require(JPATH_BASE.'/'.$file);
		else :
			echo '<p class="alert alert-danger">'.Jtext::_('MOD_FREECONTENT_INCLUDE_ALERT').'</p>';
		endif;
	endif;

	// custom html content
	$html = $params->get('html');
	if(!empty($html)) echo $html;

	?>
</div>
