<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.pagenavigation
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$lang = JFactory::getLanguage();

$modal = ($_GET['ml']) ? '?ml=1' : '';
?>

<ul class="pager pagenav">
<?php if ($row->prev) :
	$direction = $lang->isRTL() ? 'right' : 'left'; ?>
	<li class="previous">
		<a href="<?php echo $row->prev.$modal; ?>" rel="prev" class="hasTooltip" data-position="fixed" data-placement="right" data-title="<div class='all-expand tam5'><?php echo $row->prev_label; ?></div>">
			<?php echo  $row->prev_label; ?>
		</a>
	</li>
<?php endif; ?>
<?php if ($row->next) :
	$direction = $lang->isRTL() ? 'left' : 'right'; ?>
	<li class="next">
		<a href="<?php echo $row->next.$modal; ?>" rel="next" class="hasTooltip" data-position="fixed" data-placement="left" data-title="<div class='all-expand tam5'><?php echo $row->next_label; ?></div>">
			<?php echo $row->next_label; ?>
		</a>
	</li>
<?php endif; ?>
</ul>
