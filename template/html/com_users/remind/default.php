<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
?>
<div class="remind<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h5 class="page-header">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h5>
	<?php endif; ?>

	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="form-validate">
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
		<fieldset>
			<p class="alert alert-info">
				<span class="base-icon-info-circled"></span> <?php echo JText::_($fieldset->label); ?>
			</p>
			<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field) : ?>
				<div class="form-group">
					<div><?php echo $field->label; ?></div>
					<?php echo $field->input; ?>
				</div>
			<?php endforeach; ?>
		</fieldset>
		<?php endforeach; ?>
		<button type="submit" class="btn btn-primary validate"><?php echo JText::_('JSUBMIT'); ?></button>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
