<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$params = $this->params;
?>

<div id="archive-items">
	<?php foreach ($this->items as $i => $item) : ?>
		<?php $info = $item->params->get('info_block_position', 0); ?>
		<div class="row<?php echo $i % 2; ?>">

			<?php if (($params->get('show_parent_category') && !empty($this->item->parent_slug)) || $params->get('show_category')) : ?>
				<div class="category-info">
					<?php if ($params->get('show_parent_category') && !empty($this->item->parent_slug)) : ?>
						<span class="parent-category-name">
							<?php $title = $this->escape($this->item->parent_title);
							$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>';?>
							<?php if ($params->get('link_parent_category') && !empty($this->item->parent_slug)) : ?>
								<?php echo $url; ?>
							<?php else : ?>
								<?php echo $title; ?>
							<?php endif; ?>
						</span>
						<?php if ($params->get('show_category')) echo '<span class="category-separator"> &raquo; </span>'; ?>
					<?php endif; ?>
					<?php if ($params->get('show_category')) : ?>
						<span class="category-name">
							<?php $title = $this->escape($this->item->category_title);
							$url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)) . '">' . $title . '</a>';?>
							<?php if ($params->get('link_category') && $this->item->catslug) : ?>
								<?php echo $url; ?>
							<?php else : ?>
								<?php echo $title; ?>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="page-header">
				<h2>
					<?php if ($params->get('link_titles')) : ?>
						<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug)); ?>"> <?php echo $this->escape($item->title); ?></a>
					<?php else: ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
				</h2>
				<?php if ($params->get('show_publish_date')) : ?>
					<small class="date-published">
						<span class="published">
							<span class="glyphicon glyphicon-calendar"></span> <?php echo JText::sprintf(JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC3'))); ?>
						</span>
						<?php if ($params->get('show_author')) echo ''; ?>
					</small>
				<?php endif; ?>
		
				<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
					<small class="createdby">
						<span class="glyphicon glyphicon-user"></span> 
						<?php $author = $this->item->author; ?>
						<?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author); ?>
						<?php if (!empty($this->item->contactid ) && $params->get('link_author') == true) : ?>
							<?php
							echo JText::sprintf(
							'COM_CONTENT_WRITTEN_BY',
							JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id=' . $this->item->contactid), $author)
							); ?>
						<?php else :?>
							<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
						<?php endif; ?>
					</small>
				<?php endif; ?>
				
				<?php if ($params->get('show_hits')) : ?>
					<small class="hits">
						<span class="glyphicon glyphicon-eye-open"></span> <?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
					</small>
				<?php endif; ?>
			</div>

		<?php if ($params->get('show_intro')) :?>
			<div class="intro"> <?php echo JHtml::_('string.truncate', $item->introtext, $params->get('introtext_limit')); ?> </div>
		<?php endif; ?>

	</div>
	<?php endforeach; ?>
</div>
<?php if ($this->params->get('show_pagination')) : ?>
<div class="text-center">
	<?php if ($this->params->def('show_pagination_results', 1) && $this->pagination->getPagesCounter()) : ?>
	<p class="counter">
		<span class="label label-default"><?php echo $this->pagination->getPagesCounter(); ?></label>
	</p>
	<?php endif; ?>
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php endif; ?>