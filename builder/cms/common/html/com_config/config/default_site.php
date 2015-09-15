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

<h4 class="page-header"><span class="base-icon-cog"></span> <?php echo JText::_('COM_CONFIG_SITE_SETTINGS'); ?></h4>
<fieldset>
	<?php
	foreach ($this->form->getFieldset('site') as $field):
	?>
		<div class="form-group">
			<div><?php echo $field->label; ?></div>
			<?php echo $field->input; ?>
		</div>
	<?php
	endforeach;
	?>
</fieldset>