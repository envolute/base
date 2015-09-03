<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (!empty($this->searchword)):
?>

	<div class="search-results">
	<?php foreach($this->results as $result) : ?>
		<div class="search-result-item">
			<h4 class="result-title strong">
				<?php if ($result->href) :?>
					<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
						<?php echo $this->escape($result->title);?>
					</a>
				<?php else:?>
					<?php echo $this->escape($result->title);?>
				<?php endif; ?>
			</h4>
			<div class="result-text">
				<p><?php echo $result->text; ?></p>
				<?php if ($result->section) : ?>
					<p class="result-info small">
						<?php echo $this->escape($result->section); ?>
						<?php if ($this->params->get('show_date')) : ?>
							<span class="result-created">
								- <?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
							</span><br />
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
			
		</div>
		
		<hr />
		
	<?php endforeach; ?>
	</div>
	
	<div class="text-center">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>