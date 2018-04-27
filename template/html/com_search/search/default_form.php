<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// JHtml::_('bootstrap.tooltip');

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();
?>
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post">

	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
				<div class="input-group">
					<input type="text" name="searchword" class="form-control form-control-lg" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" />
					<div class="input-group-btn">
						<button name="Search" onclick="this.form.submit()" class="btn btn-default btn-lg hasTooltip" data-animation="false" title="<?php echo JHtml::tooltipText('COM_SEARCH_SEARCH');?>">
							<span class="base-icon-search"></span>
						</button>
					</div>
				</div>
				<input type="hidden" name="task" value="search" />
			</div>
		</div>
		<?php if ($this->params->get('search_areas', 1)) : ?>
			<div class="col-lg">
				<label class="d-block font-weight-bold m-0"><?php echo JText::_('COM_SEARCH_SEARCH_ONLY');?></label>
				<?php foreach ($this->searchareas['search'] as $val => $txt) :
					$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
				?>
				<div class="form-check form-check-inline">
					<label for="area-<?php echo $val;?>" class="form-check-label">
						<input type="checkbox" name="areas[]" class="form-check-input" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> >
						<?php echo JText::_($txt); ?>
					</label>
				</div>
				<?php endforeach; ?>
				<div class="h-sm hidden-lg-up clear"></div>
			</div>
		<?php endif; ?>
	</div>
	<div class="row align-items-end">
		<?php if ($this->params->get('search_phrases', 1)) : ?>
			<div class="col-lg">
				<div class="phrases-box">
					<?php echo $this->lists['searchphrase']; ?>
				</div>
			</div>
			<div class="col-lg">
				<div class="ordering-box text-lg-right">
					<?php echo $this->lists['ordering'];?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<hr />

	<?php if ($this->total > 0) : ?>
		<div class="form-limit float-right">
			<label for="limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<p class="counter text-muted m-0">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
	<?php endif; ?>

	<?php if (!empty($this->searchword)):?>
	<p class="searchintro clearfix"><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="badge badge-info">' . $this->total . '</span>');?></p>
	<?php endif;?>

</form>
