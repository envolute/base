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

// IMPORTANTE
// A diferença para a view 'Base' é que aqui não existem as informações exclusivas sobre a categoria
?>
<div class="categoryBaseView" itemscope itemtype="https://schema.org/Blog">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h4 class="page-header"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h4>
	<?php endif; ?>

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
