<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.combobox');
JHtml::_('formbehavior.chosen', 'select');

$hasContent = empty($this->item['module']) || $this->item['module'] == 'custom' || $this->item['module'] == 'mod_custom';

// If multi-language site, make language read-only
if (JLanguageMultilang::isEnabled())
{
	$this->form->setFieldAttribute('language', 'readonly', 'true');
}

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'config.cancel.modules' || document.formvalidator.isValid(document.getElementById('modules-form')))
		{
			Joomla.submitform(task, document.getElementById('modules-form'));
		}
	}
");
?>

<form action="<?php echo JRoute::_('index.php?option=com_config'); ?>" method="post" name="adminForm" id="modules-form" class="form-validate edit">
	
	<!-- Begin Content -->
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary"
				onclick="Joomla.submitbutton('config.save.modules.apply')">
				<i class="icon-apply"></i>
				<?php echo JText::_('JAPPLY') ?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-primary"
				onclick="Joomla.submitbutton('config.save.modules.save')">
				<i class="icon-save"></i>
				<?php echo JText::_('JSAVE') ?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn"
				onclick="Joomla.submitbutton('config.cancel.modules')">
				<i class="icon-cancel"></i>
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
	</div>
	
	<h4 class="page-header">
		<span class="base-icon-cog"></span> <?php echo JText::_('COM_CONFIG_MODULES_SETTINGS_TITLE'); ?>: <em><?php echo $this->item['title'] ?></em> 
		<div class="pull-right clear-float-xs">
			<span class="label label-primary"><?php echo $this->item['module'] ?></span>
		</div>
	</h4>
			
	<div class="row">
		<div class="col-md-6">
			<div class="clearfix">
				<div class="form-group pull-left right-space">
					<div class="control-label">
						<?php echo $this->form->getLabel('title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('title'); ?>
					</div>
				</div>
	
				<div class="form-group pull-left right-space">
					<div class="control-label">
						<?php echo $this->form->getLabel('showtitle'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('showtitle'); ?>
					</div>
				</div>
			</div>
			<?php
			if (JFactory::getUser()->authorise('core.edit.state', 'com_modules.module.' . $this->item['id'])): ?>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
			<?php endif ?>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('position'); ?>
				</div>
				<div class="controls">
					<?php echo $this->loadTemplate('positions'); ?>
				</div>
			</div>
			<hr />
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('publish_up'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('publish_up'); ?>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('publish_down'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('publish_down'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div class="form-group pull-left right-space">
					<div class="control-label">
						<?php echo $this->form->getLabel('access'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('access'); ?>
					</div>
				</div>
				<div class="form-group pull-left right-space">
					<div class="control-label">
						<?php echo $this->form->getLabel('ordering'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('ordering'); ?>
					</div>
				</div>
	
				<div class="form-group pull-left right-space">
					<div class="control-label">
						<?php echo $this->form->getLabel('language'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('language'); ?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('note'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('note'); ?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div id="options">
				<?php echo $this->loadTemplate('options'); ?>
			</div>
			<?php if ($hasContent): ?>
			<div class="tab-pane" id="custom">
				<?php echo $this->form->getInput('content'); ?>
			</div>
			<?php endif; ?>
		</div>

		<input type="hidden" name="id" value="<?php echo $this->item['id'];?>" />
		<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return', null, 'base64');?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>

	</div>

	<!-- End Content -->

</form>