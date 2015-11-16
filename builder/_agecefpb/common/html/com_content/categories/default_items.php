<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$class = ' class="first"';
JHtml::_('behavior.tooltip');

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
?>
	<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
		
		<?php if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) : ?>
			<li id="cat-<?php echo $item->id; ?>">
				<h3 class="item-title">
					<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));?>">
						<?php echo $this->escape($item->title); ?>
					</a>
					<?php if (count($item->getChildren()) > 0) : ?>
						<a href="#category-<?php echo $item->id;?>" data-toggle="collapse" class="btn btn-primary btn-xs pull-right">
							<i class="glyphicon glyphicon-resize-vertical"></i>
						</a>
					<?php endif;?>
				</h3>
				
				<?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
					<small class="category-num-itens">
						<?php echo JText::_('COM_CONTENT_NUM_ITEMS').' '.$item->numitems; ?>
					</small>
				<?php endif; ?>
					
				<?php if ($this->params->get('show_subcat_desc_cat') == 1 && $item->description) :?>
					<div class="category-desc">
						<?php echo JHtml::_('content.prepare', $item->description, '', 'com_content.categories'); ?>
					</div>
				<?php endif; ?>
		
				<?php if (count($item->getChildren()) > 0) :?>
					<ul class="collapse fade in" id="category-<?php echo $item->id;?>">
						<?php
						$this->items[$item->id] = $item->getChildren();
						$this->parent = $item;
						$this->maxLevelcat--;
						echo $this->loadTemplate('items');
						$this->parent = $item->getParent();
						$this->maxLevelcat++;
						?>
					</ul>
				<?php
				endif; ?>
		
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>