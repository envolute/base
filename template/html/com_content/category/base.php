<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// JHtml::_('behavior.caption');

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/libraries/envolute/helpers/base.php');

?>

<div class="categoryBaseView" itemscope itemtype="https://schema.org/Blog">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h5 class="page-header"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h5>
	<?php elseif ($this->params->get('show_category_title', 1)) : ?>
		<h5 class="page-header"><?php echo $this->category->title; ?></h5>
	<?php endif; ?>

	<?php
	// CATEGORY DESCRIPTION & TAGS
	$categoryDesc = '';
	if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1) || ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags))) :
		$categoryDesc = '<div class="category-desc clearfix">';
			if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) :
					$categoryDesc .= '<img src="'.$this->category->getParams()->get('image').'" class="'.$this->params->get('description_image_class').'" alt="'.htmlspecialchars($this->category->getParams()->get('image_alt'), ENT_COMPAT, 'UTF-8').'"/>';
			endif;
			if ($this->params->get('show_description') && $this->category->description) :
					$categoryDesc .= JHtml::_('content.prepare', $this->category->description, '', 'com_content.category');
			endif;
			if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) :
				$this->category->tagLayout = new JLayoutFile('joomla.content.tags');
				$categoryDesc .= '<div class="category-tags">'.$this->category->tagLayout->render($this->category->tags->itemTags).'</div>';
			endif;
		$categoryDesc .= '</div>';
	endif;
	?>

	<?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
		<?php if ($this->params->get('show_no_articles', 1)) : ?>
			<p class="alert alert-warning">
				<?php echo $this->params->get('custom_no_articles', JText::_('COM_CONTENT_NO_ARTICLES')); ?>
			</p>
		<?php endif; ?>
	<?php else: ?>
		<?php
		// PHP CODE
		$code = $this->params->get('code');
		if(!empty($code)) :
			$code=ltrim($code,'<?php');
			$code=rtrim($code,'?>');
			echo eval($code);
		endif;
		?>
	<?php endif; ?>

	<?php $leadingcount = 0; ?>
	<?php if (!empty($this->lead_items)) : ?>
		<div class="items-leading clearfix">
			<?php
			$t = $this->params->get('lead_container', 'div');
			echo '<'.$t.' class="items '.$this->params->get('lead_container_class').' clearfix" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">';
			foreach ($this->lead_items as &$item) :
				if($item->state == 0) echo '<span class="system-unpublished">';
				$this->item = & $item;
				echo $this->loadTemplate('leading');
				if($item->state == 0) echo '</span>';
				$leadingcount++;
			endforeach;
			echo '</'.$t.'>';
			?>
		</div><!-- end items-leading -->
	<?php endif; ?>

	<?php
	$introcount = (count($this->intro_items));
	$counter = 0;
	?>

	<?php if (!empty($this->intro_items)) : ?>
		<div class="items-intro clearfix">
			<?php
			$t = $this->params->get('intro_container', 'div');
			echo '<'.$t.' class="items '.$this->params->get('intro_container_class').' clearfix">';
			foreach ($this->intro_items as $key => &$item) :
				if($item->state == 0) echo '<span class="system-unpublished">';
				$this->item = & $item;
				echo $this->loadTemplate('intro');
				if($item->state == 0) echo '</span>';
			endforeach;
			echo '</'.$t.'>';
			?>
		</div>
	<?php endif; ?>

	<?php // Add pagination links ?>
	<?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pt-3">
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
				<p class="list-stats small text-muted mb-1">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>
