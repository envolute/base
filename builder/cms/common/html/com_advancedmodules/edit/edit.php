<?php
/**
 * @package         Advanced Module Manager
 * @version         4.22.7
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

JHtml::_('bootstrap.framework');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.combobox');
JHtml::_('formbehavior.chosen', 'select');

$hasContent = empty($this->item->module) || isset($this->item->xml->customContent);
$hasContentFieldName = 'content';

// For a later improvement
if ($hasContent)
{
	$hasContentFieldName = 'content';
}

// Get Params Fieldsets
$this->fieldsets = $this->form->getFieldsets('params');
$this->hidden_fields = '';

$script = "
Joomla.submitbutton = function(task)
{
	if (task == 'module.cancel' || document.formvalidator.isValid(document.id('module-form'))) {";
if ($hasContent)
{
	$script .= $this->form->getField($hasContentFieldName)->save();
}
$script .= "	Joomla.submitform(task, document.getElementById('module-form'));
				if (self != top)
				{
					window.top.setTimeout('window.parent.SqueezeBox.close()', 1000);
				}
			}
	};";
if (JFactory::getUser()->authorise('core.admin'))
{
	$script .= "
	jQuery(document).ready(function()
	{
		// add alert on remove assignment buttons
		jQuery('button.nn_remove_assignment').click(function()
		{
			if(confirm('" . str_replace('<br />', '\n', addslashes(JText::_('AMM_DISABLE_ASSIGNMENT'))) . "')) {
				jQuery('div#toolbar-options button').click();
			}
		});
	});";
}

JFactory::getDocument()->addScriptDeclaration($script);
nnFrameworkFunctions::addScriptVersion(JURI::root(true) . '/media/nnframework/js/script.min.js');
nnFrameworkFunctions::addScriptVersion(JURI::root(true) . '/media/nnframework/js/toggler.min.js');

//JFactory::getDocument()->addStyleSheetVersion(JURI::root(true) . '/media/nnframework/css/frontend.min.css');
?>

<form action="<?php echo JRoute::_('index.php?option=com_advancedmodules&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="module-form" class="form-validate edit">

	<!-- Begin Content -->
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary"
				onclick="Joomla.submitbutton('module.apply')">
				<i class="icon-apply"></i>
				<?php echo JText::_('JAPPLY') ?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-primary"
				onclick="Joomla.submitbutton('module.save')">
				<i class="icon-save"></i>
				<?php echo JText::_('JSAVE') ?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn"
				onclick="Joomla.submitbutton('module.cancel')">
				<i class="icon-cancel"></i>
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
	</div>
	
	<h4 class="page-header">
	
		<span class="base-icon-cog"></span> <?php echo JText::sprintf('AMM_MODULE_EDIT', $this->item->title); ?> 
		<div class="pull-right clear-float-xs">
			<span class="label label-primary"><?php echo $this->item->module ?></span>
		</div>
	</h4>
			
	<div class="row bottom-space">
		<div class="col-md-9">
			<div class="clearfix">
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('title'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('title'); ?>
					</div>
				</div>

				<?php
				if ($hasContent)
				{
					//echo '<hr />' . $this->form->getInput($hasContentFieldName) . '<hr />';
					echo '<hr class="clear" />' . $this->form->getInput($hasContentFieldName);
				}
				$this->fieldset = 'basic';
				$html = JLayoutHelper::render('joomla.edit.fieldset', $this);
				echo $html;
				?>
			</div>
		</div>
		<div class="col-md-3">
			<div class="clearfix">
				<?php echo $this->form->getControlGroup('showtitle'); ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('position'); ?>
					</div>
					<div class="controls">
						<?php echo $this->loadTemplate('positions'); ?>
					</div>
				</div>
				<?php
				// Set main fields.
				$this->fields = array(
					'published',
					'access',
					'ordering',
					'note'
				);
				?>
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				<fieldset>
					<?php if ($this->item->client_id == 0) : ?>
						<?php echo $this->render($this->assignments, 'pre_post_html'); ?>
					<?php endif; ?>
					<?php if ($this->item->client_id == 0 && $this->config->show_hideempty) : ?>
						<?php echo $this->render($this->assignments, 'hideempty'); ?>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
	</div>

	<?php echo JHtml::_('bootstrap.startAccordion', 'collapseTypes'); ?>

	<?php if ($this->item->client_id == 0) : ?>
		<?php echo JHtml::_('bootstrap.addSlide', 'moduleSlide', JText::_('AMM_ASSIGNMENTS'), 'assignment'); ?>
		<?php echo $this->loadTemplate('assignment'); ?>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
	<?php endif; ?>

	<div class="form-horizontal">
		<?php
		$this->fieldsets = array();
		$this->ignore_fieldsets = array('basic', 'description');
		echo JLayoutHelper::render('joomla.edit.params', $this);
		?>
	</div>

	<?php echo JHtml::_('bootstrap.endAccordion'); ?>

	<div class="btn-group">
		<button type="button" class="btn btn-primary"
			onclick="Joomla.submitbutton('module.apply')">
			<i class="icon-apply"></i>
			<?php echo JText::_('JAPPLY') ?>
		</button>
	</div>
	<div class="btn-group">
		<button type="button" class="btn btn-primary"
			onclick="Joomla.submitbutton('module.save')">
			<i class="icon-save"></i>
			<?php echo JText::_('JSAVE') ?>
		</button>
	</div>
	<div class="btn-group">
		<button type="button" class="btn"
			onclick="Joomla.submitbutton('module.cancel')">
			<i class="icon-cancel"></i>
			<?php echo JText::_('JCANCEL') ?>
		</button>
	</div>
	<!-- End Content -->


	<?php echo $this->hidden_fields; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="current" value="<?php echo base64_encode('index.php?option=com_advancedmodules&layout=edit&id=' . (int) $this->item->id); ?>" />
	<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return', null, 'base64'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $this->form->getInput('module'); ?>
	<?php echo $this->form->getInput('client_id'); ?>
</form>