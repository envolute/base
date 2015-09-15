<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

JHtml::_('bootstrap.tooltip');
?>
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post">
	<div class="well">
		<div class="row">				
			<div class="col-sm-8 bottom-expand">
				<div class="input-group">
					<input type="text" name="searchword" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" maxlength="150" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox form-control input-lg" /></label>
					<span class="input-group-btn">
						<button name="Search" onclick="this.form.submit()" class="btn btn-lg btn-primary">
							<?php echo JText::_('COM_SEARCH_SEARCH');?>
						</button>
					</span>
				</div>
			</div>
			<div class="col-sm-4">
				<input type="hidden" name="task" value="search" />
				<?php echo $this->lists['ordering'];?>
			</div>
		
		</div>
		
		<div class="row top-expand">
			<?php
			$span = '12';
			$legend = '';
			if ($this->params->get('search_areas', 1)) :
				$span = '6';
				$legend = '<legend>'.JText::_('COM_SEARCH_FOR').'</legend>';
			endif;
			?>
			<fieldset class="form-inline col-sm-<?php echo $span; ?>">
				<?php echo $legend; ?>
				<div class="phrases-box">
					<?php echo $this->lists['searchphrase']; ?>
				</div>
			</fieldset>
		
			<?php if ($this->params->get('search_areas', 1)) : ?>
				<fieldset class="form-inline col-sm-6">
					<legend><?php echo JText::_('COM_SEARCH_SEARCH_ONLY');?></legend>
					<?php foreach ($this->searchareas['search'] as $val => $txt) :
						$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
					?>
						<label for="area-<?php echo $val;?>" class="checkbox">
							<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> >
							<?php echo JText::_($txt); ?>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		
		</div>
	</div>
		
	<div class="clear"></div>
	
	<div class="row">
	<?php if (!empty($this->searchword)):?>
		<div class="col-sm-8">
			<h4 class="searchintro">
				<?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="label label-info">'. $this->total. '</span>');?>
			</h4>
		</div>
		<?php if ($this->total > 0) : ?>
			<div class="col-md-4 form-limit text-right">
				<span class="label label-info"><?php echo $this->pagination->getPagesCounter(); ?></span> 
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		<?php endif; ?>
	<?php endif;?>
	</div>
		
	<div class="clear top-expand bottom-space border-bottom"></div>
</form>