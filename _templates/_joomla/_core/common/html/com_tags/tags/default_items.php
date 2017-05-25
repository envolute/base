<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

// Get the user object.
$user = JFactory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
$canEdit = $user->authorise('core.edit', 'com_tags');
$canCreate = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');

$columns = $this->params->get('tag_columns', 1);

// Avoid division by 0 and negative columns.
if ($columns < 1)
{
	$columns = 1;
}

$bsspans = floor(12 / $columns);

if ($bsspans < 1)
{
	$bsspans = 1;
}

$bscolumns = min($columns, floor(12 / $bsspans));
$n = count($this->items);
JFactory::getDocument()->addScriptDeclaration("
		var resetFilter = function() {
		document.getElementById('filter-search').value = '';
	}
");
?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (($this->params->get('filter_field') != 'hide' && $this->params->get('filter_field') !== '0') || $this->params->get('show_pagination_limit')) : ?>
	<fieldset class="fieldset-embed p-2">
		<?php if ($this->params->get('filter_field') !== '0') : ?>
			<div class="float-left">
				<label class="filter-search-lbl" hidden for="filter-search">
					<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL') . '&#160;'; ?>
				</label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="field-search" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_TAGS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>" />
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div class="btn-group float-right">
				<label for="limit" hidden>
					<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		<?php endif; ?>

		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
		<input type="hidden" name="task" value="" />
	</fieldset>
	<?php endif; ?>

	<?php if ($this->items == false || $n == 0) : ?>
		<p class="alert alert-warning base-icon-attention"><?php echo JText::_('COM_TAGS_NO_TAGS'); ?></p>
	<?php else : ?>
		<div class="row">
			<?php foreach ($this->items as $i => $item) : ?>
				<?php if ((!empty($item->access)) && in_array($item->access, $this->user->getAuthorisedViewLevels())) : ?>
					<div class="col-sm-6 col-md-4 col-lg-3 col-xs-2">
						<h5 class="base-icon-tags">
							<a href="<?php echo JRoute::_(TagsHelperRoute::getTagRoute($item->id . '-' . $item->alias)); ?>">
								<?php echo $this->escape($item->title); ?>
							</a>
						</h5>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php // Add pagination links ?>
		<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
			<div class="pt-3">
				<?php if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="list-stats small text-muted mb-1">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	<?php endif;?>
</form>
