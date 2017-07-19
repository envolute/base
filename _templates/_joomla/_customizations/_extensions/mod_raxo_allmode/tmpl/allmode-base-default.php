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
JHtml::stylesheet('modules/mod_raxo_allmode/tmpl/allmode-base-default/allmode-base-default.css');
?>


<?php if ($toplist) { ?>
<div class="allmode-base-topbox">
<?php
// All-mode TOP Items Output
foreach ($toplist as $item) { ?>

	<div class="allmode-base-topitem">

		<?php if ($item->image) { ?>
		<div class="allmode-base-img"><?php echo $item->image; ?></div>
		<?php } ?>

		<?php if ($item->date || $item->category || $item->hits || $item->comments_count || $item->rating) { ?>
		<ul class="allmode-base-info hlist hlist-condensed list-trim">

			<?php if ($item->date) { ?>
			<li class="allmode-base-date"><span class="base-icon-calendar"></span> <?php echo $item->date; ?></li>
			<?php } ?>

			<?php if ($item->category) { ?>
			<li class="allmode-base-category"><?php echo $item->category; ?></li>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<li class="allmode-base-hits"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
			<?php } ?>

			<?php if ($item->comments_count) { ?>
			<li class="allmode-base-comments">
				<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
			</li>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<li class="allmode-base-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
			<?php } ?>

		</ul>
		<?php } ?>

		<?php if ($item->title) { ?>
		<h3 class="allmode-base-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h3>
		<?php } ?>

		<?php if ($item->author) { ?>
			<div class="text-muted text-xs"><span class="base-icon-user"></span> <?php echo $item->author; ?></div>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-base-text"><?php echo $item->text; ?>

			<?php if ($item->readmore) { ?>
			<span class="allmode-base-readmore"><?php echo $item->readmore; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

	</div>

<?php } ?>
</div>
<?php } ?>


<?php if ($list) { ?>
<div class="allmode-base-itemsbox">
<?php																			// All-mode Items Output
foreach ($list as $item) { ?>

	<div class="allmode-base-item">

		<?php if ($item->image) { ?>
		<div class="allmode-base-img"><?php echo $item->image; ?></div>
		<?php } ?>

		<?php if ($item->date || $item->category || $item->hits || $item->comments_count || $item->rating) { ?>
		<ul class="allmode-base-info hlist hlist-condensed list-trim">

			<?php if ($item->date) { ?>
			<li class="allmode-base-date"><span class="base-icon-calendar"></span> <?php echo $item->date; ?></li>
			<?php } ?>

			<?php if ($item->category) { ?>
			<li class="allmode-base-category"><?php echo $item->category; ?></li>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<li class="allmode-base-hits"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
			<?php } ?>

			<?php if ($item->comments_count) { ?>
			<li class="allmode-base-comments">
				<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
			</li>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<li class="allmode-base-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
			<?php } ?>

		</ul>
		<?php } ?>

		<?php if ($item->title) { ?>
			<h4 class="allmode-base-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h4>
		<?php } ?>

		<?php if ($item->author) { ?>
			<div class="text-muted text-xs"><span class="base-icon-user"></span> <?php echo $item->author; ?></div>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-base-text"><?php echo $item->text; ?>

			<?php if ($item->readmore) { ?>
			<span class="allmode-base-readmore"><?php echo $item->readmore; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

	</div>

<?php } ?>
</div>
<?php } ?>
