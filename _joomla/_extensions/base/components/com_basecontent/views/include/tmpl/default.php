<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base Content
* Version:	1.0.0
* Date:		24/02/2017
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;
?>

<div class="baseContent">
	<?php

	// page header
	if ($this->params->get('show_page_heading', 1) && $this->escape($this->params->get('page_heading'))) :
		echo '<h4 class="page-header">'.$this->escape($this->params->get('page_heading')).'</h4>';
	endif;

	if(!empty($this->code)) :
		$code=ltrim($this->code,'<?php');
		$code=rtrim($code,'?>');
		echo eval($code);
	endif;

	// INCLUDE
	if(!empty($this->phpFile) && file_exists($this->phpFile)) :
		if(strpos($this->phpFile, 'http') === false) :
			require(JPATH_BASE.'/'.$this->phpFile);
		else :
			echo '<p class="alert alert-danger">'.Jtext::_('COM_BASECONTENT_INCLUDE_ALERT').'</p>';
		endif;
	endif;

	?>
</div>
