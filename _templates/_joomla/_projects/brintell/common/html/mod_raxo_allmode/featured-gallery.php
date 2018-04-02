<?php
/**
 * =============================================================
 * RAXO All-mode PRO J3.x - Envolute Base Template
 * -------------------------------------------------------------
 * @copyright		Copyright (C) 2009-2014 Envolute
 * @license		GNU General Public License v2.0
 * 			http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.envolute.com
 * =============================================================
 */


// no direct access
defined('_JEXEC') or die;

// add template CSS
$app = JFactory::getApplication();
?>
<div class="featured-gallery">

	<?php if ($toplist) { ?>
		<div class="row">
			<?php
			// All-mode TOP Items Output
			foreach ($toplist as $item) {
			?>
				<div class="col-md-4">
					<figure class="img-fluid pos-relative">
						<?php if ($item->image) echo $item->image; ?>
						<?php if ($item->title) { ?>
							<a class="text-white" href="<?php echo $item->link; ?>">
								<figcaption class="pos-absolute pos-bottom-0 w-100 bg-dark-opacity-75 text-sm lh-1-2 p-2">
									<?php echo $item->title; ?>
								</figcaption>
							</a>
						<?php } ?>
					</figure>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ($list) { ?>
		<div class="row">
			<?php
			// All-mode Items Output
			foreach ($list as $item) { ?>
				<div class="col-md-3">
					<figure class="img-fluid mb-2">
						<?php if ($item->image) echo $item->image; ?>
					</figure>
					<?php if ($item->title) { ?>
						<figcaption class="text-sm">
							<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
							<ul class="set-list inline bordered list-trim small text-muted mb-1">

								<?php if ($item->category) { ?>
								<li><?php echo $item->category; ?></li>
								<?php } ?>

								<?php if ($item->author) { ?>
								<li><span class="base-icon-user"></span> <?php echo $item->author; ?></li>
								<?php } ?>

								<?php if ($item->date) { ?>
								<li><?php echo $item->date; ?></li>
								<?php } ?>

								<?php if ($item->hits) { ?>
								<li><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
								<?php } ?>

								<?php if ($item->comments_count) { ?>
								<li>
									<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
								</li>
								<?php } ?>

								<?php if ($item->rating) { ?>
								<li title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
								<?php } ?>

							</ul>
							<?php } ?>
							<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
						</figcaption>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

</div>
