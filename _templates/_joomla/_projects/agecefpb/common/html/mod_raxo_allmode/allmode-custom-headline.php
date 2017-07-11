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

<div class="row">
	<?php if ($toplist) { ?>
		<div class="main-headline col-lg-7">
		<?php
		// All-mode TOP Items Output
		foreach ($toplist as $item) { ?>

			<div class="headline pb-3 clearfix">
				<figure class="pos-relative img-fluid">
					<?php if ($item->image) echo $item->image; ?>
					<?php if ($item->title) { ?>
						<figcaption class="pos-absolute pos-bottom-0 w-full bg-black-80 p-2 p-md-3 text-lg lh-1-2">
							<a class="text-white" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
						</figcaption>
					<?php } ?>
				</figure>
			</div>

		<?php } ?>
	</div>
	<?php } ?>


	<?php if ($list) { ?>
	<div class="items-headline col">
	<?php
	// All-mode Items Output
	foreach ($list as $item) { ?>

		<div class="headline-item mb-3 clearfix">

			<?php if ($item->image) { ?>
			<figure class="float-left mr-3"><?php echo $item->image; ?></figure>
			<?php } ?>

			<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
			<ul class="headline-info list-inline bordered no-space small mb-1">

				<?php if ($item->category) { ?>
				<li class="headline-category list-inline-item"><?php echo $item->category; ?></li>
				<?php } ?>

				<?php if ($item->author) { ?>
				<li class="headline-author list-inline-item"><span class="base-icon-user"></span> <?php echo $item->author; ?></li>
				<?php } ?>

				<?php if ($item->date) { ?>
				<li class="headline-date list-inline-item"><?php echo $item->date; ?></li>
				<?php } ?>

				<?php if ($item->hits) { ?>
				<li class="headline-hits list-inline-item"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
				<?php } ?>

				<?php if ($item->comments_count) { ?>
				<li class="headline-comments list-inline-item">
					<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
				</li>
				<?php } ?>

				<?php if ($item->rating) { ?>
				<li class="headline-rating list-inline-item" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
				<?php } ?>

			</ul>
			<?php } ?>

			<?php if ($item->title) { ?>
				<h6 class="m-0 lh-1-2"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h6>
			<?php } ?>

		</div>

	<?php } ?>
	</div>
	<?php } ?>
</div>
