<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_breadcrumbs
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<ul class="breadcrumb">
<?php if ($params->get('showHere', 1))
	{
		echo '<li class="active"><span class="base-icon-location hasTooltip" title="' .JText::_('MOD_BREADCRUMBS_HERE').'"></span></li>';
	}
?>
<?php for ($i = 0; $i < $count; $i ++) :

	// desabilita visualização do link para 'publicações'
	$posts = JURI::base(true).'/posts';
	if($list[$i]->link != JURI::base(true).'/posts') :

		// Workaround for duplicate Home when using multilanguage
		if ($i == 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link == $list[$i - 1]->link)
		{
			continue;
		}
		// If not the last item in the breadcrumbs add the separator
		echo '<li>';
		if ($i < $count - 1)
		{
			if (!empty($list[$i]->link)) {
				echo '<a href="'.$list[$i]->link.'" class="pathway">'.$list[$i]->name.'</a>';
			} else {
				echo '<span>'.$list[$i]->name,$limit.'</span>';
			}
			//if ($i < $count - 2) { echo '<span class="divider">/</span>'; }

		}  elseif ($params->get('showLast', 1)) { // when $i == $count -1 and 'showLast' is true
			//if($i > 0){ echo '<span class="divider">/</span>'; }
			echo '<span>'.$list[$i]->name,$limit.'</span>';
		}
		echo '</li>';

	endif;

endfor; ?>
</ul>
