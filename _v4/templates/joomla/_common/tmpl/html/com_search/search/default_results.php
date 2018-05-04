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

	<div class="search-results pt-3">
	<?php foreach($this->results as $result) : ?>
		<div class="search-result-item">
			<h5 class="result-title mb-1 strong">
				<?php if ($result->href) :?>
					<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
						<?php echo $this->escape($result->title);?>
					</a>
				<?php else:?>
					<?php echo $this->escape($result->title);?>
				<?php endif; ?>
			</h5>
			<?php if(!empty($result->text)) : ?>
				<div class="result-text pt-1 lh-1-2">
					<?php echo $result->text; ?>
				</div>
			<?php endif; ?>
			<?php if ($result->section) : ?>
				<div class="result-info pt-1 small">
					<?php echo $this->escape($result->section); ?>
					<?php if ($this->params->get('show_date')) : ?>
						<span class="result-created">
							- <?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
						</span><br />
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<hr />
	<?php endforeach; ?>
	</div>

	<div class="text-center">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>
