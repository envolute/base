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
$tmplDir = JURI::base() . 'templates/' . $app->getTemplate();
JHtml::stylesheet($tmplDir.'/html/mod_raxo_allmode/allmode-base-default/allmode-base-default.css');
?>


<?php if ($toplist) { ?>
<div class="allmode-base-topbox">
<?php
// All-mode TOP Items Output
foreach ($toplist as $item) { ?>

	<div class="allmode-base-topitem">

		<?php if ($item->image) { ?>
		<figure><?php echo $item->image; ?></figure>
		<?php } ?>

		<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
		<ul class="allmode-base-info list-inline bordered list-trim">

			<?php if ($item->category) { ?>
			<li class="allmode-base-category list-inline-item"><?php echo $item->category; ?></li>
			<?php } ?>

			<?php if ($item->author) { ?>
			<li class="allmode-base-author list-inline-item"><span class="base-icon-user"></span> <?php echo $item->author; ?></li>
			<?php } ?>

			<?php if ($item->date) { ?>
			<li class="allmode-base-date list-inline-item"> <?php echo $item->date; ?></li>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<li class="allmode-base-hits list-inline-item"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
			<?php } ?>

			<?php if ($item->comments_count) { ?>
			<li class="allmode-base-comments list-inline-item">
				<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
			</li>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<li class="allmode-base-rating list-inline-item" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
			<?php } ?>

		</ul>
		<?php } ?>

		<?php if ($item->title) { ?>
		<h4><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h4>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-base-desc"><?php echo $item->text; ?>

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
		<figure><?php echo $item->image; ?></figure>
		<?php } ?>

		<?php if ($item->category || $item->author || $item->date || $item->hits || $item->comments_count || $item->rating) { ?>
		<ul class="allmode-base-info list-inline bordered list-trim">

			<?php if ($item->category) { ?>
			<li class="allmode-base-category list-inline-item"><?php echo $item->category; ?></li>
			<?php } ?>

			<?php if ($item->author) { ?>
			<li class="allmode-base-author list-inline-item"><span class="base-icon-user"></span> <?php echo $item->author; ?></li>
			<?php } ?>

			<?php if ($item->date) { ?>
			<li class="allmode-base-date list-inline-item"><?php echo $item->date; ?></li>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<li class="allmode-base-hits list-inline-item"><span class="base-icon-chart-bar"></span> <?php echo $item->hits; ?></li>
			<?php } ?>

			<?php if ($item->comments_count) { ?>
			<li class="allmode-base-comments list-inline-item">
				<a href="<?php echo $item->comments_link; ?>"><span class="base-icon-chat"></span> <?php echo $item->comments_count; ?></a>
			</li>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<li class="allmode-base-rating list-inline-item" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></li>
			<?php } ?>

		</ul>
		<?php } ?>

		<?php if ($item->title) { ?>
			<h5><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h5>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-base-desc"><?php echo $item->text; ?>

			<?php if ($item->readmore) { ?>
			<span class="allmode-base-readmore"><?php echo $item->readmore; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

	</div>

<?php } ?>
</div>
<?php } ?>
