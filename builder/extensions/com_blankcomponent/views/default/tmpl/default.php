<?php
/**
* Author:	Omar Muhammad
* Email:	admin@omar84.com
* Website:	http://omar84.com
* Component:Blank Component
* Version:	3.0.0
* Date:		03/11/2012
* copyright	Copyright (C) 2012 http://omar84.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/
// no direct access
defined('_JEXEC') or die;
?>
<div class="blank">

<?php if ($this->params->get('show_page_heading', 1) && $this->escape($this->params->get('page_heading'))) : ?>
<h4 class="page-header">
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h4>
<?php endif; ?>

<?php
// "url" params
if(!empty($this->urlParams)) :
	$vars = explode('&', $this->urlParams);
	for($i = 0; $i < count($vars); $i++) eval('$'.$vars[$i].';');
endif;

// custom file included
if(!empty($this->include)) :
	if(strpos($this->include, 'http') === false) :
		require(JPATH_BASE.'/'.$this->include);
	else :
		echo '<p class="alert alert-danger"><strong>'.JText::_('COM_BLANKCOMPONENT').':</strong><br />'.JText::_('COM_BLANKCOMPONENT_FILE_ALERT').'</p>';
	endif;
endif;
?>

</div>
