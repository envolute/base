<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_CONTACT_NO_ARTICLES'); ?>	 </p>
<?php else : ?>

	<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit')) :?>
	<fieldset class="filters btn-toolbar">
		<?php if ($this->params->get('filter_field') != 'hide') :?>
			<div class="btn-group">
				<label class="filter-search-lbl element-invisible" for="filter-search"><span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span><?php echo JText::_('COM_CONTACT_FILTER_LABEL').'&#160;'; ?></label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC'); ?>" />
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
	</fieldset>
	<?php endif; ?>

		<ul class="list">
			<?php foreach ($this->items as $i => $item) : ?>

				<?php if (in_array($item->access, $this->user->getAuthorisedViewLevels())) : ?>
					<?php if ($this->items[$i]->published == 0) : ?>
						<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
					<?php else: ?>
						<li class="cat-list-row<?php echo $i % 2; ?>" >
					<?php endif; ?>
	
						<h4 class="list-title strong">
							<a href="<?php echo JRoute::_(ContactHelperRoute::getContactRoute($item->slug, $item->catid)); ?>">
								<span class="glyphicon glyphicon-user small"></span> <?php echo $item->name; ?></a>
							<?php if ($this->items[$i]->published == 0) : ?>
								<span class="glyphicon glyphicon-user small"></span> <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
							<?php endif; ?>
							
							<?php if ($this->params->get('show_position_headings') && !empty($item->con_position)) : ?>
									<small>(<?php echo $item->con_position; ?>)</small>
							<?php endif; ?>
	
						</h4>
						<div class="small">
							<?php if ($this->params->get('show_email_headings')) : ?>
									<?php echo $item->email_to; ?><br />
							<?php endif; ?>
							<?php if ($this->params->get('show_suburb_headings') AND !empty($item->suburb)) : ?>
								<?php echo $item->suburb . ', '; ?>
							<?php endif; ?>
		
							<?php if ($this->params->get('show_state_headings') AND !empty($item->state)) : ?>
								<?php echo $item->state . ', '; ?>
							<?php endif; ?>
		
							<?php if ($this->params->get('show_country_headings') AND !empty($item->country)) : ?>
								<?php echo $item->country; ?>
							<?php endif; ?>
						</div>

						<?php 
						if (
						($this->params->get('show_telephone_headings') AND !empty($item->telephone)) ||
						($this->params->get('show_mobile_headings') AND !empty ($item->mobile)) ||
						($this->params->get('show_fax_headings') AND !empty($item->fax))
						) : ?>
							<h4 class="strong">
								<?php if (!empty($item->telephone) || !empty($item->mobile)) : ?>
									<span class="glyphicon glyphicon-phone-alt"></span>&nbsp;
								<?php endif; ?>
								<?php echo (!empty($item->telephone)) ? $item->telephone : ''; ?>
								<?php echo (!empty($item->telephone) && !empty($item->mobile)) ? ' / ' : ''; ?>
								<?php echo (!empty($item->mobile)) ? $item->mobile : ''; ?>
								<?php echo (!empty($item->fax)) ? '(fax: '.$item->fax.')' : ''; ?>
							</h4>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>

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
		<div>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		</div>
</form>
<?php endif; ?>