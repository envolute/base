<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.caption');
?>
<div class="categories-list <?php echo $this->pageclass_sfx;?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h4 class="page-header">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h4>
	<?php endif; ?>
	<?php
	if ($this->params->get('show_base_description')) :
		$category_desc = JHtml::_('content.prepare', $this->params->get('categories_description'), '', 'com_content.categories');
		if(!empty($category_desc)) :
	?>
			<div class="category-desc">
			<?php 	//If there is a description in the menu parameters use that; ?>
				<?php if($this->params->get('categories_description')) : ?>
					<p>
						<?php echo nl2br ($category_desc); ?>
					</p>
				<?php  else: ?>
					<?php //Otherwise get one from the database if it exists. ?>
					<?php  if ($this->parent->description) : ?>
							<?php  echo JHtml::_('content.prepare', $this->parent->description, '', 'com_content.categories'); ?>
					<?php  endif; ?>
				<?php  endif; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<ul class="list list-tree">
		<?php echo $this->loadTemplate('items'); ?>
	</ul>
</div>