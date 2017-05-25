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
// Do we really have to make it so people can see unpublished tags???
$canEdit = $user->authorise('core.edit', 'com_tags');
$canCreate = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');
$items = $this->items;
$n = count($this->items);

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');
?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') !== '0' || $this->params->get('show_pagination_limit')) :?>
	<fieldset class="filters btn-toolbar well well-sm">
		<?php if ($this->params->get('filter_field') != 'hide') :?>
			<div>
				<label class="filter-search-lbl element-invisible" for="filter-search">
					<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL').'&#160;'; ?>
				</label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_TAGS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>" />
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div class="btn-group pull-right">
				<label for="limit" class="element-invisible">
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
	<?php endif; ?>

	<?php if ($this->items == false || $n == 0) : ?>
		<p> <?php echo JText::_('COM_TAGS_NO_ITEMS'); ?></p>
	<?php else : ?>

	<ul class="list list-expanded clearfix">
		<?php foreach ($this->items as $i => $item) : ?>
			<?php if ($this->items[$i]->core_state == 0) : ?>
			<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
			<?php else: ?>
			<li class="cat-list-row<?php echo $i % 2; ?>" >
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
			
				<?php $images  = json_decode($item->core_images);?>
				<?php if ($this->params->get('tag_list_show_item_image', 1) == 1 && !empty($images->image_intro)) :?>
					<div class="item-image pull-left right-space-sm">
						<a href="<?php echo JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
							<?php $img = baseHelper::thumbnail(htmlspecialchars($images->image_intro),'100','80');?>
							<img src="<?php echo $img;?>" class="img-responsive" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>">
						</a>
					</div>
				<?php endif; ?>
				
				<h3 class="item-title">
					<a href="<?php echo JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
						<?php echo $this->escape($item->core_title); ?>
					</a>
				</h3>
				
				<?php if ($this->params->get('tag_list_show_item_description', 1)) : ?>
					<span class="tag-body item-intro">
						<?php
						$intro =  JHtml::_('string.truncate', strip_tags($item->core_body), $this->params->get('tag_list_item_maximum_characters'));
						echo trim(preg_replace('/\s*\{[^}]*\}/', '', $intro));
						?>
					</span>
				<?php endif; ?>

			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ($this->params->get('show_pagination')) : ?>
	 <div class="pagination">
		<?php if ($this->params->get('show_pagination_results', 1) && $this->pagination->getPagesCounter()) : ?>
			<p class="counter">
				<span class="label label-default"><?php echo $this->pagination->getPagesCounter(); ?></label>
			</p>
		<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		</br>
	<?php endif; ?>
</form>

<?php endif; ?>