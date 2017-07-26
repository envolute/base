<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base App
* Version:	1.0.0
* Date:		24/02/2017
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;
?>

<div class="baseApp">
	<?php

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

	$app = $params->get('app');

	if(!empty($app) && file_exists(JPATH_BASE.'/base-apps/'.$app.'/'.$app.'.php')) :

		// PARAMS
		// Config View
		// TAGS - evita conflito entre instâncias da mesma APP
		${$app.'AppTag'} = $params->get('appTag', $app);
		$appTag = ${$app.'AppTag'};
		${$appTag.'RelTag'} = $params->get('relTag');
		// LIST
		${$appTag.'ShowApp'} = $params->get('showApp');
		${$appTag.'ShowList'} = $params->get('showList');
		${$appTag.'ListModal'} = $params->get('listModal');
		${$appTag.'ListFull'} = $params->get('listFull');
		${$appTag.'StaticToolbar'} = $params->get('staticToolbar');
		${$appTag.'ShowAddBtn'} = $params->get('showAddBtn');
		${$appTag.'AddText'} = $params->get('addText');
		${$appTag.'hasUpload'} = $params->get('hasUpload');
		${$appTag.'RelTag'} = $params->get('relTag');
		${$appTag.'RelTable'} = $params->get('relTable');
		${$appTag.'RelNameId'} = $params->get('relNameId');
		${$appTag.'RelId'} = $params->get('relId');
		${$appTag.'AppNameId'} = $params->get('appNameId');
		${$appTag.'RelListId'} = $params->get('relListId');
		${$appTag.'RelListNameId'} = $params->get('relListNameId');
		${$appTag.'OnlyChildList'} = $params->get('onlyChildList');
		${$appTag.'FieldUpdated'} = $params->get('fieldUpdated');
		${$appTag.'TableField'} = $params->get('tableField');
		${$appTag.'HideParentField'} = $params->get('hideParentField');

		// APP
		require(JPATH_BASE.'/base-apps/'.$app.'/'.$app.'.php');

	endif;

	?>
</div>
