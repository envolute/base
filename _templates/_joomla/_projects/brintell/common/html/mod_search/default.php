<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="search">
    <form class="form-search" action="<?php echo JRoute::_('index.php');?>" method="post" class="form-inline">
	<?php
	$class = $place = '';
	if(!$button){
		$class = ' nobtn';
		$place = ' placeholder="'.$text.'"';
	}

	$input = '<input type="search" name="searchword" id="mod-search-searchword" maxlength="50" class="form-control form-control-sm field-search'.$class.'"'.$place.' />';

	if($button){
		$btn ='<span class="input-group-btn"><button type="submit" class="btn btn-primary">'.$button_text.'</button></span>';
		$append = '<div class="input-group">'.$input.$btn.'</div>';
		$prepend = '<div class="input-group">'.$btn.$input.'</div>';
	} else {
		$append = $prepend = $input;
	}

	echo ($button_pos == 'bottom' || $button_pos == 'left') ? $prepend : $append;
	?>
    	<input type="hidden" name="task" value="search" />
    	<input type="hidden" name="option" value="com_search" />
    	<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
    </form>
</div>
