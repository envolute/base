<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="panel panel-default bottom-space">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo JText::_('COM_CONFIG_SEO_SETTINGS'); ?></h3>
	</div>
	<div class="panel-body">
		<?php
		foreach ($this->form->getFieldset('seo') as $field):
		?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
		<?php
		endforeach;
		?>
	</div>
</div>