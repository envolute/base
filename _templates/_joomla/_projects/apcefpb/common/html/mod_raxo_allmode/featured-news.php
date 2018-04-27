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
<div class="row no-gutters featured-news">

	<?php if ($toplist) { ?>
		<div class="col pr-2">
			<?php
			// All-mode TOP Items Output
			foreach ($toplist as $item) {
			?>
				<div class="clearfix">
					<figure class="img-fluid pos-relative">
						<?php if ($item->image) echo $item->image; ?>
						<?php if ($item->title) { ?>
							<figcaption class="pos-absolute pos-bottom-0 w-100 bg-dark-opacity-75 text-lg lh-1-2 py-2 px-3">
								<a class="text-white" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
							</figcaption>
						<?php } ?>
					</figure>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ($list) { ?>
		<div class="col-lg-4 pl-2">

			<ul class="set-list list-trim clearfix">
			<?php
			// All-mode Items Output
			foreach ($list as $item) { ?>

				<li class="p-0 clearfix">

					<?php if ($item->image) { ?>
					<figure class="img-fluid float-left mr-3 mb-3"><?php echo $item->image; ?></figure>
					<?php } ?>

					<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
					<ul class="set-list inline bordered list-trim small text-muted">

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

					<?php if ($item->title) { ?>
						<h6 class="font-condensed m-0"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h6>
					<?php } ?>

				</li>

			<?php } ?>
			</ul>

		</div>
	<?php } ?>

</div>
