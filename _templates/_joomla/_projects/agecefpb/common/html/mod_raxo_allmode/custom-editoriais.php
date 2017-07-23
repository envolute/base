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
<div class="allmode-base custom-editoriais">

	<?php if ($toplist) { ?>
		<div class="allmode-main-items">
			<?php
			// All-mode TOP Items Output
			foreach ($toplist as $item) { ?>

				<div class="allmode-main-item row no-gutters b-top-2 b-bottom-2 b-primary mb-4 clearfix">

					<div class="col-6 col-md-8 col-lg-7 col-xl-6 zindex-1" style="min-height: 132px;">
						<?php if ($item->title) { ?>
							<h5 class="lh-1-1 pt-2 pr-2">
								<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
							</h5>
						<?php } ?>
						<?php if ($item->category || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
						<ul class="allmode-info set-list inline bordered list-trim">

							<?php if ($item->category) { ?>
							<li class="allmode-category"><?php echo $item->category; ?></li>
							<?php } ?>

							<?php if ($item->date) { ?>
							<li class="allmode-date"><?php echo $item->date; ?></li>
							<?php } ?>

							<?php if ($item->hits) { ?>
							<li class="allmode-hits"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
							<?php } ?>

							<?php if ($item->comments_count) { ?>
							<li class="allmode-comments">
								<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
							</li>
							<?php } ?>

							<?php if ($item->rating) { ?>
							<li class="allmode-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
							<?php } ?>

						</ul>
						<?php } ?>
					</div>
					<div class="col">
						<?php if ($item->image) { ?>
						<figure class="img-fluid m-0 text-right"><?php echo $item->image; ?></figure>
						<?php } ?>
					</div>
					<div class="col-12 p-2 lh-1 bg-gray-200">
						<?php if ($item->author) { ?>
						<small>Autor:</small><div class="font-condensed text-truncate"><?php echo $item->author?></div>
						<?php } ?>
					</div>

				</div>

			<?php } ?>
		</div>
	<?php } ?>

	<?php if ($list) { ?>
		<div class="allmode-sub-items">

			<ul class="clearfix">
			<?php
			// All-mode Items Output
			foreach ($list as $item) { ?>

				<li class="allmode-sub-item clearfix">

					<?php if ($item->image) { ?>
					<div class="allmode-image"><?php echo $item->image; ?></div>
					<?php } ?>

					<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
					<ul class="allmode-info set-list inline bordered list-trim">

						<?php if ($item->category) { ?>
						<li class="allmode-category"><?php echo $item->category; ?></li>
						<?php } ?>

						<?php if ($item->author) { ?>
						<li class="allmode-author"><span class="base-icon-user"></span> <?php echo $item->author; ?></li>
						<?php } ?>

						<?php if ($item->date) { ?>
						<li class="allmode-date"><?php echo $item->date; ?></li>
						<?php } ?>

						<?php if ($item->hits) { ?>
						<li class="allmode-hits"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
						<?php } ?>

						<?php if ($item->comments_count) { ?>
						<li class="allmode-comments">
							<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
						</li>
						<?php } ?>

						<?php if ($item->rating) { ?>
						<li class="allmode-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
						<?php } ?>

					</ul>
					<?php } ?>

					<?php if ($item->title) { ?>
						<h6><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h6>
					<?php } ?>

				</li>

			<?php } ?>
			</ul>

		</div>
	<?php } ?>

</div>
