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
JLoader::register('baseHelper', JPATH_BASE.'/libraries/envolute/helpers/base.php');
?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') !== '0' || $this->params->get('show_pagination_limit')) :?>
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
		<p class="alert alert-warning base-icon-attention"> <?php echo JText::_('COM_TAGS_NO_ITEMS'); ?></p>
	<?php else : ?>

	<div class="tag-items pt-3 clearfix">
		<?php foreach ($this->items as $i => $item) : ?>
			<?php if ($this->items[$i]->core_state == 0) : ?>
				<div class="tag-item system-unpublished">
			<?php else: ?>
				<div class="tag-item">
			<?php endif; ?>

				<?php $images  = json_decode($item->core_images);?>
				<?php if ($this->params->get('tag_list_show_item_image', 1) == 1 && (!empty($images->image_intro) || !empty($images->image_fulltext))) :?>
					<?php
					// Imagem de capa ou conteúdo
					$img = $images->image_intro;
					if(empty($img) && !empty($images->image_fulltext)) $img = $images->image_fulltext;
					// Para imagens remotas ou sem largura e altura definidas, será mostrada a imagem original...
					$imgFile = baseHelper::thumbnail(htmlspecialchars($img), '95', '80');
					?>
					<a href="<?php echo JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
						<img class="img-fluid float-left mr-3 mb-1" src="<?php echo $imgFile;?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>">
					</a>
				<?php endif; ?>
				<h5 class="tag-item-title m-0 strong">
				<?php if (($item->type_alias == 'com_users.category') || ($item->type_alias == 'com_banners.category')) : ?>
					<?php echo $this->escape($item->core_title); ?>
				<?php else: ?>
					<a href="<?php echo JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
						<?php echo $this->escape($item->core_title); ?>
					</a>
				<?php endif; ?>
				</h5>
				<?php if ($this->params->get('tag_list_show_item_description', 1)) : ?>
					<div class="tag-item-description pt-1 lh-1-2">
						<?php
						$intro =  JHtml::_('string.truncate', strip_tags($item->core_body), $this->params->get('tag_list_item_maximum_characters'));
						echo trim(preg_replace('/\s*\{[^}]*\}/', '', $intro));
						?>
					</div>
				<?php endif; ?>

				<?php if ($this->params->get('tag_list_show_date')) : ?>
					<div class="tag-item-date pt-1 small text-muted">
						<?php
							echo JHtml::_('date', $item->displayDate, $this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3'))));
						?>
					</div>
				<?php endif; ?>

			</div>
			<hr />
		<?php endforeach; ?>
	</div>

	<?php if ($this->params->get('show_pagination')) : ?>
		<div class="py-3">
			<?php if ($this->params->get('show_pagination_results', 1) && $this->pagination->getPagesCounter()) : ?>
				<p class="list-stats small text-muted mb-1">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</form>

<?php endif; ?>
