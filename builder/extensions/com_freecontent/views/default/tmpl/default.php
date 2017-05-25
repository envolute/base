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

<div class="base-freecontent">
	<?php
	// page header
	if ($this->params->get('show_page_heading', 1) && $this->escape($this->params->get('page_heading'))) :
		echo '<h4 class="page-header">'.$this->escape($this->params->get('page_heading')).'</h4>';
	endif;

	// custom code
	if($this->params->get('css')) $doc->addStyleDeclaration($this->params->get('css'));
	if($this->params->get('script')) $doc->addScriptDeclaration($this->params->get('script'));
	$code = $this->params->get('code');
	if(!empty($code)) :
		$code=ltrim($code,'<?php');
		$code=rtrim($code,'?>');
		echo eval($code);
	endif;

	// custom file included
	$file = $this->params->get('include');
	if(!empty($file)) :
		if(strpos($file, 'http') === false) :
			require(JPATH_BASE.'/'.$file);
		else :
			echo '<p class="alert alert-danger">'.Jtext::_('COM_FREECONTENT_INCLUDE_ALERT').'</p>';
		endif;
	endif;

	// custom html content
	$html = $this->params->get('html');
	if(!empty($html)) echo $html;

	?>
</div>
