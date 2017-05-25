<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework');

$n			= count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (($this->params->get('filter_field') != 'hide' && $this->params->get('filter_field') !== '0') || $this->params->get('show_pagination_limit')) :?>
		<fieldset class="fieldset-embed p-2">
			<?php if ($this->params->get('filter_field') != 'hide') :?>
				<div class="float-left">
					<label class="filter-search-lbl" hidden for="filter-search">
						<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL').'&#160;'; ?>
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
			<div class="clearfix"></div>
		</fieldset>
	<?php else :?>
		<hr class="mt-2 mb-3" />
	<?php endif; ?>

	<?php if ($this->items == false || $n == 0) : ?>
		<p class="alert alert-warning base-icon-attention"> <?php echo JText::_('COM_TAGS_NO_ITEMS'); ?></p></div>
	<?php else : ?>
		<ul class="list-unstyled bordered list-lg clearfix">
			<?php foreach ($this->items as $i => $item) : ?>
				<?php if ($this->items[$i]->core_state == 0) : ?>
				 <li class="system-unpublished">
				<?php else: ?>
					<li>
				<?php endif; ?>

					<?php if ($this->params->get('tag_list_show_date')) : ?>
						<span class="list-date small">
							<?php
							echo JHtml::_(
								'date', $item->displayDate,
								$this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))
							); ?>
						</span>
					<?php endif; ?>

					<h5 class="tag-item-title m-0 strong">
						<a href="<?php echo JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
							<?php echo $this->escape($item->core_title); ?>
						</a>
						<?php if ($item->core_state == 0) : ?>
							<span class="list-published badge badge-warning">
								<?php echo JText::_('JUNPUBLISHED'); ?>
							</span>
						<?php endif; ?>
					</h5>

				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</form>
