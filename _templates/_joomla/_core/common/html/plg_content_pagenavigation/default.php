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

<div class="pagenav row no-gutters py-4 b-top mt-5">
	<?php if ($row->prev) :
		$direction = $lang->isRTL() ? 'right' : 'left'; ?>
		<div class="col b-right">
			<a href="<?php echo $row->prev.$modal; ?>" rel="prev" class="d-block lh-1-2 text-lg">
				<span class="base-icon-left-big"></span> <?php echo  $row->prev_label; ?>
			</a>
		</div>
	<?php endif; ?>
	<?php if ($row->next) :
		$direction = $lang->isRTL() ? 'left' : 'right'; ?>
		<div class="col text-right b-left">
			<a href="<?php echo $row->next.$modal; ?>" rel="next" class="d-block lh-1-2 text-lg">
				<?php echo $row->next_label; ?>
				<span class="base-icon-right-big"></span>
			</a>
		</div>
	<?php endif; ?>
</div>
