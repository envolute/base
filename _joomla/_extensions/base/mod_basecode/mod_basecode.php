<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base Code
* Version:	1.0.0
* Date:		24/02/2017
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;

$code = $params->get('code');
if(!empty($code)) :
	$code=ltrim($code,'<?php');
	$code=rtrim($code,'?>');
	echo eval($code);
endif;

// INCLUDE
$file = $params->get('phpFile');
if(!empty($file) && file_exists($file)) :
	if(strpos($file, 'http') === false) :
		require(JPATH_BASE.'/'.$file);
	else :
		echo '<p class="alert alert-danger">'.Jtext::_('MOD_BASEAPP_INCLUDE_ALERT').'</p>';
	endif;
endif;

?>
