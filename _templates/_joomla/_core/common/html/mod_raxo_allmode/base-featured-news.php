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
<div class="allmode-base featured-news">

	<?php if ($toplist) { ?>
		<div class="allmode-main-items<?php if(count($toplist) > 1) echo ' allmode-slider no-container'?>">
			<?php
			$tag		= 'div';
			$tagClass	= 'pb-3 mb-3';
			// Slider
			if(count($toplist) > 1) :
				$doc = JFactory::getDocument();
				// Importa biblioteca bxslider
				$doc->addStyleSheet(JURI::root().'templates/base/libs/content/bxslider/jquery.bxslider.min.css');
				$doc->addScript(JURI::root().'templates/base/libs/content/bxslider/jquery.bxslider.min.js');
				// CHAMADA DO SLIDER
				// esconde imagens no carregamento
				$doc->addStyleDeclaration('.allmode-slider-'.$module->id.' > li:not(:first-child) { position: absolute; top: 0; visibility: hidden; }');
				$script = '
				jQuery(window).on('load', function(){
					jQuery(".allmode-slider-'.$module->id.'").bxSlider({
						mode: "horizontal",
						autoHover: true,
						auto: true,
						pause: 7000,
						controls: false,
						pager: true,
						infiniteLoop: true,
						onSliderLoad:function(currentIndex){
							// mostra as imagens após o carregamento do plugin
							jQuery(".allmode-slider-'.$module->id.' img").attr("title","");
							jQuery(".allmode-slider-'.$module->id.' > li:not(:first-child)").css("visibility", "visible");
						}
					});
				});
				';
				$doc->addScriptDeclaration($script);
				echo '<ul id="allmode-slider-'.$module->id.'" class="bxslider">';
				$tag		= 'li';
				$tagClass	= '';
			endif;
			?>

				<?php
				// All-mode TOP Items Output
				foreach ($toplist as $item) {
				?>

					<<?php echo $tag?> class="allmode-main-item <?php echo $tagClass?> clearfix">
						<figure class="img-fluid">
							<?php if ($item->image) echo $item->image; ?>
							<?php if ($item->title) { ?>
								<figcaption>
									<a class="text-white" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
								</figcaption>
							<?php } ?>
						</figure>
					</<?php echo $tag?>>

				<?php } ?>
			<?php if($tag == 'li') echo '</ul>'; ?>
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
					<div class="allmode-image img-fluid"><?php echo $item->image; ?></div>
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
