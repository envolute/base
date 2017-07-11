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

<?php if ($toplist) { ?>
	<div class="main-article row">
	<?php
	// All-mode TOP Items Output
	foreach ($toplist as $item) { ?>

		<div class="article col-md-6 mb-4 clearfix">

			<?php if ($item->image) { ?>
			<figure class="img-fluid"><?php echo $item->image; ?></figure>
			<?php } ?>

			<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
			<ul class="article-info list-inline bordered no-space small mb-1">

				<?php if ($item->category) { ?>
				<li class="article-category list-inline-item"><?php echo $item->category; ?></li>
				<?php } ?>

				<?php if ($item->author) { ?>
				<li class="article-author list-inline-item"><span class="base-icon-user"></span> <?php echo $item->author; ?></li>
				<?php } ?>

				<?php if ($item->date) { ?>
				<li class="article-date list-inline-item"><?php echo $item->date; ?></li>
				<?php } ?>

				<?php if ($item->hits) { ?>
				<li class="article-hits list-inline-item"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
				<?php } ?>

				<?php if ($item->comments_count) { ?>
				<li class="article-comments list-inline-item">
					<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
				</li>
				<?php } ?>

				<?php if ($item->rating) { ?>
				<li class="article-rating list-inline-item" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
				<?php } ?>

			</ul>
			<?php } ?>

			<?php if ($item->title) { ?>
				<h5 class="pb-3 m-0 lh-1-2 border-bottom border-bottom-2 border-primary" style="min-height:4.3rem">
					<a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
				</h5>
			<?php } ?>

		</div>

	<?php } ?>
</div>
<?php } ?>

<?php if ($list) { ?>
<div class="items-article row">
	<?php
	// All-mode Items Output
	foreach ($list as $item) { ?>

		<div class="article-item col-md-6 mb-4 clearfix">

			<?php if ($item->image) { ?>
			<figure class="img-fluid"><?php echo $item->image; ?></figure>
			<?php } ?>

			<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
			<ul class="article-info list-inline bordered no-space small mb-1">

				<?php if ($item->category) { ?>
				<li class="article-category list-inline-item"><?php echo $item->category; ?></li>
				<?php } ?>

				<?php if ($item->author) { ?>
				<li class="article-author list-inline-item"><span class="base-icon-user"></span> <?php echo $item->author; ?></li>
				<?php } ?>

				<?php if ($item->date) { ?>
				<li class="article-date list-inline-item"><?php echo $item->date; ?></li>
				<?php } ?>

				<?php if ($item->hits) { ?>
				<li class="article-hits list-inline-item"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
				<?php } ?>

				<?php if ($item->comments_count) { ?>
				<li class="article-comments list-inline-item">
					<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
				</li>
				<?php } ?>

				<?php if ($item->rating) { ?>
				<li class="article-rating list-inline-item" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
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
