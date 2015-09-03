<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;
?>

<ul class="list">
	<?php foreach ($this->link_items as &$item) : ?>
		<li>
			<?php if ($this->params->get('show_publish_date') && $this->params->get('intro_show_date')) : ?>
				<small class="list-date">
					<?php 
					echo substr(JText::sprintf(JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC4'))),0,5);
					// "SUBSTR" -> esconder o ano na data '00.00.00' -> '00.00'
					?>
				</small>
				<i class="icon-arrow-right-3"></i>
			<?php endif; ?>
			<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>">
				<?php echo $item->title; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>